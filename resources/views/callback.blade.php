<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Đang xử lý đăng nhập...</title>
</head>
<body>
    <p>Vui lòng chờ trong giây lát...</p>
    <script>
        // Lấy dữ liệu từ Controller
        const data = {
            status: "{{ $status }}",
            token: "{{ $token ?? '' }}",
            user: @json($user ?? null),
            message: "{{ $message ?? '' }}"
        };

        // Gửi data về cửa sổ cha (Trang Login Vuejs)
        // '*' cho phép gửi đến mọi domain thay bằng 'http://localhost:5173' để bảo mật hơn
        if (window.opener) {
            window.opener.postMessage(data, 'http://localhost:5173')
        }

        // Đóng popup này lại
        window.close();
    </script>
</body>
</html>
