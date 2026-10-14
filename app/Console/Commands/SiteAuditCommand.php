<?php

namespace App\Console\Commands;

use App\Services\SiteAuditorService;
use Illuminate\Console\Command;

class SiteAuditCommand extends Command
{
    protected $signature = 'site:audit';

    protected $description = 'Sitenin tüm rotalarını, sitemap sayfalarını, butonlarını ve kalite puanını otonom olarak denetler.';

    public function handle(SiteAuditorService $auditor)
    {
        $this->info('===================================================================');
        $this->info('  🤖 OTONOM SİTE KALİTE, BUTON & BUG DENETÇİSİ');
        $this->info('===================================================================');
        $this->comment('Sitemap ve rotalar taranıyor, tüm butonlar test ediliyor...');

        $results = $auditor->runFullAudit();

        $this->newLine();
        $this->line('🎯 SİSTEM SAĞLIK PUANI: <fg=black;bg='.($results['score'] >= 90 ? 'green' : ($results['score'] >= 70 ? 'yellow' : 'red')).'> %'.$results['score'].' </fg=black;bg='.($results['score'] >= 90 ? 'green' : ($results['score'] >= 70 ? 'yellow' : 'red')).'>');
        $this->line('🕒 Tarama Zamanı: '.$results['checked_at']);
        $this->line('📦 Aktif Mod: '.strtoupper($results['site_mode']));
        $this->newLine();

        // 1. Kritik Hatalar
        if (count($results['bugs']) > 0) {
            $this->error('🔴 KRİTİK HATALAR ('.count($results['bugs']).' Adet):');
            foreach ($results['bugs'] as $b) {
                $this->line('  ❌ <fg=red>'.$b['title'].'</fg=red>: '.$b['desc']);
            }
            $this->newLine();
        } else {
            $this->info('✅ Hiçbir kritik sistem hatası bulunamadı!');
            $this->newLine();
        }

        // 2. Uyarılar
        if (count($results['warnings']) > 0) {
            $this->warn('🟡 EKSİKLER & UYARILAR ('.count($results['warnings']).' Adet):');
            foreach ($results['warnings'] as $w) {
                $this->line('  ⚠️  <fg=yellow>'.$w['title'].'</fg=yellow>: '.$w['desc']);
            }
            $this->newLine();
        }

        // 3. Fikirler
        if (count($results['ideas']) > 0) {
            $this->comment("💡 'ŞU OLSA ÇOK DAHA İYİ OLUR' BÜYÜME TAVSİYELERİ (".count($results['ideas']).' Adet):');
            foreach ($results['ideas'] as $i) {
                $this->line('  🚀 <fg=cyan>'.$i['title'].'</fg=cyan>: '.$i['desc']);
            }
            $this->newLine();
        }

        // 4. Derin URL & Buton Tablosu
        if (isset($results['url_audit'])) {
            $urlAudit = $results['url_audit'];
            $this->info('🌐 SİTEMAP & BUTON SAĞLIK ANALİZİ:');
            $this->line('  📄 Taranan Sayfa: <fg=cyan>'.$urlAudit['scanned_urls_count'].' URL</fg=cyan>  |  🔘 Toplam Buton/Link: <fg=white>'.$urlAudit['total_buttons_found'].'</fg=white>  |  ✔ Sağlıklı: <fg=green>'.$urlAudit['total_healthy'].'</fg=green>  |  ⚠️ Sahte (#): <fg=yellow>'.$urlAudit['total_dummy'].'</fg=yellow>');
            $this->newLine();

            $headers = ['URL', 'Kategori', 'Yanıt', 'Süre', 'Toplam', 'Çalışan', 'Sahte (#)'];
            $rows = [];
            foreach ($urlAudit['pages'] as $p) {
                $rows[] = [
                    $p['url'],
                    $p['category'],
                    $p['status_code'] == 200 ? '200 OK' : $p['status_code'],
                    $p['time_ms'].'ms',
                    $p['analysis']['total'],
                    $p['analysis']['healthy_count'],
                    $p['analysis']['dummy_count'] > 0 ? $p['analysis']['dummy_count'].' (!)' : '0',
                ];
            }
            $this->table($headers, $rows);
            $this->newLine();

            $dummyFound = [];
            foreach ($urlAudit['pages'] as $p) {
                foreach ($p['analysis']['items'] as $it) {
                    if ($it['status'] === 'warning') {
                        $dummyFound[] = '  ⚠️  '.$p['url'].' -> <fg=yellow>['.$it['text'].']</fg=yellow> (Hedef: '.$it['target'].') - '.$it['note'];
                    }
                }
            }
            if (! empty($dummyFound)) {
                $this->warn('🔎 TESPİT EDİLEN İŞLEVSİZ / SAHTE BUTONLARDAN ÖRNEKLER (İlk 5):');
                foreach (array_slice($dummyFound, 0, 5) as $df) {
                    $this->line($df);
                }
                $this->newLine();
            }
        }

        // 5. Başarıyla Geçen Kontroller
        $this->info('📋 BAŞARIYLA GEÇEN KONTROLLER ('.count($results['passed']).' Nokta):');
        foreach ($results['passed'] as $p) {
            $this->line('  ✔ '.$p);
        }

        $this->newLine();
        $this->info('===================================================================');
        $this->info('  Denetim tamamlandı. Görsel buton raporu: /admin/site-audit');
        $this->info('===================================================================');

        return Command::SUCCESS;
    }
}
