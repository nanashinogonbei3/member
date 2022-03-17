<?php

        session_start();



        $parent_category_id = $_SESSION['material_category']['parent_category_id'];

        $material_category_name = $_SESSION['material_category']['material_category_name'];



try {

        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
    
        $dbh = new PDO($dsn, 'root', '');

        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

        // 材料・子供カテゴリーテーブル(material_children_categories)のインサートを行う
        $sql = 'INSERT INTO material_categories(material_category_name,
        users_id, recipe_id, parent_category_id)';
        $sql .='values(:material_category_name,
        :users_id, :recipe_id, :parent_category_id)';

        // SQL文を実行する準備
        $stmt = $dbh->prepare ( $sql );

    foreach ($parent_category_id as $v) {

        $stmt->bindValue(':material_category_name',$_SESSION['material_category']["material_category_name"],PDO::PARAM_STR);
        $stmt->bindValue(':users_id', $_SESSION['material_category']['users_id'], PDO::PARAM_INT);
        $stmt->bindValue(':recipe_id', $_SESSION['material_category']['recipe_id'], PDO::PARAM_INT);
        $stmt->bindValue(':parent_category_id', $v,PDO::PARAM_INT);    

    // sqlを実行
    $stmt->execute ();

        }
        // 処理が完了したら画面に遷移する
        header("Location: ./confirm.php?id= ".$_SESSION['recipe_id']." " );
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