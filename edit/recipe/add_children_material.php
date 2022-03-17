<?php
// ここで、親カテゴリー階層下に追加する材料を親材料カテゴリー、material_parent_categories登録します。
session_start();

    
    // ["id"]=>
    // array(1) {
    //   [0]=>
    //   string(1) "4"
    // }
// 'parent_category'は配列に入っているから、foreachで回して取り出す。
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