<?php 

try {


$dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';


$dbh = new PDO($dsn,'root','');

$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

// $_POST['del'] (index.php91行目、 name="del" →チェックボックスのname属性）
if(isset($_POST["del"])) {


// データベースmaterial_categoriesのカラム'id'を削除します。
// 「:id」の部分は「パラメータ」といいます。データベースのidカラムです。
$sql = 'DELETE FROM recipe_categories WHERE category_id=:category_id';


//SQL文を実行する準備をします。
$stmt = $dbh->prepare($sql);

$stmt->bindValue(':category_id', $_POST['category_id'],PDO::PARAM_INT);

$stmt->execute();


} else {



$sql = 'UPDATE recipe_categories ';

$sql .= 'WHERE category_id=:category_id';


//SQL文を実行する準備をします。
$stmt = $dbh->prepare($sql);

$stmt->bindValue(':category_id', $_POST["category_id"],PDO::PARAM_INT);

$stmt->execute();

}


} catch (PDOException $e) {
    echo 'proceduresのDBに接続できません: ',  $e->getMessage(), "\n";
    echo '<pre>';
    var_dump($e);
    echo '</pre>';
    echo $e->getMessage();
    exit;
}


  // 処理が完了したら（confirm.php）へリダイレクト
  header("Location: ./confirm.php?id=" .$_POST['my_recipe_id']);
  exit;

?>


