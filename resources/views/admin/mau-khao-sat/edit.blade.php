@extends('layouts.admin')

@section('title', 'Chỉnh sửa mẫu khảo sát')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.mau-khao-sat.index') }}">Mẫu khảo sát</a></li>
            <li class="breadcrumb-item active">Chỉnh sửa: {{ $mauKhaoSat->ten_mau }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <!-- Thông tin mẫu -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin mẫu khảo sát</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.mau-khao-sat.update', $mauKhaoSat) }}" id="formUpdateMau">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label">Tên mẫu khảo sát <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('ten_mau') is-invalid @enderror" 
                                       name="ten_mau" value="{{ old('ten_mau', $mauKhaoSat->ten_mau) }}" required>
                                @error('ten_mau')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Trạng thái</label>
                                <select class="form-select @error('trangthai') is-invalid @enderror" name="trangthai">
                                    <option value="draft" {{ old('trangthai', $mauKhaoSat->trangthai) == 'draft' ? 'selected' : '' }}>
                                        Nháp
                                    </option>
                                    <option value="active" {{ old('trangthai', $mauKhaoSat->trangthai) == 'active' ? 'selected' : '' }}>
                                        Hoạt động
                                    </option>
                                    <option value="inactive" {{ old('trangthai', $mauKhaoSat->trangthai) == 'inactive' ? 'selected' : '' }}>
                                        Không hoạt động
                                    </option>
                                </select>
                                @error('trangthai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Đối tượng khảo sát</label>
                            <input type="text" class="form-control" 
                                   value="{{ $mauKhaoSat->doiTuong->ten_doituong ?? 'N/A' }}" disabled>
                            <small class="text-muted">Không thể thay đổi đối tượng sau khi tạo mẫu</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control @error('mota') is-invalid @enderror" 
                                      name="mota" rows="3">{{ old('mota', $mauKhaoSat->mota) }}</textarea>
                            @error('mota')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danh sách câu hỏi -->
            <div class="card shadow">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Danh sách câu hỏi ({{ $mauKhaoSat->cauHoi->count() ?? 0 }} câu)</h5>
                        <button class="btn btn-primary btn-sm" onclick="showModalThemCauHoi()">
                            <i class="bi bi-plus"></i> Thêm câu hỏi
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if(($mauKhaoSat->cauHoi->count() ?? 0) == 0)
                        <div class="text-center py-5">
                            <i class="bi bi-question-circle fs-1 text-muted"></i>
                            <p class="text-muted">Chưa có câu hỏi nào</p>
                            <button class="btn btn-primary" onclick="showModalThemCauHoi()">
                                <i class="bi bi-plus"></i> Thêm câu hỏi đầu tiên
                            </button>
                        </div>
                    @else
                        <div id="danhSachCauHoi" class="sortable">
                            @php $stt = 1; @endphp
                            @foreach($mauKhaoSat->cauHoi->sortBy('thutu') as $cauHoi)
                                <div class="card mb-3 question-item" data-id="{{ $cauHoi->id }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge bg-secondary me-2">Câu {{ $stt++ }}</span>
                                                    <h6 class="mb-0">
                                                        {{ $cauHoi->noidung_cauhoi }}
                                                        @if($cauHoi->batbuoc)
                                                            <span class="text-danger">*</span>
                                                        @endif
                                                    </h6>
                                                </div>
                                                
                                                <div class="mb-2">
                                                    <span class="badge bg-info">
                                                        @switch($cauHoi->loai_cauhoi)
                                                            @case('single_choice')
                                                                Chọn một
                                                                @break
                                                            @case('multiple_choice')
                                                                Chọn nhiều
                                                                @break
                                                            @case('text')
                                                                Văn bản
                                                                @break
                                                            @case('likert')
                                                                Thang đo Likert
                                                                @break
                                                            @case('rating')
                                                                Đánh giá
                                                                @break
                                                            @case('date')
                                                                Ngày tháng
                                                                @break
                                                            @case('number')
                                                                Số
                                                                @break
                                                            @default
                                                                {{ $cauHoi->loai_cauhoi }}
                                                        @endswitch
                                                    </span>
                                                </div>
                                                
                                                @if(in_array($cauHoi->loai_cauhoi, ['single_choice', 'multiple_choice', 'likert']))
                                                    <ol class="mb-0 ps-3">
                                                        @foreach($cauHoi->phuongAnTraLoi as $pa)
                                                            <li>{{ $pa->noidung }}</li>
                                                        @endforeach
                                                    </ol>
                                                @endif
                                            </div>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-secondary handle" title="Kéo để sắp xếp">
                                                    <i class="bi bi-grip-vertical"></i>
                                                </button>
                                                <button class="btn btn-outline-primary" onclick="editCauHoi({{ $cauHoi->id }})" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" onclick="deleteCauHoi({{ $cauHoi->id }})" title="Xóa">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Thông tin -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h6 class="card-title">Thông tin</h6>
                    <table class="table table-sm">
                        <tr>
                            <td class="text-muted">ID:</td>
                            <td><strong>{{ $mauKhaoSat->id }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Đối tượng:</td>
                            <td>{{ $mauKhaoSat->doiTuong->ten_doituong ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Số câu hỏi:</td>
                            <td>
                                <span class="badge bg-info">{{ $mauKhaoSat->cauHoi->count() ?? 0 }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Người tạo:</td>
                            <td>{{ $mauKhaoSat->nguoiTao->hoten ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Ngày tạo:</td>
                            <td>{{ $mauKhaoSat->created_at ? $mauKhaoSat->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Cập nhật:</td>
                            <td>{{ $mauKhaoSat->updated_at ? $mauKhaoSat->updated_at->format('d/m/Y H:i') : 'N/A' }}</td>
                        </tr>
                    </table>

                    @if(($mauKhaoSat->dotKhaoSat->count() ?? 0) > 0)
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            Mẫu này đang được sử dụng trong {{ $mauKhaoSat->dotKhaoSat->count() }} đợt khảo sát
                        </div>
                    @endif
                </div>
            </div>

            <!-- Thao tác -->
            <div class="card shadow">
                <div class="card-body">
                    <h6 class="card-title">Thao tác</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.mau-khao-sat.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại danh sách
                        </a>
                        
                        <button type="button" class="btn btn-info" onclick="copyMauKhaoSat()">
                            <i class="bi bi-files"></i> Sao chép mẫu này
                        </button>
                        
                        @if($mauKhaoSat->trangthai == 'active' && ($mauKhaoSat->cauHoi->count() ?? 0) > 0)
                            <a href="{{ route('dot-khao-sat.create') }}?mau_khaosat_id={{ $mauKhaoSat->id }}" 
                               class="btn btn-success">
                                <i class="bi bi-calendar-plus"></i> Tạo đợt khảo sát
                            </a>
                        @endif
                        
                        @if(($mauKhaoSat->dotKhaoSat->count() ?? 0) == 0)
                            <button type="button" class="btn btn-danger" onclick="deleteMauKhaoSat()">
                                <i class="bi bi-trash"></i> Xóa mẫu này
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal thêm/sửa câu hỏi -->
<div class="modal fade" id="modalCauHoi" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Thêm câu hỏi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formCauHoi">
                    <input type="hidden" id="cauHoiId">
                    
                    <div class="mb-3">
                        <label class="form-label">Nội dung câu hỏi <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="noiDungCauHoi" rows="2" required 
                                  placeholder="Nhập nội dung câu hỏi..."></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Loại câu hỏi</label>
                            <select class="form-select" id="loaiCauHoi" onchange="changeLoaiCauHoi()">
                                <option value="single_choice">Chọn một</option>
                                <option value="multiple_choice">Chọn nhiều</option>
                                <option value="text">Văn bản</option>
                                <option value="likert">Thang đo Likert (5 mức)</option>
                                <option value="rating">Đánh giá (1-5 sao)</option>
                                <option value="date">Ngày tháng</option>
                                <option value="number">Số</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Thứ tự</label>
                            <input type="number" class="form-control" id="thuTu" value="0" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="batBuoc" checked>
                                <label class="form-check-label" for="batBuoc">
                                    Bắt buộc trả lời
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div id="phuongAnContainer">
                        <label class="form-label">Phương án trả lời</label>
                        <div id="danhSachPhuongAn">
                            <!-- Phương án sẽ được thêm bằng JS -->
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="addPhuongAn()">
                            <i class="bi bi-plus"></i> Thêm phương án
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="saveCauHoi()">
                    <i class="bi bi-save"></i> Lưu câu hỏi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Forms ẩn -->
<form id="formCopyMau" action="{{ route('admin.mau-khao-sat.copy', $mauKhaoSat) }}" method="POST" style="display: none;">
    @csrf
</form>

<form id="formDeleteMau" action="{{ route('admin.mau-khao-sat.destroy', $mauKhaoSat) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('styles')
<style>
    .sortable {
        min-height: 50px;
    }
    .question-item {
        cursor: move;
    }
    .question-item.dragging {
        opacity: 0.5;
    }
    .handle {
        cursor: grab;
    }
    .handle:active {
        cursor: grabbing;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    const mauKhaoSatId = {{ $mauKhaoSat->id }};
    let sortable = null;
    
    // Initialize sortable
    document.addEventListener('DOMContentLoaded', function() {
        const el = document.getElementById('danhSachCauHoi');
        if (el) {
            sortable = Sortable.create(el, {
                handle: '.handle',
                animation: 150,
                onEnd: function(evt) {
                    updateQuestionOrder();
                }
            });
        }
    });
    
    // Modal thêm câu hỏi
    function showModalThemCauHoi() {
        $('#modalTitle').text('Thêm câu hỏi');
        $('#cauHoiId').val('');
        $('#formCauHoi')[0].reset();
        $('#batBuoc').prop('checked', true);
        changeLoaiCauHoi();
        $('#modalCauHoi').modal('show');
    }
    
    // Thay đổi loại câu hỏi
    function changeLoaiCauHoi() {
        const loai = $('#loaiCauHoi').val();
        const container = $('#phuongAnContainer');
        
        if (['single_choice', 'multiple_choice'].includes(loai)) {
            container.show();
            $('#danhSachPhuongAn').html(`
                <div class="input-group mb-2">
                    <span class="input-group-text">1</span>
                    <input type="text" class="form-control phuong-an" placeholder="Nhập phương án...">
                    <button class="btn btn-outline-danger" type="button" onclick="removePhuongAn(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="input-group mb-2">
                    <span class="input-group-text">2</span>
                    <input type="text" class="form-control phuong-an" placeholder="Nhập phương án...">
                    <button class="btn btn-outline-danger" type="button" onclick="removePhuongAn(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `);
        } else if (loai === 'likert') {
            container.show();
            $('#danhSachPhuongAn').html(`
                <div class="input-group mb-2">
                    <span class="input-group-text">1</span>
                    <input type="text" class="form-control phuong-an" value="Hoàn toàn không đồng ý" readonly>
                </div>
                <div class="input-group mb-2">
                    <span class="input-group-text">2</span>
                    <input type="text" class="form-control phuong-an" value="Không đồng ý" readonly>
                </div>
                <div class="input-group mb-2">
                    <span class="input-group-text">3</span>
                    <input type="text" class="form-control phuong-an" value="Trung lập" readonly>
                </div>
                <div class="input-group mb-2">
                    <span class="input-group-text">4</span>
                    <input type="text" class="form-control phuong-an" value="Đồng ý" readonly>
                </div>
                <div class="input-group mb-2">
                    <span class="input-group-text">5</span>
                    <input type="text" class="form-control phuong-an" value="Hoàn toàn đồng ý" readonly>
                </div>
            `);
        } else {
            container.hide();
        }
    }
    
    // Thêm phương án
    function addPhuongAn() {
        const count = $('#danhSachPhuongAn .input-group').length + 1;
        $('#danhSachPhuongAn').append(`
            <div class="input-group mb-2">
                <span class="input-group-text">${count}</span>
                <input type="text" class="form-control phuong-an" placeholder="Nhập phương án...">
                <button class="btn btn-outline-danger" type="button" onclick="removePhuongAn(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `);
        updatePhuongAnNumbers();
    }
    
    // Xóa phương án
    function removePhuongAn(btn) {
        if ($('#danhSachPhuongAn .input-group').length > 2) {
            $(btn).closest('.input-group').remove();
            updatePhuongAnNumbers();
        } else {
            alert('Phải có ít nhất 2 phương án');
        }
    }
    
    // Cập nhật số thứ tự phương án
    function updatePhuongAnNumbers() {
        $('#danhSachPhuongAn .input-group').each(function(index) {
            $(this).find('.input-group-text').text(index + 1);
        });
    }
    
    // Lưu câu hỏi
    function saveCauHoi() {
        const cauHoiId = $('#cauHoiId').val();
        const data = {
            noidung_cauhoi: $('#noiDungCauHoi').val(),
            loai_cauhoi: $('#loaiCauHoi').val(),
            thutu: $('#thuTu').val() || 0,
            batbuoc: $('#batBuoc').is(':checked') ? 1 : 0,
            phuong_an: []
        };
        
        // Lấy phương án trả lời
        $('.phuong-an').each(function() {
            if ($(this).val().trim()) {
                data.phuong_an.push($(this).val().trim());
            }
        });
        
        // Validate
        if (!data.noidung_cauhoi) {
            alert('Vui lòng nhập nội dung câu hỏi');
            return;
        }
        
        if (['single_choice', 'multiple_choice', 'likert'].includes(data.loai_cauhoi) && data.phuong_an.length < 2) {
            alert('Vui lòng nhập ít nhất 2 phương án');
            return;
        }
        
        // Submit
        const url = cauHoiId 
            ? `/cau-hoi/${cauHoiId}` 
            : `/admin.mau-khao-sat/${mauKhaoSatId}/cau-hoi`;
        const method = cauHoiId ? 'PUT' : 'POST';
        
        $.ajax({
            url: url,
            method: method,
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#modalCauHoi').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Có lỗi xảy ra: ' + (xhr.responseJSON?.message || 'Vui lòng thử lại'));
            }
        });
    }
    
    // Sửa câu hỏi
    function editCauHoi(id) {
        // TODO: Load data câu hỏi và hiển thị modal edit
        alert('Chức năng đang phát triển');
    }
    
    // Xóa câu hỏi
    function deleteCauHoi(id) {
        if (!confirm('Bạn có chắc chắn muốn xóa câu hỏi này?')) return;
        
        $.ajax({
            url: `/cau-hoi/${id}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                alert('Có lỗi xảy ra: ' + (xhr.responseJSON?.message || 'Vui lòng thử lại'));
            }
        });
    }
    
    // Cập nhật thứ tự câu hỏi
    function updateQuestionOrder() {
        const order = [];
        $('.question-item').each(function(index) {
            order.push({
                id: $(this).data('id'),
                thutu: index + 1
            });
        });
        
        $.ajax({
            url: '/cau-hoi/update-order',
            method: 'POST',
            data: { items: order },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Đã cập nhật thứ tự');
            }
        });
    }
    
    // Sao chép mẫu
    function copyMauKhaoSat() {
        if (confirm('Bạn có chắc chắn muốn sao chép mẫu khảo sát này?')) {
            $('#formCopyMau').submit();
        }
    }
    
    // Xóa mẫu
    function deleteMauKhaoSat() {
        if (confirm('Bạn có chắc chắn muốn xóa mẫu khảo sát này?\nHành động này không thể hoàn tác!')) {
            $('#formDeleteMau').submit();
        }
    }
</script>
@endpush