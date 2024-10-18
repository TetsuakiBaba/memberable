# memberable
ultra simple user management system, especially for small websites, association, conference, etc. This system is originaly designed for a ADADA International Membership management system. 

![screenshot](teaser.png)

## Installation
1. Edit config.php file according to your env.
2. Edit mailer/PHPMailer.php file according to your smtp server.
3. upload *.php, mailer/*, images/* and scss/* to your server.
4. open yourdomain.com/initialize_db.php in your browser.
5. delete initialize_db.php file.
6. Login with admin username which is written in config.php file.
7. Done!

## Easy server upload [ memo]
Better to use lftp for uploading files to your server. Here is  a sample upload script.
```
open -u [username],[password] [server address]
set ftp:passive-mode true
cd [to your remote directory for uploading]
mirror --exclude "mailer/sample\.php" --exclude ".gitignore" --exclude "upload\.txt" --exclude ".DS_Store" --exclude ".*\.db" --exclude "node_modules/.*" --exclude ".git/.*"  --exclude "initialize_db\.php" --exclude "config\.php" --exclude "README\.md"  -R  --dry-run ./ ./
ls
bye
```
Once you confirm the files to be uploaded, remove --dry-run option and run the script.

## How to create an acount and login 
1. Access yourdomain.com/register.php to register
2. Access yourdomain.com/login.php to login

## dependencies
  * php 7.0 or higher
  * phpLiteAdmin is included for complex database operations. You can access it on admin's dashboard.php
