@extends('layouts.app')

@section('title', 'Chỉnh sửa mẫu khảo sát')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h1>Chỉnh sửa mẫu khảo sát</h1>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-success" onclick="saveMauKhaoSat()">
                    <i class="bi bi-save"></i> Lưu
                </button>
                <a href="{{ route('mau-khao-sat.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <form id="formMauKhaoSat">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Tên mẫu khảo sát</label>
                            <input type="text" class="form-control" name="ten_mau" value="{{ $mauKhaoSat->ten_mau }}"
                                required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Đối tượng</label>
                            <select class="form-select" name="ma_doituong" disabled>
                                <option value="{{ $mauKhaoSat->ma_doituong }}">
                                    {{ $mauKhaoSat->doiTuong->ten_doituong }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="mota" rows="2">{{ $mauKhaoSat->mota }}</textarea>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danh sách câu hỏi -->
        <div class="card mt-3">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <h5 class="mb-0">Danh sách câu hỏi</h5>
                    </div>
                    <div class="col text-end">
                        <button class="btn btn-primary btn-sm" onclick="showModalThemCauHoi()">
                            <i class="bi bi-plus"></i> Thêm câu hỏi
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="danhSachCauHoi">
                @foreach($mauKhaoSat->nhomCauHoi as $nhom)
                    <div class="nhom-cau-hoi mb-4">
                        <h6 class="text-primary">{{ $nhom->ten_nhom }}</h6>
                        <div class="list-group" data-nhom-id="{{ $nhom->id }}">
                            @foreach($nhom->cauHoi as $cauHoi)
                                <div class="list-group-item" data-cauhoi-id="{{ $cauHoi->id }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="fw-bold">
                                                {{ $loop->iteration }}. {{ $cauHoi->noidung_cauhoi }}
                                                @if($cauHoi->batbuoc)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </div>
                                            <div class="mt-2">
                                                @if(in_array($cauHoi->loai_cauhoi, ['single_choice', 'multiple_choice']))
                                                    @foreach($cauHoi->phuongAnTraLoi as $phuongAn)
                                                        <div class="form-check">
                                                            <input class="form-check-input"
                                                                type="{{ $cauHoi->loai_cauhoi == 'single_choice' ? 'radio' : 'checkbox' }}"
                                                                disabled>
                                                            <label class="form-check-label">
                                                                {{ $phuongAn->noidung }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @elseif($cauHoi->loai_cauhoi == 'text')
                                                    <input type="text" class="form-control" placeholder="Câu trả lời văn bản" disabled>
                                                @elseif($cauHoi->loai_cauhoi == 'likert')
                                                    <div class="d-flex justify-content-between">
                                                        @foreach($cauHoi->phuongAnTraLoi as $phuongAn)
                                                            <div class="text-center">
                                                                <input type="radio" class="form-check-input" disabled>
                                                                <br>
                                                                <small>{{ $phuongAn->noidung }}</small>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" onclick="editCauHoi({{ $cauHoi->id }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="deleteCauHoi({{ $cauHoi->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    </div>

    <!-- Modal thêm câu hỏi -->
    <div class="modal fade" id="modalCauHoi" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm câu hỏi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCauHoi">
                        <div class="mb-3">
                            <label class="form-label">Nội dung câu hỏi</label>
                            <textarea class="form-control" name="noidung_cauhoi" rows="2" required></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Loại câu hỏi</label>
                                <select class="form-select" name="loai_cauhoi" onchange="changeLoaiCauHoi(this.value)">
                                    <option value="single_choice">Chọn một</option>
                                    <option value="multiple_choice">Chọn nhiều</option>
                                    <option value="text">Văn bản</option>
                                    <option value="likert">Thang đo Likert</option>
                                    <option value="rating">Đánh giá</option>
                                    <option value="date">Ngày tháng</option>
                                    <option value="number">Số</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Thứ tự</label>
                                <input type="number" class="form-control" name="thutu" value="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="batbuoc" id="batbuoc" checked>
                                    <label class="form-check-label" for="batbuoc">
                                        Bắt buộc
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="phuongAnContainer">
                            <label class="form-label">Phương án trả lời</label>
                            <div id="danhSachPhuongAn">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="phuong_an[]" placeholder="Phương án 1">
                                    <button class="btn btn-outline-danger" type="button" onclick="removePhuongAn(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="phuong_an[]" placeholder="Phương án 2">
                                    <button class="btn btn-outline-danger" type="button" onclick="removePhuongAn(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary" onclick="addPhuongAn()">
                                <i class="bi bi-plus"></i> Thêm phương án
                            </button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="saveCauHoi()">Lưu</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const mauKhaoSatId = {{ $mauKhaoSat->id }};

        function showModalThemCauHoi() {
            $('#formCauHoi')[0].reset();
            $('#modalCauHoi').modal('show');
        }

        function changeLoaiCauHoi(loai) {
            const container = $('#phuongAnContainer');

            if (['single_choice', 'multiple_choice'].includes(loai)) {
                container.show();
                $('#danhSachPhuongAn').html(`
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="phuong_an[]" placeholder="Phương án 1">
                                <button class="btn btn-outline-danger" type="button" onclick="removePhuongAn(this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="phuong_an[]" placeholder="Phương án 2">
                                <button class="btn btn-outline-danger" type="button" onclick="removePhuongAn(this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        `);
            } else if (loai === 'likert') {
                container.show();
                $('#danhSachPhuongAn').html(`
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="phuong_an[]" value="Hoàn toàn không đồng ý">
                            </div>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="phuong_an[]" value="Không đồng ý">
                            </div>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="phuong_an[]" value="Trung lập">
                            </div>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="phuong_an[]" value="Đồng ý">
                            </div>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="phuong_an[]" value="Hoàn toàn đồng ý">
                            </div>
                        `);
            } else {
                container.hide();
            }
        }

        function addPhuongAn() {
            const count = $('#danhSachPhuongAn .input-group').length + 1;
            $('#danhSachPhuongAn').append(`
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" name="phuong_an[]" placeholder="Phương án ${count}">
                            <button class="btn btn-outline-danger" type="button" onclick="removePhuongAn(this)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    `);
        }

        function removePhuongAn(btn) {
            if ($('#danhSachPhuongAn .input-group').length > 2) {
                $(btn).closest('.input-group').remove();
            }
        }

        function saveCauHoi() {
            const formData = new FormData($('#formCauHoi')[0]);

            $.ajax({
                url: `/mau-khao-sat/${mauKhaoSatId}/cau-hoi`,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        $('#modalCauHoi').modal('hide');
                        location.reload();
                    }
                },
                error: function (xhr) {
                    alert('Có lỗi xảy ra: ' + xhr.responseJSON.message);
                }
            });
        }

        function deleteCauHoi(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa câu hỏi này?')) return;

            $.ajax({
                url: `/cau-hoi/${id}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    }
                }
            });
        }

        function saveMauKhaoSat() {
            const formData = $('#formMauKhaoSat').serialize();

            $.ajax({
                url: `/mau-khao-sat/${mauKhaoSatId}`,
                method: 'PUT',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    alert('Lưu thành công!');
                }
            });
        }
    </script>
@endpush