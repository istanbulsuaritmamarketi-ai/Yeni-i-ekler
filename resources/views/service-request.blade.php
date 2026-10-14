<x-frontend-layout 
    title="Su Arıtma Servisi ve Filtre Değişim Randevusu - İstanbul Su Arıtma" 
    description="İstanbul genelinde su arıtma cihazı montajı, filtre değişimi ve arıza bakım onarımı için online servis talebi oluşturun.">
    <div class="bg-gray-50 py-12 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h1 class="text-3xl font-extrabold text-gray-900 mb-4">Servis ve Randevu Talebi</h1>
                <p class="text-gray-600 max-w-2xl mx-auto mb-6">Filtre değişimi, arıza onarımı veya cihaz bakımı için aşağıdaki formu doldurarak randevu oluşturabilirsiniz. Ekiplerimiz en kısa sürede sizi arayacaktır.</p>
                <a href="https://wa.me/905555555555?text={{ urlencode('Merhaba, hızlı servis/arıza talebi oluşturmak istiyorum. (Kaynak: Servis Sayfası)') }}" target="_blank" class="inline-flex items-center justify-center bg-green-500 text-white font-bold py-3 px-6 rounded-full shadow hover:bg-green-600 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    WhatsApp'tan Hızlı Servis Talebi
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg mb-8 text-center text-lg font-medium shadow-sm">
                    {{ session('success') }}
                </div>
                <script>
                    window.dataLayer = window.dataLayer || [];
                    window.dataLayer.push({
                        'event': 'generate_lead',
                        'lead_type': 'Servis ve Filtre Randevusu'
                    });
                </script>
            @endif

            <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
                <form action="{{ route('service.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Kişisel Bilgiler -->
                    <div class="p-8 border-b border-gray-100">
                        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <span class="bg-blue-600 text-white w-8 h-8 rounded-full inline-flex items-center justify-center mr-3 text-sm">1</span>
                            İletişim Bilgileri
                        </h2>
                        
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Ad Soyad</label>
                                <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon Numarası</label>
                                <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}" required placeholder="0555 555 55 55" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="region_id" class="block text-sm font-medium text-gray-700 mb-1">Hizmet Bölgesi (İlçe)</label>
                                <select name="region_id" id="region_id" required class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="">-- İlçe Seçiniz --</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>İstanbul - {{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Açık Adres</label>
                                <textarea name="address" id="address" rows="3" required class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('address') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Servis Detayları -->
                    <div class="p-8 border-b border-gray-100 bg-gray-50">
                        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <span class="bg-blue-600 text-white w-8 h-8 rounded-full inline-flex items-center justify-center mr-3 text-sm">2</span>
                            Servis Detayları
                        </h2>
                        
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Hizmet Türü</label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none">
                                        <input type="radio" name="service_type" value="Filtre Değişimi" class="sr-only" checked>
                                        <span class="flex flex-1">
                                            <span class="flex flex-col">
                                                <span class="block text-sm font-medium text-gray-900">Filtre Değişimi</span>
                                                <span class="mt-1 flex items-center text-xs text-gray-500">Periyodik bakım ve filtreler</span>
                                            </span>
                                        </span>
                                        <svg class="h-5 w-5 text-blue-600 ml-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                    </label>
                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none">
                                        <input type="radio" name="service_type" value="Arıza / Bakım" class="sr-only">
                                        <span class="flex flex-1">
                                            <span class="flex flex-col">
                                                <span class="block text-sm font-medium text-gray-900">Arıza & Onarım</span>
                                                <span class="mt-1 flex items-center text-xs text-gray-500">Su akıtma, ses yapma vb.</span>
                                            </span>
                                        </span>
                                    </label>
                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none">
                                        <input type="radio" name="service_type" value="Cihaz Montajı" class="sr-only">
                                        <span class="flex flex-1">
                                            <span class="flex flex-col">
                                                <span class="block text-sm font-medium text-gray-900">Kurulum / Montaj</span>
                                                <span class="mt-1 flex items-center text-xs text-gray-500">Yeni cihaz veya taşıma</span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="sm:col-span-2">
                                <label for="device_info" class="block text-sm font-medium text-gray-700 mb-1">Cihaz Marka/Modeli (Biliyorsanız)</label>
                                <input type="text" name="device_info" id="device_info" value="{{ old('device_info') }}" placeholder="Örn: LG Membranlı X Cihazı" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="issue_description" class="block text-sm font-medium text-gray-700 mb-1">Sorun veya Talebiniz Detayı</label>
                                <textarea name="issue_description" id="issue_description" rows="3" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('issue_description') }}</textarea>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Sorunla İlgili Görsel (İsteğe Bağlı)</label>
                                <input type="file" name="image" id="image" accept="image/*" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="mt-1 text-xs text-gray-500">Cihazın, arızalı parçanın veya eski filtrenin fotoğrafını yükleyebilirsiniz. (Maksimum 2MB)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Randevu Zamanı -->
                    <div class="p-8">
                        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <span class="bg-blue-600 text-white w-8 h-8 rounded-full inline-flex items-center justify-center mr-3 text-sm">3</span>
                            Randevu Zamanı
                        </h2>
                        
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                            <div>
                                <label for="preferred_date" class="block text-sm font-medium text-gray-700 mb-1">Tercih Edilen Tarih</label>
                                <input type="date" name="preferred_date" id="preferred_date" value="{{ old('preferred_date', \Carbon\Carbon::tomorrow()->format('Y-m-d')) }}" min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}" required class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                <p class="mt-1 text-xs text-gray-500">Randevular en erken yarın için oluşturulabilir.</p>
                            </div>
                            <div>
                                <label for="preferred_time" class="block text-sm font-medium text-gray-700 mb-1">Tercih Edilen Saat Dilimi</label>
                                <select name="preferred_time" id="preferred_time" required class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="Sabah (09:00 - 13:00)">Sabah (09:00 - 13:00)</option>
                                    <option value="Öğle (13:00 - 16:00)">Öğle (13:00 - 16:00)</option>
                                    <option value="Akşam (16:00 - 19:00)">Akşam (16:00 - 19:00)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="px-8 py-6 bg-gray-50 border-t border-gray-100 flex items-center justify-end">
                        <button type="submit" class="bg-green-600 text-white font-bold py-3 px-8 rounded-lg shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                            Servis Randevusu Oluştur
                        </button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>

    <!-- UI Interaction Script (Radio buttons) -->
    <script>
        document.querySelectorAll('input[type="radio"][name="service_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('input[type="radio"][name="service_type"]').forEach(r => {
                    const label = r.closest('label');
                    const svg = label.querySelector('svg');
                    if(r.checked) {
                        label.classList.add('border-blue-500', 'ring-1', 'ring-blue-500');
                        if(!svg) {
                            label.insertAdjacentHTML('beforeend', '<svg class="h-5 w-5 text-blue-600 ml-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>');
                        }
                    } else {
                        label.classList.remove('border-blue-500', 'ring-1', 'ring-blue-500');
                        if(svg) svg.remove();
                    }
                });
            });
        });
    </script>
</x-frontend-layout>
