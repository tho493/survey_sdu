@extends('layouts.app')

@section('title', $dotKhaoSat->ten_dot)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>{{ $dotKhaoSat->ten_dot }}</h4>
                    <p class="mb-0 text-muted">{{ $mauKhaoSat->doiTuong->ten_doituong }}</p>
                </div>
                <div class="card-body">
                    <form id="formKhaoSat">
                        @csrf
                        
                        <!-- Thông tin người trả lời -->
                        <div class="mb-4">
                            <h5>Thông tin cá nhân</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mã số <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="ma_nguoi_traloi" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="metadata[hoten]" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Đơn vị</label>
                                    <input type="text" class="form-control" name="metadata[donvi]">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="metadata[email]">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Câu hỏi khảo sát -->
                        @foreach($mauKhaoSat->nhomCauHoi as $nhom)
                            @if($nhom->hienthi_tennhom)
                                <h5 class="mt-4 mb-3">{{ $nhom->ten_nhom }}</h5>
                            @endif
                            
                            @foreach($nhom->cauHoi as $cauHoi)
                                <div class="mb-4" data-cauhoi-id="{{ $cauHoi->id }}">
                                    <label class="form-label">
                                        {{ $loop->parent->iteration }}.{{ $loop->iteration }}. {{ $cauHoi->noidung_cauhoi }}
                                        @if($cauHoi->batbuoc)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    
                                    @switch($cauHoi->loai_cauhoi)
                                        @case('single_choice')
                                            @foreach($cauHoi->phuongAnTraLoi as $phuongAn)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" 
                                                           name="cau_tra_loi[{{ $cauHoi->id }}]" 
                                                           value="{{ $phuongAn->id }}"
                                                           {{ $cauHoi->batbuoc ? 'required' : '' }}>
                                                    <label class="form-check-label">
                                                        {{ $phuongAn->noidung }}
                                                    </label>
                                                </div>
                                            @endforeach
                                            @break
                                            
                                        @case('multiple_choice')
                                            @foreach($cauHoi->phuongAnTraLoi as $phuongAn)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="cau_tra_loi[{{ $cauHoi->id }}][]" 
                                                           value="{{ $phuongAn->id }}">
                                                    <label class="form-check-label">
                                                        {{ $phuongAn->noidung }}
                                                    </label>
                                                </div>
                                            @endforeach
                                            @break
                                            
                                        @case('text')
                                            <textarea class="form-control" 
                                                      name="cau_tra_loi[{{ $cauHoi->id }}]" 
                                                      rows="3"
                                                      {{ $cauHoi->batbuoc ? 'required' : '' }}></textarea>
                                            @break
                                            
                                        @case('likert')
                                            <div class="d-flex justify-content-between">
                                                @foreach($cauHoi->phuongAnTraLoi as $phuongAn)
                                                    <div class="text-center">
                                                        <input type="radio" class="form-check-input" 
                                                               name="cau_tra_loi[{{ $cauHoi->id }}]" 
                                                               value="{{ $phuongAn->id }}"
                                                               {{ $cauHoi->batbuoc ? 'required' : '' }}>
                                                        <br>
                                                        <small>{{ $phuongAn->noidung }}</small>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @break
                                    @endswitch
                                </div>
                            @endforeach
                        @endforeach

                        <div class="text-center mt-5">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-send"></i> Gửi khảo sát
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
    $(document).ready(function() {
        $('#formKhaoSat').on('submit', function(e) {
            e.preventDefault();
            
            // Validate form
            if (!this.checkValidity()) {
                e.stopPropagation();
                this.classList.add('was-validated');
                return;
            }
            
            // Disable submit button
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i> Đang gửi...');
            
            // Submit form
            $.ajax({
                url: '{{ route("khao-sat.store", $dotKhaoSat) }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.redirect;
                    }
                },
                error: function(xhr) {
                    alert('Có lỗi xảy ra: ' + (xhr.responseJSON?.message || 'Vui lòng thử lại'));
                    submitBtn.prop('disabled', false).html('<i class="bi bi-send"></i> Gửi khảo sát');
                }
            });
        });
        
        // Handle conditional questions
        $('input[type="radio"], input[type="checkbox"]').on('change', function() {
            // Logic xử lý câu hỏi điều kiện
            checkConditionalQuestions();
        });
    });
    
    function checkConditionalQuestions() {
        // Implement logic for conditional questions
    }
</script>
@endpush