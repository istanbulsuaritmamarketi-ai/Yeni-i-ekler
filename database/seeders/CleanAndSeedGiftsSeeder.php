<?php

namespace Database\Seeders;

use App\Models\Gift;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CleanAndSeedGiftsSeeder extends Seeder
{
    public function run(): void
    {
        // Eski sahte/su arıtma ve mock hediyeleri tamamen temizle
        DB::statement('DELETE FROM gifts');

        $gifts = [
            [
                'name' => 'Lüks Spesiyal Kalpli Çikolata Kutusu',
                'badge' => 'Tatlı Sürpriz',
                'category' => 'upsell',
                'description' => 'Çiçeğinizin yanında teslim edilecek taze Belçika spesiyal çikolata kutusu.',
                'price' => 199.00,
                'market_price' => 299.00,
                'icon' => 'fas fa-cookie-bite',
                'color' => 'pink',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Sevimli Yumuşak Peluş Ayıcık (25 cm)',
                'badge' => 'En Çok Tercih Edilen',
                'category' => 'upsell',
                'description' => 'Kırmızı kalpli, yumuşacık sevimli peluş ayıcık.',
                'price' => 249.00,
                'market_price' => 350.00,
                'icon' => 'fas fa-heart',
                'color' => 'rose',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Kalp Formlu Uçan Helyum Balon',
                'badge' => 'Romantik Dokunuş',
                'category' => 'upsell',
                'description' => 'Kırmızı renkli, parıltılı uçan kalp balon.',
                'price' => 149.00,
                'market_price' => 220.00,
                'icon' => 'fas fa-heart',
                'color' => 'rose',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Şık Paşabahçe Silindir Cam Vazo',
                'badge' => 'Kullanışlı',
                'category' => 'upsell',
                'description' => 'Buketinizin hemen suya konulabilmesi için kristal şeffaf cam vazo.',
                'price' => 120.00,
                'market_price' => 180.00,
                'icon' => 'fas fa-wine-glass-alt',
                'color' => 'emerald',
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($gifts as $gift) {
            Gift::create($gift);
        }
    }
}
