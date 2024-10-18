<?php
// データベースのパス
// define('DB_PATH', '../memberable.settings/adada_memberable.db');
define('DB_PATH', 'sample_memberable.db');

// サイトのURL（パスワード再発行のリンクで使用）
define('SITE_URL', 'https://myadada.adada.info/'); // 実際のドメインに置き換えてください

// メール送信元アドレス
define('MAIL_FROM', 'no-reply@adada.info'); // 実際のメールアドレスに置き換えてください

// admin mail address
define('ADMIN_EMAIL', 'office@adada.info'); // 実際のメールアドレスに置き換えてください

// admin initial password
define('ADMIN_PASSWORD', 'Admin@1234'); // 管理者の初期パスワード

// header of member_id
define('MEMBER_ID_HEADER', 'adada'); // メンバーIDの先頭に付与する文字列

// title of login.php
define('LOGIN_TITLE', 'Login to myADADA'); // ログインページのタイトル

// header logo image path
define('HEADER_LOGO', 'https://adada.info/cache/images/cached_ef3b881b9775c9e599b094ec32380d7a.png'); // ヘッダーに表示するロゴ画像のパス

// footer of page
define('FOOTER_TEXT', '&copy; 2024 Asia Digital Art and Design Association'); // フッターに表示するテキスト

// agree text
define('AGREE_TEXT', '   I understand that by creating an account, I am joining ADADA Member. The personal information entered herein shall be used solely for the academic development of ADADA and will not be provided to any third party without his/her consent.'); // 利用規約に同意する文言

// 組織名
define('ASSOCIATION_NAME', 'ADADA International'); // 組織名

// membership services
define('MEMBERSHIP_SERVICES', [
    [
        'title' => '<i class="bi bi-envelope"></i> Email Support',
        'description' => 'If you have any questions about ADADA, please contact us directly at this email address.',
        'link' => 'mailto:office@adada.info?subject=Inquiry',
    ],
    [
        'title' => '<i class="bi bi-archive"></i> Digital Archive of Procceedings',
        'description' => 'You can browse all the proceedings of the international conference, which has been running since 2003.',
        'link' => 'https://myadada.adada.info/ADADA_Archives/index.php',
    ],
    [
        'title' => '<i class="bi bi-postcard"></i> Membership Certificate',
        'description' => 'You can download a certificate of membership in PNG format.',
        'link' => 'certificate.php',
    ],
]); // メンバーシップサービス