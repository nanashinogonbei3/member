<?php
session_start();
// ログイン情報が無ければ、ログイン画面にリダイレクト
if (empty($_SESSION['member'])) {
    header('Location: ../login/join.php');
    exit();
}

require_once('../class/db/Base.php');
require_once('../class/db/CreateRecipes.php');
// 1ページの$list でFETCH ALL の表示数
define('max_view',6);


try{
    

            $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));

            $date = $dt->format('Y-m-d');

            //データに接続するための文字列
            $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

            // my_sqlのpassword
            $dbh = new PDO($dsn,'root','');

            $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

           
            $sql = "SELECT *
             FROM favorite_products
            JOIN product_lists ON favorite_products.favorite_product_id = 
            product_lists.id
            WHERE favorite_products.members_id = '" . $_SESSION['member'] . "' ";
      
            $stmt = $dbh->prepare($sql);
        
            $stmt->execute();

            $result = $dbh->query ( $sql ); 

            $list = $result->fetchall( PDO::FETCH_ASSOC );

               
            // お気に入りレシピの数
            $total_count = count($list);
            // echo $total_count;
        
            // ページ数= 全商品数/1ページの表示数
            // トータルページ数※ceilは小数点を切り上げる関数1.6⇒2
            $pages= ceil($total_count/ max_view); 
            
          
            //現在いるページのページ番号を取得
            if (!isset($_GET['page_id'])) { 
                $now = 1;
            } else {
                $now = $_GET['page_id'];
            }
            

            // ページネーションの1ページ目のsqlの処理・1ページ以外のsqlの処理
            //表示するページを取得するSQLを準備
            $select = $dbh->prepare("SELECT distinct *
            FROM favorite_products
            JOIN product_lists ON favorite_products.favorite_product_id = 
            product_lists.id
            WHERE favorite_products.members_id = '" . $_SESSION['member'] .  
            "' ORDER BY favorite_products.favorite_product_id DESC LIMIT :start,:max ");
            
            if ($now == 1) {
            //1ページ目の処理
                    $select->bindValue(":start",$now -1,PDO::PARAM_INT);
                    $select->bindValue(":max",max_view,PDO::PARAM_INT);
                } else {
            //1ページ目以外の処理
                    $select->bindValue(":start",($now -1 ) * max_view,PDO::PARAM_INT);
                    $select->bindValue(":max",max_view,PDO::PARAM_INT);
                }
            //実行し結果を取り出しておく
            $select->execute();
            $data = $select->fetchAll(PDO::FETCH_ASSOC);


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



} catch (Exception $e) {
    echo 'DBに接続できません: ',  $e->getMessage(), "\n";
}


?>

<!DOCTYPE html>
<html lang="jp">
    
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>お気に入り商品アイテム</title>
  

    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
     <!-- 全体 -->
    <link rel="stylesheet" href="css/stylesheet8.css">
    <!-- ページネーション -->
    <link rel="stylesheet" href="css/style_paging3.css">
    

</head>

<body>

        <div class='div_p'><dt class="title_font">商品詳細ページ</dt>
            <!-- ボタン -->
            <!-- 商品の編集(管理者用) -->
            <?php if($_SESSION['member'] == 104) : ?>
                <div class="div_logout1">
                    <input type="button"  value='商品の編集' class="logout_btn"
                    onclick="location.href='./edit/confirm.php?id=<?php echo $id  ?>'" >   
                </div>
    <!-- ----------------------------------------------- -->
        
            <?php endif ?>   
            <!-- マイページ -->
            <div class="div_logout1">
                            <input type="button"  value='マイページ' class="logout_btn"
                            onclick="location.href='../login/process.php'" >
                            
            </div>

      
        <!-- div_pおわり -->        
        </div>
        
</form>


<!-- ----------------------------------------------- -->
<div class="item_6">
      
<div class="inlineBlock">
    <!-- 戻る -->
    <input type="button" class="re-order" onclick="window.history.back();" value="前のページに戻る">
  
    <!-- 商品一覧ボタン -->
        <input type="button" value="商品一覧" class="shop-order" onclick="
        location.href='../product/product_lists.php'"
        value='商品一覧' >
</div> 
<br>
<!-- -------------------------------------------------------------- -->
<br>
<!-- ページングCSS -->
<div class="div_w5">
<div class="flex">  
    <ul class="bar">
        <li> 
        <span style="color:#000000;font-size:15px;font-weight:lighter">
        お気に入り商品数、全:<span style="color:green;font-size:24px"><?php echo $total_count ?></span>品</span>
        &nbsp;&nbsp; 

        <?php if($pages >= 2) { ?>
        <!-- ページが２ページ以上あればページングを表示する -->
            <?php 
                //ページネーションを表示    
                if ($now > 1) {
                // 1ページより大きいなら、「前へ」表示
                echo '<a href="?page_id=',($now-1),'">  
                 <img src="../icon_img/pre.png"
                 alt="前へ" width="25" height="25" border="0">
                </a>';
                
                } else {
                //  1ページよりも小さい＝ページが無い、場合は矢印は表示させない。
                }
                ?>
       
                    <?php
                        // 1 2 3 と、表示するページの数を$pagesを今回は使わず、1 2 3 4 5 と、'5'つにする。
                        for ( $n = 1; $n <= $pages; $n ++) {
                            if ( $n == $now ) {
                              // 現在表示されているページなら、リンクは付けない。
                              echo "<span style='padding: 5px;'>$now</span>";
                            } else {
                                echo "<a href='./favorite_recipe.php?page_id=$n' style='padding: 5px;'>$n</a>";
                                // それ以外のページの数字には、リンクを貼る
                                // hrefのリンクは、表示現在表示するリンクに修正して使うこと。
                            }
                        }  
                    ?> 
        
                <?php
                    if ($now < $pages) {
                    // 表示ページが最終ページより小さいなら、「次へ」表示
                    echo '<a href="?page_id=',($now+1),'">  
                    <img src="../icon_img/next.png"
                        alt="次へ" width="25" height="25" border="0" margin-top:1px>
                    </a>';
                    }       
                ?>
            <?php }elseif($pages == 1){
                // ページ数が1なら、ページングは非表示。
            } ?>
  
      
        </li> 
    </ul> 
    

</div>

</div>


<!-- レシピ一覧表示欄 -->
<div class="inline_block_7">
<div class="parent">   
<?php if(!empty($data)) { ?> 
  <?php foreach ($data as $v) : ?>
    <div class="item_2">
           
        <span style="color:green;font-size:24px">
        <dt class="wf-sawarabimincho">
        <!-- データベースからFETCH()した、レシピ名 -->   
        ■<?php echo $v['product_name'].$v['amount'] ?></dt></span>
        <!-- FETCH()した、商品を使用したレシピ画像 -->
        <a href="../product/product_introduction.php?id=
        <?php echo $v['id'] ?>">
        <dt><img id="recipe" src="../product/images/<?php echo $v['img'] ?>" alt=""></dt>
        </a>
        <span style="color:#BD0711;font-size:21px;font-weight:bold"><?php echo $v['price']."円" ?></span>
        
    </div>
        <?php endforeach ?>
<?php } else {
    
} ?>
      
        </div> 
        </div>
</div>
 
</div>
<!-- inlin_block_7おわり -->
</div>


<!-- compresive -->
</div>
<!-- inline_block_6 -->
</div>
<!-- DIV item_6おわり -->
</div>

<script src="./js/movepage.js"></script>
</body>
</html>

