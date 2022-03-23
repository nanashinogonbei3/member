<?php
session_start();



// このファイルでは、テキストフォームで材料名を検索した結果を出力します

    // フォーム未入力ならリダイレクト
    if (empty($_GET['product']) ) {
        
        header("Location: ../index.php?id=");

        exit;
    }
     


    try {

            // テキスト入力フォームで検索した場合
            if (!empty($_GET['product'])) {
                
                $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

                $dbh = new PDO($dsn, 'root', '');


                // Qiita https://qiita.com/jyunia0110/items/7166c6146fbf0b9d8d80
                // SELECT distinct で重複した行（レコード）をひとつにまとめる
                $stmt = $dbh->prepare("SELECT product_lists.id, product_name, img, amount, coo, price,
                makers.names
                 FROM product_lists LEFT OUTER JOIN makers ON product_lists.maker_id = makers.id 
                WHERE ( product_name LIKE :product ) 
                " );

                $stmt->bindValue(":product", '%' . addcslashes($_GET['product'], '\_%') . '%');
                
                $stmt->execute();
         
     
        }

                // 配列を変数に代入する
                $list = $stmt->fetchAll( PDO::FETCH_ASSOC );


                if ($stmt->rowCount()) {

                    $_SESSION['productList'] = $list;
                    // 検索結果をセッションに渡す

                    header("Location: ../../top/acodion.php?id=");
                    // DB登録処理完了後、検索結果画面へ遷移する
                    exit;

             
                } elseif (empty($list)) {
                   
                    $_SESSION['product_error'] = '他の検索条件でも探してみてくださいね';
                    header("Location: ../../top/acodion.php?id=");
                    // DB登録処理完了後、検索結果画面へ遷移する
                    exit;
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