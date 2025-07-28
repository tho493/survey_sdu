<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Hệ thống khảo sát</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem 0 rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card login-card">
                    <div class="card-body p-5">
                        <h3 class="text-center mb-4">Hệ thống khảo sát</h3>

                        @if($errors->any())
                            <div class="alert alert-danger">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Tên đăng nhập</label>
                                <input type="text" class="form-control" name="tendangnhap"
                                    value="{{ old('tendangnhap') }}" required autofocus>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mật khẩu</label>
                                <input type="password" class="form-control" name="matkhau" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                Đăng nhập
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>