<?php
// config.phpを読み込む
require('./config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 必要なファイルを読み込む
require('./PHPMailer.php');
require('./Exception.php');
require('./SMTP.php');

// リクエストの内容を取得
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$mail = new PHPMailer(true);

try {
    // データの検証
    if (!isset($data['senderName'], $data['recipientAddresses'], $data['subject'], $data['message'])) {
        throw new Exception('必要なデータが不足しています');
    }

    $senderName = strip_tags(trim($data['senderName']));
    $subject = strip_tags(trim($data['subject']));
    $message = strip_tags(trim($data['message']));
    $recipientAddressesInput = $data['recipientAddresses'];

    // 受信者アドレスを配列に変換
    if (is_array($recipientAddressesInput)) {
        $recipientAddresses = $recipientAddressesInput;
    } else {
        $recipientAddresses = explode(',', $recipientAddressesInput);
    }

    // 各メールアドレスをトリムし、正しいメールアドレスか検証
    $recipientAddresses = array_map('trim', $recipientAddresses);
    $recipientAddresses = array_filter($recipientAddresses, function ($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    });

    // 有効な受信者がいるか確認
    if (empty($recipientAddresses)) {
        throw new Exception('有効な受信者のメールアドレスがありません');
    }

    // SMTP設定
    $mail->isSMTP();
    $mail->Host = $SMTP_SERVER;
    $mail->SMTPAuth = true;
    $mail->Username = $SMTP_USERNAME; // SMTPサーバーのユーザー名
    $mail->Password = $SMTP_PASSWORD; // SMTPサーバーのパスワード
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;    // SSL
    $mail->Port = $SMTP_PORT;

    // 送信者情報
    $mail->setFrom($SMTP_SENDER_ADDRESS, $senderName);

    // 日本語の文字エンコーディングを設定
    $mail->CharSet = 'UTF-8';

    // メール内容
    $mail->isHTML(false); // プレーンテキスト形式で送信
    $mail->Subject = $subject;
    $mail->Body    = $message;

    // 受信者を追加
    foreach ($recipientAddresses as $address) {
        $mail->addAddress($address);
    }

    // メール送信
    if ($mail->send()) {
        echo 'success';
    } else {
        echo 'fail to send: ' . $mail->ErrorInfo;
    }
} catch (Exception $e) {
    echo 'fail to send: ' . $e->getMessage();
}
