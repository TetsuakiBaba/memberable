<?php
session_start();
require 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // フォームデータの取得
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $db = new PDO('sqlite:' . DB_PATH);
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // セッションにユーザー情報を保存
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['is_admin'] = $user['is_admin'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['member_id'] = $user['member_id'];
            $_SESSION['affiliation'] = $user['affiliation'];
            $_SESSION['nationality'] = $user['nationality'];
            header('Location: dashboard.php');
            exit();
        } else {
            $message = 'Incorrect email or password.';
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

    <title><?php echo LOGIN_TITLE; ?></title>
    <!-- BootstrapのCSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
</head>

<body>
    <div class="container">
        <h2 class="mt-5 mb-4"><?php echo LOGIN_TITLE; ?></h2>
        <?php if ($message): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form method="post">
            <!-- Email -->
            <div class="form-group">
                <label>Email address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <!-- Password -->
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <!-- Submit -->
            <div class="mb-3">
                <button type="submit" class="me-4 btn btn-primary">Login</button>
                <a class="form-control-label" href="forgot_password.php"> Forgot your password?</a>
            </div>
        </form>

        <button class="btn btn-danger" onclick="location.href='register.php'">Create a new account</button>

        <hr>
        <footer>
            <p class="text-center text-muted small"><?php echo FOOTER_TEXT; ?></p>
        </footer>
    </div>
</body>

</html>