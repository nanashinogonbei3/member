<?php
        session_start();


        $id = $_GET['recipe_id'];


        if(empty($_GET['recipe_id']) ) {
            header("Location: ./edit_recipe_subtitle.php?id=". $_GET['recipe_id']);
            // confirm.php からmy_recipe のid で飛ばされた、$_GET['recipe_id']
            exit;


    } else {




    try{

        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
        
        $dbh = new PDO($dsn,'root','');

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // タイトルが無く、且つコメントが空で無ければ、
        if (empty($_GET['sub_title']) && !empty($_GET['comment'])) {

                $sql = 'INSERT INTO recipe_subtitles (';
                $sql .= 'comment,';
                $sql .= 'recipe_id';
                $sql .= ') values (';
                $sql .= ':comment,';
                $sql .= ':recipe_id';
                $sql .= ')';

                $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);  

                $stmt = $dbh->prepare($sql);

                $stmt->bindValue(':comment', $_GET['comment'], PDO::PARAM_STR);
                $stmt->bindValue(':recipe_id', $_GET['recipe_id'], PDO::PARAM_INT);

                $stmt->execute();

                // 処理が完了したら画面に遷移する
                header("Location: ./confirm.php?id='".$id."'");
                exit;


            // コメントが空で、タイトルがあれば、タイトルだけをインサートする
        } elseif (empty($_GET['comment']) && !empty($_GET['sub_title'])) {  


                $sql = 'INSERT INTO recipe_subtitles (sub_title, recipe_id) ';
                $sql .='values(:sub_title, :recipe_id) ';
                

                $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);  

                $stmt = $dbh->prepare($sql);

                $stmt->bindValue(':sub_title', $_GET['sub_title'], PDO::PARAM_STR);
                $stmt->bindValue(':recipe_id', $_GET['recipe_id'], PDO::PARAM_INT);

                $stmt->execute();

                // 処理が完了したら画面に遷移する
                header("Location: ./confirm.php?id='".$id."'");
                exit;


        } else {    // どちらも空で無ければ、ふたつとも両方インサートする


                $sql = 'INSERT INTO recipe_subtitles (sub_title, comment, recipe_id) ';
                $sql .='values(:sub_title, :comment, :recipe_id)';

                $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);  

                $stmt = $dbh->prepare($sql);

                $stmt->bindValue(':sub_title', $_GET['sub_title'], PDO::PARAM_STR);
                $stmt->bindValue(':comment', $_GET['comment'], PDO::PARAM_STR);
                $stmt->bindValue(':recipe_id', $_GET['recipe_id'], PDO::PARAM_INT);

                $stmt->execute();

                // 処理が完了したら画面に遷移する
                header("Location: ./confirm.php?id='".$id."'");
                exit;

        }
                        
        
    } catch (PDOException $e) {
        echo 'my_recipeのDBに接続できません: ',  $e->getMessage(), "\n";
        echo '<pre>';
        var_dump($e);
        echo '</pre>';
        exit;
    } 

}

?>