<?php
require 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    try {
        $db = new PDO('sqlite:' . DB_PATH);
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // リセットトークンの生成
            $token = bin2hex(random_bytes(16));
            $expire = time() + 3600; // 1時間後に期限切れ

            // データベースにトークンを保存
            $stmt = $db->prepare("UPDATE users SET reset_token = ?, reset_token_expire = ? WHERE email = ?");
            $stmt->execute([$token, $expire, $email]);

            // リセットリンクの作成
            $reset_link = SITE_URL . 'reset_password.php?token=' . $token;

            // メールの送信
            $subject = 'Password Reset Request';
            $message_body = "Click the following link to reset your password:\n\n" . $reset_link;
            $headers = 'From: ' . MAIL_FROM;

            if (mail($email, $subject, $message_body, $headers)) {
                $message = 'Password reset email sent.';
            } else {
                $message = 'Failed to send email.';
            }
        } else {
            $message = 'Email address not found.';
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
    }
}
?>

<!-- HTML部分 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password</title>
    <!-- BootstrapのCSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
</head>

<body>
    <div class="container">
        <h2 class="mt-5">Forgot Password</h2>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form method="post">
            <!-- Email -->
            <div class="form-group">
                <label>Email address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <!-- Submit -->
            <button type="submit" class="btn btn-primary">Send Reset Link</button>
        </form>

        <hr>
        <footer>
            <p class="text-center text-muted small"><?php echo FOOTER_TEXT; ?></p>
        </footer>
    </div>
</body>

</html>