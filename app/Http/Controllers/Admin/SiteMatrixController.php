<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class SiteMatrixController extends Controller
{
    public function index()
    {
        $sites = config('site_matrix.sites', []);
        $modules = config('site_matrix.modules', []);
        $independentApps = config('site_matrix.independent_apps', []);

        // İstatistikler
        $totalSites = count($sites);
        $totalModules = count($modules);
        $totalApps = count($independentApps);

        $cpanel1Count = collect($sites)->filter(fn ($s) => str_contains($s['server'] ?? '', 'cPanel 1'))->count();
        $cpanel2Count = collect($sites)->filter(fn ($s) => str_contains($s['server'] ?? '', 'cPanel 2'))->count() + $totalApps;

        return view('admin.site_matrix.index', compact(
            'sites',
            'modules',
            'independentApps',
            'totalSites',
            'totalModules',
            'totalApps',
            'cpanel1Count',
            'cpanel2Count'
        ));
    }
}
