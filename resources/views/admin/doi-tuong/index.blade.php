@extends('layouts.admin')

@section('title', 'Quản lý đối tượng khảo sát')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Quản lý đối tượng khảo sát</h1>
            <a href="{{ route('admin.doi-tuong.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Thêm đối tượng
            </a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Mã đối tượng</th>
                                <th>Tên đối tượng</th>
                                <th>Số mẫu KS</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($doiTuongs as $doiTuong)
                                <tr>
                                    <td>{{ $doiTuong->ma_doituong }}</td>
                                    <td>{{ $doiTuong->ten_doituong }}</td>
                                    <td>{{ $doiTuong->mau_khao_sat_count }}</td>
                                    <td>
                                        <span class="badge bg-{{ $doiTuong->trangthai ? 'success' : 'secondary' }}">
                                            {{ $doiTuong->trangthai ? 'Hoạt động' : 'Khóa' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.doi-tuong.edit', $doiTuong->id) }}"
                                                class="btn btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if($doiTuong->mau_khao_sat_count == 0)
                                                <form method="POST" action="{{ route('admin.doi-tuong.destroy', $doiTuong) }}"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Không có dữ liệu</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $doiTuongs->links() }}
            </div>
        </div>
    </div>
@endsection