<?php
    // 必要なファイルを読み込む
    $id = $_POST['id'];



    session_start();

    require_once('../../class/db/Base.php');
    require_once('../../class/db/CreateRecipes.php');



    $fileName = $_FILES['img']['name'];


if (!empty($fileName)) {
    
    $ext = substr($fileName, -3);
        if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png') {
            $error['image'] = 'type';
            echo '<p class= "error">* 写真などは「.gif」または「.jpg」
            「.png」の画像を指定してください</p>' ;
            exit;
    }   

    } if (empty($error['image'])) {  

                    
        $image = date('YmdHis') .  $fileName;


        move_uploaded_file($_FILES['img']['tmp_name'],


        '../images/' . $image);
        $_POST['img'] = $image;
    }

// 空送信のリダイレクト処理
if(empty($fileName) AND empty($_POST['product_name']) AND empty($_POST['categorie_name'])
    AND empty($_POST['handling_start_date']) ) {
          //confirm.phpへリダイレクト
          header("Location: ./confirm.php?id=" . $_POST['id']);
          exit;
    }  


// 商品テーブルの、カラムのUPDATE文のはじまり

//画像が入っていた場合、
if (!empty($fileName)) {
    // 商品名が空だったら、 
    if (empty($product_name)) {
        
    
        try {

            $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
        
            $dbh = new PDO($dsn, 'root', '');
        
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // SQL（更新）の実行
            $sql = "UPDATE product_lists SET 
            img=:img  WHERE id=:id";

            $stmt = $dbh->prepare ( $sql );

            $stmt->bindParam ( ":img", $_POST['img'], PDO::PARAM_STR );
            $stmt->bindParam ( ":id", $_POST['id'], PDO::PARAM_INT );

            // sqlを実行
            $stmt->execute ();

            // 処理が完了したら./confirm.phpへリダイレクト
            header("Location: ./confirm.php?id=" . $_POST['id']);
            exit;

        } catch (PDOException $e) {
            echo 'my_recipeのDBに接続できません: ',  $e->getMessage(), "\n";
            echo '<pre>';
            var_dump($e);
            echo '</pre>';
            exit;
        }
    
    } elseif($_POST['product_name']) {

        //商品名があれば、画像と全てのカラムにUPDATEする。
        try {

            $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
        
            $dbh = new PDO($dsn, 'root', '');
        
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // SQL（更新）の実行
            $sql = "UPDATE product_lists SET product_name=:product_name,
            img=:img, categorie_name =:categorie_name,	
            handling_start_date=:handling_start_date WHERE id=:id";

            $stmt = $dbh->prepare ( $sql );
            
            $stmt->bindParam ( ":img", $_POST['img'], PDO::PARAM_STR );
            $stmt->bindParam ( ":product_name", $_POST['product_name'], PDO::PARAM_STR );
            $stmt->bindParam ( ":categorie_name", $_POST['categorie_name'], PDO::PARAM_STR );
            $stmt->bindParam ( ":handling_start_date", $_POST['handling_start_date'], PDO::PARAM_STR );
            $stmt->bindParam ( ":id", $_POST['id'], PDO::PARAM_INT );

            // sqlを実行
            $stmt->execute ();

            // 処理が完了したら./confirm.phpへリダイレクト
            header("Location: ./confirm.php?id=" . $_POST['id']);
            // confirm.php からmy_recipe のid で飛ばされた、$_POST['recipe_id']
            exit;

        } catch (PDOException $e) {
            echo 'my_recipeのDBに接続できません: ',  $e->getMessage(), "\n";
            echo '<pre>';
            var_dump($e);
            echo '</pre>';
            exit;
        }
    
    
    
    }
    
  //画像が空だったら、画像以外の全てのカラムを更新する、 
} elseif (empty($fileName) ) {
 

        try {
        
            $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
        
            $dbh = new PDO($dsn, 'root', '');
        
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // SQL（更新）の実行
            $sql = "UPDATE product_lists SET 
            product_name=:product_name, categorie_name =:categorie_name,	
            handling_start_date=:handling_start_date WHERE id=:id";

            $stmt = $dbh->prepare ( $sql );

            
            $stmt->bindParam ( ":product_name", $_POST['product_name'], PDO::PARAM_STR );
            $stmt->bindParam ( ":categorie_name", $_POST['categorie_name'], PDO::PARAM_STR );
            $stmt->bindParam ( ":handling_start_date", $_POST['handling_start_date'], PDO::PARAM_STR );
            $stmt->bindParam ( ":id", $_POST['id'], PDO::PARAM_INT );

            // sqlを実行
            $stmt->execute ();

            // 処理が完了したら./confirm.phpへリダイレクト
            header("Location: ./confirm.php?id=" . $_POST['id']);
            // confirm.php からmy_recipe のid で飛ばされた、$_POST['recipe_id']
            exit;

        } catch (PDOException $e) {
            echo 'my_recipeのDBに接続できません: ',  $e->getMessage(), "\n";
            echo '<pre>';
            var_dump($e);
            echo '</pre>';
            exit;
        }

}    
    

?>