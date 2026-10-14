# ==============================================================================
# 🌸 YENİÇİÇEK — CANLI SUNUCUYA YÜKLEME PAKETİ OLUŞTURUCU (ZIP EXPORT)
# ==============================================================================
# Bu script, projeyi sunucuya (cPanel, Plesk, VPS) yüklemeye hazır temiz bir ZIP haline getirir.
# Gereksiz node_modules, .git ve önbellek dosyalarını otomatik olarak hariç tutar.
#
# Kullanım:
#   powershell -ExecutionPolicy Bypass -File .\package_release.ps1
# ==============================================================================

Write-Host "🌸 Yeniçiçek Üretim Paketi Oluşturuluyor..." -ForegroundColor Cyan

# 1. Vite Varlıklarını Derle
Write-Host "📦 1/4 Frontend üretim derlemesi (npm run build) kontrol ediliyor..." -ForegroundColor Yellow
npm run build

# 2. Laravel Önbelleklerini Temizle
Write-Host "🧹 2/4 Yerel önbellekler temizleniyor..." -ForegroundColor Yellow
php artisan optimize:clear

# 3. ZIP Dosya Adı ve Yolu
$timestamp = Get-Date -Format "yyyyMMdd_HHmm"
$zipPath = "..\yenicicek_release_$timestamp.zip"

Write-Host "🗜️ 3/4 Dosyalar sıkıştırılıyor -> $zipPath ..." -ForegroundColor Yellow

$excludeDirs = @(
    "node_modules",
    ".git",
    ".github",
    ".idea",
    ".vscode",
    "tests"
)

$excludeFiles = @(
    ".phpunit.result.cache",
    ".env",
    "package_release.ps1"
)

# 7-Zip varsa en hızlı ve küçük şekilde sıkıştır, yoksa PowerShell Compress-Archive kullan
if (Get-Command 7z -ErrorAction SilentlyContinue) {
    7z a -tzip $zipPath . "-xr!node_modules" "-xr!.git" "-xr!tests" "-x!.env" "-x!.phpunit.result.cache"
} else {
    # Geçici dizin oluşturup temiz kopyalama
    $tempDir = [System.IO.Path]::Combine([System.IO.Path]::GetTempPath(), "yenicicek_export_" + [System.Guid]::NewGuid().ToString())
    New-Item -ItemType Directory -Path $tempDir | Out-Null

    $robocopyArgs = @(
        ".",
        $tempDir,
        "/E",
        "/XD", "node_modules", ".git", "tests", "storage\framework\cache\data", "storage\framework\sessions", "storage\framework\views", "storage\logs",
        "/XF", ".env", ".phpunit.result.cache", "package_release.ps1"
    )
    & robocopy @robocopyArgs | Out-Null

    # Boş storage alt klasörlerini oluştur (.gitkeep ile)
    @("storage/app/public/products", "storage/app/public/categories", "storage/framework/cache/data", "storage/framework/sessions", "storage/framework/views", "storage/logs") | ForEach-Object {
        $p = Join-Path $tempDir $_
        if (-not (Test-Path $p)) { New-Item -ItemType Directory -Path $p -Force | Out-Null }
        Set-Content -Path (Join-Path $p ".gitkeep") -Value ""
    }

    Compress-Archive -Path "$tempDir\*" -DestinationPath $zipPath -Force
    Remove-Item -Path $tempDir -Recurse -Force
}

Write-Host "✅ Başarılı! Dağıtım paketi hazırlandı:" -ForegroundColor Green
Write-Host "📁 Dosya: $zipPath" -ForegroundColor White
Write-Host "💡 Bu zip dosyasını sunucunuzun public_html veya proje dizinine yükleyip açabilirsiniz." -ForegroundColor Cyan
