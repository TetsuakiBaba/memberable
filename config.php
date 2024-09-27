<?php
// データベースのパス
define('DB_PATH', 'sample_memberable.db');

// サイトのURL（パスワード再発行のリンクで使用）
define('SITE_URL', 'http://yourdomain.com/'); // 実際のドメインに置き換えてください

// メール送信元アドレス
define('MAIL_FROM', 'no-reply@yourdomain.com'); // 実際のメールアドレスに置き換えてください

// admin mail address
define('ADMIN_EMAIL', 'admin@example.com'); // 実際のメールアドレスに置き換えてください

// admin initial password
define('ADMIN_PASSWORD', 'Admin@1234'); // 管理者の初期パスワード

// header of member_id
define('MEMBER_ID_HEADER', 'mymember'); // メンバーIDの先頭に付与する文字列

// title of login.php
define('LOGIN_TITLE', 'my Login'); // ログインページのタイトル

// header logo image path
define('HEADER_LOGO', ' ?icon=F4D7'); // ヘッダーに表示するロゴ画像のパス

// footer of page
define('FOOTER_TEXT', '&copy; 2024 Your Company'); // フッターに表示するテキスト

// agree text
define('AGREE_TEXT', 'I understand that by creating an account, I am joining the member. The personal information entered herein shall be used solely for the academic development and will not be provided to any third party without his/her consent.'); // 利用規約に同意する文言

// 組織名
define('ASSOCIATION_NAME', 'Association name'); // 組織名



// membership services
define('MEMBERSHIP_SERVICES', [
    [
        'title' => '<i class="bi bi-envelope"></i> Email Support',
        'description' => 'If you have any questions about ADADA, please contact us directly at this email address.',
        'link' => 'mailto:office@example.com?subject=Inquiry',
    ],
    [
        'title' => '<i class="bi bi-postcard"></i> Membership Certificate',
        'description' => 'You can download a certificate of membership in PNG format.',
        'link' => 'certificate.php',
    ],
]); // メンバーシップサービス