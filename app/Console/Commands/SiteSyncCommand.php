<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SiteSyncCommand extends Command
{
    /**
     * Komut imzası ve parametreleri
     */
    protected $signature = 'site:sync 
                            {target? : Hedef site kodu (örn: cicek_evi, su_aritma veya all)}
                            {--dry-run : Gerçek kopyalama yapmadan sadece değişen dosyaları listeler}
                            {--force : Onay sormadan doğrudan kopyalar}';

    /**
     * Komut açıklaması
     */
    protected $description = 'Master Core güncellemelerini (Servisler, Denetçiler, Matris) diğer müşteri/kardeş sitelere senkronize eder.';

    /**
     * Senkronize edilecek güvenli ana çekirdek dosyaları (Siteden siteye değişen .env, veritabanı vb. hariç)
     */
    protected array $syncableFiles = [
        'app/Services/SiteAuditorService.php',
        'app/Console/Commands/SiteAuditCommand.php',
        'config/site_matrix.php',
        'config/districts.php',
    ];

    /**
     * Komut yürütme
     */
    public function handle(): int
    {
        $target = $this->argument('target') ?? 'all';
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $sites = config('site_matrix.sites', []);

        $this->info('===================================================================');
        $this->info(' 🔄 YİĞİT AJANS ÇOKLU SİTE SENKRONİZASYON MOTORU');
        $this->info('===================================================================');

        if ($dryRun) {
            $this->warn('⚠️  MOD: Simülasyon (Dry Run) — Dosyalar sadece kontrol edilecek, kopyalanmayacak.');
        }

        // Hedef siteleri filtrele
        $targetSites = [];
        if ($target === 'all') {
            $targetSites = $sites;
        } elseif (isset($sites[$target])) {
            $targetSites[$target] = $sites[$target];
        } else {
            $this->error("Hata: Tanımlı olmayan site kodu: '{$target}'.");
            $this->line('Kullanılabilir site kodları: '.implode(', ', array_keys($sites)));

            return 1;
        }

        $baseWww = 'C:\\laragon\\www';
        $sitePaths = [
            'su_aritma' => $baseWww.'\\su-aritma-market',
            'cicek_evi' => $baseWww.'\\istanbul-cicek-evi',
            'esenyurt_cicek' => $baseWww.'\\esenyurt-cicekcisi',
            'onur_insaat' => $baseWww.'\\onur-insaat',
            'reform_insaat' => $baseWww.'\\reform-insaat',
            'cozum_portali' => $baseWww.'\\cozum-portali',
            'yigit_ajans' => $baseWww.'\\yigit-ajans',
        ];

        $totalUpdated = 0;
        $totalSkipped = 0;

        foreach ($targetSites as $code => $siteInfo) {
            $siteName = $siteInfo['name'] ?? $code;
            $destPath = $sitePaths[$code] ?? null;

            $this->newLine();
            $this->line("<fg=cyan;options=bold>▶ SİTE:</> <fg=yellow>{$siteName}</> ({$code})");

            if (! $destPath || ! File::isDirectory($destPath)) {
                $this->line("  <fg=gray>⏩ Klasör henüz yerel ortamda açılmamış:</> {$destPath}");

                continue;
            }

            $rows = [];

            foreach ($this->syncableFiles as $relPath) {
                $sourceFile = base_path($relPath);
                $destFile = $destPath.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relPath);

                if (! File::exists($sourceFile)) {
                    $rows[] = [$relPath, '<fg=red>Kaynak Eksik</>'];

                    continue;
                }

                $sourceHash = md5_file($sourceFile);
                $destExists = File::exists($destFile);
                $destHash = $destExists ? md5_file($destFile) : null;

                if ($destExists && $sourceHash === $destHash) {
                    $rows[] = [$relPath, '<fg=green>Güncel (Birebir Aynı)</>'];
                    $totalSkipped++;

                    continue;
                }

                $status = $destExists ? '<fg=yellow>Fark Var (Güncellenecek)</>' : '<fg=blue>Yeni Dosya (Kopyalanacak)</>';

                if (! $dryRun) {
                    File::ensureDirectoryExists(dirname($destFile));
                    File::copy($sourceFile, $destFile);
                    $status = $destExists ? '<fg=green>Başarıyla Güncellendi</>' : '<fg=green>Kopyalandı</>';
                    $totalUpdated++;
                } else {
                    $totalUpdated++;
                }

                $rows[] = [$relPath, $status];
            }

            $this->table(['Dosya Yolu', 'Durum'], $rows);
        }

        $this->newLine();
        $this->info('===================================================================');
        if ($dryRun) {
            $this->info(" ✨ Simülasyon Tamamlandı: {$totalUpdated} dosya güncellenebilir, {$totalSkipped} dosya zaten güncel.");
        } else {
            $this->info(" 🎉 Senkronizasyon Tamamlandı: {$totalUpdated} dosya aktarıldı, {$totalSkipped} dosya korundu.");
        }
        $this->info('===================================================================');

        return 0;
    }
}
