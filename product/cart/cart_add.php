<?php session_start();



$_SESSION["cart"] [] = $_SESSION['add'];
unset($_SESSION['add']);


header('Location: ./cart.php');
exit; 


?>


