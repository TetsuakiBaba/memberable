<?php
session_start();
require 'config.php';

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$message = '';

try {
    $db = new PDO('sqlite:' . DB_PATH);
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $message = 'Error: ' . $e->getMessage();
}

// プロフィールの更新
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $email = $_POST['email'];
    $name = $_POST['name'];
    $affiliation = $_POST['affiliation'];
    $position = $_POST['position'];
    $nationality = $_POST['nationality'];

    try {
        // Emailの重複チェック
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        $existing_user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_user) {
            $message = 'The email address is already in use by another account.';
        } else {
            $stmt = $db->prepare("UPDATE users SET email = ?, name = ?, affiliation = ?, position = ?, nationality = ? WHERE id = ?");
            $stmt->execute([$email, $name, $affiliation, $position, $nationality, $_SESSION['user_id']]);

            // セッション内のメールアドレスを更新
            $_SESSION['email'] = $email;

            $message = 'Profile updated.';
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
    }
}

// パスワードの変更
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    // 現在のパスワードの確認
    if (password_verify($current_password, $user['password'])) {
        // 新しいパスワードのバリデーション
        if (
            strlen($new_password) < 8 ||
            !preg_match('/[A-Za-z]/', $new_password) ||
            !preg_match('/[0-9]/', $new_password) ||
            !preg_match('/[\W]/', $new_password)
        ) {
            $message = 'New password must be at least 8 characters long and include letters, numbers, and symbols.';
        } else {
            // パスワードの更新
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $_SESSION['user_id']]);
            $message = 'Password changed successfully.';
        }
    } else {
        $message = 'Current password is incorrect.';
    }
}

// アカウントの削除
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_account'])) {
    try {
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        session_destroy();
        header('Location: register.php');
        exit();
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
    }
}

