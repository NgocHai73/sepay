<!DOCTYPE html>
<html>
<head>
    <title>Sepay Payment</title>
</head>
<body>
    <h1>Thanh Toán với Sepay</h1>
    <form action="{{ route('payment.process') }}" method="POST">
        @csrf
        <label for="amount">Số tiền:</label>
        <input type="number" name="amount" id="amount" required>
        <button type="submit">Thanh Toán</button>
    </form>
</body>
</html>
