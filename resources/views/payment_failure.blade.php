<!DOCTYPE html>
<html>
<head>
    <title>Payment Failure</title>
</head>
<body>
    <h1>Thanh Toán Thất Bại</h1>
    @if(session('error'))
        <p>{{ session('error') }}</p>
    @else
        <p>Đã có lỗi xảy ra, vui lòng thử lại.</p>
    @endif
</body>
</html>
