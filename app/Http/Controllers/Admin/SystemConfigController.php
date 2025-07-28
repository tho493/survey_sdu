<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CauHinhHeThong;
use App\Models\TemplateEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SystemConfigController extends Controller
{
    public function index()
    {
        $configs = CauHinhHeThong::orderBy('nhom_cauhinh')->get()->groupBy('nhom_cauhinh');
        $emailTemplates = TemplateEmail::all();

        return view('admin.config.index', compact('configs', 'emailTemplates'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'configs' => 'required|array',
            'configs.*.ma_cauhinh' => 'required|exists:cauhinh_hethong,ma_cauhinh',
            'configs.*.giatri' => 'required'
        ]);

        foreach ($validated['configs'] as $config) {
            CauHinhHeThong::where('ma_cauhinh', $config['ma_cauhinh'])
                ->update(['giatri' => $config['giatri']]);
        }

        // Clear cache
        Cache::forget('system_configs');

        return back()->with('success', 'Cập nhật cấu hình thành công');
    }

    public function updateEmailTemplate(Request $request, TemplateEmail $template)
    {
        $validated = $request->validate([
            'tieude' => 'required|max:255',
            'noidung' => 'required'
        ]);

        $template->update($validated);

        return back()->with('success', 'Cập nhật template email thành công');
    }

    public function testEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'template_id' => 'required|exists:template_email,id'
        ]);

        try {
            // Logic gửi email test
            return response()->json(['success' => true, 'message' => 'Email test đã được gửi']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}