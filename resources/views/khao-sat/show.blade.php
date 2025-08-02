<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $dotKhaoSat->ten_dot }} - Khảo sát</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .question-card {
            margin-bottom: 20px;
            border-left: 3px solid #007bff;
        }
        .required-mark {
            color: #dc3545;
        }
        .progress-section {
            position: sticky;
            top: 20px;
        }
        .likert-scale {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
        }
        .likert-option {
            text-align: center;
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="card-title">{{ $dotKhaoSat->ten_dot }}</h2>
                        <p class="text-muted">
                            <i class="bi bi-tag"></i> {{ $mauKhaoSat->doiTuong->ten_doituong }} | 
                            <i class="bi bi-calendar"></i> Hạn cuối: {{ $dotKhaoSat->denngay->format('d/m/Y') }}
                        </p>
                        @if($dotKhaoSat->mota)
                            <p>{{ $dotKhaoSat->mota }}</p>
                        @endif
                    </div>
                </div>

                <form id="formKhaoSat" method="POST" action="{{ route('khao-sat.store', $dotKhaoSat) }}">
                    @csrf
                    
                    <!-- Thông tin người trả lời -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Thông tin của bạn</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Mã số <span class="required-mark">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="ma_nguoi_traloi" required>
                                    <small class="text-muted">Mã sinh viên, mã nhân viên, mã doanh nghiệp...</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Họ và tên <span class="required-mark">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="metadata[hoten]" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Đơn vị/Khoa</label>
                                    <input type="text" class="form-control" name="metadata[donvi]">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="metadata[email]">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Câu hỏi khảo sát -->
                    @php $questionNumber = 0; @endphp
                    @foreach($mauKhaoSat->nhomCauHoi as $nhom)
                        @if($nhom->hienthi_tennhom && $nhom->ten_nhom)
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">{{ $nhom->ten_nhom }}</h5>
                                </div>
                                <div class="card-body">
                        @else
                            <div class="card shadow-sm mb-4">
                                <div class="card-body">
                        @endif
                        
                        @foreach($nhom->cauHoi as $cauHoi)
                            @php $questionNumber++; @endphp
                            <div class="question-card card mb-3" data-question-id="{{ $cauHoi->id }}">
                                <div class="card-body">
                                    <label class="form-label">
                                        <strong>Câu {{ $questionNumber }}:</strong> 
                                        {{ $cauHoi->noidung_cauhoi }}
                                        @if($cauHoi->batbuoc)
                                            <span class="required-mark">*</span>
                                        @endif
                                    </label>
                                    
                                    @switch($cauHoi->loai_cauhoi)
                                        @case('single_choice')
                                            <div class="mt-2">
                                                @foreach($cauHoi->phuongAnTraLoi as $phuongAn)
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="radio" 
                                                               name="cau_tra_loi[{{ $cauHoi->id }}]" 
                                                               value="{{ $phuongAn->id }}"
                                                               id="pa_{{ $phuongAn->id }}"
                                                               {{ $cauHoi->batbuoc ? 'required' : '' }}>
                                                        <label class="form-check-label" for="pa_{{ $phuongAn->id }}">
                                                            {{ $phuongAn->noidung }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @break
                                            
                                        @case('multiple_choice')
                                            <div class="mt-2">
                                                @foreach($cauHoi->phuongAnTraLoi as $phuongAn)
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" 
                                                               name="cau_tra_loi[{{ $cauHoi->id }}][]" 
                                                               value="{{ $phuongAn->id }}"
                                                               id="pa_{{ $phuongAn->id }}">
                                                        <label class="form-check-label" for="pa_{{ $phuongAn->id }}">
                                                            {{ $phuongAn->noidung }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @break
                                            
                                        @case('text')
                                                                                        <textarea class="form-control mt-2" 
                                                      name="cau_tra_loi[{{ $cauHoi->id }}]" 
                                                      rows="3"
                                                      placeholder="Nhập câu trả lời của bạn..."
                                                      {{ $cauHoi->batbuoc ? 'required' : '' }}></textarea>
                                            @break
                                            
                                        @case('likert')
                                            <div class="likert-scale mt-3">
                                                @foreach($cauHoi->phuongAnTraLoi as $phuongAn)
                                                    <div class="likert-option">
                                                        <input type="radio" class="form-check-input" 
                                                               name="cau_tra_loi[{{ $cauHoi->id }}]" 
                                                               value="{{ $phuongAn->id }}"
                                                               id="pa_{{ $phuongAn->id }}"
                                                               {{ $cauHoi->batbuoc ? 'required' : '' }}>
                                                        <br>
                                                        <label class="form-check-label small" for="pa_{{ $phuongAn->id }}">
                                                            {{ $phuongAn->noidung }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @break
                                            
                                        @case('rating')
                                            <div class="mt-2">
                                                <div class="btn-group" role="group">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <input type="radio" class="btn-check" 
                                                               name="cau_tra_loi[{{ $cauHoi->id }}]" 
                                                               value="{{ $i }}"
                                                               id="rating_{{ $cauHoi->id }}_{{ $i }}"
                                                               {{ $cauHoi->batbuoc ? 'required' : '' }}>
                                                        <label class="btn btn-outline-primary" 
                                                               for="rating_{{ $cauHoi->id }}_{{ $i }}">
                                                            {{ $i }}
                                                        </label>
                                                    @endfor
                                                </div>
                                                <small class="text-muted d-block mt-1">1 = Rất không hài lòng, 5 = Rất hài lòng</small>
                                            </div>
                                            @break
                                            
                                        @case('date')
                                            <input type="date" class="form-control mt-2" 
                                                   name="cau_tra_loi[{{ $cauHoi->id }}]"
                                                   {{ $cauHoi->batbuoc ? 'required' : '' }}>
                                            @break
                                            
                                        @case('number')
                                            <input type="number" class="form-control mt-2" 
                                                   name="cau_tra_loi[{{ $cauHoi->id }}]"
                                                   placeholder="Nhập số..."
                                                   {{ $cauHoi->batbuoc ? 'required' : '' }}>
                                            @break
                                    @endswitch
                                </div>
                            </div>
                        @endforeach
                        
                        </div>
                    </div>
                    @endforeach

                    <!-- Submit button -->
                    <div class="text-center my-4">
                        <button type="button" class="btn btn-secondary btn-lg me-2" onclick="history.back()">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                            <i class="bi bi-send"></i> Gửi khảo sát
                        </button>
                    </div>
                </form>
            </div>

            <!-- Sidebar Progress -->
            <div class="col-lg-4">
                <div class="progress-section">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">Tiến độ hoàn thành</h6>
                            <div class="progress mb-3" style="height: 25px;">
                                <div class="progress-bar" role="progressbar" style="width: 0%;" 
                                     id="progressBar">0%</div>
                            </div>
                            <p class="text-muted small mb-0">
                                Đã trả lời: <span id="answeredCount">0</span>/{{ $questionNumber }} câu
                            </p>
                        </div>
                    </div>

                    <div class="card shadow-sm mt-3">
                        <div class="card-body">
                            <h6 class="card-title">Lưu ý</h6>
                            <ul class="small mb-0">
                                <li>Câu hỏi có dấu <span class="required-mark">*</span> là bắt buộc</li>
                                <li>Vui lòng kiểm tra kỹ trước khi gửi</li>
                                <li>Mỗi người chỉ được tham gia 1 lần</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Update progress
            function updateProgress() {
                const totalQuestions = $('.question-card').length;
                let answeredQuestions = 0;
                
                $('.question-card').each(function() {
                    const questionId = $(this).data('question-id');
                    const inputs = $(this).find('input[name^="cau_tra_loi"], textarea[name^="cau_tra_loi"]');
                    
                    let isAnswered = false;
                    inputs.each(function() {
                        if ($(this).is(':checkbox') || $(this).is(':radio')) {
                            if ($(this).is(':checked')) {
                                isAnswered = true;
                            }
                        } else if ($(this).val().trim() !== '') {
                            isAnswered = true;
                        }
                    });
                    
                    if (isAnswered) {
                        answeredQuestions++;
                        $(this).addClass('border-success');
                    } else {
                        $(this).removeClass('border-success');
                    }
                });
                
                const progress = Math.round((answeredQuestions / totalQuestions) * 100);
                $('#progressBar').css('width', progress + '%').text(progress + '%');
                $('#answeredCount').text(answeredQuestions);
            }
            
            // Update progress on input change
            $('input, textarea').on('change keyup', updateProgress);
            
            // Form submission
            $('#formKhaoSat').on('submit', function(e) {
                e.preventDefault();
                
                // Validate required fields
                let isValid = true;
                $(this).find('[required]').each(function() {
                    if ($(this).is(':radio')) {
                        const name = $(this).attr('name');
                        if (!$(`input[name="${name}"]:checked`).length) {
                            isValid = false;
                            $(this).closest('.question-card').addClass('border-danger');
                        }
                    } else if (!$(this).val()) {
                        isValid = false;
                        $(this).closest('.question-card').addClass('border-danger');
                    }
                });
                
                if (!isValid) {
                    alert('Vui lòng trả lời tất cả câu hỏi bắt buộc!');
                    return;
                }
                
                // Disable submit button
                $('#submitBtn').prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-2"></span>Đang gửi...');
                
                // Submit form
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            window.location.href = response.redirect;
                        }
                    },
                    error: function(xhr) {
                        alert('Có lỗi xảy ra: ' + (xhr.responseJSON?.message || 'Vui lòng thử lại'));
                        $('#submitBtn').prop('disabled', false)
                            .html('<i class="bi bi-send"></i> Gửi khảo sát');
                    }
                });
            });
            
            // Initialize progress
            updateProgress();
        });
    </script>
</body>
</html>