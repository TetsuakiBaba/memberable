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
            $message_body = "Click the following link to reset your password:　" . $reset_link;
            $headers = 'From: ' . MAIL_FROM;

            // if (mail($email, $subject, $message_body, $headers)) {
            //     $message = 'Password reset link has been sent to ' . $email . '.';
            // } else {
            //     $message = 'Failed to send email to ' . $email . '.';
            // }
            echo <<<JS
            <script>
                document.addEventListener("DOMContentLoaded", async function() {
                    let ret = await sendMail('ADADA auto mailer', "$email", "$subject", "$message_body", './mailer/mailer.php');
                    if( ret === 'success' ) {
                        document.getElementById('message').innerText = 'Password reset link has been sent to $email.';
                    } else {
                        document.getElementById('message').innerText = 'Failed to send email to $email.';
                    }
                    document.getElementById('message').classList.add(ret === 'success' ? 'alert-success' : 'alert-danger');
                    document.getElementById('message').hidden = false;
                });
            </script>
            JS;
        } else {
            echo <<<JS
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    document.getElementById('message').innerText = 'Email address not found.';
                    document.getElementById('message').classList.add('alert-danger');
                    document.getElementById('message').hidden = false;
                });
            </script>
            JS;
        }
    } catch (Exception $e) {
        echo <<<JS
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById('message').innerText = 'Error: ' + "$e->getMessage()" + '.';
                document.getElementById('message').hidden = false;
            });
        </script>
        JS;
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
    <link rel="stylesheet" href="./scss/custom.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <div class="container-sm">
        <img class="mt-5 mb-2" src="<?php echo HEADER_LOGO; ?>" style="width:auto;height:36px;">
        <h2 class="display-5 mb-4">
            Forgot Password?
        </h2>
        <div id="message" class="alert alert-info" hidden></div>
        <form method="post">
            <!-- Email -->
            <div class="form-group mb-3">
                <label>Email address</label>
                <input id="recipientAddress" type="email" name="email" class="form-control" required>
            </div>
            <!-- Submit -->
            <div class="d-grid gap-2 col-6 mx-auto">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send-fill"></i> Send Reset Link
                </button>
            </div>
        </form>

        <hr>
        <footer>
            <p class="text-center text-muted small"><?php echo FOOTER_TEXT; ?></p>
        </footer>
    </div>

    <input type="hidden" id="senderName" value="ADADA auto mailer">
    <input type="hidden" id="recipientAddresses" value="<?php echo $email; ?>">
    <input type="hidden" id="subject" value="Password Reset Request">
    <input type="hidden" id="message" value="hello">

    <script src="./mailer/SMTPSender.js">
    </script>
</body>

</html>