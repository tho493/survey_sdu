@extends('layouts.admin')

@section('title', 'Sửa đối tượng khảo sát')

@section('content')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.doi-tuong.index') }}">Quản lý đối tượng khảo sát</a>
                </li>
                <li class="breadcrumb-item active">Sửa: {{ $doiTuong->ten_doituong }}</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0">Sửa đối tượng khảo sát</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.doi-tuong.update', $doiTuong->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Mã đối tượng</label>
                                <input type="text" class="form-control" value="{{ $doiTuong->id }}" disabled>
                                <small class="text-muted">Mã đối tượng không thể thay đổi</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tên đối tượng <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('ten_doituong') is-invalid @enderror"
                                    name="ten_doituong" value="{{ old('ten_doituong', $doiTuong->ten_doituong) }}" required>
                                @error('ten_doituong')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mô tả</label>
                                <textarea class="form-control @error('mota') is-invalid @enderror" name="mota"
                                    rows="3">{{ old('mota', $doiTuong->mota) }}</textarea>
                                @error('mota')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Trạng thái</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="trangthai" id="trangthai"
                                        value="1" {{ old('trangthai', $doiTuong->trangthai) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="trangthai">
                                        Hoạt động
                                    </label>
                                </div>
                            </div>

                            <div class="text-end">
                                <a href="{{ route('admin.doi-tuong.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Hủy
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Lưu thay đổi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h6 class="card-title">Thông tin</h6>
                        <table class="table table-sm">
                            <tr>
                                <td class="text-muted" width="40%">Mã đối tượng:</td>
                                <td><strong>{{ $doiTuong->ma_doituong }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Số mẫu khảo sát:</td>
                                <td>
                                    <span class="badge bg-info">{{ $doiTuong->mauKhaoSat->count() }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Ngày tạo:</td>
                                <td>{{ $doiTuong->created_at ? $doiTuong->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                            </tr>
                        </table>

                        @if($doiTuong->mauKhaoSat->count() > 0)
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                Đối tượng này đang được sử dụng trong {{ $doiTuong->mauKhaoSat->count() }} mẫu khảo sát
                            </div>

                            <h6 class="mt-3">Danh sách mẫu khảo sát liên quan:</h6>
                            <ul class="list-group">
                                @foreach($doiTuong->mauKhaoSat->take(5) as $mau)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>{{ $mau->ten_mau }}</span>
                                        <span class="badge bg-{{ $mau->trangthai == 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($mau->trangthai) }}
                                        </span>
                                    </li>
                                @endforeach
                                @if($doiTuong->mauKhaoSat->count() > 5)
                                    <li class="list-group-item text-center text-muted">
                                        ... và {{ $doiTuong->mauKhaoSat->count() - 5 }} mẫu khác
                                    </li>
                                @endif
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection