<?php
session_start();
// 空送信のとき、リダイレクトのためセッションに渡す。

$_SESSION['parent_category_id'] = $_GET['parent_category_id'];


// このファイルでは、商品（材料名）からテキストで検索した結果を出力します

    // フォーム未入力ならリダイレクト
    if (empty($_GET['product_name']) ) {
        
        header("Location: ../../edit/recipe/edit_material_confirm.php?id=");
       
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

                // 配列を変数に代入する
                $list = $stmt->fetchAll( PDO::FETCH_ASSOC );


                if (!empty($list)) {

                    if ($stmt->rowCount()) {

                        $_SESSION['product'] = $list;
                        header("Location:./test_product_update.php?id=");
                        // DB登録処理完了後、リダイレクト
                        exit;
                    }

                    
                } elseif (empty($list)) {

                       $_SESSION['error_zero'] = "別名で検索ください（例）ペッパー⇒パウダー";

                        header("Location: ./test_product_update.php?id=");
                        exit;

                }      
           
            }

    } catch (PDOException $e) {
            echo 'ProceduresのDBに接続できません: ',  $e->getMessage(), "\n";
            echo '<pre>';
            var_dump($e);
            echo '</pre>';
            echo $e->getMessage();
            exit;
    }

    ?>