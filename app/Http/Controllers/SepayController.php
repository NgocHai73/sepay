<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class SepayController extends Controller
{
    public function index()
    {
        return view('payment');
    }

    public function processPayment(Request $request)
    {
        $amount = $request->input('amount');

        // Gọi API của Sepay để xử lý thanh toán
        $response = $this->callSepayApi($amount);

        // Kiểm tra response và đảm bảo không phải là null
        if (is_null($response) || !isset($response['status'])) {
            // Log lỗi nếu có
            Log::error('Invalid Sepay response', ['response' => $response]);
            return redirect()->route('payment.failure')->with('error', 'Thanh toán thất bại. Vui lòng thử lại.');
        }

        // Lưu thông tin thanh toán vào cơ sở dữ liệu
        $payment = new Payment();
        $payment->amount = $amount;
        $payment->status = $response['status'];
        $payment->save();

        // Kiểm tra kết quả trả về từ Sepay
        if ($response['status'] == 'success') {
            return redirect()->route('payment.success');
        } else {
            // Log chi tiết lỗi
            Log::error('Sepay payment failed', ['response' => $response]);
            return redirect()->route('payment.failure')->with('error', 'Thanh toán thất bại. Vui lòng thử lại.');
        }
    }

    public function paymentSuccess()
    {
        return view('payment_success');
    }

    public function paymentFailure()
    {
        return view('payment_failure');
    }

    private function callSepayApi($amount)
    {
        $apiUrl = 'https://api.sepay.vn/payment';
        $apiKey = env('SEPAY_API_KEY');

        $params = [
            'amount' => $amount,
            'api_key' => $apiKey,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            Log::error('Sepay API request failed: ' . curl_error($ch));
            return null;
        }

        $decodedResponse = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Error decoding Sepay API response: ' . json_last_error_msg());
            return null;
        }

        Log::info('Sepay API response', ['response' => $decodedResponse, 'http_code' => $httpCode]);

        return $decodedResponse;
    }
}
