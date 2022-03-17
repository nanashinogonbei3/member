<?php
session_start();

$_SESSION = array();

session_destroy();

// クッキーに保存したメールアドレスも削除,空の値を指定して有効期限も切ります
setcookie('email' , '', time()-3600);

header('Location: ../logout/logout.php');
exit;
?>