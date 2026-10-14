<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'position' => 'grid_1',
                'title' => 'Gül Buketleri',
                'subtitle' => 'İthal Taze Kırmızı & Pembe Güller',
                'badge' => 'Aynı Gün Teslim',
                'button_text' => 'Hemen İncele',
                'link_url' => '/kategori/gul-buketleri',
                'image_path' => null,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'position' => 'grid_2',
                'title' => 'Kutuda Tasarımlar',
                'subtitle' => 'Lüks Kadife Kutulu Çiçekler',
                'badge' => 'Özel Seri',
                'button_text' => 'Keşfet',
                'link_url' => '/kategori/vazoda-ve-kutuda-tasarimlar',
                'image_path' => null,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'position' => 'grid_3',
                'title' => 'Asil Orkideler',
                'subtitle' => 'Çift Dallı Kraliyet Beyaz Orkide',
                'badge' => 'Çok Satan',
                'button_text' => 'Hemen İncele',
                'link_url' => '/kategori/orkide-ve-saksi-cicekleri',
                'image_path' => null,
                'order' => 3,
                'is_active' => true,
            ],
            [
                'position' => 'grid_4',
                'title' => 'Mevsim Çiçekleri',
                'subtitle' => 'Papatya, Lilyum & Kır Çiçekleri',
                'badge' => 'Taptaze',
                'button_text' => 'İncele',
                'link_url' => '/kategori/mevsim-cicekleri-ve-lilyum',
                'image_path' => null,
                'order' => 4,
                'is_active' => true,
            ],
            [
                'position' => 'grid_5',
                'title' => 'Doğum Günü',
                'subtitle' => 'Sevdiklerinize En Zarif Sürpriz',
                'badge' => 'Kart Notu Hediye',
                'button_text' => 'Göz At',
                'link_url' => '/kategori/dogum-gunu-cicekleri',
                'image_path' => null,
                'order' => 5,
                'is_active' => true,
            ],
            [
                'position' => 'grid_6',
                'title' => 'Romantik Seri',
                'subtitle' => 'Yıldönümü & Sevgiliye Çiçekler',
                'badge' => 'Özel İndirim',
                'button_text' => 'Seçimi Gör',
                'link_url' => '/kategori/yildonumu-ve-sevgiliye',
                'image_path' => null,
                'order' => 6,
                'is_active' => true,
            ],
            [
                'position' => 'grid_7',
                'title' => 'Haftanın Özel Çiçek Fırsatları',
                'subtitle' => 'Seçili Aranjmanlarda %30\'a Varan İndirimler & Hızlı Kurye',
                'badge' => 'FIRSAT KAMPANYASI',
                'button_text' => 'Tüm Fırsatları Gör',
                'link_url' => '/urunler',
                'image_path' => null,
                'order' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($banners as $b) {
            Banner::updateOrCreate(
                ['position' => $b['position']],
                $b
            );
        }
    }
}
