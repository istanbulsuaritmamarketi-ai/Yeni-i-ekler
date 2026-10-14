<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Region;
use App\Models\ServiceRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportMongoData extends Command
{
    protected $signature = 'isam:import-mongo {--file=database/data/mongo_data.json}';

    protected $description = 'Eski MongoDB Atlas müşteri, servis ve hatırlatıcı verilerini MySQL veritabanına aktarır';

    public function handle()
    {
        $filePath = base_path($this->option('file'));

        if (! file_exists($filePath)) {
            $this->error("Veri dosyası bulunamadı: {$filePath}");

            return 1;
        }

        $this->info("Veri dosyası okunuyor: {$filePath}...");
        $raw = file_get_contents($filePath);
        $data = json_decode($raw, true);

        if (! $data || ! isset($data['customers'])) {
            $this->error('Geçersiz JSON formatı!');

            return 1;
        }

        $customersData = $data['customers'] ?? [];
        $servicesData = $data['services'] ?? [];
        $remindersData = $data['reminders'] ?? [];

        $this->info('Toplam '.count($customersData).' müşteri, '.count($servicesData).' servis ve '.count($remindersData).' hatırlatıcı bulundu.');

        $defaultRegion = Region::first();
        $defaultRegionId = $defaultRegion?->id;

        // 1. MÜŞTERİLERİ AKTAR
        $this->info("\n--- 1. Müşteriler Aktarılıyor ---");
        $mongoIdMap = [];
        $importedCustomers = 0;
        $updatedCustomers = 0;

        foreach ($customersData as $c) {
            $mongoOid = $c['_id']['$oid'] ?? null;
            $name = trim(($c['name'] ?? '').' '.($c['surname'] ?? ''));
            $phone = trim($c['phone'] ?? '');
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

            if (empty($phone) && empty($name)) {
                continue;
            }

            $createdAt = isset($c['created_at']['$date'])
                ? Carbon::parse($c['created_at']['$date'])
                : Carbon::now();

            $customer = null;
            if ($phone) {
                $customer = Customer::where('phone', $phone)
                    ->orWhere('phone', $cleanPhone)
                    ->first();
            }

            if ($customer) {
                $customer->update([
                    'name' => $name ?: $customer->name,
                    'address' => $c['address'] ?? $customer->address,
                    'device_brand' => $c['device_brand'] ?? $customer->device_brand,
                    'device_model' => $c['device_model'] ?? $customer->device_model,
                    'tds_value' => $c['tds_value'] ?? $customer->tds_value,
                ]);
                $updatedCustomers++;
            } else {
                $customer = Customer::create([
                    'name' => $name ?: 'İsimsiz Müşteri',
                    'phone' => $phone,
                    'address' => $c['address'] ?? 'Belirtilmedi',
                    'region_id' => $defaultRegionId,
                    'device_brand' => $c['device_brand'] ?? null,
                    'device_model' => $c['device_model'] ?? null,
                    'tds_value' => $c['tds_value'] ?? null,
                    'status' => 'active',
                ]);

                $customer->created_at = $createdAt;
                $customer->updated_at = $createdAt;
                $customer->saveQuietly();
                $importedCustomers++;
            }

            if ($mongoOid) {
                $mongoIdMap[$mongoOid] = $customer;
            }
        }

        $this->info("✓ {$importedCustomers} yeni müşteri eklendi, {$updatedCustomers} mevcut müşteri güncellendi.");

        // 2. SERVİSLERİ VE FİLTRE DEĞİŞİMLERİNİ AKTAR
        $this->info("\n--- 2. Servis Geçmişi ve Filtre Değişimleri Aktarılıyor ---");
        $serviceMongoMap = [];
        $importedServices = 0;

        $serviceTypeLabels = [
            'montaj' => 'Yeni Montaj / Kurulum',
            'bakim' => 'Periyodik Bakım',
            'filtre_degisimi' => 'Filtre Değişimi',
            'ariza' => 'Arıza / Tamir',
        ];

        foreach ($servicesData as $s) {
            $sMongoOid = $s['_id']['$oid'] ?? null;
            $custMongoOid = $s['customer_id'] ?? null;

            $customer = $mongoIdMap[$custMongoOid] ?? null;

            if (! $customer) {
                continue;
            }

            $serviceCreatedAt = isset($s['created_at']['$date'])
                ? Carbon::parse($s['created_at']['$date'])
                : Carbon::now();

            $reminderDate = null;
            if (isset($s['reminder_date']['$date'])) {
                $reminderDate = Carbon::parse($s['reminder_date']['$date']);
            } elseif (isset($s['reminder_date']) && is_string($s['reminder_date'])) {
                try {
                    $reminderDate = Carbon::parse($s['reminder_date']);
                } catch (\Exception $e) {
                }
            }

            // Fotoğrafları base64'ten çöz ve storage'a kaydet
            $savedPhotos = [];
            if (! empty($s['photos']) && is_array($s['photos'])) {
                foreach ($s['photos'] as $idx => $photoData) {
                    if (str_starts_with($photoData, 'data:image')) {
                        try {
                            @[$type, $dataPart] = explode(';', $photoData);
                            @[, $dataPart] = explode(',', $dataPart);
                            $decoded = base64_decode($dataPart);
                            if ($decoded) {
                                $fileName = 'services/mongo_'.($sMongoOid ?: uniqid())."_{$idx}.jpg";
                                Storage::disk('public')->put($fileName, $decoded);
                                $savedPhotos[] = Storage::disk('public')->url($fileName);
                            }
                        } catch (\Exception $e) {
                        }
                    } elseif (filter_var($photoData, FILTER_VALIDATE_URL)) {
                        $savedPhotos[] = $photoData;
                    }
                }
            }

            $rawType = strtolower($s['service_type'] ?? 'filtre_degisimi');
            $typeFormatted = $serviceTypeLabels[$rawType] ?? ($s['service_type'] ?? 'Filtre Değişimi');

            // Var olan aynı servisi mükerrer kaydetmemek için kontrol
            $existingService = ServiceRequest::where('customer_id', $customer->id)
                ->whereDate('created_at', $serviceCreatedAt->toDateString())
                ->first();

            if ($existingService) {
                if ($sMongoOid) {
                    $serviceMongoMap[$sMongoOid] = $existingService;
                }

                continue;
            }

            $service = new ServiceRequest;
            $service->customer_id = $customer->id;
            $service->customer_name = $customer->name;
            $service->customer_phone = $customer->phone;
            $service->region_id = $customer->region_id ?: $defaultRegionId;
            $service->address = $customer->address ?? 'Belirtilmedi';
            $service->service_type = $typeFormatted;
            $service->changed_filters = $s['changed_filters'] ?? null;
            $service->service_note = $s['service_note'] ?? null;
            $service->issue_description = $s['service_note'] ?? 'Geçmiş servis kaydı';
            $service->device_info = trim(($customer->device_brand ?? '').' '.($customer->device_model ?? ''));
            $service->tds_value = $s['tds_value'] ?? null;
            $service->service_price = $s['price'] ?? 0;
            $service->photos = $savedPhotos;
            $service->preferred_date = $serviceCreatedAt->toDateString();
            $service->preferred_time = $serviceCreatedAt->format('H:i');
            $service->assigned_staff = 'Mobil Saha Servisi';
            $service->next_maintenance_date = $reminderDate;
            $service->is_reminder_sent = false;
            $service->status = 'Tamamlandı';
            $service->created_at = $serviceCreatedAt;
            $service->updated_at = $serviceCreatedAt;
            $service->saveQuietly();

            if ($sMongoOid) {
                $serviceMongoMap[$sMongoOid] = $service;
            }
            $importedServices++;
        }

        $this->info("✓ {$importedServices} adet servis geçmişi başarıyla oluşturuldu.");

        // 3. HATIRLATICILARI EŞLEŞTİR
        $this->info("\n--- 3. Hatırlatıcılar Senkronize Ediliyor ---");
        $syncedReminders = 0;

        foreach ($remindersData as $r) {
            $srvOid = $r['service_id'] ?? null;
            $isSent = (bool) ($r['is_sent'] ?? false);

            if ($srvOid && isset($serviceMongoMap[$srvOid])) {
                $service = $serviceMongoMap[$srvOid];
                $service->is_reminder_sent = $isSent;
                $service->saveQuietly();
                $syncedReminders++;
            }
        }

        $this->info("✓ {$syncedReminders} adet hatırlatıcı durumu güncellendi.");
        $this->info("\n========================================================");
        $this->info(" TÜM MONGODB VERİLERİ CENUTA MYSQL'E BAŞARIYLA AKTARILDI! ");
        $this->info("========================================================\n");

        return 0;
    }
}
