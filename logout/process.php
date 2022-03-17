<?php
session_start();

$_SESSION = array();
// if (ini_set('session.use_cookies')) {

    // $params = session_get_cookie_params();
    // setcookie(session_name() . '', time() - 42000,
        // $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        // セッションで使ったファンクションを削除する
// }
session_destroy();

// クッキーに保存したメールアドレスも削除,空の値を指定して有効期限も切ります
setcookie('email' , '', time()-3600);

header('Location: ../logout/logout.php');
exit;

?>