// 管理者への昇格（管理者のみ表示）
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['promote_user']) && $_SESSION['is_admin']) {
    $user_id = $_POST['user_id'];
    try {
        $stmt = $db->prepare("UPDATE users SET is_admin = 1 WHERE id = ?");
        $stmt->execute([$user_id]);
        $message = 'User promoted to admin.';
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
    <title>Dashboard</title>
    <!-- BootstrapのCSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <nav class="navbar bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="<?php echo HEADER_LOGO; ?>" alt="Logo" height="36" class="d-inline-block align-text-top">
            </a>
            <!-- Logout -->
            <a href="logout.php">
                <button class="btn btn-danger">
                    Logout <i class="bi bi-door-closed"></i>
                </button>
            </a>
        </div>
    </nav>
    <div class="container mt-4">
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- 管理者用機能 -->
        <?php if ($_SESSION['is_admin']): ?>
            <a href="admin.php"><button class="btn btn-outline-primary mb-3"><i class="bi bi-shield-lock"></i> Go to Admin Page</button></a>
        <?php endif; ?>

        <!-- ユーザー情報の表示と更新 -->
        <div class="row" data-masonry='{"percentPosition": false }'>
            <div class="col-12">
                <div class="text-center text-muted mb-3">
                    <i class="bi bi-person-circle" style="font-size:10rem;line-height:0px;"></i><br>
                    <span class="fs-3"><?php echo htmlspecialchars($user['name']); ?></span>
                </div>
            </div>

        </div>

        <!-- もし MEMBERSHIP_SERVICESが定義されていれば -->
        <?php if (defined('MEMBERSHIP_SERVICES')) {


        ?>
            <div class="row" data-masonry='{"percentPosition": true }'>
                <div class="col-sm-12 col-md-12 col-lg-6 mb-3">
                    <div class="card">
                        <h5 class="card-header">
                            Membership Service
                        </h5>
                        <div class="card-body">
                            <ul class="list-group">
                            <?php
                            // serviceの数だけforeachでループ処理
                            foreach (MEMBERSHIP_SERVICES as $service) {
                                echo '<li class="list-group-item d-flex justify-content-between align-items-start"><div class="ms-2 me-auto"><div class="fw-bold">' . $service['title'] . '</div>' . $service['description'] . '</div><span class="fs-5 badge rounded-pill"><a href="' . $service['link'] . '" target="_blank"><i class="bi bi-box-arrow-up-right"></i></a></span></li>';
                            }
                        } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-6">
                    <div class="card mb-3">
                        <h5 class="card-header">
                            Profile
                        </h5>
                        <div class="card-body">
                            <form method="post">
                                <!-- Member ID -->
                                <div class="form-group mb-3">
                                    <label>Member ID</label>
                                    <input type="text" name="member_id" class="form-control" value="<?php echo htmlspecialchars($user['member_id']); ?>" disabled>
                                </div>
                                <!-- Member grade -->
                                <div class="form-group mb-3">
                                    <label>Grade</label>
                                    <input type="text" name="grade" class="form-control" value="<?php echo htmlspecialchars($user['grade']); ?>" disabled>
                                </div>
                                <!-- Email Address -->
                                <div class="form-group mb-3">
                                    <label>Email Address</label>
                                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <!-- Name -->
                                <div class="form-group mb-3">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                </div>
                                <!-- Affiliation -->
                                <div class="form-group mb-3">
                                    <label>Affiliation</label>
                                    <input type="text" name="affiliation" class="form-control" value="<?php echo htmlspecialchars($user['affiliation']); ?>" required>
                                </div>
                                <!-- Position -->
                                <div class="form-group mb-3">
                                    <label>Position</label>
                                    <input type="text" name="position" class="form-control" value="<?php echo htmlspecialchars($user['position']); ?>" required>
                                </div>
                                <!-- Nationality -->
                                <div class="form-group mb-3">
                                    <label>Nationality</label>
                                    <input type="text" name="nationality" class="form-control" value="<?php echo htmlspecialchars($user['nationality']); ?>" required>
                                </div>
                                <!-- Update Button -->
                                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-6">
                    <!-- パスワード変更 -->
                    <div class="card mb-3">
                        <h5 class="card-header">
                            Change Password
                        </h5>
                        <div class="card-body">
                            <form method="post">
                                <!-- Current Password -->
                                <div class="form-group mb-3">
                                    <label>Current Password</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>
                                <!-- New Password -->
                                <div class="form-group mb-3">
                                    <label>New Password</label>
                                    <input type="password" name="new_password" class="form-control" required>
                                    <small class="form-text text-muted">At least 8 characters, including letters, numbers, and symbols.</small>
                                </div>
                                <!-- Change Password Button -->
                                <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                            </form>
                        </div>
                    </div>

                    <!-- アカウント削除 -->
                    <div class="alert alert-danger" role="alert">
                        <h5>Danger Zone</h5>
                        <p>If you wish to unsubscribe from this membership, please delete your account. This action cannot be undone, and you must register a new account in order to rejoin the membership.</p>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" onclick="toggleDeleteButton(this);">
                            <label class="form-check-label" for="flexCheckDefault">
                                I understand the above and will proceed with membership withdrawal.
                            </label>
                        </div>

                        <form method="post" onsubmit="return confirm(' Are you sure you want to delete your account?');">
                            <button type="submit" name="delete_account" class="btn btn-danger form-control" disabled>Delete Account</button>
                        </form>
                    </div>
                </div>
            </div>
            <hr>
            <footer>
                <p class="text-center text-muted small"><?php echo FOOTER_TEXT; ?></p>
            </footer>
    </div>

    <script>
        function toggleDeleteButton(e) {
            const deleteButton = document.querySelector('button[name="delete_account"]');
            deleteButton.disabled = !e.checked;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/gh/TetsuakiBaba/placeholderable@main/placeholderable.js" crossorigin="anonymous" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js" integrity="sha384-GNFwBvfVxBkLMJpYMOABq3c+d3KnQxudP/mGPkzpZSTYykLBNsZEnG2D9G/X/+7D" crossorigin="anonymous" async></script>

</body>

</html>