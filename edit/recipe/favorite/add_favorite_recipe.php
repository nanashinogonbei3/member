<?php
session_start();

$id = $_POST['favorite_recipe_id'];


if(empty($_POST['favorite_recipe_id']) ) {

    header("Location: ../release_recipe.php?id=".$id); 
    exit;
}

       
try {
   
        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
       
        $dbh = new PDO($dsn,'root','');

        $sql = 'INSERT INTO favorite_recipes (';
        $sql .= 'favorite_recipe_id,';
        $sql .= 'members_id,';
        $sql .= 'is_completed';
        $sql .= ') values (';
        $sql .= ':favorite_recipe_id,';
        $sql .= ':members_id,';
        $sql .= ':is_completed';
        $sql .= ')';

        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);  

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':favorite_recipe_id', $_POST['favorite_recipe_id'], PDO::PARAM_INT);
        $stmt->bindValue(':members_id', $_POST['members_id'], PDO::PARAM_INT);
        $stmt->bindValue(':is_completed', $_POST['is_completed'], PDO::PARAM_INT);

        $stmt->execute();
     
        header("Location: ../release_recipe.php?id=".$id); 
        exit;


} catch (PDOException $e) {
    echo 'categoriesのDBに接続できません: ',  $e->getMessage(), "\n";
    echo '<pre>';
    var_dump($e);
    echo '</pre>';
    echo $e->getMessage();
    exit;
}
