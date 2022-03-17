<?php
session_start();

require_once('../class/db/Base.php');
require_once('../class/db/CreateRecipes.php');


if(!empty($_SESSION['member'])) {
    $sessionMember = $_SESSION['member'];
}

// serch_recipe_multiple class="php"の検索結果をセッションに渡す。
if(!empty($_SESSION['product_used_recipeName'])) {
    $productUsedRecipeName = $_SESSION['product_used_recipeName'];
}

// どの商品アイテムか？
$id = $_GET['id'];
$_SESSION['id'] = $id;

// 1ページの$list でFETCH ALL の表示数
define('max_view',15);


try{

    // セッションにメンバーIDが無ければ、ログイン画面に遷移する。
    if (empty($_SESSION['member'])) {
        header ( "Location: ../login/join.php" );
    }
  
    
    if (empty($_GET['id']) || empty($id)) {
       header ( "Location: ../edit/recipe/release_recipe.php" );


        } else {


            $id = $_GET['id'];

            $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));

            $date = $dt->format('Y-m-d');

            //データに接続するための文字列
            $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

            // my_sqlのpassword
            $dbh = new PDO($dsn,'root','');

            $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            //  冒頭で作った変数の$id = $_GET['id'] 選んだ商品id をここで代入する
            $sql = "SELECT *
             FROM product_lists
            LEFT OUTER JOIN makers ON product_lists.maker_id = makers.id
            WHERE product_lists.id= '" . $id . "' ";
      
            $stmt = $dbh->prepare($sql);
        
            $stmt->execute();

            $result = $dbh->query ( $sql ); 

            $list = $result->fetch( PDO::FETCH_ASSOC );


            // my_recipesのテーブルからこの商品を使ったレシピをFETCHします。
            $sql = "SELECT my_recipes.id,
            my_recipes.recipe_name, complete_img, cooking_time,
            cost, how_many_servings, product_lists.product_name 
            FROM product_lists
            INNER JOIN materials ON materials.product_id = product_lists.id
            INNER JOIN my_recipes ON materials.recipe_id = my_recipes.id
            WHERE product_lists.id= '" . $id . "' ";
      
            $stmt = $dbh->prepare($sql);
        
            $stmt->execute();

            $result = $dbh->query ( $sql ); 

            $recipe = $result->fetch( PDO::FETCH_ASSOC );


            // favorite_productsテーブル
            $sql = "SELECT favorite_products.favorite_product_id, favorite_products.is_completed
             FROM favorite_products
            INNER JOIN product_lists ON favorite_products.favorite_product_id = product_lists.id
            WHERE favorite_products.favorite_product_id= '" . $id . "'
            AND favorite_products.members_id = '".$sessionMember."'
            ";
      
            $stmt = $dbh->prepare($sql);
        
            $stmt->execute();

            $result = $dbh->query ( $sql ); 

            $record = $result->fetch( PDO::FETCH_ASSOC );
        
            // お気に入り商品の重複チェック
            // $array_count = array_count_values ( $record );

            // 一回目のお気に入りは、INSERT（add_favorite_product.php)で。
            // 2回目以降は、UPDATE（update_favorite_product.php）で。
            // ※2回目以降は、completed==1なら、value=0,completed==0なら、value=1を代入するだけ。
         


            // セッションに記録された時間が、今の時間よりも大きい、つまりログイン時間から
            // 1時間以上たっていた場合,という意味
        if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
            // （1時間が経過していたら、）ログアウトし、ログイン画面に遷移する
            $_SESSION['time'] = time();
            // 現在の時刻で上書きします。こうすることで、何か行動したことで上書きすることで
            // 最後の時刻から１時間を記録することができるようになる。 
            } elseif ($_SESSION['member'] = [] ) {
                header('Location: ../login/join.php');
                exit();
                // 更新時刻より１時間経過していなくとも、クッキーの削除でセッション情報が空になったら
                // ログイン画面に遷移する
            } else {
                header('Location: ../login/join.php');
                exit();
                // 何か行動した更新時刻より１時間経過したら、自動的にログイン画面に遷移します
            }


        }


} catch (Exception $e) {
    echo 'DBに接続できません: ',  $e->getMessage(), "\n";
}


?>

<!DOCTYPE html>
<html lang="jp">
    
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>商品紹介ページ</title>
  

    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/stylesheet6.css">


