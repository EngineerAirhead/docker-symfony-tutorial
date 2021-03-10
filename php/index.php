<?php
echo '<h1>Hello world!</h1>';

try {
    $dbh = new PDO('mysql:host=myproject_mysql;dbname=myproject', 'myproject_user', 'myproject_password');

    print 'Connection established!';
} catch (PDOException $e) {
    print 'Error!: ' . $e->getMessage() . '<br/>';
}

echo phpinfo();
