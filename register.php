<?php
require 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // フォームデータの取得
    $email = $_POST['email'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $affiliation = $_POST['affiliation'];
    $position = $_POST['position'];
    $nationality = $_POST['nationality'];

    // パスワードのバリデーション
    if (
        strlen($password) < 8 ||
        !preg_match('/[A-Za-z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[\W]/', $password)
    ) {
        $message = 'Password must be at least 8 characters long and include letters, numbers, and symbols.';
    } else {
        // パスワードのハッシュ化
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $db = new PDO('sqlite:' . DB_PATH);

            // ユーザーの挿入
            $stmt = $db->prepare("INSERT INTO users (email, password, name, affiliation, position, nationality) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$email, $hashed_password, $name, $affiliation, $position, $nationality]);

            // ユーザーIDの取得
            $user_id = $db->lastInsertId();

            // member_idの生成
            $current_year = date('Y');
            $member_id = 'adada' . $current_year . str_pad($user_id, 4, '0', STR_PAD_LEFT);

            // member_idの更新
            $stmt = $db->prepare("UPDATE users SET member_id = ? WHERE id = ?");
            $stmt->execute([$member_id, $user_id]);

            $message = 'Registration completed. Please log in.';
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
        }
    }
}
?>

<!-- HTML部分 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <!-- BootstrapのCSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
</head>

<body>
    <div class="container">
        <h2 class="mt-5">Account Registration</h2>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
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
                <small class="form-text text-muted">At least 8 characters, including letters, numbers, and symbols.</small>
            </div>
            <!-- Name -->
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <!-- Affiliation -->
            <div class="form-group">
                <label>Affiliation</label>
                <input type="text" name="affiliation" class="form-control" required>
            </div>
            <!-- Position -->
            <div class="form-group">
                <label>Position</label>
                <input type="text" name="position" class="form-control" required>
            </div>
            <!-- Nationality -->
            <div class="form-group">
                <label>Nationality</label>
                <input type="text" name="nationality" class="form-control" required>
            </div>
            <!-- Submit -->
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        <p class="mt-3">Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
</body>

</html>