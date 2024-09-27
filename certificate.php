<?php
session_start();
require 'config.php';

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Membership</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <!-- BootstrapのCSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Libre Baskerville', serif;
            background-color: #f5f5f5;

            margin: 0;
        }

        .container-certificate {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .certificate {
            width: 1000px;
            height: 700px;
            padding: 50px;
            border: 15px solid #394C87;
            background-image: url('');
            /* 背景画像 */
            background-color: #fff;
            background-size: cover;
            background-position: center;
            text-align: center;
            position: relative;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }

        .certificate h1 {
            font-size: 42px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
            letter-spacing: 2px;
        }

        .certificate p {
            font-size: 18px;
            margin: 10px 0;
            color: #333;
        }

        .certificate .name {
            font-family: 'Great Vibes', cursive;
            font-size: 48px;
            font-weight: normal;
            margin: 30px 0;
            /* text-decoration: underline; */
            color: #333;
        }

        .certificate .subtitle {
            font-size: 20px;
            margin-bottom: 20px;
            color: #666;
        }

        .certificate .date {
            position: absolute;
            bottom: 10px;
            left: 50px;
            font-size: 0.7rem;
            color: #333;
        }

        .certificate .signature {
            position: absolute;
            bottom: 10px;
            right: 50px;
            font-size: 16px;
            text-align: center;
            color: #333;
        }

        .certificate .signature img {
            width: 150px;
            height: auto;
        }

        .certificate .signature p {
            margin-top: 10px;
        }

        .certificate .seal {
            position: absolute;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            width: 200px;
            height: 200px;
        }

        /* ボタンのスタイル */
        .download-button {
            position: absolute;
            top: -50px;
            /* 証明書の上部にボタンを配置 */
            left: 50%;
            transform: translateX(-50%);
            padding: 10px 20px;
            background-color: #394C87;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="text-center mt-4">
        <button class="btn btn-outline-dark" id="download-btn">Download as PNG</button>
    </div>
    <div class="container-certificate">
        <div class="certificate">
            <!-- シール画像 -->
            <div class="seal">
                <img src=" ?text=LOGO&bgcolors=#394C87" alt="Seal">
            </div>
            <h1 style="margin-top:20%; margin-bottom:4rem;">CERTIFICATE OF MEMBERSHIP</h1>
            <p><?php echo ASSOCIATION_NAME; ?></p>
            <p>We hereby present this to certify that</p>
            <p class="name">Tetsuaki Baba</p>
            <p>is a <?php echo $_SESSION['grade'] ?> member of the association</p>
            <div class="member_id">
                <p>Member ID: <?php echo $_SESSION['member_id']; ?></p>
            </div>

            <div class="date">
                <p>Date Issued: <?php echo date('F j, Y');  ?> </p>

            </div>

            <div class="signature">
                <img src=" ?text=signature image dummy&bgcolors=#394C87" alt="Signature">
                <p>Authorized Signature</p>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/gh/TetsuakiBaba/placeholderable@main/placeholderable.js" crossorigin="anonymous" type="text/javascript"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const certificate = document.querySelector('.certificate');
            document.querySelector('#download-btn').addEventListener('click', function() {
                html2canvas(certificate, {
                    scale: 2
                }).then(canvas => {
                    const dataUrl = canvas.toDataURL('image/png');
                    const link = document.createElement('a');
                    link.download = 'certificate-of-membership.png';
                    link.href = dataUrl;
                    link.click();
                });
            });
        });
    </script>

</body>

</html>