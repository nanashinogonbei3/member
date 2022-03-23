<?php
session_start();



$id = $_GET["recipe_id"];

// 必要なファイルを読み込む
require_once('../../class/db/Base.php');
require_once('../../class/db/CreateRecipes.php');

$_SESSION['parent_category_id'] = $_GET['parent_category_id'];


// このファイルでは、商品（材料名）からテキストで検索した結果を出力します

    // フォーム未入力ならリダイレクト
    if (empty($_GET['product_name']) ) {
        
        header("Location: ../../edit/recipe/confirm.php?id='".$id."' ");
        exit;
    }
 
    try {

            // テキスト入力フォームで検索した場合
            if (!empty($_GET['product_name'])) {
                
                $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

                $dbh = new PDO($dsn, 'root', '');


                // Qiita https://qiita.com/jyunia0110/items/7166c6146fbf0b9d8d80
                // SELECT distinct で重複した行（レコード）をひとつにまとめる
                $stmt = $dbh->prepare("SELECT product_lists.id, product_name, img, amount, coo, price,
                makers.names
                 FROM product_lists LEFT OUTER JOIN makers ON product_lists.maker_id = makers.id 
                WHERE ( product_name LIKE :product_name ) 
                " );

                $stmt->bindValue(":product_name", '%' . addcslashes($_GET['product_name'], '\_%') . '%');
                
                $stmt->execute();

                $list= $stmt->fetchAll( PDO::FETCH_ASSOC );

              
  

                if (!empty($list)) {

                    if ($stmt->rowCount()) {

                        $_SESSION['product'] = $list;
                    
                        header("Location: ./test_product.php?id=");
                        // DB登録処理完了後、リダイレクト
                        exit;
                    }
                } elseif (empty($list)) {

                        echo "別名で検索ください（例）ペッパー⇒パウダー";
                    ?>
                        <!-- 戻る -->
                        <input type="button" class="re-order" onclick="window.history.back();" value="前のページに戻る">
                                <!-- 戻るおわり -->
                        <!-- onclick="location.href='../../edit/recipe/confirm.php?id=' .$_GET['recipe_id']" > -->
                    
                    <?php
                        // $null = "別名で検索ください（ペッパー⇒パウダー）";
                        // $_SESSION['null'] = $null;

                        exit;

                        header("Location: ./test_product.php?id=");
                        // DB登録処理完了後、リダイレクト
                        exit;

                }
            
             
            }

    } catch (PDOException $e) {
            echo 'ProceduresのDBに接続できません: ',  $e->getMessage(), "\n";
            echo '<pre>';
            var_dump($e);
            echo '</pre>';
            // echo $e->getMessage();
            exit;
    }


    ?>