<?php
// ここで、ユーザーが作成したカテゴリをcategoriesテーブルに登録します。
session_start();

 
$id = $_SESSION['recipe_id'];

// 1.エラー内容:親カテゴリーID⓵を選択しても、
// foreachの最後の値⑤が選択されてしまう。
// 対応方法:
// <td><input type="radio" name="id[]" value='<echo $v['id'] ></td>
// 2.正しく挿入されない問題 ↓foreachで配列をぐるぐる回してバインドする
// array(4) {
//     ["users_id"]=>
//     string(2) "10"
//     ["id"]=>
//     array(1) { ->この親カテゴリーIDは配列だから、インサート時に配列foreachでまわす。
//       [0]=>
//       string(1) "6"
//     }
//     ["categories_name"]=>
//     string(15) "薬膳カレー"
//     ["send"]=>
//     string(30) "新規カテゴリーの追加"
//   }
// プルダウンの問題 /edit/recipe/confirm.php のカテゴリ登録も同様の不具合
// あと、戻る時、php?=id でしても、confirmに戻れない謎。

$parentCategoryId = $_SESSION['categories']['id'];



 try {
// 2021/07/05
        //⓵radio update文 唯一無二なので、アップデートできる
        //⓶chebox  複数選択なので、一旦今あるのをdelete文でカラムを削除して、新たにインサートし直す
        //     1）中間テーブルのcategoryカラムの該当レコードを削除
        //     2）INSERT文を書く
        // Delete文  DELETE FROM recipe_categories WHERE category_id = 10; (例)
        // var_dump($_GET['id']);
        // $category_id = $_GET['id']; GETから受け取るカテゴリーidの変数をDELETE文に代入する
        

        // ⓶checkboxの✅した  1）中間テーブルのcategoryカラムの該当レコードを削除

         // $sql = "DELETE FROM recipe_categories WHERE category_id = $category_id
        // AND recipe_id = $myrecipeId ";

        // $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // $stmt = $dbh->prepare($sql);
    
        // $stmt->execute();


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