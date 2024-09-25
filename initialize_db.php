<?php
require 'config.php';

try {
    $db = new PDO('sqlite:' . DB_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ユーザーテーブルの作成
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT UNIQUE,
        password TEXT,
        name TEXT,
        affiliation TEXT,
        position TEXT,
        nationality TEXT,
        member_id TEXT UNIQUE,
        is_admin INTEGER DEFAULT 0,
        reset_token TEXT,
        reset_token_expire INTEGER
    )");

    // 管理者アカウントの作成（初回のみ）
    $email = ADMIN_EMAIL; // 管理者のメールアドレス
    $password = ADMIN_PASSWORD;     // 管理者のパスワード
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $name = 'Administrator';
    $affiliation = 'Organization';
    $position = 'Administrator';
    $nationality = 'Country';
    $is_admin = 1;

    // 管理者が既に存在しない場合のみ追加
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE is_admin = 1");
    $stmt->execute();
    $admin_exists = $stmt->fetchColumn();

    if (!$admin_exists) {
        // 一時的にユーザーを追加してIDを取得
        $stmt = $db->prepare("INSERT INTO users (email, password, name, affiliation, position, nationality, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$email, $hashed_password, $name, $affiliation, $position, $nationality, $is_admin]);
        $user_id = $db->lastInsertId();

        // member_idの生成
        $current_year = date('Y');
        $member_id = 'adada' . $current_year . str_pad($user_id, 4, '0', STR_PAD_LEFT);

        // member_idの更新
        $stmt = $db->prepare("UPDATE users SET member_id = ? WHERE id = ?");
        $stmt->execute([$member_id, $user_id]);

        echo "管理者アカウントが作成されました。\n";
        echo "メールアドレス: $email\n";
        echo "パスワード: $password\n";
    } else {
        echo "管理者アカウントは既に存在します。\n";
    }

    echo "データベースの初期化が完了しました。";
} catch (Exception $e) {
    echo "エラー: " . $e->getMessage();
}
