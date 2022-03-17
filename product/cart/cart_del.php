<?php session_start();


// カートの商品ごとに空にする
// $_SESSION['cart'][0],[1],[2], の何を選択されるかわからないので、
// unset関数で、商品ごとに削除するために、
// cart_show.php から送られてきたname="key"＄_POST['KEY']を受け取り、
// だから$_POST['key']を消すという意味で、unset($_SESSION['cart'][$_POST['key']]); と入力する。
unset($_SESSION['cart'][$_POST['key']]);


header('Location: ./cart_show.php');
exit; 
?>


