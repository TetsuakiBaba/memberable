<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>テストメール送信</title>
</head>

<body>
    <h1>テストメール送信フォーム</h1>
    <p>このページはfetchを利用してmailer.phpにアクセスしてメールを送信するサンプルです。mailer.phpは同じディレクトリにあるconfig.phpで設定を読み込みます。githubにはconfig.phpをアップロードしないようにしてください。</p>
    <form id="mailForm">
        <label for="senderName">送信者名:</label><br>
        <input type="text" id="senderName" name="senderName" value="ADADA auto mailer" required><br><br>

        <label for="recipientAddresses">受信アドレス（複数のアドレスはカンマで区切ってください）:</label><br>
        <textarea id="recipientAddresses" name="recipientAddresses" rows="4" cols="50" required></textarea><br><br>

        <label for="subject">件名:</label><br>
        <input type="text" id="subject" name="subject" required><br><br>

        <label for="message">メール内容:</label><br>
        <textarea id="message" name="message" rows="10" cols="50" required></textarea><br><br>

        <button type="button" onclick="sendMail()">送信</button>
    </form>

    <div id="result"></div>

    <script>
        function sendMail() {
            const senderName = document.getElementById('senderName').value;
            const recipientAddresses = document.getElementById('recipientAddresses').value;
            const subject = document.getElementById('subject').value;
            const message = document.getElementById('message').value;

            const data = {
                senderName: senderName,
                recipientAddresses: recipientAddresses,
                subject: subject,
                message: message
            };

            fetch('mailer.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.text())
                .then(result => {
                    document.getElementById('result').innerText = result;
                })
                .catch(error => {
                    document.getElementById('result').innerText = 'エラーが発生しました: ' + error;
                });
        }
    </script>
</body>

</html>