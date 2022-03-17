<?php session_start();

foreach($_SESSION['cart'] as $key => $v) {
    if($v['id'] == $_SESSION['update']['id']) {
        unset($_SESSION['cart'][$key]);
        $_SESSION['cart'][] = $_SESSION['update'];
    }
}

header('Location: ./cart.php');
exit; 



?>


