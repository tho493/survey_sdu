<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DoiTuongKhaoSat;
use Illuminate\Http\Request;

class DoiTuongController extends Controller
{
    public function index()
    {
        $doiTuongs = DoiTuongKhaoSat::withCount('mauKhaoSat')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.doi-tuong.index', compact('doiTuongs'));
    }

    public function create()
    {
        return view('admin.doi-tuong.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ma_doituong' => 'required|unique:doituong_khaosat|max:20',
            'ten_doituong' => 'required|max:255',
            'mota' => 'nullable',
        ]);

        DoiTuongKhaoSat::create($validated);

        return redirect()->route('admin.doi-tuong.index')
            ->with('success', 'Tạo đối tượng khảo sát thành công');
    }

    public function edit($ma_doituong)
    {
        $doiTuong = DoiTuongKhaoSat::findOrFail($ma_doituong);
        return view('admin.doi-tuong.edit', compact('doiTuong'));
    }

    public function update(Request $request, DoiTuongKhaoSat $doiTuong)
    {
        $validated = $request->validate([
            'ten_doituong' => 'required|max:255',
            'mota' => 'nullable',
            'trangthai' => 'boolean'
        ]);

        $doiTuong->update($validated);

        return redirect()->route('admin.doi-tuong.index')
            ->with('success', 'Cập nhật đối tượng khảo sát thành công');
    }

    public function destroy(DoiTuongKhaoSat $doiTuong)
    {
        if ($doiTuong->mauKhaoSat()->count() > 0) {
            return back()->with('error', 'Không thể xóa đối tượng đã có mẫu khảo sát');
        }

        $doiTuong->delete();

        return redirect()->route('admin.doi-tuong.index')
            ->with('success', 'Xóa đối tượng khảo sát thành công');
    }
}