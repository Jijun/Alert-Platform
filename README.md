# Alert Platform <img src="https://tpicloud.github.io/Image/Favicon/Alert%20Platform.ico" width="32px"><br><br>


## Description
This platform can receive alerts posted by other software, then send the alert to specific person by matching the patterns.

Currently, this platform is connected with the DingTalk. You may modify the code to satisfy your needs.

### Core Design Concepts
1. User privacy;
2. Database security;
3. Emphasizing client-side processing;
4. Balancing the GPU and CPU;
5. Maximizing server performance.

### Programing Languages
Though the code is mostly written in PHP, some pieces are written in [C++](https://raw.githubusercontent.com/Pi-314159265/Small-Programs-in-CPP/58c19154a89af09d127808fff832cc914e06e848/Codes-and-Associate-Files/multi_threading.cpp), Python, HTML, JavaScript, and CSS.

## How to Use
### Hardware Recommendations
```
2-Core CPU
2-GiB Memory
50-GiB SSD
```

### Prerequisites
__Python 3.6+, PHP 7+ *(incl. PHP-CLI)*__

__php-fpm, php-curl, php-gd, php-intl, php-mbstring, php-soap, php-xml, php-xmlrpc, php-zip__

__pip::idna__

__zip, unzip__

__Nginx__

### Configuration
The Server folder includes server-side scripts and dependencies.

The Client folder includes client-side scripts, which is about how to POST data to the server.

The Supplements folder includes files such as PHP and Nginx sample configurations.

#### Server Configuration
1. Move all files from "Server" to the __parent directory__ of Web Root;
2. Make following files executable:
+ `chmod +x tools/email/sendEmail.py`,
+ `chmod +x tools/multi_threading/main`,
+ `chmod +x tools/randNum`;
3. Add your database information by `nano tools/SQL/SQL.php` (Lines 11 - 15);
4. Modify the SMTP settings by `nano tools/email/sendEmail.py` (Lines 25 - 28);
4. Create following tables:
```sql
CREATE TABLE Alerts (ID INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, Date TIMESTAMP, IP VARCHAR(129) NOT NULL, Service VARCHAR(2000) NOT NULL, Details VARCHAR(4000) NOT NULL, Others VARCHAR(8000));
CREATE TABLE Regex (Type CHAR(1) NOT NULL, Regex VARCHAR(1000) NOT NULL, Send VARCHAR(2000) NOT NULL, Date TIMESTAMP, Assignee VARCHAR(200));
CREATE TABLE Login (Usr VARCHAR(200) NOT NULL,Pwd CHAR(41) NOT NULL, PIN INT(5) NOT NULL, Last_Login TIMESTAMP, Permission INT(1) NOT NULL, Assignee VARCHAR(200));
CREATE TABLE Tel (Name VARCHAR(500) NOT NULL, Phone CHAR(11) NOT NULL, DDGroup CHAR(64) NOT NULL, Date TIMESTAMP, Assignee VARCHAR(200));
```
5. Reboot.

### Testing
The code has been tested on Nginx/Debian 9 and Chrome 75.

### Attention
1. If the parent directory is not `/var/www`, please check the file path in each source code file during installation.
2. When you are trying to modify the code, please make sure the table data type and max variable-length support it. You should also pay attention to the code comment.

## Disclaimer

Main Contributor: [@PI](https://github.com/Pi-314159), [@Ranger](https://github.com/jijun), and [@guanxiaobo](https://github.com/guanxiaobo).

Alert Platform is created for EQXIU and licensed under the MIT license. You are free to reuse and modify the source code.

Please note that this software uses third party components that are licensed under CC BY 3.0 and Apache.
