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
                            <a href="{{ route('admin.dot-khao-sat.create') }}?mau_khaosat_id={{ $mauKhaoSat->id }}" 
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
<div class="modal fade" id="modalCauHoi" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Thêm câu hỏi mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="validation-errors" class="alert alert-danger d-none"></div> 
                <!-- thông báo lỗi hiển thị ở id validation-errors -->

                <form id="formCauHoi" onsubmit="saveCauHoi(event)">
                    <input type="hidden" id="cauHoiId">
                    
                    <div class="mb-3">
                        <label for="noiDungCauHoi" class="form-label">Nội dung câu hỏi <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="noiDungCauHoi" rows="2" required placeholder="Nhập nội dung câu hỏi..."></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="loaiCauHoi" class="form-label">Loại câu hỏi</label>
                            <select class="form-select" id="loaiCauHoi" onchange="togglePhuongAnContainer()">
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
                            <label for="thuTu" class="form-label">Thứ tự</label>
                            <input type="number" class="form-control" id="thuTu" value="0" min="0">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="batBuoc" checked>
                                <label class="form-check-label" for="batBuoc">Bắt buộc trả lời</label>
                            </div>
                        </div>
                    </div>
                    
                    <div id="phuongAnContainer">
                        <label class="form-label">Phương án trả lời <span id="phuongAnRequired" class="text-danger">*</span></label>
                        <div id="danhSachPhuongAn">
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary mt-2" id="btnAddPhuongAn" onclick="addPhuongAn()">
                            <i class="bi bi-plus"></i> Thêm phương án
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="btnSaveCauHoi" onclick="saveCauHoi(event)">
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
    const modalCauHoi = new bootstrap.Modal(document.getElementById('modalCauHoi'));

    function showModalThemCauHoi() {
        $('#modalTitle').text('Thêm câu hỏi mới');
        $('#formCauHoi')[0].reset(); // Reset form
        $('#cauHoiId').val('');      // Xóa ID câu hỏi cũ (nếu có)
        $('#batBuoc').prop('checked', true);
        $('#validation-errors').addClass('d-none').html(''); // Ẩn thông báo lỗi
        
        togglePhuongAnContainer(); // Cập nhật hiển thị phương án
        modalCauHoi.show();
    }

    function togglePhuongAnContainer() {
        const loai = $('#loaiCauHoi').val();
        const container = $('#phuongAnContainer');
        const isChoiceType = ['single_choice', 'multiple_choice', 'likert'].includes(loai);
        
        container.toggle(isChoiceType);
        
        // Nếu là loại câu hỏi lựa chọn, tự động thêm phương án nếu chưa có
        if (isChoiceType && $('#danhSachPhuongAn').is(':empty')) {
            if (loai === 'likert') {
                const likertOptions = [
                    'Rất không hài lòng', 'Không hài lòng', 'Bình thường', 'Hài lòng', 'Rất hài lòng'
                ];
                likertOptions.forEach(option => addPhuongAn(option, true)); // Thêm và đánh dấu readonly
            } else {
                addPhuongAn('');
                addPhuongAn('');
            }
        }
    }

    function addPhuongAn(value = '', isReadonly = false) {
        const count = $('#danhSachPhuongAn .input-group').length + 1;
        const readonlyAttr = isReadonly ? 'readonly' : '';
        const html = `
            <div class="input-group mb-2">
                <span class="input-group-text">${count}</span>
                <input type="text" class="form-control phuong-an" value="${value}" ${readonlyAttr}>
                <button class="btn btn-outline-danger" type="button" onclick="removePhuongAn(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        $('#danhSachPhuongAn').append(html);
    }

    function removePhuongAn(btn) {
        if ($('#danhSachPhuongAn .input-group').length > 2) {
            $(btn).closest('.input-group').remove();
            // Cập nhật lại số thứ tự
            $('#danhSachPhuongAn .input-group').each(function(index) {
                $(this).find('.input-group-text').text(index + 1);
            });
        } else {
            alert('Phải có ít nhất 2 phương án trả lời.');
        }
    }

    function saveCauHoi(event) {
        event.preventDefault();
        const btn = $('#btnSaveCauHoi');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Đang lưu...');
        
        const cauHoiId = $('#cauHoiId').val();
        const data = {
            noidung_cauhoi: $('#noiDungCauHoi').val(),
            loai_cauhoi: $('#loaiCauHoi').val(),
            thutu: $('#thuTu').val() || 0,
            batbuoc: $('#batBuoc').is(':checked') ? 1 : 0,
            phuong_an: []
        };
        
        $('.phuong-an').each(function() {
            if ($(this).val().trim() !== '') {
                data.phuong_an.push($(this).val().trim());
            }
        });
        
        const url = cauHoiId ? `/admin/cau-hoi/${cauHoiId}` : `/admin/mau-khao-sat/${mauKhaoSatId}/cau-hoi`;
        const method = cauHoiId ? 'PUT' : 'POST';
        
        $.ajax({
            url: url,
            method: method,
            data: data,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                if (response.success) {
                    modalCauHoi.hide();
                    location.reload(); // Tải lại trang để cập nhật danh sách
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="bi bi-save"></i> Lưu câu hỏi');
                
                if (xhr.status === 422) {
                    // Hiển thị lỗi validation
                    let errorHtml = '<ul>';
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        errorHtml += '<li>' + value[0] + '</li>';
                    });
                    errorHtml += '</ul>';
                    $('#validation-errors').html(errorHtml).removeClass('d-none');
                } else {
                    // alert('Đã xảy ra lỗi không mong muốn. Vui lòng thử lại.');
                    console.log(xhr.responseJSON);
                }
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
            url: `/admin/cau-hoi/${id}`,
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
            url: '/admin/cau-hoi/update-order',
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