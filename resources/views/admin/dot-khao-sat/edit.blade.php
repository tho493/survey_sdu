@extends('layouts.admin')

@section('title', 'Sửa đợt khảo sát')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dot-khao-sat.index') }}">Đợt khảo sát</a></li>
            <li class="breadcrumb-item active">Sửa: {{ $dotKhaoSat->ten_dot }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">Sửa thông tin đợt khảo sát</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dot-khao-sat.update', $dotKhaoSat) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Tên đợt khảo sát <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('ten_dot') is-invalid @enderror" 
                                   name="ten_dot" value="{{ old('ten_dot', $dotKhaoSat->ten_dot) }}" required>
                            @error('ten_dot')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mẫu khảo sát <span class="text-danger">*</span></label>
                            <select class="form-select @error('mau_khaosat_id') is-invalid @enderror" 
                                    name="mau_khaosat_id" required>
                                @foreach($mauKhaoSats as $mau)
                                    <option value="{{ $mau->id }}" 
                                        {{ old('mau_khaosat_id', $dotKhaoSat->mau_khaosat_id) == $mau->id ? 'selected' : '' }}>
                                        {{ $mau->ten_mau }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mau_khaosat_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Năm học <span class="text-danger">*</span></label>
                            <select class="form-select @error('namhoc_id') is-invalid @enderror" 
                                    name="namhoc_id" required>
                                @foreach($namHocs as $nh)
                                    <option value="{{ $nh->id }}" 
                                        {{ old('namhoc_id', $dotKhaoSat->namhoc_id) == $nh->id ? 'selected' : '' }}>
                                        {{ $nh->namhoc }}
                                    </option>
                                @endforeach
                            </select>
                            @error('namhoc_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tungay') is-invalid @enderror" 
                                       name="tungay" value="{{ old('tungay', $dotKhaoSat->tungay->format('Y-m-d')) }}" required>
                                @error('tungay')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('denngay') is-invalid @enderror" 
                                       name="denngay" value="{{ old('denngay', $dotKhaoSat->denngay->format('Y-m-d')) }}" required>
                                @error('denngay')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control @error('mota') is-invalid @enderror" 
                                      name="mota" rows="3">{{ old('mota', $dotKhaoSat->mota) }}</textarea>
                            @error('mota')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <a href="{{ route('dot-khao-sat.show', $dotKhaoSat) }}" class="btn btn-secondary">
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
    </div>
</div>
@endsection