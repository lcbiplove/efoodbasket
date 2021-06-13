# eFoodBaket

![efoodBasketlogo](https://lcbiplove.github.io/e-food-basket/images/efoodbasket-name.png)

This is the final efoodbasket repository. [Previous repo](https://lcbiplove.github.io/e-food-basket/) was mainly for the design of the website (frontend). So, that will be seen as the reference while development of backend. To see the changes from backend, clone the repository to your device. You can follow the [Basic Shared Repository Workflow](https://uoftcoders.github.io/studyGroup/lessons/git/collaboration/lesson/) (You can only look out for topic: Basic Shared Repository Workflow) to push the changes you have made.

### Requirements

To check the development of the website,  you need to have following tools:
1. [Xampp of PHP version 7.3.28 / PHP 7.3.28](https://www.apachefriends.org/download.html)
2. [Oracle Database Express](https://www.oracle.com/database/technologies/xe-downloads.html)
3. [Windows 64-bit with JDK 8 included](https://www.oracle.com/tools/downloads/sqldev-downloads.html)

### OCI connection

To connect oracle database with php, oci_connect() is used. Check out php official [docs](https://www.php.net/manual/en/function.oci-connect.php) to explore the usage of the function. 
To enable oci facility, you can configure `php.ini` file from xampp by clicking `config` button on **Apache** tab. After you open the `php.ini` file in notepad, search for `oci` then, remove **semicolon(;)** infront of these two lines:
```
extension=oci8_12c
extension=pdo_oci
```
Restart xampp. Now, your oci will be working. To check if oci is enabled. Go to the link: http://localhost/dashboard/phpinfo.php
Below, you will get oci table, and see that `enabled` value inside table.

After you have completed the installation of `Oracle express`, you should create a portable container (PDB). To enable, walk through [this tutorial](https://www.youtube.com/watch?v=gaelhF2us28). After you have added `XEPDB1`, you should add user to that container:
Run cmd and type the code:
```
# Entering database commandline
sqlplus

# You will be prompted to enter username and passwod
Enter username: sys as sysdba  # Type this
Enter password: # Here, type the password that you entered while installing

# Connect to PDB container
connect SYSTEM/YOUR_PASSWORD@XEPDB1;

# Creation of efoodbasket user
CREATE USER efoodbasket IDENTIFIED BY "pm..2021";

# Grant permissions
GRANT CONNECT, RESOURCE, DBA TO efoodbasket;

# Exit from sqlplus
exit
```

To connect to our php, our code is like:
```php
define('DB_USERNAME', 'efoodbasket');
define('DB_PASSWORD', 'pm..2021');

$connection = oci_connect (DB_USERNAME, DB_PASSWORD, "//localhost:1521/XEPDB1") or die(oci_error());
```

### References
1. https://www.oracle.com/database/technologies/appdev/xe/quickstart.html
2. https://www.php.net/manual/en/function.oci-connect.php



