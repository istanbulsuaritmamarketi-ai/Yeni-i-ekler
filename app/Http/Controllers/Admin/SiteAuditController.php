<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SiteAuditorService;
use Illuminate\Http\Request;

class SiteAuditController extends Controller
{
    protected SiteAuditorService $auditor;

    public function __construct(SiteAuditorService $auditor)
    {
        $this->auditor = $auditor;
    }

    public function index(Request $request)
    {
        $audit = $this->auditor->runFullAudit();

        if ($request->wantsJson()) {
            return response()->json($audit);
        }

        return view('admin.site_audit.index', compact('audit'));
    }
}
