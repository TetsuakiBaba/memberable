/**
 * 
 * @param {string} senderName 送信者名
 * @param {string} recipientAddress 送信先アドレス（誰に送るか）（複数人に送る場合はカンマ区切り）
 * @param {string} subject 件名
 * @param {string} message 本文（メールの内容。プレーンテキスト）
 * @param {string} path_mailer_php maierl.phpへのパス
 */

async function sendMail(senderName, recipientAddress, subject, message, path_mailer_php) {
    const data = {
        senderName: senderName,
        recipientAddresses: recipientAddress,
        subject: subject,
        message: message
    };
    try {
        const response = await fetch(path_mailer_php, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        const result = await response.text();
        return result;
    } catch (error) {
        return error;
    }
}
