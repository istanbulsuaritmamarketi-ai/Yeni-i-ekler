<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servis-Fisi-{{ $service->id }} - {{ $settings['receipt_company_title'] ?? config('app.name', 'İstanbul Su Arıtma Marketi') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-gray-100 p-4 sm:p-8 text-gray-800">

    <!-- Yazdır, Ücret & Tahsilat Ayar Çubuğu (Yazdırmada Gizlenir) -->
    <div class="max-w-3xl mx-auto mb-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-200 flex flex-wrap justify-between items-center gap-4 no-print">
        <a href="{{ route('admin.services.show', $service) }}" class="text-xs font-bold text-gray-600 hover:text-gray-900 bg-gray-50 px-3.5 py-2 rounded-xl border transition-colors">
            &larr; Servis Detayına Dön
        </a>

        <!-- Hızlı Kontroller (Ücret & Tahsilat Şekli) -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- Ücret Girişi -->
            <div class="flex items-center gap-1.5">
                <span class="text-xs font-bold text-gray-600">Tutar:</span>
                <input type="text" 
                    id="priceInput"
                    placeholder="Boş (Kalemle)" 
                    value="{{ $service->service_price && $service->service_price > 0 ? number_format($service->service_price, 2, ',', '.') : '' }}"
                    oninput="updatePrintPrice(this.value)"
                    class="text-xs font-bold rounded-xl border-gray-300 py-1.5 px-2.5 w-32 focus:ring-blue-500 focus:border-blue-500 text-blue-700 bg-blue-50/50">
            </div>

            <!-- Tahsilat Şekli Seçimi -->
            <div class="flex items-center gap-1.5">
                <span class="text-xs font-bold text-gray-600">Ödeme:</span>
                <select id="paymentSelect" onchange="updatePrintPayment(this.value)" class="text-xs font-bold rounded-xl border-gray-300 py-1.5 px-2.5 focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                    <option value="" {{ empty($service->payment_method) ? 'selected' : '' }}>Boş (Kalemle İşaretle)</option>
                    <option value="Nakit" {{ $service->payment_method == 'Nakit' ? 'selected' : '' }}>Nakit</option>
                    <option value="POS" {{ in_array($service->payment_method, ['POS', 'Kredi Kartı / POS', 'Kredi Kartı']) ? 'selected' : '' }}>Kredi Kartı / POS</option>
                    <option value="Havale" {{ in_array($service->payment_method, ['Havale', 'Havale / EFT', 'EFT']) ? 'selected' : '' }}>Havale / EFT</option>
                </select>
            </div>
        </div>

        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2 rounded-xl shadow text-xs flex items-center gap-2 transition-colors">
            🖨️ Fişi Yazdır (A4)
        </button>
    </div>

    <!-- A4 Servis Fişi Kağıdı -->
    <div class="max-w-3xl mx-auto bg-white p-8 sm:p-12 rounded-2xl shadow-md border border-gray-200">
        
        <!-- Üst Başlık & Firma Bilgileri -->
        <div class="flex justify-between items-start border-b pb-8">
            <div>
                <h1 class="text-2xl font-black text-blue-900 tracking-tight">
                    {{ $settings['receipt_company_title'] ?? 'İSTANBUL SU ARITMA MARKETİ' }}
                </h1>
                <p class="text-xs text-gray-500 mt-1">Yetkili Montaj, Filtre Değişimi &amp; Teknik Servis</p>
                <p class="text-xs text-gray-500">İstanbul / Türkiye | İletişim: {{ $settings['receipt_phone'] ?? ($settings['phone'] ?? '0850 ...') }}</p>
            </div>
            <div class="text-right">
                <span class="inline-block bg-emerald-50 text-emerald-800 text-xs font-extrabold px-3 py-1 rounded-lg border border-emerald-200 mb-1">
                    TEKNİK SERVİS FİŞİ
                </span>
                <p class="text-sm font-bold text-gray-900">#SRV-{{ str_pad($service->id, 5, '0', STR_PAD_LEFT) }}</p>
                <p class="text-xs text-gray-500">{{ $service->created_at->translatedFormat('d F Y - H:i') }}</p>
            </div>
        </div>

        <!-- Müşteri ve Adres Bilgisi -->
        <div class="grid grid-cols-2 gap-8 my-8 text-xs leading-relaxed">
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                <h3 class="font-bold text-gray-900 uppercase text-[11px] mb-2 text-blue-900">Müşteri Bilgileri</h3>
                <p><strong class="text-gray-700">Müşteri Adı:</strong> {{ $service->customer_name }}</p>
                <p><strong class="text-gray-700">Telefon:</strong> {{ $service->customer_phone }}</p>
                <p><strong class="text-gray-700">İlçe / Bölge:</strong> {{ $service->region->name ?? 'Belirtilmemiş' }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                <h3 class="font-bold text-gray-900 uppercase text-[11px] mb-2 text-blue-900">Servis &amp; Randevu</h3>
                <p><strong class="text-gray-700">İşlem Türü:</strong> {{ $service->service_type }}</p>
                <p><strong class="text-gray-700">Randevu Tarihi:</strong> {{ \Carbon\Carbon::parse($service->preferred_date)->format('d.m.Y') }} ({{ $service->preferred_time }})</p>
                <p><strong class="text-gray-700">Atanan Teknisyen:</strong> {{ $service->assigned_staff ?: 'Teknik Ekip' }}</p>
            </div>
        </div>

        <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100 text-xs">
            <p><strong class="text-gray-700">Adres:</strong> {{ $service->address }}</p>
        </div>

        <!-- Cihaz ve İşlem Detayları -->
        <div class="mb-6 overflow-hidden rounded-xl border border-gray-200 text-xs">
            <table class="w-full text-left">
                <thead class="bg-gray-100 text-gray-600 uppercase text-[10px] font-bold">
                    <tr>
                        <th class="p-3">Yapılan İşlem / Değişen Parçalar</th>
                        <th class="p-3">Cihaz Modeli</th>
                        <th class="p-3 text-right">Sonraki Bakım Tarihi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="p-4 font-bold text-gray-900">
                            {{ $service->service_type }}
                            @if($service->issue_description)
                                <p class="text-gray-500 font-normal mt-1 text-[11px]">Not: {{ $service->issue_description }}</p>
                            @endif
                        </td>
                        <td class="p-4 text-gray-700">{{ $service->device_info ?: 'Ev Tipi Su Arıtma Cihazı' }}</td>
                        <td class="p-4 text-right font-extrabold text-blue-900">
                            {{ $service->next_maintenance_date ? \Carbon\Carbon::parse($service->next_maintenance_date)->format('d.m.Y') : '6 Ay Sonra' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Fiyat / Hizmet Bedeli & Ödeme Alanı (İster Bilgisayardan, İster Kalemle) -->
        <div class="mb-8 p-5 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-300 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <span class="text-xs font-bold text-gray-700 uppercase block mb-2">Ödeme / Tahsilat Şekli:</span>
                <div class="flex items-center gap-5 text-xs text-gray-800 font-medium">
                    <!-- Nakit Kutusu -->
                    <label class="flex items-center gap-1.5">
                        <span id="boxNakit" class="w-4 h-4 border-2 border-gray-500 rounded flex items-center justify-center font-bold text-[11px] bg-white">
                            {{ $service->payment_method == 'Nakit' ? '✓' : '' }}
                        </span>
                        <span>Nakit</span>
                    </label>

                    <!-- Kredi Kartı / POS Kutusu -->
                    <label class="flex items-center gap-1.5">
                        <span id="boxPOS" class="w-4 h-4 border-2 border-gray-500 rounded flex items-center justify-center font-bold text-[11px] bg-white">
                            {{ in_array($service->payment_method, ['POS', 'Kredi Kartı / POS', 'Kredi Kartı']) ? '✓' : '' }}
                        </span>
                        <span>Kredi Kartı / POS</span>
                    </label>

                    <!-- Havale / EFT Kutusu -->
                    <label class="flex items-center gap-1.5">
                        <span id="boxHavale" class="w-4 h-4 border-2 border-gray-500 rounded flex items-center justify-center font-bold text-[11px] bg-white">
                            {{ in_array($service->payment_method, ['Havale', 'Havale / EFT', 'EFT']) ? '✓' : '' }}
                        </span>
                        <span>Havale / EFT</span>
                    </label>
                </div>
            </div>
            
            <div class="text-right">
                <span class="text-xs font-bold text-gray-500 block uppercase tracking-wider">Hizmet / İşlem Tutarı</span>
                
                <!-- Bilgisayarda Girilmiş Fiyat Kutusu -->
                <div id="digitalPriceBox" class="{{ $service->service_price && $service->service_price > 0 ? '' : 'hidden' }}">
                    <span id="digitalPriceText" class="text-2xl font-black text-blue-900 tracking-tight">
                        {{ $service->service_price && $service->service_price > 0 ? number_format($service->service_price, 2, ',', '.') . ' TL' : '' }}
                    </span>
                </div>

                <!-- Boş Bırakıldığında Çıkan Noktalı Alan (Kalemle Yazmak İçin) -->
                <div id="blankPriceBox" class="{{ $service->service_price && $service->service_price > 0 ? 'hidden' : '' }}">
                    <div class="mt-1 flex items-center justify-end gap-1.5">
                        <span class="inline-block border-b-2 border-dashed border-gray-400 w-36 h-6"></span>
                        <span class="font-black text-gray-800 text-lg">TL</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- İmza Alanı -->
        <div class="grid grid-cols-2 gap-8 mt-10 pt-6 border-t border-dashed text-center text-xs text-gray-500">
            <div>
                <p class="font-bold text-gray-700">Teknik Servis Yetkilisi</p>
                <p class="text-[11px] text-gray-400 mt-0.5">{{ $service->assigned_staff ?: 'Yetkili Teknisyen' }}</p>
                <div class="h-12"></div>
                <p class="border-t pt-1">İmza / Kaşe</p>
            </div>
            <div>
                <p class="font-bold text-gray-700">Hizmeti Teslim Alan Müşteri</p>
                <p class="text-[11px] text-gray-400 mt-0.5">{{ $service->customer_name }}</p>
                <div class="h-12"></div>
                <p class="border-t pt-1">İmza</p>
            </div>
        </div>

    </div>

    <!-- Dinamik Ücret ve Tahsilat Güncelleme Scripti -->
    <script>
        function updatePrintPrice(val) {
            val = val.trim();
            const digitalBox = document.getElementById('digitalPriceBox');
            const blankBox = document.getElementById('blankPriceBox');
            const textSpan = document.getElementById('digitalPriceText');

            if (val && val !== '0') {
                if (!val.includes('TL')) {
                    textSpan.innerText = val + ' TL';
                } else {
                    textSpan.innerText = val;
                }
                digitalBox.classList.remove('hidden');
                blankBox.classList.add('hidden');
            } else {
                digitalBox.classList.add('hidden');
                blankBox.classList.remove('hidden');
            }
        }

        function updatePrintPayment(method) {
            document.getElementById('boxNakit').innerText = (method === 'Nakit') ? '✓' : '';
            document.getElementById('boxPOS').innerText = (method === 'POS' || method === 'Kredi Kartı / POS') ? '✓' : '';
            document.getElementById('boxHavale').innerText = (method === 'Havale' || method === 'Havale / EFT') ? '✓' : '';
        }
    </script>

</body>
</html>
