<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PhanQuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('hoten', 'like', '%' . $request->search . '%')
                    ->orWhere('tendangnhap', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('quyen')) {
            $query->where('quyen', $request->quyen);
        }

        if ($request->filled('trangthai')) {
            $query->where('trangthai', $request->trangthai);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $permissions = $this->getPermissionList();
        return view('admin.users.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tendangnhap' => 'required|unique:taikhoan|max:50',
            'matkhau' => 'required|min:6',
            'hoten' => 'required|max:100',
            'email' => 'nullable|email|unique:taikhoan',
            'sodienthoai' => 'nullable|max:20',
            'quyen' => 'required|in:admin,manager,viewer',
            'permissions' => 'array'
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'tendangnhap' => $validated['tendangnhap'],
                'matkhau' => Hash::make($validated['matkhau']),
                'hoten' => $validated['hoten'],
                'email' => $validated['email'],
                'sodienthoai' => $validated['sodienthoai'],
                'quyen' => $validated['quyen']
            ]);

            // Phân quyền chi tiết
            if (isset($validated['permissions']) && $validated['quyen'] !== 'admin') {
                foreach ($validated['permissions'] as $chucnang => $quyen) {
                    PhanQuyen::create([
                        'taikhoan_id' => $user->id,
                        'chucnang' => $chucnang,
                        'quyen' => $quyen
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.users.index')
                ->with('success', 'Tạo tài khoản thành công');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function edit(User $user)
    {
        $permissions = $this->getPermissionList();
        $userPermissions = $user->phanQuyen->pluck('quyen', 'chucnang')->toArray();

        return view('admin.users.edit', compact('user', 'permissions', 'userPermissions'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'hoten' => 'required|max:100',
            'email' => 'nullable|email|unique:taikhoan,email,' . $user->id,
            'sodienthoai' => 'nullable|max:20',
            'quyen' => 'required|in:admin,manager,viewer',
            'trangthai' => 'boolean',
            'matkhau' => 'nullable|min:6',
            'permissions' => 'array'
        ]);

        DB::beginTransaction();
        try {
            $updateData = [
                'hoten' => $validated['hoten'],
                'email' => $validated['email'],
                'sodienthoai' => $validated['sodienthoai'],
                'quyen' => $validated['quyen'],
                'trangthai' => $validated['trangthai'] ?? 1
            ];

            if (!empty($validated['matkhau'])) {
                $updateData['matkhau'] = Hash::make($validated['matkhau']);
            }

            $user->update($updateData);

            // Cập nhật phân quyền
            $user->phanQuyen()->delete();
            if (isset($validated['permissions']) && $validated['quyen'] !== 'admin') {
                foreach ($validated['permissions'] as $chucnang => $quyen) {
                    PhanQuyen::create([
                        'taikhoan_id' => $user->id,
                        'chucnang' => $chucnang,
                        'quyen' => $quyen
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.users.index')
                ->with('success', 'Cập nhật tài khoản thành công');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể xóa tài khoản của chính mình');
        }

        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'Xóa tài khoản thành công');
    }

    private function getPermissionList()
    {
        return [
            'mau_khaosat' => 'Mẫu khảo sát',
            'dot_khaosat' => 'Đợt khảo sát',
            'bao_cao' => 'Báo cáo',
            'doi_tuong' => 'Đối tượng khảo sát',
            'cau_hinh' => 'Cấu hình hệ thống'
        ];
    }
}