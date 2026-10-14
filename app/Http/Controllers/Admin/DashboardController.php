<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Gift;
use App\Models\Order;
use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use App\Models\Project;
use App\Services\SiteAuditorService;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class DashboardController extends Controller
{
    public function index(SiteAuditorService $auditor)
    {
        $siteMode = config('app.site_mode', env('SITE_MODE', 'ecommerce'));

        // 1. Ekosistem Projeleri
        $rawSites = config('site_matrix.sites', []);
        $baseWww = 'C:\\laragon\\www';

        $siteMatrix = [];
        foreach ($rawSites as $code => $site) {
            $folder = $site['folder'] ?? $code;
            $localPath = $baseWww . DIRECTORY_SEPARATOR . $folder;
            $existsLocally = File::isDirectory($localPath);

            $site['code'] = $code;
            $site['folder'] = $folder;
            $site['local_path'] = $localPath;
            $site['local_exists'] = $existsLocally;
            $site['sync_cmd'] = "php artisan site:sync {$code}";
            $site['clone_cmd'] = "robocopy \"C:\\laragon\\www\\yigit-core\" \"{$localPath}\" /E /XD vendor node_modules storage\\logs .git";
            $site['env_snippet'] = "SITE_MODE={$site['mode']}\nTHEME={$site['theme']}\nAPP_URL={$site['local_url']}";

            if ($site['mode'] === 'ecommerce') {
                $site['badge'] = 'bg-emerald-100 text-emerald-800';
            } elseif ($site['mode'] === 'corporate') {
                $site['badge'] = 'bg-amber-100 text-amber-800';
            } else {
                $site['badge'] = 'bg-indigo-100 text-indigo-800';
            }

            $siteMatrix[$code] = $site;
        }

        $independentApps = config('site_matrix.independent_apps', []);
        $coreModules = config('site_matrix.modules', []);
        $audit = $auditor->runFullAudit();

        // 2. Florist / E-Ticaret Özel Metrikleri
        $flowerStats = [
            'today_orders' => Order::where(function ($q) {
                $q->whereDate('delivery_date', Carbon::today())
                  ->orWhereDate('created_at', Carbon::today());
            })->count(),
            'preparing_orders' => Order::whereIn('status', ['Yeni Sipariş', 'Atölyede Hazırlanıyor', 'Hazırlanıyor', 'pending', 'processing'])->count(),
            'photo_review' => Order::where('status', 'Fotoğraf Onayında')->count(),
            'on_delivery' => Order::whereIn('status', ['Kuryede / Dağıtımda', 'Yolda', 'on_delivery', 'Kuryede'])->count(),
            'completed_orders' => Order::whereIn('status', ['Teslim Edildi', 'Tamamlandı'])->count(),
            'today_revenue' => Order::whereDate('created_at', Carbon::today())->where('payment_status', 'paid')->sum('total_amount'),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'total_flowers' => Product::count(),
            'total_gifts' => Gift::count(),
            'active_banners' => Banner::where('is_active', true)->count(),
        ];

        $recentOrders = Order::with(['items.product', 'region'])->latest()->take(6)->get();

        // 3. Genel Sistem İstatistikleri
        $stats = [
            'total_sites' => count($siteMatrix) + count($independentApps),
            'total_modules' => 14,
            'health_score' => $audit['score'] ?? 100,
            'critical_bugs' => count($audit['bugs'] ?? []),
            'total_pages' => Page::count(),
            'total_posts' => Post::count(),
            'total_projects' => Project::count(),
            'active_districts' => config('districts.active', 'all'),
        ];

        return view('dashboard', compact(
            'siteMatrix',
            'independentApps',
            'coreModules',
            'audit',
            'stats',
            'flowerStats',
            'recentOrders',
            'siteMode'
        ));
    }
}
