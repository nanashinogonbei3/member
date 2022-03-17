<?php session_start();


// SESSION['cart']があれば
if (!empty($_SESSION['cart'])) {
        // name=add_cartボタンが押されたらエラーチェックを走らせる。
        if (!empty($_POST['add_cart'])) {
               
                // ----------------------------
                if($_POST['num'] === '') {
                    $error['num'] = 'blank';
                }
                    if(empty($error)) {
                                   
                        $_SESSION['update'] = $_POST;
                        header('Location: ./cart_update.php');
                        exit();  
                }
                // ----------------------------
        }
}
?>

<!DOCTYPE html>
<html lang="jp">


<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>shop cart</title>

  <!-- 全体CSS -->
  <link rel="stylesheet" href="css/stylesheet5.css">
  
</head>
<meta charset="utf-8">



<?php
    $img = '画像';
    $product_name = '商品名';
    $price = '価格';
    $num = '注文数';
?>

<?php
if (!empty($_POST["submit"])) {

    header("Location: ../product_introduction_no_login.php?id=".$_SESSION['id']); 
        exit;
    }
?>

    <div class='div_1'>

<?php if(isset($_SESSION["cart"]) && count($_SESSION["cart"] ) > 0 ) : ?>

    <table class='table'>
        <thead>
            <tr>
                <th><?= $img ?></th>
                <th><?= $product_name ?></th>
                <th><?= $price ?></th>
                <th><?= $num ?></th>
                <th></th>
            </tr>
    </thead>
    <tbody>


    <form method="POST" action="">
        <input type="hidden" name="id" value="<?= $_POST['id'] ?>">
        <input type="hidden" name="client_id" value="<?= $_SESSION['member'] ?> ">
       
    
        <td>
            <!-- 買い物カゴの商品画像 -->
            <img id="img3" src="../../product/images/<?php echo $_POST['img'] ?>" >
            <input type="hidden" name="img" value="<?= $_POST['img'] ?>"    >
        </td>

        <td>
            <!-- 商品名 -->
            <?= $_POST['product_name'] ?>
            <input type="hidden" name="product_name" value="<?= $_POST['product_name'] ?>" >
        </td>

        <td>
            <!-- 価格 -->
            <?= $_POST ['price'] ?>
            <input type="hidden" name="price" value="<?= $_POST ['price'] ?>" >円
        </td>

        <td>
            <!-- 個数 -->
            <input type="text" class="input_count" name="num" style="width:2rem;">&nbsp;個

            <?php if (isset($_POST) && empty($error['num'])) : ?>
                    <p class= "error">* 数量を入力してください</p>         
            <?php endif ?>  
        </td>

        <td>
            <!-- カートに入れる -->
            <input type="submit" name="add_cart" value='カートに入れる' class="shop-order">
           
        </td> 
        
    </form>                             
    </tr>

    </tbody>
    </table>
<?php endif ?>


    <p><a href="./cart_show.php" target="_self" style="text-decoration:none;">カートを見る</a></p>

    <!-- 戻る -->
    <input type="button" class="re-order" onclick="window.history.back();" value="前のページに戻る">


  
</div>
      

</body>
</html>

