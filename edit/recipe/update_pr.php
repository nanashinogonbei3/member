<?php

    session_start();

    require_once('../../class/db/Base.php');
    require_once('../../class/db/CreateRecipes.php');

 // 後ほど50行目のif構文に使用する変数に代入する
    $descriptions = $_POST['descriptions'];
   

// 画像のエラーチェック とmove_uploaded_file($_FILES['p_img']['tmp_name'],をここでする
    $fileName = $_FILES['p_img']['name']; 

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


           move_uploaded_file($_FILES['p_img']['tmp_name'],


           '../../create/recipe/pimg/' . $image);
           $_POST['p_img'] = $image;
       
       }

 // もし$fileName に画像があれば、descriptionsと、p_img カラムを同時に更新する
if (!empty($fileName) ) {
    

// フォームで画像ファイルを選択し、かつdescriptionsに入力があれば、以下の処理を行う
      if(!empty($descriptions)) {
        
            try {


                // 調理手順＊テーブルへ インスタンス生成
                $db_procedures = new Procedures(); 

                // データベースに接続するための文字列（DSN 接続文字列）
                $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
            
                // PDOクラスのインスタンスを作る
                // 引数は、上記のDSN、データベースのユーザー名、パスワード
                // XAMPPの場合はデフォルトでパスワードなし、MAMPの場合は「root」
                $dbh = new PDO($dsn, 'root', '');
            
                // エラーが起きたときのモードを指定する
                // 「PDO::ERRMODE_EXCEPTION」を指定すると、エラー発生時に例外がスローされる
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            
                // string $update_time, string $release_date, int $is_deleted, int $id_select)
                // マイレシピ☆テーブルのレコードをアップデートするsql文
                $sql = 'UPDATE procedures set p_recipe_id=:p_recipe_id, p_img=:p_img, ';
                $sql .= 'descriptions=:descriptions ';
                $sql .= 'where id=:id';
            
                // SQL文を実行する準備
                $stmt = $dbh->prepare($sql);

             
                // SQL（更新）の実行
                $stmt->bindParam ( ":p_recipe_id", $_POST['p_recipe_id'], PDO::PARAM_INT );
                $stmt->bindParam ( ":p_img", $_POST['p_img'], PDO::PARAM_STR );
                $stmt->bindParam ( ":descriptions", $_POST['descriptions'], PDO::PARAM_STR );
                $stmt->bindParam ( ":id", $_POST['id'], PDO::PARAM_INT );

                // sqlを実行
                $stmt->execute ();

        
                // 処理が完了したら（confirm.php）へリダイレクト
                header("Location: ./confirm.php?id=" . $_POST['p_recipe_id']);
                exit;


            } catch (PDOException $e) {
                echo 'proceduresのDBに接続できません: ',  $e->getMessage(), "\n";
                var_dump($e);
        
                exit;
            }

        //  descriptionsの入力が無くて、且つ画像ファイルの選択だけがあった場合、画像のみの更新を行い、
        //  descriptionsの'空'の上書きをさせない
      } elseif (empty($descriptions)) {
        
 
 
            try {


                // 調理手順＊テーブルへ インスタンス生成
                $db_procedures = new Procedures(); 

                // データベースに接続するための文字列（DSN 接続文字列）
                $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
            
                // PDOクラスのインスタンスを作る
                // 引数は、上記のDSN、データベースのユーザー名、パスワード
                // XAMPPの場合はデフォルトでパスワードなし、MAMPの場合は「root」
                $dbh = new PDO($dsn, 'root', '');
            
                // エラーが起きたときのモードを指定する
                // 「PDO::ERRMODE_EXCEPTION」を指定すると、エラー発生時に例外がスローされる
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            
                // string $update_time, string $release_date, int $is_deleted, int $id_select)
                // マイレシピテーブルのレコードをアップデートするsql文
                $sql = 'UPDATE procedures set p_recipe_id=:p_recipe_id, p_img=:p_img ';
                $sql .= 'where id=:id';
            
                // SQL文を実行する準備
                $stmt = $dbh->prepare($sql);

                // SQL（更新）の実行

                $stmt->bindParam ( ":p_recipe_id", $_POST['p_recipe_id'], PDO::PARAM_INT );
                $stmt->bindParam ( ":p_img", $_POST['p_img'], PDO::PARAM_STR );
                $stmt->bindParam ( ":id", $_POST['id'], PDO::PARAM_INT );

                // sqlを実行
                $stmt->execute ();

        
                // 処理が完了したら（confirm.php）へリダイレクト
                header("Location: ./confirm.php?id=" . $_POST['p_recipe_id']);
                exit;


            } catch (PDOException $e) {
                echo 'proceduresのDBに接続できません: ',  $e->getMessage(), "\n";
                var_dump($e);
                echo $e->getMessage();
                exit;
            }

      }

    // 画像選択フォームが空だったら、入力されたdiscriptions だけの更新を行い、
    // 画像ファイルの'空'の上書き、及び更新は行わない
} elseif (empty($fileName) ) {
    


                // 調理手順＊テーブルへ// インスタンス生成
                $db_procedures = new Procedures(); 

                try {



                    // データベースに接続するための文字列（DSN 接続文字列）
                    $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
                
                    // PDOクラスのインスタンスを作る
                    // 引数は、上記のDSN、データベースのユーザー名、パスワード
                    // XAMPPの場合はデフォルトでパスワードなし、MAMPの場合は「root」
                    $dbh = new PDO($dsn, 'root', '');
                
                    // エラーが起きたときのモードを指定する
                    // 「PDO::ERRMODE_EXCEPTION」を指定すると、エラー発生時に例外がスローされる
                    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                
    
                    // マイレシピ☆テーブルのレコードをアップデートするsql文
                    $sql = 'UPDATE procedures SET p_recipe_id=:p_recipe_id, ';
                    $sql .= 'descriptions=:descriptions ';
                    $sql .= 'where id=:id';
                
                    // SQL文を実行する準備
                    $stmt = $dbh->prepare($sql);

                    // SQL（更新）の実行

                    $stmt->bindParam ( ":p_recipe_id", $_POST['p_recipe_id'], PDO::PARAM_INT );
                    $stmt->bindParam ( ":descriptions", $_POST['descriptions'], PDO::PARAM_STR );
                    $stmt->bindParam ( ":id", $_POST['id'], PDO::PARAM_INT );

                    // sqlを実行
                    $stmt->execute ();
                    // 更新行の取得
                    // $result = $stmt->rowCount ();  
                           
                    // 処理が完了したら（confirm.php）へリダイレクト
                    header("Location: ./confirm.php?id=" . $_POST['p_recipe_id']);
            
                    exit;


                } catch (PDOException $e) {
                    echo 'proceduresのDBに接続できません: ',  $e->getMessage(), "\n";
                    var_dump($e);
                  
                    exit;
                }
    
} else {

    // なにもしない
                     
}
    ?>