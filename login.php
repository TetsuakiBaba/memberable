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
            $_SESSION['grade'] = $user['grade'];
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
    <!-- manifest.jsonの読み込み -->
    <link rel="manifest" href="./manifest.json">

    <link href="./scss/custom.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <div class="container-sm">
        <img class="mt-5 mb-2" src="<?php echo HEADER_LOGO; ?>" style="width:auto;height:36px;">
        <h2 class="display-5 mb-4">
            <?php echo LOGIN_TITLE; ?>
        </h2>
        <?php if ($message): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form method="post">
            <!-- Email -->
            <div class="form-group mb-3">
                <label>Email address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <!-- Password -->
            <div class="form-group mb-3">
                <label>Password</label>
                <div class="input-group">
                    <input type="password" name="password" class="form-control" id="password" required>
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility()">
                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>
                <script>
                    function togglePasswordVisibility() {
                        var passwordField = document.getElementById('password');
                        var togglePasswordIcon = document.getElementById('togglePasswordIcon');
                        if (passwordField.type === 'password') {
                            passwordField.type = 'text';
                            togglePasswordIcon.classList.remove('bi-eye');
                            togglePasswordIcon.classList.add('bi-eye-slash');
                        } else {
                            passwordField.type = 'password';
                            togglePasswordIcon.classList.remove('bi-eye-slash');
                            togglePasswordIcon.classList.add('bi-eye');
                        }
                    }
                </script>

            </div>
            <!-- Submit -->
            <div class="d-grid gap-2 col-6 mx-auto">
                <button type="submit" class="me-2 btn btn-primary"><i class="bi bi-door-open"></i> Login</button>
                <a class="text-center form-control-label" href="forgot_password.php"> Forgot your password?</a>
            </div>

        </form>

        <hr class="mt-5">
        <div class="d-grid gap-2 col-6 mx-auto">
            <button class="btn btn-outline-primary" onclick="location.href='register.php'">
                <i class="bi bi-person-plus-fill"></i> Create a new account
            </button>
        </div>

        <hr>
        <footer>
            <p class="text-center text-muted small"><?php echo FOOTER_TEXT; ?></p>
        </footer>
    </div>
</body>

</html>