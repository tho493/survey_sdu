@extends('layouts.admin')

@section('title', 'Thêm đối tượng khảo sát')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0">Thêm đối tượng khảo sát</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.doi-tuong.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Mã đối tượng <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('ma_doituong') is-invalid @enderror"
                                    name="ma_doituong" value="{{ old('ma_doituong') }}" maxlength="20" required>
                                @error('ma_doituong')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Mã ngắn gọn, không dấu, không khoảng trắng</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tên đối tượng <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('ten_doituong') is-invalid @enderror"
                                    name="ten_doituong" value="{{ old('ten_doituong') }}" required>
                                @error('ten_doituong')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mô tả</label>
                                <textarea class="form-control @error('mota') is-invalid @enderror" name="mota"
                                    rows="3">{{ old('mota') }}</textarea>
                                @error('mota')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-end">
                                <a href="{{ route('admin.doi-tuong.index') }}" class="btn btn-secondary">Hủy</a>
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