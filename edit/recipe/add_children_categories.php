<?php
// ここで、ユーザーが作成したカテゴリをcategoriesテーブルに登録します。
session_start();

 
$id = $_SESSION['recipe_id'];



$parentCategoryId = $_SESSION['categories']['id'];



 try {



        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
    
        $dbh = new PDO($dsn, 'root', '');

        // カテゴリーテーブルのインサートを行う
        $sql = 'INSERT categories (categories_name, users_id, parent_category_id)';
        $sql .='values(:categories_name, :users_id, :parent_category_id)';
      

        // SQL文を実行する準備
        $stmt = $dbh->prepare ( $sql );


  // 複数選択された場合foreachで回して配列入っている値を全てのバインドする
  foreach ($parentCategoryId as $v) {


        $stmt->bindValue(':categories_name',$_SESSION['categories']["categories_name"],PDO::PARAM_STR);
        $stmt->bindValue(':users_id', $_SESSION['categories']["users_id"],PDO::PARAM_INT);
        $stmt->bindValue(':parent_category_id',$v, PDO::PARAM_INT);

        // sqlを実行
        $stmt->execute ();
            
  }


        // 処理が完了したら画面に遷移する
        header("Location: ./confirm.php?id='".$id."'" );
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