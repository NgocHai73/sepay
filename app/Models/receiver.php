<?php
    $servername = "localhost";
    $username = "webhooks_receiver";
    $password = "MotThanhVienCuaSepay";
    $dbname = "webhooks_receiver";


    // Ket noi den MySQL
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Kiem tra ket noi
    if ($conn->connect_error) {
        echo json_encode(['success'=>FALSE, 'message' => 'MySQL connection failed: '. $conn->connect_error]);
        die();
    }

    // Lay du lieu tu webhooks, xem cac truong du lieu tai https://docs.sepay.vn/tich-hop-webhooks.html#du-lieu
    $data = json_decode(file_get_contents('php://input'));

    if(!is_object($data)) {
        echo json_encode(['success'=>FALSE, 'message' => 'No data']);
        die();
    }

    // Khoi tao cac bien
    $gateway = $data->gateway;
    $transaction_date = $data->transactionDate;
    $account_number = $data->accountNumber;
    $sub_account = $data->subAccount;

    $transfer_type = $data->transferType;
    $transfer_amount = $data->transferAmount;
    $accumulated = $data->accumulated;

    $code = $data->code;
    $transaction_content = $data->content;
    $reference_number = $data->referenceCode;
    $body = $data->description;

    $amount_in = 0;
    $amount_out = 0;

    // Kiem tra giao dich tien vao hay tien ra
    if($transfer_type == "in")
        $amount_in = $transfer_amount;
    else if($transfer_type == "out")
        $amount_out = $transfer_amount;

    // Tao query SQL
    $sql = "INSERT INTO tb_transactions (gateway, transaction_date, account_number, sub_account, amount_in, amount_out, accumulated, code, transaction_content, reference_number, body) VALUES ('{$gateway}', '{$transaction_date}', '{$account_number}', '{$sub_account}', '{$amount_in}', '{$amount_out}', '{$accumulated}', '{$code}', '{$transaction_content}', '{$reference_number}', '{$body}')";

    // Chay query de luu giao dich vao CSDL
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success'=>TRUE]);
    } else {
        echo json_encode(['success'=>FALSE, 'message' => 'Can not insert record to mysql: ' . $conn->error]);
    }

?>