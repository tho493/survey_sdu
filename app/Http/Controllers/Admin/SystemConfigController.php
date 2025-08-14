<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CauHinhHeThong;
use App\Models\TemplateEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class SystemConfigController extends Controller
{
    public function index()
    {
        // $configs = CauHinhHeThong::orderBy('nhom_cauhinh')->get()->groupBy('nhom_cauhinh'); // Sắp xếp theo nhóm
        $configs = CauHinhHeThong::orderBy('nhom_cauhinh')->orderBy('mota')->get()->groupBy('nhom_cauhinh'); // Sắp xếp theo mô tả
        $emailTemplates = TemplateEmail::all();

        return view('admin.config.index', compact('configs', 'emailTemplates'));
    }

    public function updateConfigs(Request $request)
    {
        if ($request->has('configs')) {
            foreach ($request->configs as $id => $values) {
                $config = CauHinhHeThong::find($id);
                if ($config) {
                    // Không cho phép sửa key, chỉ sửa giá trị
                    $newValue = $values['giatri'];

                    // Xử lý giá trị boolean
                    if ($config->ma_cauhinh === 'app.debug') {
                        $newValue = filter_var($newValue, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
                    }

                    // Không lưu mật khẩu rỗng
                    if ($config->ma_cauhinh === 'mail.password' && empty($newValue)) {
                    } else {
                        $config->update(['giatri' => $newValue]);
                    }
                }
            }
        }

        Cache::forget('db_env_configs');
        Artisan::call('config:clear');

        return back()->with('success', 'Cập nhật cấu hình thành công. Thay đổi sẽ có hiệu lực sau vài phút.');
    }

    //template email

    public function storeEmailTemplate(Request $request)
    {
        $validated = $request->validate([
            'ten_template' => 'required|string|max:255',
            'ma_template' => 'required|string|max:50|unique:template_email,ma_template|regex:/^[a-z0-9_]+$/',
            'tieude' => 'required|string|max:255',
            'noidung' => 'required|string',
            'bien_template' => 'nullable|string'
        ]);

        if (!empty($validated['bien_template'])) {
            $validated['bien_template'] = array_map('trim', explode(',', $validated['bien_template']));
        }

        TemplateEmail::create($validated);

        return back()->with('success', 'Thêm template email mới thành công.');
    }

    // Cập nhật một template email đã có.
    public function updateEmailTemplate(Request $request, TemplateEmail $template)
    {
        $validated = $request->validate([
            'ten_template' => 'required|string|max:255',
            'tieude' => 'required|string|max:255',
            'noidung' => 'required|string',
            'bien_template' => 'nullable|string'
        ]);

        if (!empty($validated['bien_template'])) {
            $validated['bien_template'] = array_map('trim', explode(',', $validated['bien_template']));
        } else {
            $validated['bien_template'] = null;
        }

        $template->update($validated);

        return back()->with('success', 'Cập nhật template email thành công.');
    }

    // Xóa một template email.
    public function destroyEmailTemplate(TemplateEmail $template)
    {
        $template->delete();
        return back()->with('success', 'Đã xóa template email: ' . $template->ten_template);
    }

    public function testEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'template_id' => 'required|exists:template_email,id'
        ]);

        try {
            return response()->json(['success' => true, 'message' => 'Email test đã được gửi']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}