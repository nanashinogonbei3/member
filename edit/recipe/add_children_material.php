<?php
// ここで、親カテゴリー階層下に追加する材料を親材料カテゴリー、material_parent_categories登録します。
session_start();



// array(4) {
//     ["recipe_id"]=>
//     string(2) "27"

    // ["id"]=>
    // array(1) {
    //   [0]=>
    //   string(1) "4"（id<=7,7以下は、material_parent_categoriesテーブルの、
    //   idです。./confirm.php $count参照.親・材料カテゴリーのidです。
    //   idが８であれば、./confirm.php の子供カテゴリ（ユーザ定義の
    //   子供・材料カテゴリーのidです。$children参照.）
//     }

//     ["material_name"]=>
//     string(18) "ターメリック"
//     ["amount"]=>
//     string(10) "大さじ1"
//   }
  
// ["id"]=>
// array(1) {
//   [0]=>
//   string(1) "4"
// }
// ↑'parent_category'は配列に入っているから、ぐるぐる回して取り出す必要がある。
$parent_categoryId = $_GET['id'];


 try {

        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
    
        $dbh = new PDO($dsn, 'root', '');


         // もし、親の材料カテゴリーのラジオボタン選択入力があれば、
        if (!empty($_GET["id"]) ) {

                // カテゴリーテーブルのインサートを行う
                $sql = 'INSERT materials (recipe_id, parent_category_id, material_name, amount)';
                $sql .='values(:recipe_id, :parent_category_id, :material_name, :amount)';
            
                // SQL文を実行する準備
                $stmt = $dbh->prepare ( $sql );

                    //もしも✅したのが親・材料カテゴリーのidが7以下であれば、 
                    // if ($_GET['id'] =1 ) {
                    // $_GET['id']の配列を変数に渡してぐるぐる回して中身を取り出す$Vをバインドする。
                    foreach($parent_categoryId as $v) {
                        

                        $stmt->bindValue(':recipe_id',$_GET["recipe_id"],PDO::PARAM_INT);
                        $stmt->bindValue(':parent_category_id',$v,PDO::PARAM_INT); 
                        $stmt->bindValue(':material_name', $_GET["material_name"],PDO::PARAM_STR);
                        $stmt->bindValue(':amount',$_GET["amount"],PDO::PARAM_STR);

            

                        // sqlを実行
                        $stmt->execute ();

                        
                    
                    }
                        
            

            // materials.material_category_idの✅送信が無ければ・・・
        } elseif (empty($_GET["id"])) {

            $sql = 'INSERT materials (recipe_id, material_name, amount)';
            $sql .='values(:recipe_id, :material_name, :amount)';

            // SQL文を実行する準備
            $stmt = $dbh->prepare ( $sql );

            $stmt->bindValue(':recipe_id',$_GET["recipe_id"],PDO::PARAM_INT);
            $stmt->bindValue(':material_name', $_GET["material_name"],PDO::PARAM_STR);
            $stmt->bindValue(':amount',$_GET["amount"],PDO::PARAM_STR);           

            // sqlを実行
            $stmt->execute ();

        }

            // 処理が完了したら画面に遷移する
            header("Location: ./confirm.php?id=" .$_GET['recipe_id']);
            exit;

    } catch (PDOException $e) {
        echo 'proceduresのDBに接続できません: ',  $e->getMessage(), "\n";
        echo '<pre>';
        var_dump($e);
        echo '</pre>';
        echo $e->getMessage();
        exit;
    }
   

    ?>