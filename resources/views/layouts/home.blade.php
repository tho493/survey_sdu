<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> @yield('title', "Trang chủ") - Hệ thống khảo sát trực tuyến </title>

    {{-- Tailwind via CDN for quick prototyping; replace with @vite for production --}}
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        /* Optional: custom container width to feel closer to the screenshot */
        .container-narrow {
            max-width: 1100px
        }

        .shadow-soft {
            box-shadow: 0 10px 25px rgba(0, 0, 0, .06)
        }

        header {
            position: sticky;
            top: 0;
            z-index: 50;
        }

        @yield('style')
    </style>
</head>

<body class="bg-white text-slate-800">
    {{-- Top bar --}}
    <header class="bg-[#1f66b3] text-white">
        <div class="w-full px-2">
            <div class="flex items-center justify-between py-3">
                <div class="flex items-center gap-3">
                    <a href="/" class="h-10 w-10 rounded-full bg-white/95 grid place-items-center">
                        <span><img src="../image/logo.png" alt=""></span>
                    </a>
                    <p class="hidden sm:block text-sm md:text-base font-semibold tracking-wide">
                        CHẤT LƯỢNG TOÀN DIỆN · HỢP TÁC SÂU RỘNG · PHÁT TRIỂN BỀN VỮNG
                    </p>
                </div>
                <nav>
                    <a href="https://saodo.edu.vn/vi/about/Gioi-thieu-ve-truong-Dai-hoc-Sao-Do.html"
                        class="text-white/90 hover:text-white text-sm font-medium">GIỚI THIỆU</a>
                </nav>
            </div>
        </div>
    </header>

    {{-- Page Content --}}
    @yield('content')

    {{-- Footer --}}
    <footer class="border-t border-slate-200 py-8 bg-white">
        <div
            class="mx-auto container-narrow px-4 text-slate-700 text-sm flex flex-col md:flex-row md:justify-between md:items-start gap-8">
            <div class="md:w-2/3 mb-4 md:mb-0">
                <div class="font-semibold text-base mb-2 text-[#1f66b3] flex items-center gap-3">
                    Trường Đại học Sao Đỏ
                    <a href="/admin"
                        class="ml-3 px-3 py-1 rounded bg-[#1f66b3] text-white text-xs font-medium hover:bg-[#174a7e] transition"
                        title="Truy cập trang quản trị">
                        <i class="bi bi-shield-lock-fill mr-1"></i> Quản trị
                    </a>
                </div>
                <div class="mb-1"><i class="bi bi-geo-alt-fill mr-1"></i> Địa chỉ: Số 24, Đường Thái Học 2, Phường Sao
                    Đỏ, Thành phố Chí Linh, Tỉnh Hải Dương</div>
                <div class="mb-1"><i class="bi bi-telephone-fill mr-1"></i> Điện thoại: (0220) 3882 402</div>
                <div class="mb-1"><i class="bi bi-printer-fill mr-1"></i> Fax: (0220) 3882 921</div>
                <div class="mb-1"><i class="bi bi-envelope-fill mr-1"></i> Email: <a href="mailto:info@saodo.edu.vn"
                        class="text-[#1f66b3] hover:underline">info@saodo.edu.vn</a></div>
                <div class="mb-2"><i class="bi bi-globe2 mr-1"></i> Website: <a href="https://saodo.edu.vn"
                        class="text-[#1f66b3] hover:underline" target="_blank">https://saodo.edu.vn</a></div>
                <div class="text-slate-500 mt-4">
                    © {{ date('Y') }} Trường Đại học Sao Đỏ · Hệ thống khảo sát trực tuyến
                </div>
            </div>
            <div class="md:w-1/3 flex justify-center md:justify-end">
                <iframe
                    src="https://www.google.com/maps?q=Trường+Đại+học+Sao+Đỏ,+Số+24,+Đường+Thái+Học+2,+Phường+Sao+Đỏ,+Chí+Linh,+Hải+Dương&output=embed"
                    width="300" height="180" style="border:0; border-radius: 8px;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade" title="Bản đồ Trường Đại học Sao Đỏ"></iframe>
            </div>
        </div>
    </footer>
</body>

</html>