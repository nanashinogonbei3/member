<?php
session_start();


// 必要なファイルを読み込む
require_once('../class/db/Base.php');
require_once('../class/db/CreateRecipes.php');

// 送信データを受け取る.(ログインメンバーのid)
$id = $_POST;
// 1ページの$list でFETCH ALL の表示数
define('max_view', 7);


try {


  $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
  $date = $dt->format('Y-m-d');

  //データに接続するための文字列
  $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

  $dbh = new PDO($dsn, 'root', '');

  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



  // 全てのメンバーが作った公開中のレシピをFETCH()で取り出して、全部見られるようにします。
  // my_recipesテーブルとmembersテーブルをリレーションする。
  // 宣言して分けるためです。

  $sql = "SELECT my_recipes.id as recipe_id, my_recipes.recipe_name, my_recipes.complete_img,
             members.id, members.nickname, my_recipes.update_time 
            FROM my_recipes, members WHERE my_recipes.members_id = members.id AND is_released = 1 AND my_recipes.is_deleted = 0
            ORDER BY my_recipes.created_date DESC";
  // レシピ名にリンクを付けました
  // 長いsql文になったのは、my_recipes.id のid とmembers.idが混同したのを、my_recipes.idはrecipe_idですと

  $stmt = $dbh->prepare($sql);

  $stmt->execute();

  $result = $dbh->query($sql);

  $list = $result->fetchAll(PDO::FETCH_ASSOC);

  foreach ($list as $v) {

    $recipeId = $v['recipe_id'];
  }




  // ここから、マイレシピのページングの処理
  $total_count = count($list);

  // トータルデータ件数
  $pages = ceil($total_count / max_view);
  // トータルページ数※ceilは小数点を切り捨てる関数


  //現在いるページのページ番号を取得
  if (!isset($_GET['page_id'])) {
    $now = 1;
  } else {
    $now = $_GET['page_id'];
  }

  // ページネーションの1ページ目のsqlの処理・1ページ以外のsqlの処理
  //表示するページを取得するSQLを準備

  $select = $dbh->prepare("SELECT my_recipes.id as recipe_id, my_recipes.recipe_name, 
            my_recipes.complete_img, members.id, members.nickname, my_recipes.update_time 
            FROM my_recipes, members WHERE my_recipes.members_id = members.id 
            AND is_released = 1 ORDER BY my_recipes.id DESC LIMIT :start,:max ");

  if ($now == 1) {
    //1ページ目の処理
    $select->bindValue(":start", $now - 1, PDO::PARAM_INT);
    $select->bindValue(":max", max_view, PDO::PARAM_INT);
  } else {
    //1ページ目以外の処理
    $select->bindValue(":start", ($now - 1) * max_view, PDO::PARAM_INT);
    $select->bindValue(":max", max_view, PDO::PARAM_INT);
  }
  //実行し結果を取り出しておく
  $select->execute();
  $data = $select->fetchAll(PDO::FETCH_ASSOC);


  // 調理手順テーブル
  $sql = "SELECT * FROM procedures, my_recipes WHERE procedures.p_recipe_id = my_recipes.id AND is_released = 1
            ORDER BY update_time";

  $stmt = $dbh->prepare($sql);

  $stmt->execute();

  $result = $dbh->query($sql);

  $report = $result->fetchAll(PDO::FETCH_ASSOC);


  // プルダウン検索のカテゴリをFETCHする

  //  親カテゴリーの階層ごとに登録済みの、子カテゴリー名とIDをFETCHする
  // 子カテゴリー (1) [カレー・国]

  $sql = "SELECT  categories.id, categories.categories_name, 
            categories.parent_category_id
            FROM parent_categories JOIN categories ON parent_categories.id = 
            categories.parent_category_id 
            WHERE parent_categories.id = 1 AND categories.is_deleted = 0 
            AND categories.users_id = 56 ";
  // 管理者が作ったカテゴリーIDだけを表示する


  $stmt = $dbh->prepare($sql);

  $stmt->execute();

  $result = $dbh->query($sql);

  $category = $result->fetchAll(PDO::FETCH_ASSOC);



  //  親カテゴリーID(2)階層下に登録済みの、子カテゴリー名とIDをFETCHする
  // 子カテゴリー (2) 副菜・おかず
  // 管理者が作ったカテゴリーIDだけを表示する
  $sql = "SELECT  categories.id, categories.categories_name, 
            categories.parent_category_id
            FROM parent_categories JOIN categories ON parent_categories.id = 
            categories.parent_category_id WHERE parent_categories.id = 2 AND categories.is_deleted = 0 
            AND categories.users_id = 56";



  $stmt = $dbh->prepare($sql);

  $stmt->execute();

  $result = $dbh->query($sql);

  $category2 = $result->fetchAll(PDO::FETCH_ASSOC);



  //  親カテゴリーID(3)の階層下に登録済みの、子カテゴリー名とIDをFETCHする       
  // カテゴリー (3) 具材・カレーの色
  // 管理者が作ったカテゴリーIDだけを表示する
  $sql = "SELECT  categories.id, categories.categories_name, 
            categories.parent_category_id
            FROM parent_categories JOIN categories ON parent_categories.id = 
            categories.parent_category_id WHERE parent_categories.id = 3 AND categories.is_deleted = 0 
            AND categories.users_id = 56 ";


  $stmt = $dbh->prepare($sql);

  $stmt->execute();

  $result = $dbh->query($sql);

  $category3 = $result->fetchAll(PDO::FETCH_ASSOC);


  //  親カテゴリーID(4)の階層下に登録済みの、子カテゴリー名とIDをFETCHする   

  //  (4) ナン/ライス
  // 管理者が作ったカテゴリーIDだけを表示する
  $sql = "SELECT  categories.id, categories.categories_name, 
            categories.parent_category_id
            FROM parent_categories JOIN categories ON parent_categories.id = 
            categories.parent_category_id WHERE parent_categories.id = 4 AND categories.is_deleted = 0 
            AND categories.users_id = 56 ";



  $stmt = $dbh->prepare($sql);

  $stmt->execute();

  $result = $dbh->query($sql);

  $category4 = $result->fetchAll(PDO::FETCH_ASSOC);




  //  親カテゴリーID(5)階層下に登録済みの、子カテゴリー名とIDをFETCHする   

  //  (5) スィーツ・飲み物
  // 管理者が作ったカテゴリーIDだけを表示する
  $sql = "SELECT  categories.id, categories.categories_name, 
            categories.parent_category_id
            FROM parent_categories JOIN categories ON parent_categories.id = 
            categories.parent_category_id WHERE parent_categories.id = 5 AND categories.is_deleted = 0 
            AND categories.users_id = 56 ";



  $stmt = $dbh->prepare($sql);

  $stmt->execute();

  $result = $dbh->query($sql);

  $category5 = $result->fetchAll(PDO::FETCH_ASSOC);


  //  親カテゴリーID(6)の階層下に登録済みの、子供カテゴリーをFETCHする

  //  (6) [趣向のカレー]

  $sql = "SELECT  categories.id, categories.categories_name, 
            categories.parent_category_id
            FROM parent_categories JOIN categories ON parent_categories.id = 
            categories.parent_category_id 
            WHERE parent_categories.id = 6 AND categories.is_deleted = 0 ";



  $stmt = $dbh->prepare($sql);

  $stmt->execute();

  $result = $dbh->query($sql);

  $category6 = $result->fetchAll(PDO::FETCH_ASSOC);

  // 親カテゴリーテーブル おわり


} catch (Exception $e) {
  echo 'DBに接続できません: ',  $e->getMessage(), "\n";
  echo '<pre>';
  var_dump($e);
  echo '</pre>';
  exit;
}

?>

<!DOCTYPE html>
<html lang="jp">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>レシピノート トップページ</title>

  <!-- フォント -->
  <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
  <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
  <!-- google おしゃれ日本語漢字フォント -->
  <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />

  <!-- 全体 -->
  <link rel="stylesheet" href="css/stylesheet2_2.css">
  <!--  ログアウト・ボタン -->
  <link rel="stylesheet" href="css/logout_btn.css">
  <!--  サムネイル画像 -->
  <link rel="stylesheet" href="css/stylesheet1_2.css">
  <!--  タブ -->
  <link rel="stylesheet" href="css/stylesheet3.css">
  <!--  ページネーション -->
  <link rel="stylesheet" href="css/style_paging.css">
  <!--  虫眼鏡検索バー -->
  <link rel="stylesheet" href="css/stylesheet7.css">
  <!--  アコーディオン検索バー -->
  <link rel="stylesheet" href="./css/stylesheet_a.css">


</head>



<body>


  <div class='div_p'>my recipes notes

    <!-- マイページ -->
    <div class="div_logout1">
      <input type="button" value='マイページ' class="logout_btn" onclick="location.href='../login/join.php'">

    </div>

    <!-- ログイン -->
    <div class="div_logout1">
      <input type="button" value='ログイン' class="logout_btn" onclick="location.href='../login/join.php'">

    </div>


    <!-- 虫眼鏡 検索バー -->

    <!-- ここにレシピアイテム検索ツールがはいります -->
    <form action="serch_mushimegane.php" method="GET">
      <!-- 検索ワード入力画面 -->


      <!-- 虫眼鏡検索で何か入力がされたら、 -->
      <input type="text" name="serch" value="" placeholder='レシピ/カテゴリ/ニックネーム' style="width:200px;" />

      <!-- 虫眼鏡のボタン -->
      <button type="mushimegane" class="form-button" type="submit"><img id="mushimegane" src="./css_img/mushimegane.png">
      </button>

      <!-- ↓ div_mushimegane おわり -->
  </div>
  </form>


  <!-- エラー 検索結果が無い時 -->
  <div class="div_error">
    <?php
    if (!empty($_SESSION['error'])) {
      // 虫眼鏡検索の検索結果のエラーmessageを変数に受け取る /serch_mushimegane.php
      $error = $_SESSION['error'];
      // エラーを表示
      echo "<dt>" . "$error" . "</dt>"; ?>
      <form action="" method="POST">
        <?php if (isset($_POST['clear'])) : ?>
          <?php unset($_SESSION['error']); ?>
        <?php endif ?>
        <!-- 「クリア」ボタンでエラーメッセージを削除 -->
        <input type="submit" name="clear" value="クリア" />
      </form>



    <?php } elseif (!empty($_SESSION['error1'])) {

      // 食材✅検索結果のエラーmessageを変数に受け取る /serch_material.php
      $error1 = $_SESSION['error1'];
      // エラーを表示
      echo "<dt>" . "$error1" . "</dt>"; ?>
      <form action="" method="POST">
        <?php if (isset($_POST['clear'])) : ?>
          <?php unset($_SESSION['error1']); ?>
        <?php endif ?>
        <!-- 「クリア」ボタンでエラーメッセージを削除 -->
        <input type="submit" name="clear" value="クリア" />
      </form>

    <?php } elseif (!empty($_SESSION['error2'])) {

      // カテゴリー✅ボックスのカテゴリ検索した結果を受け取る ./serch_multiple.php
      $error2 = $_SESSION['error2'];
      // エラーを表示
      echo "<dt>" . "$error2" . "</dt>"; ?>
      <form action="" method="POST">
        <?php if (isset($_POST['clear'])) : ?>
          <?php unset($_SESSION['error2']); ?>
        <?php endif ?>
        <!-- 「クリア」ボタンでエラーメッセージを削除 -->
        <input type="submit" name="clear" value="クリア" />
      </form>

    <?php } elseif (!empty($_SESSION['error3'])) {

      // 食材テキスト入力フォーム検索結果を受け取る serch_material_multiple.php'(テキストフォーム)
      $error3 = $_SESSION['error3'];
      echo "<dt>" . "$error3" . "</dt>"; ?>
      <form action="" method="POST">
        <?php if (isset($_POST['clear'])) : ?>
          <?php unset($_SESSION['error3']); ?>
        <?php endif ?>
        <!-- 「クリア」ボタンでエラーメッセージを削除 -->
        <input type="submit" name="clear" value="クリア" />
      </form>

    <?php } elseif (!empty($_SESSION['error5'])) {

      // メンバーの「ニックネームのテキスト入力フォーム」の検索結果を受け取る。 serch_member.phpの検索結果
      $error5 = $_SESSION['error5'];
      echo "<dt>" . "$error5" . "</dt>"; ?>
      <form action="" method="POST">
        <?php if (isset($_POST['clear'])) : ?>
          <?php unset($_SESSION['error5']); ?>
        <?php endif ?>
        <!-- 「クリア」ボタンでエラーメッセージを削除 -->
        <input type="submit" name="clear" value="クリア" />
      </form>

    <?php } elseif (!empty($_SESSION['error6'])) {

      // レシピIDをテキスト入力した検索結果を受け取る。serch_recipe_id.php
      $error6 = $_SESSION['error6'];
      echo "<dt>" . "$error6" . "</dt>"; ?>
      <form action="" method="POST">
        <?php if (isset($_POST['clear'])) : ?>
          <?php unset($_SESSION['error6']); ?>
        <?php endif ?>
        <!-- 「クリア」ボタンでエラーメッセージを削除 -->
        <input type="submit" name="clear" value="クリア" />
      </form>

    <?php } elseif (!empty($_SESSION['product_error'])) {

      // 商品名テキスト入力した検索結果を受け取る。(serch_products.php)
      $productError = $_SESSION['product_error'];
      echo "<dt>" . "$productError" . "</dt>"; ?>
      <form action="" method="POST"> ?>
        <?php if (isset($_POST['clear'])) : ?>
          <?php unset($_SESSION['product_error']); ?>
        <?php endif ?>
        <!-- セッション破棄（結果の削除） -->
        <input type="submit" name="clear" value="クリア" />
      </form>

    <?php } ?>


    <!-- div_error おわり -->
  </div>


  <!-- ↓ div_p おわり -->
  </div>

  <div class='inline_block_2'>

    <div class="comprehensive">

      <!-- SIDE はじまり -->
      <div class="side">

        <!-- ---アコーディオン検索バーのはじまり-- -->

        <div class="div_width">

          <!-- 1 -->
          <input id="acd-check1" class="acd-check" type="checkbox">
          <label class="acd-label" for="acd-check1">

            <!-- 検索アイコン画像虫眼鏡 -->
            <img id="acd" src="../icon_img/serch3.png" width="21px" height="auto">
            <img id="acd" src="../icon_img/3country.png" width="21px" height="auto">
            レシピ / カレーと国
          </label>
          <div class="acd-content">


            <!-- フォーム検索画面遷移⓵ -->
            <form action="serch_multiple.php" method="GET">

              <!-- 国・地域 -->
              <dt>●国・地域</dt>

              <!-- 国・地域 -->
              <?php foreach ($category as $v) : ?>
                <table>
                  <tr>
                    <td><input type="checkbox" name="category_id[]" value='<?php echo $v['id']  ?>'></td>
                    <td><?php echo $v['categories_name'] ?></td>
                  </tr>
                </table>
              <?php endforeach ?>
              </table>
              <pre></pre>


              <!-- 具材 -->

              <dt>●具材・色</dt>
              <?php foreach ($category3 as $v) : ?>
                <table class="table">
                  <tr>
                    <td><input type="checkbox" name="category_id[]" value='<?php echo $v['id']  ?>'></td>
                    <td><?php echo $v['categories_name'] ?></td>
                  </tr>
                </table>
              <?php endforeach ?>
              <pre></pre>



              <!-- 趣向のカレー -->
              <dt>●趣向のカレー</dt>
              <?php foreach ($category6 as $v) : ?>
                <table class="table">
                  <tr>
                    <td><input type="checkbox" name="category_id[]" value='<?php echo $v['id'] ?>'></td>
                    <td><?php echo $v['categories_name'] ?></td>
                  </tr>
                </table>
              <?php endforeach ?>
              <pre></pre>



              <dt>●副菜とおかず</dt>
              <?php foreach ($category2 as $v) : ?>
                <table>
                  <tr>
                    <td><input type="checkbox" name="category_id[]" value='<?php echo $v['id'] ?>'></td>
                    <td><?php echo $v['categories_name'] ?></td>
                  </tr>
                </table>
              <?php endforeach ?>
              <pre></pre>


              <!-- 送信ボタン⓵ボタン -->
              <div class="btn">
                <input type="submit" value="検索" class="btn-border">
                <input type="reset" value="リセット">
              </div>

          </div>
          </form>
          <!-- ⓵のフォームおわり -->



          <!-- ２ -->

          <input id="acd-check2" class="acd-check" type="checkbox">
          <label class="acd-label" for="acd-check2">
            <!-- アイコン画像・にんじん・ミート -->
            <img id="acd" src="../icon_img/carot.png" width="24px" height="auto">
            <img id="acd" src="../icon_img/meet.png" width="24px" height="auto">
            &nbsp;食材
          </label>
          <div class="acd-content">

            <form action="serch_material.php" method="GET">
              <table class="table">
                <dt>●冷蔵庫にある食材でさがす</dt>
                <tr>
                  <td><input type="checkbox" name="material[]" value='ニンジン' id=""></td>
                  <td class="menu">ニンジン</td>
                </tr>
                <tr>
                  <td><input type="checkbox" name="material[]" value='ピザ用チーズ' id=""></td>
                  <td class="menu">ピザ用チーズ</td>
                </tr>
                <tr>
                  <td><input type="checkbox" name="material[]" value='小麦粉' id=""></td>
                  <td class="menu">小麦粉</td>
                </tr>
                <tr>
                  <td><input type="checkbox" name="material[]" value='全粒粉' id=""></td>
                  <td class="menu">全粒粉</td>
                </tr>
                <tr>
                  <td><input type="checkbox" name="material[]" value='ほうれん草' id=""></td>
                  <td class="menu">ほうれん草</td>
                </tr>
                <tr>
                  <td><input type="checkbox" name="material[]" value='牛乳' id=""></td>
                  <td class="menu">牛乳</td>
                </tr>
                <tr>
                  <td><input type="checkbox" name="material[]" value='トマト' id=""></td>
                  <td class="menu">トマト</td>
                </tr>
              </table>

              <!-- 送信ボタン⓶ボタン -->
              <div class="btn">
                <input type="submit" value="検索" class="btn-border">
                <input type="reset" value="リセット">
              </div>

            </form>
            <!-- 2のフォームおわり -->


            <!-- 3 -->


            <!-- 食材で選ぶ -->
            <!-- フォーム検索画面遷移③ -->

            <form action="serch_material_multiple.php" method="GET">
              <table>
                <dt>●食材で選ぶ</dt>
                <td><input id="material_text" type="text" name="material" value='' size="20" maxlength="20" placeholder='食材で選ぶ' maxength="255"></td>
                <td class="menu"></td>
              </table>
              <!-- 送信ボタン③ボタン -->
              <div class="btn">
                <input type="submit" value="検索" class="btn-border">
                <input type="reset" value="リセット">
              </div>
            </form>

            <pre></pre>


            <!-- ③のフォームおわり -->



            <!-- 4 -->


            <!-- フォーム検索画面遷移⓸ -->
            <form action="serch_material.php" method="GET">
              <!-- 鶏肉 -->

              <table class="table">
                <dt>●鶏肉 / ボーク / 魚介類</dt>
                <tr>
                  <td><input type="checkbox" name="material[]" value='鶏肉' id=""></td>
                  <td class="menu">鶏肉</td>
                </tr>
                <tr>
                  <td><input type="checkbox" name="material[]" value='豚肉' id=""></td>
                  <td class="menu">豚肉</td>
                </tr>
                <tr>
                  <td><input type="checkbox" name="material[]" value='牛肉' id=""></td>
                  <td class="menu">牛肉</td>
                </tr>
                <tr>
                  <td><input type="checkbox" name="material[]" value='シーフード' id=""></td>
                  <td class="menu">シーフード</td>
                </tr>
              </table>
              <pre></pre>

              <!-- 送信ボタン⓸ボタン -->
              <div class="btn">
                <input type="submit" value="検索" class="btn-border"></a>
                <input type="reset" value="リセット">
              </div>
              <pre></pre>
          </div>
          <!-- ⓸のフォームおわり-->
          </form>


          <!-- 5 -->

          <!-- フォーム検索画面遷移⑤ -->
          <form action="serch_material.php" method="GET">

            <input id="acd-check3" class="acd-check" type="checkbox">
            <label class="acd-label" for="acd-check3">
              <!-- アイコン画像・唐辛子・ローレルの葉 -->
              <img id="acd" src="../icon_img/papper.png" width="16px" height="auto">
              <img id="acd" src="../icon_img/lorel.png" width="18px" height="auto">
              スパイス
            </label>
            <div class="acd-content">

              <!-- スパイス -->
              <table class="table">
                <dt>●スパイス</dt>
                <tr>
                  <?php $turmeric = 'ターメリック' ?>
                  <td><input type="checkbox" name="material[]" value='ターメリック' id=""></td>
                  <td class="menu">ターメリック</td>
                </tr>
                <tr>
                  <td><input type="checkbox" name="material[]" value='カルダモン' id=""></td>
                  <td class="menu">カルダモン</td>
                </tr>
                <tr>
                  <td><input type="checkbox" name="material[]" value='クミン' id=""></td>
                  <td class="menu">クミン</td>
                </tr>
                <tr>
                  <td><input type="checkbox" name="material[]" value='カイエンヌペッパー' id=""></td>
                  <td class="menu">カイエンヌペッパー</td>
                </tr>
                <!--  -->
                <tr>
                  <td><input type="checkbox" name="material[]" value='チリペッパー' id=""></td>
                  <td class="menu">チリペッパー</td>
                </tr>
                <tr>
                  <td><input type="checkbox" name="material[]" value='シナモン' id=""></td>
                  <td class="menu">シナモン</td>
                </tr>
                <tr>
                  <td><input type="checkbox" name="material[]" value='フローブ' id=""></td>
                  <td class="menu">クローブ</td>
                </tr>
                <tr>
                  <td><input type="checkbox" name="material[]" value='マスタードシード' id=""></td>
                  <td class="menu">マスタードシード</td>
                </tr>

                <!--  -->
                <tr>
                  <td><input type="checkbox" name="material[]" value='コリアンダーパウダー' id=""></td>
                  <td class="menu">コリアンダーパウダー</td>
                </tr>

                <!--  -->
                <tr>
                  <td><input type="checkbox" name="material[]" value='レッドチリパウダー' id=""></td>
                  <td class="menu">レッドチリパウダー</td>
                </tr>
                <tr>
                  <td><input type="checkbox" name="material[]" value='コリアンダーシード' id=""></td>
                  <td class="menu">コリアンダーシード</td>
                </tr>
                <tr>
                  <td><input type="checkbox" name="material[]" value='フェヌグリーク' id=""></td>
                  <td class="menu">フェヌグリーク</td>
                </tr>
                <tr>
                  <td><input type="checkbox" name="material[]" value='ティムール' id=""></td>
                  <td class="menu">ティムール</td>
                </tr>
                <!--  -->
                <tr>
                  <td><input type="checkbox" name="material[]" value='メース' id=""></td>
                  <td class="menu">メース</td>
                </tr>
                <tr>
                  <td><input type="checkbox" name="material[]" value='カルダモンパウダー' id=""></td>
                  <td class="menu">カルダモンパウダー</td>
                </tr>
                <tr>
                  <td><input type="checkbox" name="material[]" value='パプリカパウダー' id=""></td>
                  <td class="menu">パプリカパウダー</td>
                </tr>
                <tr>
                  <td><input type="checkbox" name="material[]" value='カレーリーフ' id=""></td>
                  <td class="menu">カレーリーフ</td>
                </tr>
                <tr>
                  <td><input type="checkbox" name="material[]" value='ローリエ' id=""></td>
                  <td class="menu">ローリエ</td>
                </tr>


              </table>
              <pre></pre>

              <!-- 送信ボタン⑤ボタン -->
              <div class="btn">
                <input type="submit" value="検索" class="btn-border"></a>
                <input type="reset" value="リセット">
              </div>

            </div>
          </form>
          <!-- ⑤のフォームおわり -->


          <!-- 6 -->

          <input id="acd-check4" class="acd-check" type="checkbox">
          <label class="acd-label" for="acd-check4">
            <!-- アイコン画像・ナン -->
            <img id="acd" src="../icon_img/naan.png" width="31px" height="auto">

            ナン
          </label>
          <div class="acd-content">

            <!-- フォーム検索画面遷移⑥ -->
            <form action="serch_multiple.php" method="GET">

              <!-- ナン・ライス -->
              <?php foreach ($category4 as $v) : ?>
                <table class="table">
                  <tr>
                    <td><input type="checkbox" name="category_id[]" value='<?php echo $v['id'] ?>'></td>
                    <td><?php echo $v['categories_name'] ?></td>
                    <input type="hidden" name="categories_name" value="<?php echo $v['categories_name'] ?>">
                  </tr>
                </table>
              <?php endforeach ?>


              <pre></pre>

              <!-- 送信ボタン⑥ボタン -->
              <div class="btn">
                <input type="submit" value="検索" class="btn-border"></a>
                <input type="reset" value="リセット">
              </div>

          </div>
          </form>
          <!-- 検索フォーム⑥おわり -->


          <!-- 7 -->

          <input id="acd-check5" class="acd-check" type="checkbox">
          <label class="acd-label" for="acd-check5">
            <!-- アイコン画像・スイーツ -->
            <img id="acd" src="../icon_img/sweet.png" width="24px" height="auto">
            <img id="acd" src="../icon_img/cofee.png" width="24px" height="auto">
            スイーツ / 飲み物
          </label>
          <div class="acd-content">


            <!-- フォーム検索画面遷移⑦ -->
            <form action="serch_multiple.php" method="GET">

              <!--  スイーツ/飲み物 -->

              <dt>●デザート</dt>
              <?php foreach ($category5 as $v) : ?>
                <table class="table">
                  <tr>
                    <td><input type="checkbox" name="category_id[]" value='<?php echo $v['id'] ?>'></td>
                    <td><?php echo $v['categories_name'] ?></td>
                  </tr>
                </table>
              <?php endforeach ?>
              <pre></pre>

              <!-- 送信ボタン⑦ボタン -->
              <div class="btn">
                <input type="submit" value="検索" class="btn-border"></a>
                <input type="reset" value="リセット">
              </div>

          </div>
          </form>
          <!-- 検索フォーム⑦おわり -->



          <!-- 8 -->

          <input id="acd-check6" class="acd-check" type="checkbox">
          <label class="acd-label" for="acd-check6">
            <!-- アイコン画像・メンバー人ｓ -->
            <img id="acd" src="../icon_img/members.png" width="16px" height="auto">
            メンバー
          </label>
          <div class="acd-content">
            <!-- フォーム検索画面遷移⑦ -->

            <form action="serch_member.php" method="GET">

              <!-- ユーザーで探す -->
              <table class="table">
                <dt>●メンバーのレシピ検索</dt>
                <tr>
                  <td>
                    <input id="material_text" type="text" name="name" value='' size="20" maxlength="20" placeholder='ニックネームで探す' maxength="255">
                  </td>

                </tr>
              </table>
              <!-- ユーザーで探す -->

              <pre></pre>

              <!-- 送信ボタン⓼ボタン -->
              <div class="btn">
                <input type="submit" value="検索" class="btn-border"></a>
                <input type="reset" value="リセット">
              </div>

          </div>
          </form>



          <!-- ⓽ -->


          <input id="acd-check7" class="acd-check" type="checkbox">
          <label class="acd-label" for="acd-check7">
            <!-- アイコン画像・料理のアイコン画像 -->
            <img id="acd" src="../icon_img/curry.png" width="16px" height="auto">
            <img id="acd" src="../icon_img/curry3.png" width="16px" height="auto">
            <!-- レシピIDで検索する、テキスト入力フォームをつくる -->
            レシピID
          </label>
          <div class="acd-content">
            <!-- フォーム検索画面遷移⓽ -->

            <form action="serch_recipe_id.php" method="GET">

              <!-- ユーザーで探す -->
              <table class="table">
                <dt>●レシピID検索</dt>
                <tr>
                  <td><input id="material_text" type="text" name="serch" value='' size="20" maxlength="20" placeholder='レシピIDで探す' maxength="255"></td>
                  <td></td>

                </tr>
              </table>
              <!-- レシピIDで探す・おわり -->

              <pre></pre>

              <!-- 送信ボタン⓽ボタン -->
              <div class="btn">
                <input type="submit" value="検索" class="btn-border"></a>
                <input type="reset" value="リセット">
              </div>

          </div>
          </form>





          <!-- 10 -->


          <input id="acd-check8" class="acd-check" type="checkbox">
          <label class="acd-label" for="acd-check8">
            <!-- アイコン画像 -->
            <!-- <img id="acd" src="../icon_img/" width="24px" height="auto"> -->

            /
          </label>
          <div class="acd-content">


            <!-- フォーム検索画面遷移１０ -->
            <form action="serch_multiple.php" method="GET">

              <!--  商品アイテム-->

              <dt>●</dt>
              <?php foreach ($category5 as $v) : ?>
                <table class="table">
                  <tr>
                    <td><input type="checkbox" name="category_id[]" value='<?php echo $v['id'] ?>'></td>
                    <td><?php echo $v['categories_name'] ?></td>
                  </tr>
                </table>
              <?php endforeach ?>
              <pre></pre>

              <!-- 送信ボタン１０ボタン -->
              <div class="btn">
                <input type="submit" value="検索" class="btn-border"></a>
                <input type="reset" value="リセット">
              </div>

          </div>
          </form>
          <!-- 検索フォーム１０おわり -->




          <!-- 新１１ -->


          <input id="acd-check9" class="acd-check" type="checkbox">
          <label class="acd-label" for="acd-check9">
            <!-- アイコン画像・旅行-->
            <!-- <img id="acd" src="../icon_img/hikouki.png" width="24px" height="auto"> -->

            /
          </label>
          <div class="acd-content">


            <!-- フォーム検索画面遷移１1 -->
            <form action="serch_multiple.php" method="GET">

              <!--  スイーツ/飲み物 -->

              <dt>●インド</dt>
              <?php foreach ($category5 as $v) : ?>
                <table class="table">
                  <tr>
                    <td><input type="checkbox" name="category_id[]" value='<?php echo $v['id'] ?>'></td>
                    <td><?php echo $v['categories_name'] ?></td>
                  </tr>
                </table>
              <?php endforeach ?>
              <pre></pre>

              <!-- 送信ボタン１１ボタン -->
              <div class="btn">
                <input type="submit" value="検索" class="btn-border"></a>
                <input type="reset" value="リセット">
              </div>

          </div>
          </form>
          <!-- 検索フォーム１１おわり -->





          <!-- 新１２ -->


          <input id="acd-check10" class="acd-check" type="checkbox">
          <label class="acd-label" for="acd-check10">
            <!-- アイコン画像・? -->
            <!-- <img id="acd" src="../icon_img/.png" width="24px" height="auto"> -->

            /
          </label>
          <div class="acd-content">


            <!-- フォーム検索画面遷移１２ -->
            <form action="serch_multiple.php" method="GET">

              <!--   -->

              <dt>●</dt>
              <?php foreach ($category5 as $v) : ?>
                <table class="table">
                  <tr>
                    <td><input type="checkbox" name="category_id[]" value='<?php echo $v['id'] ?>'></td>
                    <td><?php echo $v['categories_name'] ?></td>
                  </tr>
                </table>
              <?php endforeach ?>
              <pre></pre>

              <!-- 送信ボタン⑦ボタン -->
              <div class="btn">
                <input type="submit" value="検索" class="btn-border"></a>
                <input type="reset" value="リセット">
              </div>

          </div>
          </form>

          <!-- div widthおわり -->
        </div>



        <!-- -------------------------------------------------------------------- -->
        <!-- タブ -->
        <div class="block1">


          <div class="div_hidari">

            <div class="tabs">
              <input id="all" type="radio" value="./index.php" onclick="location.href=this.value" name="tab_item" checked>
              <label class="tab_item" for="all">みんなのレシピ</label>
              <!-- for="all" みんなのレシピ -->
              <!-- もし未ログインなら -->
              <?php if (empty($_SESSION['member'])) { ?>
                <input id="programming" type="radio" value="../login/join.php" onclick="location.href=this.value" name="tab_item">
                <!-- 既ログインなら -->
              <?php } ?>
              <label class="tab_item" for="programming">わたしのレシピ</label>
              <!-- for="programming" わたしのレシピ -->
              <input id="design" type="radio" value="../login/join.php" onclick="location.href=this.value" name="tab_item">
              <label class="tab_item" for="design">レシピヲつくる</label>

              <!-- /member/logout/process.php -->
              <!-- for="design" レシピヲつくる -->

              <!-- 表示 -->
              <div class="tab_content" id="all_content">
                <span style="color:#F2F0CE">
                  みんなのレシピを紹介しています
                </span><br>
              </div>
              <div class="tab_content" id="programming_content">
                わたしの作成したレシピが表示されます
              </div>
              <div class="tab_content" id="design_content">
                レシピヲつくる
              </div>



              <!-- データベースからFETCH()した、 公開レシピの、サムネイル画像 -->
              <div class="item_l">
                <form action="../edit/recipe/release_recipe.php" method="post" enctype="multipart/form-data">
                  <div class="imageList">
                    <div class="imageList__view">
                      <input type="hidden" name="members_id">

                      <img id="img" src="../create/recipe/images/<?php echo $list[0]['complete_img'] ?>" onclick="changeimg('../create/recipe/images/<?php $list[0]['complete_img'] ?>')" />
                      <!-- 大きいサムネイル画像 -->

                    </div>
                </form>
                <!-- < echo $list[0]['recipe_name'] ?> -->
                <div id="thumb_img" class="imageList__thumbs">

                  <!-- 小さいサムネイル画像 -->
                  <?php foreach ($list as $v) : ?>
                    <div class="imageList__thumbnail selected">



                      <img id="img_s" src="../create/recipe/images/<?php echo $v['complete_img'] ?>" onclick="changeimg('../create/recipe/images/<?php echo $v['complete_img'] ?>')" />
                      <a href="./index.php" target="blank"></span></a>


                      <dt><span style="color:#ffffff;font-size:11px">
                          <?php echo $v['recipe_name'] ?></dt>
                    </div>

                  <?php endforeach ?>
                </div>
              </div>

            </div>
          </div>
          <!-- DIV 左側おわり -->



          <!-- DIV 右側はじまり -->
          <div class="div_migi">


            <div class="radio_div">

            </div>


            <div class="div_w">
              <!-- 入力フォーム 材料入力 -->
              <?php
              echo '<pre>';
              echo '<span style="color:green;padding: 1%;">' . $now . 'page/全' . $total_count . 'レシピ</span>';
              echo '</pre>';
              ?>
              <table width: 150px>
                <thead>
                  <tr>
                    <th>
                      <dt class="wf-sawarabimincho">id
                    </th>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <th>
                      <dt class="wf-sawarabimincho">レシピ名
                    </th>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <th>
                      <dt class="wf-sawarabimincho">作った人
                    </th>
                    <td>&nbsp;&nbsp;</td>
                    <td>&nbsp;
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                    <th>
                      <dt class="wf-sawarabimincho">公開日
                    </th>
                  </tr>
                </thead>

              </table>

              <!-- </div> -->
              <!-- div_w -->
            </div>

            <div class="div_w_under">

              <table>
                <thead>
                  <tr>
                    <th>
                      <dt class="wf-sawarabimincho">
                    </th>
                    <th>
                      <dt class="wf-sawarabimincho">
                    </th>
                    <th>&nbsp;</th>
                  </tr>
                </thead>

                <tbody>

                  <tr>
                    <!-- データの数だけ繰り返し -->
                    <?php foreach ($data as $val) : ?>
                <tbody>
                  <tr>
                    <!-- マイレシピ・テーブルのデータベースからFETCH()した  レシピid -->
                    <td><span style="color:green;font-size:13px"><?php echo $val['recipe_id'] ?></span>&nbsp;&nbsp;&nbsp;&nbsp;</td>

                    <!-- マイレシピ・テーブルのデータベースからFETCH()した  レシピ名 -->
                    <td width="200px"><span style="color:green;font-size:13px">

                        <?php
                        if (!empty($_SESSION['member'])) {
                        ?>

                          <a href="../edit/recipe/release_recipe.php?id=
                                        <?php echo $val['recipe_id'] ?>" style="text-decoration:none;"><?php echo $val['recipe_name'] ?>
                          </a>

                        <?php
                        } elseif (empty($_SESSION['member'])) {
                        ?>

                          <a href="../edit/recipe/release_recipe3.php?id=
                                        <?php echo $val['recipe_id'] ?>" style="text-decoration:none;"><?php echo $val['recipe_name'] ?>
                          </a>
                        <?php
                        }
                        ?>

                      </span></td>

                    <!-- メンバーズテーブルのデータベースからFETCH()した ニックネーム -->
                    <td>&nbsp;&nbsp;</td>
                    <td>&nbsp;&nbsp;</td>
                    <td class="td" width="115px" height="10px">
                      <img id="users" src="../icon_img/members.png">
                      <span style="color:green;font-size:13px"><?php echo $val['nickname'] ?></span>
                    </td>

                    <td>&nbsp;&nbsp;</td>
                    <td>&nbsp;&nbsp;</td>
                    <!-- マイ・レシピテーブルのデータベースからFETCH()した  公開日（=更新日） -->
                    <td widht="80px"><span style="color:green;font-size:13px"><?php echo $val['update_time'] ?></span></td>

            </div>
            </tr>
            </tbody>
          <?php endforeach ?>

          </tr>
          </tbody>

          </table>

          </div>


          <div class="div_w2">

            <!-- ページングCSS -->
            <div class="flex">

              <?php
              //ページネーションを表示    
              if ($now > 1) {
                // 1ページより大きいなら、「前へ」表示
                echo '<a href="?page_id=', ($now - 1), '">Prev</a>';
              } else {
                echo 'Prev';
              }
              ?>

              <ul class="bar">
                <li>
                  <?php
                  // $pages だとレシピの数が増えると1 2 3 ・・増え続けてしまうので
                  // $pages ではなく、5つの数字までの表示制限にする。1 2 3 4 5 まで表示
                  for ($n = 1; $n <= 10; $n++) {
                    if ($n == $now) {
                      // 現在表示されているページなら、リンクは付けない。
                      echo "<span style='padding: 5px;'>$now</span>";
                    } else {
                      // それ以外のページの数字には、リンクを貼る
                      echo "<a href='?page_id=$n' style='padding: 5px;'>$n</a>";
                    }
                  }
                  ?>
                </li>
              </ul>

              <?php
              if ($now < $pages) {
                // 表示ページが最終ページより小さいなら、「次へ」表示
                echo '<a href="?page_id=', ($now + 1), '">Next</a>';
              }
              ?>
            </div>

          </div>
          <!-- div_w おわり-->
        </div>


        <!-- DIV 右側おわり -->
        <!-- </div> -->
      </div>


      <!-- div class="comprehensive" おわり-->
    </div>





    <!-- Javascript ファイルを読み込む -->
    <script src="js/backup614/javascript.js"></script>

</body>

</html>