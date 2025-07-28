@extends('layouts.admin')

@section('title', 'Cấu hình hệ thống')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4">Cấu hình hệ thống</h1>

        <div class="row">
            <!-- Cấu hình chung -->
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Cấu hình chung</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.config.update') }}">
                            @csrf

                            @foreach($configs as $group => $items)
                                <h6 class="text-uppercase text-muted mb-3">{{ $group }}</h6>

                                @foreach($items as $config)
                                    <div class="mb-3">
                                        <label class="form-label">{{ $config->mota }}</label>
                                        <input type="hidden"
                                            name="configs[{{ $loop->parent->index }}-{{ $loop->index }}][ma_cauhinh]"
                                            value="{{ $config->ma_cauhinh }}">

                                        @if(in_array($config->ma_cauhinh, ['email_smtp_password']))
                                            <input type="password" class="form-control"
                                                name="configs[{{ $loop->parent->index }}-{{ $loop->index }}][giatri]"
                                                value="{{ $config->giatri }}">
                                        @elseif(in_array($config->ma_cauhinh, ['max_file_size', 'session_timeout']))
                                            <input type="number" class="form-control"
                                                name="configs[{{ $loop->parent->index }}-{{ $loop->index }}][giatri]"
                                                value="{{ $config->giatri }}">
                                        @else
                                            <input type="text" class="form-control"
                                                name="configs[{{ $loop->parent->index }}-{{ $loop->index }}][giatri]"
                                                value="{{ $config->giatri }}">
                                        @endif
                                    </div>
                                @endforeach

                                @if(!$loop->last)
                                    <hr>
                                @endif
                            @endforeach

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Lưu cấu hình
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Template Email -->
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Template Email</h6>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="emailTemplates">
                            @foreach($emailTemplates as $template)
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#template{{ $template->id }}">
                                            {{ $template->ten_template }}
                                        </button>
                                    </h2>
                                    <div id="template{{ $template->id }}" class="accordion-collapse collapse"
                                        data-bs-parent="#emailTemplates">
                                        <div class="accordion-body">
                                            <form method="POST"
                                                action="{{ route('admin.config.update-email-template', $template) }}">
                                                @csrf
                                                @method('PUT')

                                                <div class="mb-3">
                                                    <label class="form-label">Tiêu đề</label>
                                                    <input type="text" class="form-control" name="tieude"
                                                        value="{{ $template->tieude }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Nội dung</label>
                                                    <textarea class="form-control" name="noidung" rows="6"
                                                        required>{{ $template->noidung }}</textarea>
                                                    <small class="text-muted">
                                                        Biến có thể sử dụng: {{ implode(', ', $template->bien_template ?? []) }}
                                                    </small>
                                                </div>

                                                <div class="d-flex justify-content-between">
                                                    <button type="button" class="btn btn-sm btn-secondary"
                                                        onclick="testEmail({{ $template->id }})">
                                                        <i class="bi bi-send"></i> Test email
                                                    </button>
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-save"></i> Lưu
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal test email -->
    <div class="modal fade" id="testEmailModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Test Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="testEmailForm">
                        <input type="hidden" id="template_id">
                        <div class="mb-3">
                            <label class="form-label">Email nhận</label>
                            <input type="email" class="form-control" id="test_email" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="sendTestEmail()">
                        <i class="bi bi-send"></i> Gửi
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function testEmail(templateId) {
            document.getElementById('template_id').value = templateId;
            new bootstrap.Modal(document.getElementById('testEmailModal')).show();
        }

        function sendTestEmail() {
            const email = document.getElementById('test_email').value;
            const templateId = document.getElementById('template_id').value;

            fetch('{{ route("admin.config.test-email") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    email: email,
                    template_id: templateId
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Email test đã được gửi!');
                        bootstrap.Modal.getInstance(document.getElementById('testEmailModal')).hide();
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                });
        }
    </script>
@endpush