<?php

session_start();
// 必要なファイルを読み込む
require_once('../../class/db/Base.php');
require_once('../../class/db/CreateRecipes.php');

// マイレシピテーブルの画像のエラーチェックと画像一時保管先を指定します

    $fileName = $_FILES['complete_img']['name'];

    if (!empty($fileName)) {

        if (!empty($fileName)) {
            $ext = substr($fileName, -3);
            if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png') {
                $error['image'] = 'type';
                echo '<p class= "error">* 写真などは「.gif」または「.jpg」
                「.png」の画像を指定してください</p>' ;
                exit;
            }
        }
     

    } if (empty($error['image'])) {  

                    
        $image = date('YmdHis') .  $fileName;


        move_uploaded_file($_FILES['complete_img']['tmp_name'],


        '../../create/recipe/images/' . $image);
        $_POST['complete_img'] = $image;

    }

if(empty($fileName) AND empty($_POST['recipe_name']) AND empty($_POST['update_time'])
    AND empty($_POST['members_id'])) {
          //空送信したら./confirm.phpへリダイレクト
          header("Location: ./confirm.php?id=" . $_POST['id']);
          exit;
    }    



// 画像が入っていれば、
if (!empty($fileName)) {
    // レシピ名が入っていなかったら、
    if (empty($recipe_name)) {
     

        try {

            $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
        
            $dbh = new PDO($dsn, 'root', '');
        
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // SQL（更新）の実行
            $sql = "UPDATE my_recipes SET 
            members_id=:members_id, complete_img=:complete_img,
            update_time=:update_time WHERE id=:id";

            $stmt = $dbh->prepare ( $sql );

            
            $stmt->bindParam ( ":members_id", $_POST['members_id'], PDO::PARAM_INT );
            $stmt->bindParam ( ":complete_img", $_POST['complete_img'], PDO::PARAM_STR );
            $stmt->bindParam ( ":update_time", $update_time, PDO::PARAM_STR );
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
        // レシピ名があれば、
    } else {
      
        try {

            $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
        
            $dbh = new PDO($dsn, 'root', '');
        
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // SQL（更新）の実行
            $sql = "UPDATE my_recipes SET recipe_name=:recipe_name, 
            members_id=:members_id, complete_img=:complete_img,
            update_time=:update_time WHERE id=:id";

            $stmt = $dbh->prepare ( $sql );

            $stmt->bindParam ( ":recipe_name", $_POST['recipe_name'], PDO::PARAM_STR );
            $stmt->bindParam ( ":members_id", $_POST['members_id'], PDO::PARAM_INT );
            $stmt->bindParam ( ":complete_img", $_POST['complete_img'], PDO::PARAM_STR );
            $stmt->bindParam ( ":update_time", $update_time, PDO::PARAM_STR );
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

//画像がなくて、レシピ名だけが入力されていたらrecipe_nameだけ更新する、
} elseif (empty($fileName) ) {
     

    try {

            $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
        
            $dbh = new PDO($dsn, 'root', '');
        
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // SQL（更新）の実行
            $sql = "UPDATE my_recipes SET recipe_name=:recipe_name, 
            members_id=:members_id, update_time=:update_time WHERE id=:id";

            $stmt = $dbh->prepare ( $sql );

            $stmt->bindParam ( ":recipe_name", $_POST['recipe_name'], PDO::PARAM_STR );
            $stmt->bindParam ( ":members_id", $_POST['members_id'], PDO::PARAM_INT );
            $stmt->bindParam ( ":update_time", $update_time, PDO::PARAM_STR );
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