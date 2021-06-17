<?php

session_start();

$_SESSION = array();

if (isset($_COOKIE["PHPSESSID"])) {
    setcookie("PHPSESSID", '', time() - 1800, '/');
}

session_destroy();

echo "ログアウトしました。5秒後にログイン画面に戻ります。<br><br>";
echo "<a href='./index.html'>ログイン画面に遷移</a>";
echo '<meta http-equiv="refresh" content=" 5; url=./index.html">';
