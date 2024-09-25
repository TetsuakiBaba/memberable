<?php
require 'config.php';

$message = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    try {
        $db = new PDO('sqlite:' . DB_PATH);
        $stmt = $db->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_token_expire > ?");
        $stmt->execute([$token, time()]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $password = $_POST['password'];

                // パスワードのバリデーション
                if (
                    strlen($password) < 8 ||
                    !preg_match('/[A-Za-z]/', $password) ||
                    !preg_match('/[0-9]/', $password) ||
                    !preg_match('/[\W]/', $password)
                ) {
                    $message = 'Password must be at least 8 characters long and include letters, numbers, and symbols.';
                } else {
                    // パスワードの更新
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expire = NULL WHERE id = ?");
                    $stmt->execute([$hashed_password, $user['id']]);
                    $message = 'Password has been reset. Please <a href="login.php">login</a>.';
                }
            }
        } else {
            $message = 'Invalid or expired token.';
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
    }
} else {
    header('Location: login.php');
    exit();
}
?>

<!-- HTML部分 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <!-- BootstrapのCSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
</head>

<body>
    <div class="container">
        <h2 class="mt-5">Reset Password</h2>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if (isset($user) && !$message): ?>
            <form method="post">
                <!-- Password -->
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="password" class="form-control" required>
                    <small class="form-text text-muted">At least 8 characters, including letters, numbers, and symbols.</small>
                </div>
                <!-- Submit -->
                <button type="submit" class="btn btn-primary">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>