</head>

<body>

        <div class='div_p'><dt class="title_font">商品詳細ページ</dt>
            <!-- ボタン -->
            <!-- 商品の編集(管理者用) -->
            <?php if($_SESSION['member'] == 104) : ?>
                <div class="div_logout1">
                    <input type="button"  value='商品の編集' class="logout_btn"
                    onclick="location.href='./edit/confirm.php?id=<?php echo $id ?>'" >
                    <!-- /member/logout/process.php -->
                </div>
            <?php endif ?>   
            <!-- マイページ -->
            <div class="div_logout1">
                            <input type="button"  value='マイページ' class="logout_btn"
                            onclick="location.href='../login/process.php'" >
                            <!-- /member/logout/process.php -->
            </div>
            <!-- 商品一覧ボタン -->
            <div class="div_logout2">             
                <input type="button" value="商品一覧" class="re-order" onclick="
                location.href='./product_lists.php'">
            </div> 

             <!-- 買い物カゴボタン -->
             <div class="div_logout3">             
                <input type="button" value="カートを見る" class="shop-order" onclick="
                location.href='./cart/cart_show.php'">
            </div> 

        </div>
        <!-- div_pおわり -->
        <div class="comprehensive">
            <div class='inline_block_5'>
        
    
            <div class="block2">
            <div class="item_0">
                        <!-- データベースからFETCH()した、商品名） -->
                        <dt class="wf-sawarabimincho"><dt class="p_font_rarge"><span style="color:green"><?php echo $list['product_name'] ?></span></dt></dt>
                        <div>
                        <!-- フォーム 商品名からレシピを検索します。 -->
                        <form action="./serch_recipe_multiple.php" method="GET">
                            <input type="hidden" name="id" value="<?php echo $id ?>">
                            </div>     
                            <!-- ボタン -->
                            <div class="div_re-order">                
                                <input type="submit" class="re-order3" value="<?php echo $list['product_name'] ?>を使ったレシピ">
                            </div>
                             
                        </form> 
                    
                                <pre></pre>
                            <!-- データベースからFETCH()した、商品ID -->
                            <span style="color:green;font: size 11px;">
                            <dt class="wf-sawarabimincho">商品ID：
                            <?php echo $list['id'] ?></span></dt>                         
            </div>        
            <div class="item_1">
            <!-- 商品画像 -->      
                <span style="color:green"><dt class="wf-sawarabimincho">
                        <img id="img2" src="./images/<?php echo $list['img'] ?>"></dt>
            <br><pre></pre>

<!-- -------------------------------------------------------------------------- -->
<!-- お気に入り登録 -->


        <!-- 該当商品が、favorite_productsテーブルに登録が無ければ、 -->
        <?php if(empty($record['favorite_product_id'])) { ?>
                <form action="./favorite/add_favorite_product.php" method="POST">   
                    <!-- 条件式の値をname="is_completed"へ代入する -->
                    <input type="hidden" name="favorite_product_id" value="<?php echo $id ?>">
                    <input type="hidden" name="members_id" value="<?php echo $_SESSION['member'] ?>">
                    <input type="hidden" name="is_completed" value=1>
               
                    <!-- ボタン -->
                    <div class="favorite_btn">
                        <!-- 登録する -->
                        <input type="image" src="../icon_img/my_favorite-01.png" alt="no" width="210%">
                    </div>
                </form>

        <!-- 当該商品が、fovorite_productsテーブルに登録があれば -->
        <?php } else { ?>
            <!-- お気に入りの重複チェック -->
            <?php
            $array_count = array_count_values ( $record );
            //  重複していません。だからINSERTしましょう。 -->    
            if ($array_count ==0) {
                
            ?>    
                <form action="./favorite/add_favorite_product.php" method="POST">   
                    <!-- 条件式の値をname="is_completed"へ代入する -->
                    <input type="hidden" name="favorite_product_id" value="<?php echo $id ?>">
                    <input type="hidden" name="members_id" value="<?php echo $_SESSION['member'] ?>">
                    <input type="hidden" name="is_completed" value=1>
               
                    <!-- ボタン -->
                    <div class="favorite_btn">
                        <!-- 登録する画像 -->
                        <input type="image" src="../icon_img/my_favorite-01.png" alt="no" width="210%">
                    </div>
                </form>

            <!-- 重複しています。 -->    
            <?php } elseif($array_count >=1) { ?>
                <!-- ↓Updateソース -->
                <form action="./favorite/update_favorite_product.php" method="POST">
                    <!-- name="is_completed＝0"代入する -->
                    <input type="hidden" name="favorite_product_id" value="<?php echo $record['favorite_product_id'] ?>">
                    <input type="hidden" name="members_id" value="<?php echo $_SESSION['member'] ?>">
                    <?php if($record['is_completed']==0) {
                        $value_num = 1;
                    } elseif($record['is_completed']==1) {
                        $value_num = 0;
                    }
                    ?>
                    <input type="hidden" name="is_completed" value=<?php echo $value_num ?>> 
                
                    <!-- ボタン -->
                    <div class="favorite_btn">
                       
                        <?php if ($record['is_completed']==0) { ?>
                            <input type="image" src="../icon_img/my_favorite-01.png" alt="dont_favorite" width="210%">
                        <?php }elseif($record['is_completed']==1) { ?>
                            <input type="image" src="../icon_img/my_favorite-0.png" alt="add_favorite" width="175%" >
                        <?php } ?>
                    </div>
                    
                    
            <?php } ?>

        <?php } ?>                     
