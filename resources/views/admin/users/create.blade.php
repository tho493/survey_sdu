@extends('layouts.admin')

@section('title', 'Thêm người dùng')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0">Thêm người dùng mới</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.users.store') }}">
                            @csrf

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('tendangnhap') is-invalid @enderror"
                                        name="tendangnhap" value="{{ old('tendangnhap') }}" required>
                                    @error('tendangnhap')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('matkhau') is-invalid @enderror"
                                        name="matkhau" required>
                                    @error('matkhau')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('hoten') is-invalid @enderror"
                                        name="hoten" value="{{ old('hoten') }}" required>
                                    @error('hoten')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="text" class="form-control @error('sodienthoai') is-invalid @enderror"
                                        name="sodienthoai" value="{{ old('sodienthoai') }}">
                                    @error('sodienthoai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Quyền <span class="text-danger">*</span></label>
                                    <select class="form-select @error('quyen') is-invalid @enderror" name="quyen" id="quyen"
                                        required>
                                        <option value="">-- Chọn quyền --</option>
                                        <option value="admin" {{ old('quyen') == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="manager" {{ old('quyen') == 'manager' ? 'selected' : '' }}>Manager
                                        </option>
                                        <option value="viewer" {{ old('quyen') == 'viewer' ? 'selected' : '' }}>Viewer
                                        </option>
                                    </select>
                                    @error('quyen')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Phân quyền chi tiết -->
                            <div id="permissionsSection" style="display: none;">
                                <h6 class="mb-3">Phân quyền chi tiết</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Chức năng</th>
                                                <th>Xem</th>
                                                <th>Tạo</th>
                                                <th>Sửa</th>
                                                <th>Xóa</th>
                                                <th>Toàn quyền</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($permissions as $key => $name)
                                                <tr>
                                                    <td>{{ $name }}</td>
                                                    <td>
                                                        <input type="radio" name="permissions[{{ $key }}]" value="view"
                                                            class="form-check-input">
                                                    </td>
                                                    <td>
                                                        <input type="radio" name="permissions[{{ $key }}]" value="create"
                                                            class="form-check-input">
                                                    </td>
                                                    <td>
                                                        <input type="radio" name="permissions[{{ $key }}]" value="edit"
                                                            class="form-check-input">
                                                    </td>
                                                    <td>
                                                        <input type="radio" name="permissions[{{ $key }}]" value="delete"
                                                            class="form-check-input">
                                                    </td>
                                                    <td>
                                                        <input type="radio" name="permissions[{{ $key }}]" value="full"
                                                            class="form-check-input" checked>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="text-end">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Hủy</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Lưu
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('quyen').addEventListener('change', function () {
            const permissionsSection = document.getElementById('permissionsSection');
            if (this.value === 'manager' || this.value === 'viewer') {
                permissionsSection.style.display = 'block';
            } else {
                permissionsSection.style.display = 'none';
            }
        });
    </script>
@endpush