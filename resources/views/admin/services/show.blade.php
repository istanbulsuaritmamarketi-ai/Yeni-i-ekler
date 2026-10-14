<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Servis Detayı: ') }} <span class="text-blue-600">#{{ $service->id }}</span>
            </h2>
            @php
                $cleanPhone = preg_replace('/[^0-9]/', '', $service->customer_phone);
                if (str_starts_with($cleanPhone, '0')) {
                    $cleanPhone = '90' . substr($cleanPhone, 1);
                } elseif (!str_starts_with($cleanPhone, '90')) {
                    $cleanPhone = '90' . $cleanPhone;
                }
                
                $whatsappMsg = "💧 *İSTANBUL SU ARITMA MARKETİ - SERVİS BİLGİLENDİRMESİ*\n\n"
                             . "Sayın *" . $service->customer_name . "*,\n\n"
                             . "Servis Kaydınız: *#" . $service->id . "*\n"
                             . "Durum: *" . $service->status . "*\n"
                             . "Adres: " . ($service->district ?? '') . " / " . ($service->address ?? '') . "\n"
                             . "Talep / İşlem: " . $service->service_type . ($service->notes ? " - " . $service->notes : "") . "\n";
                if ($service->service_price > 0) {
                    $whatsappMsg .= "💰 Tutar: *" . number_format($service->service_price, 2) . " TL* (" . ($service->payment_method ?: 'Kapıda Ödeme') . ")\n";
                }
                if ($service->status === 'Tamamlandı') {
                    $whatsappMsg .= "\n✅ *Servisiniz ve montajınız başarıyla tamamlanmıştır.*\n"
                                 . "📅 Bir sonraki periyodik filtre bakım vadeniz: *" . now()->addMonths(6)->format('d.m.Y') . "*\n\n";
                }
                $whatsappMsg .= "\nBizi tercih ettiğiniz için teşekkür eder, sağlıklı günler dileriz! 💧\n"
                             . "📞 İletişim: " . ($siteSettings['phone'] ?? '0850 555 55 55') . "\n"
                             . "🌐 https://istanbulsuaritmamarketi.com";
                
                $whatsappUrl = "https://wa.me/" . $cleanPhone . "?text=" . urlencode($whatsappMsg);
            @endphp
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ $whatsappUrl }}" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-xl text-xs flex items-center gap-1.5 shadow-sm transition-colors">
                    <i class="fab fa-whatsapp text-sm"></i> WhatsApp Fişi / Mesajı Gönder
                </a>
                <a href="{{ route('admin.services.print', $service) }}" target="_blank" class="bg-gray-800 hover:bg-gray-900 text-white font-bold px-4 py-2 rounded-xl text-xs flex items-center gap-1.5 shadow-sm transition-colors">
                    <i class="fas fa-print"></i> Servis Fişi (PDF)
                </a>
                <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="inline" onsubmit="return confirm('Bu servis kaydını tamamen silmek istediğinize emin misiniz?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-700 font-bold px-3 py-2 rounded-xl text-xs flex items-center gap-1.5 border border-red-200 transition-colors">
                        <i class="fas fa-trash-alt"></i> Sil
                    </button>
                </form>
                <a href="{{ route('admin.services.index') }}" class="text-gray-600 hover:text-gray-900 border px-4 py-2 rounded-xl text-xs font-bold bg-white">
                    Geri Dön
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Durum ve Notlar -->
            <div class="md:col-span-1 space-y-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Durum Güncelle</h3>
                        @if(session('success'))
                            <div class="text-green-600 text-sm mb-4">{{ session('success') }}</div>
                        @endif
                        <form action="{{ route('admin.services.update', $service) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Servis Durumu</label>
                                <select name="status" class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="Bekliyor" {{ $service->status == 'Bekliyor' ? 'selected' : '' }}>Bekliyor</option>
                                    <option value="Randevu Verildi" {{ $service->status == 'Randevu Verildi' ? 'selected' : '' }}>Randevu Verildi</option>
                                    <option value="Teknisyen Yolda" {{ $service->status == 'Teknisyen Yolda' ? 'selected' : '' }}>Teknisyen Yolda</option>
                                    <option value="Tamamlandı" {{ $service->status == 'Tamamlandı' ? 'selected' : '' }}>Tamamlandı</option>
                                    <option value="İptal Edildi" {{ $service->status == 'İptal Edildi' ? 'selected' : '' }}>İptal Edildi</option>
                                </select>
                            </div>
                            <div class="mb-4 border-t pt-4">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Servis / İşlem Ücreti (TL)</label>
                                <input type="number" step="0.01" name="service_price" value="{{ $service->service_price }}" class="w-full rounded border-gray-300 font-extrabold text-blue-600 bg-blue-50/50 focus:border-blue-500 focus:ring-blue-500" placeholder="Örn: 750">
                                <p class="text-xs text-gray-500 mt-1">İşlem sonunda alınan veya alınacak tutar.</p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Tahsilat / Ödeme Şekli</label>
                                <select name="payment_method" class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Seçilmedi (Fişte Kalemle Seç)</option>
                                    <option value="Nakit" {{ $service->payment_method == 'Nakit' ? 'selected' : '' }}>Nakit</option>
                                    <option value="Kredi Kartı / POS" {{ $service->payment_method == 'Kredi Kartı / POS' ? 'selected' : '' }}>Kredi Kartı / POS</option>
                                    <option value="Havale / EFT" {{ $service->payment_method == 'Havale / EFT' ? 'selected' : '' }}>Havale / EFT</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Atanan Personel / Ekip</label>
                                <input type="text" name="assigned_staff" value="{{ $service->assigned_staff }}" class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Örn: Ahmet Usta">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Gelecek Bakım Tarihi</label>
                                <input type="date" name="next_maintenance_date" value="{{ $service->next_maintenance_date }}" class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Filtre değişimi veya periyodik bakım hatırlatması için.</p>
                            </div>
                            <div class="mb-4 border-t pt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Admin Notu (Müşteri Görmez)</label>
                                <textarea name="admin_notes" rows="4" class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Örn: LG filtre takıldı, 6 ay sonra aranacak.">{{ $service->admin_notes }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Bu notlar sadece personel içindir.</p>
                            </div>
                            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-semibold hover:bg-blue-700">Kaydet</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Müşteri ve Talep Detayları -->
            <div class="md:col-span-2 space-y-6">
                <!-- Talep Bilgileri -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4 border-b pb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">{{ $service->service_type }}</h3>
                                <p class="text-gray-500 text-sm mt-1">Talep Tarihi: {{ $service->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-right bg-blue-50 p-3 rounded-xl border border-blue-100">
                                    <p class="text-xs text-blue-800 font-medium mb-0.5">Randevu Tercihi</p>
                                    <p class="text-sm font-bold text-blue-900">{{ \Carbon\Carbon::parse($service->preferred_date)->format('d.m.Y') }}</p>
                                    <p class="text-xs text-blue-800">{{ $service->preferred_time }}</p>
                                </div>
                                <div class="text-right bg-emerald-50 p-3 rounded-xl border border-emerald-100">
                                    <p class="text-xs text-emerald-800 font-medium mb-0.5">İşlem / Servis Ücreti</p>
                                    <p class="text-base font-extrabold text-emerald-700">
                                        {{ $service->service_price ? number_format($service->service_price, 2, ',', '.') . ' TL' : 'Girilmedi' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                            <div>
                                <h4 class="font-bold text-gray-700 mb-2">Müşteri Bilgileri</h4>
                                <div class="bg-gray-50 p-3 rounded border">
                                    <p class="mb-1"><span class="text-gray-500">Ad Soyad:</span> <span class="font-medium">{{ $service->customer_name }}</span></p>
                                    <p class="mb-1"><span class="text-gray-500">Telefon:</span> <a href="tel:{{ $service->customer_phone }}" class="font-medium text-blue-600">{{ $service->customer_phone }}</a></p>
                                    <p><span class="text-gray-500">Bölge:</span> <span class="font-medium">{{ $service->region->name ?? 'Bilinmiyor' }}</span></p>
                                    
                                    @php
                                        $cleanPhone2 = preg_replace('/[^0-9]/', '', $service->customer_phone);
                                        if (str_starts_with($cleanPhone2, '0')) {
                                            $cleanPhone2 = '9' . $cleanPhone2;
                                        } elseif (!str_starts_with($cleanPhone2, '90') && strlen($cleanPhone2) == 10) {
                                            $cleanPhone2 = '90' . $cleanPhone2;
                                        }
                                        $waServiceMsg = "Merhaba Sn. " . $service->customer_name . ",\n\nİstanbul Su Arıtma Marketi teknik servis talebinizin durumu: *" . $service->status . "* olarak güncellenmiştir." . ($service->assigned_staff ? "\nTeknik Personelimiz: *" . $service->assigned_staff . "*" : "") . "\n\nHerhangi bir sorunuz olursa bu numaradan bize ulaşabilirsiniz. Sağlıklı günler dileriz!";
                                    @endphp
                                    <div class="mt-3 pt-3 border-t">
                                        <a href="https://wa.me/{{ $cleanPhone2 }}?text={{ urlencode($waServiceMsg) }}" target="_blank"
                                            class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-3 rounded-lg flex items-center justify-center gap-1.5 text-xs shadow-sm transition-colors">
                                            <i class="fab fa-whatsapp text-sm"></i> WhatsApp'tan Durum Bildir
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-700 mb-2">Açık Adres</h4>
                                <div class="bg-gray-50 p-3 rounded border h-full">
                                    <p class="text-gray-800">{{ $service->address }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h4 class="font-bold text-gray-700 mb-2">Cihaz Bilgisi</h4>
                            <p class="bg-gray-50 p-3 rounded border text-gray-800">{{ $service->device_info ?: 'Belirtilmemiş' }}</p>
                        </div>

                        <div class="mt-6">
                            <h4 class="font-bold text-gray-700 mb-2">Sorun veya Talep Detayı</h4>
                            <p class="bg-gray-50 p-4 rounded border text-gray-800 whitespace-pre-wrap">{{ $service->issue_description ?: 'Müşteri detay girmemiş.' }}</p>
                        </div>

                        @if($service->image_path)
                            <div class="mt-6">
                                <h4 class="font-bold text-gray-700 mb-2">Müşterinin Yüklediği Görsel</h4>
                                <a href="{{ asset('storage/' . $service->image_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $service->image_path) }}" alt="Servis Görseli" class="max-w-xs rounded border shadow-sm hover:opacity-90 transition-opacity">
                                </a>
                            </div>
                        @endif

                        <div class="mt-8 flex justify-end">
                            <a href="https://wa.me/90{{ ltrim(str_replace([' ', '-', '(', ')'], '', $service->customer_phone), '0') }}?text=Merhaba%20{{ urlencode($service->customer_name) }},%20Su%20Arıtma%20Market%20üzerinden%20oluşturduğunuz%20{{ urlencode($service->service_type) }}%20talebiniz%20için%20ulaşıyoruz." target="_blank" class="inline-flex items-center bg-green-500 text-white font-medium px-4 py-2 rounded hover:bg-green-600">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                Müşteriye WhatsApp'tan Ulaş
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
