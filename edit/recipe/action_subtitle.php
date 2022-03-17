<?php 
session_start();



try{


            $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

            $dbh = new PDO($dsn,'root','');

            $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

        if (isset($_POST["del"])) {

            $sql = 'DELETE FROM recipe_subtitles WHERE id=:id';

            $stmt = $dbh->prepare($sql);

            $stmt->bindValue(':id', $_POST['id'],PDO::PARAM_INT);

            $stmt->execute();


        } else {


            $sql = 'UPDATE recipe_subtitles ';

            $sql .= 'WHERE id=:id';

            $stmt = $dbh->prepare($sql);

            $stmt->bindValue(':id', $_POST["id"],PDO::PARAM_INT);

            $stmt->execute();

        }

    } catch (PDOException $e) {
        echo 'my_recipeのDBに接続できません: ',  $e->getMessage(), "\n";
        echo '<pre>';
        var_dump($e);
        echo '</pre>';
        exit;
    }


// 処理が完了したら./confirm.phpへリダイレクト
header("Location: ./confirm.php?id=" . $_SESSION['recipe_id']);

exit;