<!-- お気に入りおわり -->
<!-- ------------------------------------------------------------------------------------- -->
                        <br>

                       
                            <!-- "商品を買い物に入れる"ボタン -->
                            <input type="button" class="re-order" value='カートに入れる' 
                            onclick="location.href='./cart/cart.php'" style="width: 180px;" >
                            <!-- セッションに渡した$id↓ -->
                            <input type="hidden" name="id" value="<?php echo $_SESSION['id'] ?> ">
                            
                            <!-- 戻る -->
                            <input type="button" class="re-order" onclick="window.history.back();" value="前のページに戻る">
                            <!-- 戻るおわり -->
                          
                            <!-- ボタンおわり -->
                            
                         
                        
                </div>
            </div>
        </div>
</form>
<!-- フォームおわり -->
 





    <!-- DIV 右側はじまり -->
    <div class="div_migi">

<div class="div_edit_r3">

        <table>                    
          <tr>
              <th>価格</th>
              <th>メーカー</th>
              <th>内容量</th>
              <th>原産国</th>
          </tr>                  

        <!-- データベースからFETCH()した、 販売価格 -->
                   <td><span style="color:green"><?php echo $list['price'] ?></span>円</dt></td>

        <!-- データベースからFETCH()した、メーカー名  -->
                   <td width="111px">&nbsp;&nbsp;&nbsp;<span style="color:green"><?php echo $list['names'] ?></span></dt></td>

        <!-- データベースからFETCH()した、 内容量 -->
                   <td><span style="color:green"><?php echo $list['amount'] ?></span></dt></td>
        
        <!-- データベースからFETCH()した、 原産国 -->
        <td><span style="color:green"><?php echo $list['coo'] ?></span></dt></td>

               </tr>
           </tbody>              
        </table>
<!-- 区切り線 -->
<div class="line"></div>



<!-- フォームおわり -->


<!-- div_w start-->
<div class="div_w3">
    <!-- データベースからFETCH()した、商品説明 -->      
    <span style="color:green;font-size:24px">
    <dt class="wf-sawarabimincho">■商品説明</dt></span>
    <dt><?php echo $list['describes'] ?></dt>
</div>
<!-- div_wおわり -->
<!-- div_w start-->
<div class="div_w3">
    <!-- データベースからFETCH()した、使い方 -->      
    <span style="color:green;font-size:24px">
    <dt class="wf-sawarabimincho">■適した料理・用途</dt></span>
    <dt><?php echo $list['howto_use'] ?></dt>
</div>
<!-- div_w おわり-->
<!-- div_w start-->
<div class="div_w3">
    <!-- データベースからFETCH()した、特徴 -->      
    <span style="color:green;font-size:24px">
    <dt class="wf-sawarabimincho">■成分・効能</dt></span>
    <dt><?php echo $list['efficacy'] ?></dt>
</div>


    </div>                  
    <!-- div_w おわり -->
   
<!-- DIV migi 右側おわり -->
<!-- div class="comprehensive" おわり-->  
</div>


   
<!-- div_precent -->
</div>





</div>
<!-- inline_block_6 -->
</div>


<script src="./js/movepage.js"></script>
</body>
</html>

