<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Yeni Servis & Montaj Kaydı Aç') }}
            </h2>
            <a href="{{ route('admin.services.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-bold">
                <i class="fas fa-arrow-left"></i> Listeye Dön
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 p-6 sm:p-8">
                
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 text-red-700 rounded-r">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.services.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Müşteri Bilgileri -->
                    <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100 space-y-4">
                        <h3 class="font-extrabold text-sm text-gray-900 flex items-center gap-2">
                            <i class="fas fa-user text-blue-600"></i> Müşteri & İletişim Bilgileri
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1" for="customer_name">Müşteri Adı Soyadı *</label>
                                <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required placeholder="Örn: Ahmet Yılmaz"
                                    class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1" for="customer_phone">Telefon Numarası *</label>
                                <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}" required placeholder="Örn: 0555 555 55 55"
                                    class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1" for="region_id">İlçe / Bölge *</label>
                                <select name="region_id" id="region_id" required class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">-- İlçe Seçin --</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>İstanbul - {{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1" for="address">Açık Adres *</label>
                                <input type="text" name="address" id="address" value="{{ old('address') }}" required placeholder="Mahalle, Cadde, No, Daire..."
                                    class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Servis & Randevu Detayları -->
                    <div class="bg-blue-50/40 p-5 rounded-2xl border border-blue-100 space-y-4">
                        <h3 class="font-extrabold text-sm text-blue-900 flex items-center gap-2">
                            <i class="fas fa-tools text-blue-600"></i> Servis & Randevu Detayları
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1" for="service_type">İşlem Türü *</label>
                                <select name="service_type" id="service_type" required class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 font-bold">
                                    <option value="Filtre Değişimi" {{ old('service_type') == 'Filtre Değişimi' ? 'selected' : '' }}>Filtre Değişimi (Periyodik Bakım)</option>
                                    <option value="Yeni Kurulum / Montaj" {{ old('service_type') == 'Yeni Kurulum / Montaj' ? 'selected' : '' }}>Yeni Kurulum / Montaj</option>
                                    <option value="Arıza Tespiti & Onarım" {{ old('service_type') == 'Arıza Tespiti & Onarım' ? 'selected' : '' }}>Arıza Tespiti & Onarım</option>
                                    <option value="Tank / Musluk Değişimi" {{ old('service_type') == 'Tank / Musluk Değişimi' ? 'selected' : '' }}>Tank / Musluk Değişimi</option>
                                    <option value="Genel Bakım & Su Testi" {{ old('service_type') == 'Genel Bakım & Su Testi' ? 'selected' : '' }}>Genel Bakım & Su Testi (TDS Ölçümü)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1" for="device_info">Cihaz Modeli / Markası</label>
                                <input type="text" name="device_info" id="device_info" value="{{ old('device_info') }}" placeholder="Örn: 5 Aşamalı Kapalı Kasa"
                                    class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1" for="preferred_date">Randevu Tarihi *</label>
                                <input type="date" name="preferred_date" id="preferred_date" value="{{ old('preferred_date', date('Y-m-d')) }}" required
                                    class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1" for="preferred_time">Zaman Dilimi *</label>
                                <select name="preferred_time" id="preferred_time" required class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="09:00 - 12:00 (Sabah)" {{ old('preferred_time') == '09:00 - 12:00 (Sabah)' ? 'selected' : '' }}>09:00 - 12:00 (Sabah)</option>
                                    <option value="12:00 - 15:00 (Öğle)" {{ old('preferred_time') == '12:00 - 15:00 (Öğle)' ? 'selected' : '' }}>12:00 - 15:00 (Öğle)</option>
                                    <option value="15:00 - 18:00 (İkindi)" {{ old('preferred_time') == '15:00 - 18:00 (İkindi)' ? 'selected' : '' }}>15:00 - 18:00 (İkindi)</option>
                                    <option value="18:00 - 21:00 (Akşam)" {{ old('preferred_time') == '18:00 - 21:00 (Akşam)' ? 'selected' : '' }}>18:00 - 21:00 (Akşam)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1" for="status">Servis Durumu *</label>
                                <select name="status" id="status" required class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 font-bold text-blue-700">
                                    <option value="Bekliyor" {{ old('status') == 'Bekliyor' ? 'selected' : '' }}>Bekliyor</option>
                                    <option value="Randevu Verildi" {{ old('status') == 'Randevu Verildi' ? 'selected' : '' }}>Randevu Verildi</option>
                                    <option value="Teknisyen Yolda" {{ old('status') == 'Teknisyen Yolda' ? 'selected' : '' }}>Teknisyen Yolda</option>
                                    <option value="Tamamlandı" {{ old('status') == 'Tamamlandı' ? 'selected' : '' }}>Tamamlandı</option>
                                    <option value="İptal Edildi" {{ old('status') == 'İptal Edildi' ? 'selected' : '' }}>İptal Edildi</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1" for="service_price">Servis Ücreti (TL)</label>
                                <input type="number" step="0.01" name="service_price" id="service_price" value="{{ old('service_price') }}" placeholder="Örn: 750"
                                    class="w-full rounded-xl border-gray-300 text-sm font-extrabold text-blue-600 focus:border-blue-500 focus:ring-blue-500 bg-blue-50/50">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1" for="payment_method">Tahsilat Şekli</label>
                                <select name="payment_method" id="payment_method" class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Boş Bırak (Kalemle Seç)</option>
                                    <option value="Nakit" {{ old('payment_method') == 'Nakit' ? 'selected' : '' }}>Nakit</option>
                                    <option value="Kredi Kartı / POS" {{ old('payment_method') == 'Kredi Kartı / POS' ? 'selected' : '' }}>Kredi Kartı / POS</option>
                                    <option value="Havale / EFT" {{ old('payment_method') == 'Havale / EFT' ? 'selected' : '' }}>Havale / EFT</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1" for="assigned_staff">Görevli Usta / Ekip</label>
                                <input type="text" name="assigned_staff" id="assigned_staff" value="{{ old('assigned_staff') }}" placeholder="Örn: Mehmet Usta"
                                    class="w-full rounded-xl border-gray-300 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1" for="next_maintenance_date">Sonraki Filtre Tarihi</label>
                                <input type="date" name="next_maintenance_date" id="next_maintenance_date" value="{{ old('next_maintenance_date', date('Y-m-d', strtotime('+6 months'))) }}"
                                    class="w-full rounded-xl border-gray-300 text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1" for="issue_description">Müşteri Şikayeti / İstek Detayı</label>
                            <textarea name="issue_description" id="issue_description" rows="2" placeholder="Müşterinin belirttiği arıza, su tadı problemi veya özel talep..."
                                class="w-full rounded-xl border-gray-300 text-sm">{{ old('issue_description') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1" for="admin_notes">Dahili Notlar (Sadece Yetkililer Görür)</label>
                            <textarea name="admin_notes" id="admin_notes" rows="2" placeholder="Örn: 2 adet sediment filtre ve 1 adet tatlandırıcı takılacak..."
                                class="w-full rounded-xl border-gray-300 text-sm">{{ old('admin_notes') }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('admin.services.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-bold text-sm rounded-xl hover:bg-gray-200 transition-colors">
                            İptal
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow transition-colors">
                            Servis Kaydını Oluştur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
