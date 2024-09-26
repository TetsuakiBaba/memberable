<?php
session_start();
require 'config.php';

// 管理者チェック
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit();
}

$message = '';

try {
    $db = new PDO('sqlite:' . DB_PATH);

    // ユーザーリストの取得
    $stmt = $db->prepare("SELECT * FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ユーザー情報の更新
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
        $user_id = $_POST['user_id'];
        $member_id = $_POST['member_id'];
        $name = $_POST['name'];
        $affiliation = $_POST['affiliation'];
        $position = $_POST['position'];
        $nationality = $_POST['nationality'];
        $is_admin = isset($_POST['is_admin']) ? 1 : 0;
        $new_password = $_POST['new_password'];

        // パスワード更新フラグとパスワードSQL
        $password_sql = '';
        $update_password = false;

        // パスワードのバリデーションとハッシュ化
        if (!empty($new_password)) {
            if (
                strlen($new_password) < 8 ||
                !preg_match('/[A-Za-z]/', $new_password) ||
                !preg_match('/[0-9]/', $new_password) ||
                !preg_match('/[\W]/', $new_password)
            ) {
                $message = 'New password must be at least 8 characters long and include letters, numbers, and symbols.';
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $password_sql = ", password = :password";
                $update_password = true;
            }
        }

        // member_idの重複チェック
        $stmt = $db->prepare("SELECT id FROM users WHERE member_id = ? AND id != ?");
        $stmt->execute([$member_id, $user_id]);
        $existing_member = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_member) {
            $message = 'The Member ID is already in use by another account.';
        }

        // エラーメッセージがない場合に更新を実行
        if (!$message) {
            // ユーザー情報の更新クエリ
            $sql = "UPDATE users SET member_id = :member_id, name = :name, affiliation = :affiliation, position = :position, nationality = :nationality, is_admin = :is_admin $password_sql WHERE id = :user_id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':member_id', $member_id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':affiliation', $affiliation);
            $stmt->bindParam(':position', $position);
            $stmt->bindParam(':nationality', $nationality);
            $stmt->bindParam(':is_admin', $is_admin, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            if ($update_password) {
                $stmt->bindParam(':password', $hashed_password);
            }
            $stmt->execute();

            $message = 'User information updated.';
        }
    }

    // ユーザーの削除
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $message = 'User deleted.';
    }

    // データのエクスポート
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['export_data'])) {
        $data = json_encode($users, JSON_PRETTY_PRINT);
        header('Content-Disposition: attachment; filename="members.json"');
        header('Content-Type: application/json');
        echo $data;
        exit();
    }

    // データのインポート
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['import_data'])) {
        if (isset($_FILES['json_file']) && $_FILES['json_file']['error'] == UPLOAD_ERR_OK) {
            $json_data = file_get_contents($_FILES['json_file']['tmp_name']);
            $import_users = json_decode($json_data, true);

            foreach ($import_users as $import_user) {
                // データの挿入または更新
                $stmt = $db->prepare("INSERT OR REPLACE INTO users (id, email, password, name, affiliation, position, nationality, member_id, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $import_user['id'],
                    $import_user['email'],
                    $import_user['password'],
                    $import_user['name'],
                    $import_user['affiliation'],
                    $import_user['position'],
                    $import_user['nationality'],
                    $import_user['member_id'],
                    $import_user['is_admin']
                ]);
            }
            $message = 'Data imported.';
        } else {
            $message = 'Failed to upload file.';
        }
    }

    // 更新後のユーザーリストの再取得
    $stmt = $db->prepare("SELECT * FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $message = 'Error: ' . $e->getMessage();
}
?>

<!-- HTML部分 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Page</title>
    <!-- BootstrapのCSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .password-input {
            width: 12rem;
        }
    </style>
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
    <div class="container-fluid">
        <!-- 戻るボタン -->
        <a class="btn btn-outline-primary mt-3" href="dashboard.php">Back to Dashboard</a>
        <h5 class="display-5 mt-4 mb-3">Admin Page</h5>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- データのエクスポート -->
            <div class="col-sm-6 mb-3">
                <div class="card">
                    <h5 class="card-header">
                        Export Data
                    </h5>
                    <div class="card-body">
                        <form method="post">
                            <button type="submit" name="export_data" class="btn btn-success">Export as JSON</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- データのインポート -->
            <div class="col-sm-6 mb-3">
                <div class="card">
                    <h5 class="card-header">Import Data</h5>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="input-group">
                                <input type="file" name="json_file" class="form-control" accept=".json" required>

                                <button type="submit" name="import_data" class="btn btn-primary">
                                    Import Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>




            <div class="col-12 mb-3">
                <div class="card">
                    <h5 class="card-header"> User Table </h5>
                    <div class="card-body">
                        <!-- ユーザーリストの表示 -->
                        <table class="table table-sm table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Member ID</th>
                                    <th>Email</th>
                                    <th>Name</th>
                                    <th>Affiliation</th>
                                    <th>Position</th>
                                    <th>Nationality</th>
                                    <th>New Password</th>
                                    <th>Admin</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider">
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <form method="post">
                                            <!-- Member ID -->
                                            <td><input class="form-control" type="text" name="member_id" value="<?php echo htmlspecialchars($user['member_id']); ?>"></td>
                                            <!-- Email -->
                                            <td><input class="form-control" type="text" value="<?php echo htmlspecialchars($user['email']); ?>" disabled></td>
                                            <!-- Name -->
                                            <td><input class="form-control" type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>"></td>
                                            <!-- Affiliation -->
                                            <td><input class="form-control" type="text" name="affiliation" value="<?php echo htmlspecialchars($user['affiliation']); ?>"></td>
                                            <!-- Position -->
                                            <td><input class="form-control" type="text" name="position" value="<?php echo htmlspecialchars($user['position']); ?>"></td>
                                            <!-- Nationality -->
                                            <td><input class="form-control" type="text" name="nationality" value="<?php echo htmlspecialchars($user['nationality']); ?>"></td>

                                            <!-- New Password -->
                                            <td><input class="form-control password-input" type="password" name="new_password" placeholder="New Password"></td>

                                            <!-- Admin -->
                                            <td class="text-center"><input class="form-check-input" type="checkbox" name="is_admin" <?php if ($user['is_admin']) echo 'checked'; ?>></td>
                                            <!-- Actions -->
                                            <td>
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" name="update_user" class="btn btn-primary btn-sm"><i class="bi bi-floppy"></i></button>
                                                <button type="submit" name="delete_user" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </form>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <hr>
        <footer>
            <p class="text-center text-muted small"><?php echo FOOTER_TEXT; ?></p>
        </footer>
    </div>

    <!-- BootstrapのJS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/gh/TetsuakiBaba/placeholderable@main/placeholderable.js" crossorigin="anonymous" type="text/javascript"></script>
</body>

</html>