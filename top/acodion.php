<?php
session_start();

// 必要なファイルを読み込む
require_once('../class/db/Base.php');
require_once('../class/db/CreateRecipes.php');


// ./top/confirm.php(もしくはindex.php) の □虫眼鏡 serch_mushimegane.phpの検索結果のセッションを受け取って、変数に代入する。
if (!empty($_SESSION['serch'])) {
  $serch = $_SESSION['serch'];
  // 検索結果が無い場合は必ずエラーメッセージを受け取っているから、エラーのセッションを変数に代入しておく。
}


// カテゴリー✅ボックスのカテゴリ検索した結果を受け取る ./serch_multiple.php
if (!empty($_SESSION['multiple_id'])) {
  $multiples = $_SESSION['multiple_id'];
}


// serch_material_multiple.php'(テキストフォーム)、  および、serch_material_ahref.php(release_recipe.phpの材料リンク)
// 967行 材料'✅ボックス'結果をセッションで受け取る serch_material.phpで検索結果をセッションに格納したものを変数に渡す。
if (!empty($_SESSION['materials1'])) {
  $materials_check = $_SESSION['materials1'];
}


// ユーザー検索結果のセッションを受け取る serch_member.phpの検索結果をセッションに格納したものを変数に渡す。
if (!empty($_SESSION['members_recipes'])) {
  $membersrecipes = $_SESSION['members_recipes'];
}

// recipe ID をテキスト入力した検索結果を受け取る。serch_recipe_id.phpの検索結果をセッションに格納したものを変数に渡す。
if (!empty($_SESSION['serchRecipeId'])) {
  $recipeId = $_SESSION['serchRecipeId'];
} 


// 商品一覧/product/index.php で検索して、serch_mushimegane.phpの実行結果をセッションで受け取る。
if (!empty($_SESSION['productList'])) {
  $productListUp = $_SESSION['productList'];
  // 上記のセッションが空なら、下記のエラーメッセージを表示する。
} 






try {

  $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));

  $date = $dt->format('Y-m-d');

  //データに接続するための文字列
  $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

  $dbh = new PDO($dsn, 'root', '');

  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


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

  $sql = "SELECT  categories.id, categories.categories_name, 
            categories.parent_category_id
            FROM parent_categories JOIN categories ON parent_categories.id = 
            categories.parent_category_id WHERE parent_categories.id = 2 AND categories.is_deleted = 0 
            AND categories.users_id = 56";
  // 管理者が作ったカテゴリーIDだけを表示する";


  $stmt = $dbh->prepare($sql);

  $stmt->execute();

  $result = $dbh->query($sql);

  $category2 = $result->fetchAll(PDO::FETCH_ASSOC);


  //  親カテゴリーID(3)の階層下に登録済みの、子カテゴリー名とIDをFETCHする       

  // カテゴリー (3) 具材・カレーの色

  $sql = "SELECT  categories.id, categories.categories_name, 
            categories.parent_category_id
            FROM parent_categories JOIN categories ON parent_categories.id = 
            categories.parent_category_id WHERE parent_categories.id = 3 AND categories.is_deleted = 0 
            AND categories.users_id = 56 ";
  // 管理者が作ったカテゴリーIDだけを表示する";";


  $stmt = $dbh->prepare($sql);

  $stmt->execute();

  $result = $dbh->query($sql);

  $category3 = $result->fetchAll(PDO::FETCH_ASSOC);


  //  親カテゴリーID(4)の階層下に登録済みの、子カテゴリー名とIDをFETCHする   

  //  (4) ナン/ライス

  $sql = "SELECT  categories.id, categories.categories_name, 
            categories.parent_category_id
            FROM parent_categories JOIN categories ON parent_categories.id = 
            categories.parent_category_id WHERE parent_categories.id = 4 AND categories.is_deleted = 0 
            AND categories.users_id = 56 ";
  // 管理者が作ったカテゴリーIDだけを表示する";";


  $stmt = $dbh->prepare($sql);

  $stmt->execute();

  $result = $dbh->query($sql);

  $category4 = $result->fetchAll(PDO::FETCH_ASSOC);



  //  親カテゴリーID(5)階層下に登録済みの、子カテゴリー名とIDをFETCHする   

  //  (5) スィーツ・飲み物
  $sql = "SELECT  categories.id, categories.categories_name, 
            categories.parent_category_id
            FROM parent_categories JOIN categories ON parent_categories.id = 
            categories.parent_category_id WHERE parent_categories.id = 5 AND categories.is_deleted = 0 
            AND categories.users_id = 56 ";
  // 管理者が作ったカテゴリーIDだけを表示する";";


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
  // AND categories.users_id = 56 
  // 管理者が作ったカテゴリーIDも、ユーザーが作ったカテゴリも全て表示する";";


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
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>カテゴリー検索</title>
  

  <!-- CSS -->
  

  <link rel="stylesheet" href="css/stylesheet2_3.css">
  <!-- アコーディオン検索バー -->
  <link rel="stylesheet" href="css/stylesheet_a.css">
  
 

  <style>
    /* ボタン⓵ [材料に追加]のCSSの*ID */
    input[id="button"] {
      color: #ffffff;
      height: 33px;
      font-size: 16px;
      border-radius: 6px;
      border: none;
      background-color: #E9C8A7;
      background-color: #8C6A03;
    }
  </style>

</head>

<body>


  <!-- DIV＿Pはじまり -->
  <div class='div_p'>Everyone's recipes

    <!-- ログアウト -->
    <div class="div_logout"><input type="button" value='ログアウト' class="logout_btn" onclick="location.href='../logout/process.php'">

    </div>
    <!-- マイページ -->
    <div class="div_logout"><input type="button" value='マイページ' class="logout_btn" onclick="location.href='../login/process.php'">

    </div>
    <?php if (!empty($_SESSION['member'])) { ?>
      <!-- みんなのレシピ -->
      <div class="div_logout"><input type="button" value='みんなのレシピ' class="product_btn" onclick="location.href='confirm.php'">
      <?php } elseif (empty($_SESSION['member'])) { ?>
        <div class="div_logout"><input type="button" value='みんなのレシピ' class="product_btn" onclick="location.href='index.php'">
        <?php } ?>


        </div>
        <!-- 商品アイテム管理・ボタン -->
        <?php if (!empty($_SESSION['member'])) : ?>
          <?php if ($_SESSION['member'] == 104) : ?>
            <!-- 管理者頁 -->
            <div class="div_logout"><input type="button" value='商品アイテム管理' class="product_btn" onclick="location.href='../product/index.php'">

            </div>
          <?php endif ?>
        <?php endif ?>
      </div>
      <!-- DIV_Pおわり -->


      <!-- ここにレシピアイテム検索ツールがはいります -->

      <!-- ページ全体 DIV auto -->
      <div class="auto">

        <!-- comprehensive DIV autoの中でDIV sideとDIV auto-childrenを包括する -->
        <div class="comprehensive">

          <!-- --------------------------------------------- -->
          <!-- アコーディオン検索バーはじまり -->
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
                  <input type="submit" value="検索" name="send" class="btn-border">
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
                  <dt>●食材を入力して検索する</dt>
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
            <!-- 検索フォーム⑦おわり -->
            </form>




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

            <!-- アイコン画像 -->
            <input id="acd-check10" class="acd-check" type="checkbox">
            <label class="acd-label" for="acd-check10">



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
            <!-- 検索フォーム⑦おわり -->
            <!-- div widthおわり -->
          </div>



          <!-- ------------------------------------------------------------------------------- -->

          <!-- 検索結果表示画面 -->

          <!-- DIV auto_childrenはじまり -->
          <div class="auto_children">

            <!-- ↓ ここから、検索結果 レシピ一覧-->
            <?php if (!empty($multiples)) { ?>

              <br><br>
              <div>
                <dt class="wf-sawarabimincho">
                  <img id="acd" src="../icon_img/3country.png" width="21px" height="auto">

                  <span style="color:#ffffff">カテゴリ別レシピ一覧</span>
                </dt>
                <?php foreach ($multiples as $v) : ?>
                  <?php $categoryName = $v['categories_name'] ?>
                <?php endforeach ?>
                <dt><span style="color:green;font-size:21px">
                    <?php echo $categoryName ?>・カテゴリーのレシピです。</span></dt>
              </div>
              <br><br><br><br>

              <!-- ボタン -->
              <!-- セッションの削除ボタン -->
              <!-- form送信でボタンを押したらセッションを削除できます -->
              <form action="" method="POST">
                <!-- destroy ボタンが押されたら、セッションを削除し-->
                <?php if (isset($_POST['delete'])) : ?>
                  <!-- もしname="destroy" がGET送信されたら、unset($_SESSION)する -->
                  <?php unset($_SESSION['multiple_id']); ?>

                <?php endif ?>
                <!-- セッション破棄（結果の削除） -->
                <input type="submit" name="delete" value="クリア" />
              </form>



              <!-- レシピ検索結果-->
              <!-- 国/豆カレー他✅ボックス検索結果 -->
              <div class="parent">
                <!-- データの数だけ繰り返し -->
                <?php foreach ($multiples as $v) : ?>

                  <div class="div_100p">
                    <div class="div_100">
                      <!-- テーブルのデータベースからFETCH()した  完成レシピのイメージ画像 -->

                      <?php if (empty($_SESSION['member'])) { ?>
                        <a href="../edit/recipe/release_recipe3.php?id=
                    <?php echo $v['id'] ?>">
                          <img id="completeimg" src="../create/recipe/images/<?php echo $v['complete_img'] ?>">
                        </a>
                      <?php } elseif (!empty($_SESSION['member'])) { ?>
                        <a href="../edit/recipe/release_recipe.php?id=
                    <?php echo $v['id'] ?>">
                          <img id="completeimg" src="../create/recipe/images/<?php echo $v['complete_img'] ?>">
                        </a>
                      <?php } ?>
                    </div>
                    <div class="div_100">
                      <table>
                        <tbody>
                          <tr>
                            <th></th>
                          </tr>
                          <tr>
                            <th></th>
                          </tr>
                          <tr>
                            <th></th>
                          </tr>
                          <tr>
                            <th></th>
                          </tr>
                        </tbody>

                        <thead>
                          <tr>
                            <td><span style="color:green;font-size:11px">レシピID:<?php echo $v['id'] ?></td>
                          </tr>
                          <tr>
                            <td><span style="color:green;font-size:11px">カテゴリー:</span><br><?php echo $v['categories_name'] ?></td>
                          </tr>

                          <tr>
                            <!-- マイレシピテーブルのデータベースからFETCH()した レシピ名  -->
                            <td><span style="color:green;font-size:13px">

                                <!-- 未ログインなら -->
                                <?php if (empty($_SESSION['member'])) { ?>
                                  <a id="link" href="../edit/recipe/release_recipe3.php?id=
                              <?php echo $v['id'] ?>" style="text-decoration:none;"><?php echo $v['recipe_name'] ?>
                                  </a>
                                  <!-- 既にログイン中なら -->
                                <?php } elseif (!empty($_SESSION['member'])) { ?>
                                  <a id="link" href="../edit/recipe/release_recipe.php?id=
                              <?php echo $v['id'] ?>" style="text-decoration:none;"><?php echo $v['recipe_name'] ?>
                                  </a>
                                <?php } ?>
                            </td>
                          </tr>
                          <tr>
                            <td><img id='serch' src="../icon_img/members.png">
                              <!-- メンバー・あいこん画像 --><span style="font-size:13px"><?php echo $v['nickname'] ?>
                              </span></td>
                          </tr>
                        </thead>
                      </table>

                    </div>
                  </div>

                <?php endforeach ?>

              </div>
              <!-- precent -->
              <br><br>
              <!-- カテゴリ検索結果表示 おわり -->
              <pre></pre>
     

            <?php } ?>


            <!-- 区切り線 -->
            <div class="line"></div>


            <!-- ----------------------------------------------------------------------- -->

            <?php if (!empty($materials_check)) { ?>

              <br><br>
              <!-- 材料✅ボックス検索 -->
              <div>
                <dt class="wf-sawarabimincho">
                  <img id="acd" src="../icon_img/3country.png" width="21px" height="auto">
                  <img id="acd" src="../icon_img/meet.png" width="24px" height="auto">
                  <span style="color:#ffffff">・材料からの検索結果 レシピ一覧
                </dt></span>
                <br><br><br><br>

                <!-- ボタン -->
                <!-- セッションの削除ボタン -->
                <!-- form送信でボタンを押したらセッションを削除できます -->
                <form action="" method="POST">
                  <!-- destroy ボタンが押されたら、セッションを削除し-->
                  <?php if (isset($_POST['destroy'])) : ?>
                    <!-- もしname="destroy" がGET送信されたら、unset($_SESSION)する -->
                    <?php unset($_SESSION['materials1']); ?>

                  <?php endif ?>
                  <!-- セッション破棄（結果の削除） -->
                  <input type="submit" name="destroy" value="クリア" />
                </form>
                <!-- ボタンおわり -->


                <!-- レシピ検索結果-->
                <!-- 国/豆カレー他✅ボックス検索結果 -->
                <div class="parent">
                  <!-- データの数だけ繰り返し -->
                  <?php foreach ($materials_check as $key => $v) : ?>

                    <div class="div_100p">
                      <div class="div_100">
                        <!-- テーブルのデータベースからFETCH()した  完成レシピのイメージ画像 -->

                        <?php if (empty($_SESSION['member'])) { ?>
                          <a href="../edit/recipe/release_recipe3.php?id=
                        <?php echo $v['id'] ?>">
                            <img id="completeimg" src="../create/recipe/images/<?php echo $v['complete_img'] ?>">
                          </a>
                        <?php } elseif (!empty($_SESSION['member'])) { ?>
                          <a href="../edit/recipe/release_recipe.php?id=
                        <?php echo $v['id'] ?>">
                            <img id="completeimg" src="../create/recipe/images/<?php echo $v['complete_img'] ?>">
                          </a>
                        <?php } ?>
                      </div>
                      <div class="div_100">
                        <table>
                          <tbody>
                            <tr>
                              <th></th>
                            </tr>
                            <tr>
                              <th></th>
                            </tr>
                            <tr>
                              <th></th>
                            </tr>
                            <tr>
                              <th></th>
                            </tr>
                          </tbody>

                          <thead>
                            <tr>
                              <td><span style="color:green;font-size:11px">レシピID:<?php echo $v['id'] ?></td>
                            </tr>
                            <tr>
                              <td><span style="color:green;font-size:11px">カテゴリー:</span><br><?php echo $v['categories_name'] ?></td>
                            </tr>
                            <tr>
                              <td><span style="color:green;font-size:11px">材料:</span><?php echo $v['material_name'] ?></td>
                            </tr>

                            <tr>
                              <!-- マイレシピテーブルのデータベースからFETCH()した レシピ名  -->
                              <td><span style="color:green;font-size:13px">

                                  <!-- 未ログインなら -->
                                  <?php if (empty($_SESSION['member'])) { ?>
                                    <a id="link" href="../edit/recipe/release_recipe3.php?id=
                                    <?php echo $v['id'] ?>" style="text-decoration:none;"><?php echo $v['recipe_name'] ?>
                                    </a>
                                    <!-- 既にログイン中なら -->
                                  <?php } elseif (!empty($_SESSION['member'])) { ?>
                                    <a id="link" href="../edit/recipe/release_recipe.php?id=
                                    <?php echo $v['id'] ?>" style="text-decoration:none;"><?php echo $v['recipe_name'] ?>
                                    </a>
                                  <?php } ?>
                              </td>
                            </tr>
                            <tr>
                              <td><img id='serch' src="../icon_img/members.png">
                                <!-- メンバー・あいこん画像 --><span style="font-size:13px"><?php echo $v['nickname'] ?>
                                </span></td>
                            </tr>
                          </thead>
                        </table>

                      </div>
                    </div>

                  <?php endforeach ?>

                </div>
                <!-- precent -->
                <br><br>
           
              <?php } ?>



              <!-- *************************************************************************************** -->


              <!-- 区切り線 -->
              <div class="line"></div>
              <pre><br></pre>



              <!-- 表示欄 -->

              <?php if (!empty($membersrecipes)) { ?>
                <br><br>
                <div class="margin-5">
                  <dt class="wf-sawarabimincho">
                    <img id="acd" src="../icon_img/3country.png" width="21px" height="auto">
                    <img id="acd" src="../icon_img/members.png" width="16px" height="auto">
                    <span style="color:#ffffff">
                      ニックネームからレシピ検索結果 レシピ一覧</span>
                  </dt>
                  <?php foreach ($membersrecipes as $key => $v) : ?>
                    <?php $membersNickname = $v['nickname'] ?>
                  <?php endforeach ?>
                  <dt><span style="color:green;font-size:21px">
                      <?php echo $membersNickname ?>さんのレシピです。</span></dt>

                  <br><br><br><br>

                  <!-- form送信でボタンを押したらセッションを削除できます -->
                  <form action="" method="POST">
                    <!-- destroy ボタンが押されたら、セッションを削除し-->
                    <?php if (isset($_POST['exclusion'])) : ?>
                      <!-- もしname="destroy" がGET送信されたら、unset($_SESSION)する -->
                      <?php unset($_SESSION['members_recipes']); ?>
                    <?php endif ?>
                    <!-- セッション破棄（結果の削除） -->
                    <input type="submit" name="exclusion" value="クリア" />
                  </form>
                  <!-- セッション削除ボタン おわり -->

                  <br>


                  <!-- ユーザーから、彼らのレシピを検索結果を表示する -->

                  <div class="parent">

                    <!-- データの数だけ繰り返し -->
                    <?php foreach ($membersrecipes as $v) : ?>

                      <div class="div_100p">
                        <div class="div_100">
                          <!-- テーブルのデータベースからFETCH()した  完成レシピのイメージ画像 -->
                          <?php if (empty($_SESSION['member'])) { ?>
                            <a href="../edit/recipe/release_recipe3.php?id=
                        <?php echo $v['id'] ?>">
                              <img id="completeimg" src="../create/recipe/images/<?php echo $v['complete_img'] ?>">
                            </a>
                          <?php } elseif (!empty($_SESSION['member'])) { ?>
                            <a href="../edit/recipe/release_recipe.php?id=
                        <?php echo $v['id'] ?>">
                              <img id="completeimg" src="../create/recipe/images/<?php echo $v['complete_img'] ?>">
                            </a>
                          <?php } ?>
                        </div>
                        <div class="div_100">
                          <table>
                            <tbody>
                              <tr>
                                <th></th>
                              </tr>
                              <tr>
                                <th></th>
                              </tr>
                              <tr>
                                <th></th>
                              </tr>
                              <tr>
                                <th></th>
                              </tr>
                            </tbody>
                            <thead>
                              <tr>
                                <td><span style="color:green;font-size:11px">レシピID:<?php echo $v['id'] ?></span></td>
                                <!-- マイレシピテーブルのデータベースからFETCH()した レシピ名  -->
                              </tr>
                              <tr>
                                <td><span style="color:green;font-size:11px">カテゴリー:</span><br><?php echo $v['categories_name'] ?></td>
                              </tr>
                              <tr>
                                <td width="180px">
                                  <dt class="wf-sawarabimincho"><span style="color:green;font-size:15px">
                                      <!-- 未ログインなら -->
                                      <?php if (empty($_SESSION['member'])) { ?>
                                        <a id="link" href="../edit/recipe/release_recipe3.php?id=
                                  <?php echo $v['id'] ?>" style="text-decoration:none;"><?php echo $v['recipe_name'] ?>
                                        </a>
                                        <!-- 既にログイン中なら -->
                                      <?php } elseif (!empty($_SESSION['member'])) { ?>
                                        <a id="link" href="../edit/recipe/release_recipe.php?id=
                                  <?php echo $v['id'] ?>" style="text-decoration:none;"><?php echo $v['recipe_name'] ?>
                                        </a>
                                      <?php } ?>
                                  </dt>
                                </td>
                              </tr>
                              <tr>
                                <td><img id='serch' src="../icon_img/members.png">
                                  <!-- メンバー・あいこん画像 --><span style="font-size:13px"><?php echo $v['nickname'] ?>
                                  </span></td>
                              </tr>
                            </thead>
                          </table>
                        </div>
                      </div>

                    <?php endforeach ?>
                  <!-- precent -->                      
                  </div>
                  
       
                <?php } ?>


                <!-- ///////// -->




                <!-- 表示欄 -->
                <!-- 商品検索結果一覧⓶-->

                <?php if (!empty($productListUp)) { ?>
                  <br><br>
                  <div class="margin-5">
                    <dt class="wf-sawarabimincho">
                      <img id="acd" src="../icon_img/3country.png" width="21px" height="auto">
                      <img id="acd" src="../icon_img/members.png" width="16px" height="auto">
                      <span style="color:#ffffff">
                        商品アイテムの検索結果</span>
                    </dt>
                    <?php foreach ($productListUp as $key => $v) : ?>

                    <?php endforeach ?>
                    <dt><span style="color:green;font-size:21px">
                        <!-- ○○（商品）一覧 -->
                      </span></dt>

                    <br><br><br><br>

                    <!-- form送信でボタンを押したらセッションを削除できます -->
                    <form action="" method="POST">
                      <!-- destroy ボタンが押されたら、セッションを削除し-->
                      <?php if (isset($_POST['exclusion'])) : ?>
                        <!-- もしname="destroy" がGET送信されたら、unset($_SESSION)する -->
                        <?php unset($_SESSION['productList']); ?>
                      <?php endif ?>
                      <!-- セッション破棄（結果の削除） -->
                      <input type="submit" name="exclusion" value="クリア" />
                    </form>
                    <!-- セッション削除ボタン おわり -->

                    <br>


                    <!-- 商品の検索結果を表示する -->

                    <div class="parent">

                      <!-- データの数だけ繰り返し -->
                      <?php foreach ($productListUp as $v) : ?>

                        <div class="div_100p">
                          <div class="div_100">

                            <!-- ↓商品リストから、単品の詳細ページへリンク先（未完成） -->
                            <a href="../product/product_introduction.php?id=
                            <?php echo $v['id'] ?>">
                              <!-- 商品ID product_lists.id -->
                              <!-- テーブルのデータベースからFETCH()した  商品イメージ画像 -->
                              <img id="completeimg" src="../product/images/<?php echo $v['img'] ?>"></p>
                            </a>
                          </div>
                          <div class="div_100p">
                            <table>

                              <div></div>
                              <tr>
                                <td><span style="color:green;font-size:11px">商品ID:<?php echo $v['id'] ?></span></td>
                                <!-- 商品（product_lists）テーブルのデータベースからFETCH()した   -->
                              </tr>

                              <tr>
                                <td width="180px">
                                  <dt class="wf-sawarabimincho"><span style="color:green;font-size:15px">
                                      <a id="link" href="../edit/recipe/release_recipe.php?id=
                                      <?php echo $v['id'] ?>" style="text-decoration:none;"><?php echo $v['product_name'] ?>
                                      </a>
                                  </dt>
                                </td>
                              </tr>
                              <tr>
                                <td><span style="color:green;font-size:11px">内容量:</span><?php echo $v['amount'] ?></td>
                              </tr>
                              <tr>
                                <td><span style="color:green;font-size:11px">原産国:</span><?php echo $v['coo'] ?></td>
                              </tr>
                              <tr>
                                <td><span style="color:green;font-size:11px">price:</span><?php echo $v['price'] ?>円</td>
                              </tr>
                              <tr>
                                <td><span style="color:green;font-size:11px">メーカー:</span><?php echo $v['names'] ?></td>
                              </tr>

                          </div>
                          </thead>
                          </table>
                        </div>
                    </div>

                  <?php endforeach ?>

                  </div>
                
                <?php } ?>











                

                <!-- //////////////// -->


                <!-- 表示欄 -->
                <?php if (!empty($serch)) { ?>

                  <!-- 🔎虫眼鏡のテキストフォームボックス検索 -->
                  <br><br>
                  <div class="margin-5 ">
                    <dt class="wf-sawarabimincho">
                      <img id="acd" src="../icon_img/3country.png" width="21px" height="auto">
                      <img id="acd" src="../icon_img/meet.png" width="24px" height="auto">
                      <span style="color:#ffffff">虫眼鏡の検索結果 レシピ一覧
                    </dt></span>
                    <?php foreach ($serch as $key => $v) : ?>
                      <?php
                      if (!empty($v['nickname'])) :
                        $membersNickname = $v['nickname'];
                      endif;
                      ?>
                    <?php endforeach ?>
                    <dt><span style="color:green;font-size:21px">
                        <!-- からのレシピ一です。</span></dt> -->
                        <!-- form送信でボタンを押したらセッションを削除できます -->
                        <br><br><br>

                        <!-- ボタン -->
                        <!-- form送信でボタンを押したらセッションを削除できます -->
                        <form action="" method="POST">
                          <!-- destroy ボタンが押されたら、セッションを削除し-->
                          <?php if (isset($_POST['clear'])) : ?>
                            <!-- もしname="destroy" がGET送信されたら、unset($_SESSION)する -->
                            <?php unset($_SESSION['serch']); ?>

                          <?php endif ?>
                          <!-- セッション破棄（結果の削除） -->
                          <input type="submit" name="clear" value="クリア" />
                        </form>
                  </div>

                  </dt>

                  <!-- 虫眼鏡検索 🔎テキストフォームの検索結果を表示/そのレシピ名・材料名・ニックネームでレシピを検索 -->
                  <div class="parent">
                    <!-- データの数だけ繰り返し -->
                    <?php foreach ($serch as $v) : ?>

                      <div class="div_100p">
                        <div class="div_100">
                          <!-- テーブルのデータベースからFETCH()した  完成レシピのイメージ画像 -->
                          <?php if (empty($_SESSION['member'])) { ?>

                            <a href="../edit/recipe/release_recipe3.php?id=
                            <?php echo $v['id'] ?>">
                              <img id="completeimg" src="../create/recipe/images/<?php echo $v['complete_img'] ?>">
                            </a>
                          <?php } elseif (!empty($_SESSION['member'])) { ?>
                            <?php if (!empty($v['id'])) : ?>
                              <a href="../edit/recipe/release_recipe.php?id=
                              <?php echo $v['id'] ?>">
                              <?php endif ?>
                              <?php if (!empty($v['complete_img'])) : ?>
                                <img id="completeimg" src="../create/recipe/images/<?php echo $v['complete_img'] ?>">
                              <?php endif ?>
                              </a>
                            <?php } ?>
                        </div>
                        <div class="div_100">
                          <table>
                            <tbody>
                              <tr>
                                <th></th>
                              </tr>
                              <tr>
                                <th></th>
                              </tr>
                              <tr>
                                <th></th>
                              </tr>
                            </tbody>

                            <thead>
                              <tr>
                                <?php if (!empty($v['id'])) : ?>
                                  <td><span style="color:green;font-size:11px">レシピID:<?php echo $v['id'] ?></span></td>
                                <?php endif ?>
                              </tr>

                              <tr>
                                <?php if (!empty($v['categories_name'])) : ?>
                                  <td><span style="color:green;font-size:11px">カテゴリー:<?php echo $v['categories_name'] ?></span></td>
                                <?php endif ?>
                              <tr>
                              <tr>
                                <td><img id='serch' src="../icon_img/members.png">&nbsp;
                                  <?php if (!empty($v['nickname'])) : ?>
                                    <!-- メンバー・あいこん画像 --><span style="font-size:13px"><?php echo $v['nickname'] ?>
                                    <?php endif ?>
                                </td>
                              </tr>

                              <tr>
                                <!-- マイレシピテーブルのデータベースからFETCH()した レシピ名  -->
                                <td><span style="color:green;font-size:15px">
                                    <!-- 未ログインなら -->
                                    <?php if (empty($_SESSION['member'])) { ?>
                                      <a id="link" href="../edit/recipe/release_recipe3.php?id=
                                <?php echo $v['id'] ?>" style="text-decoration:none;"><?php echo $v['recipe_name'] ?>
                                      </a>
                                      <!-- 既にログイン中なら -->
                                    <?php } elseif (!empty($_SESSION['member'])) { ?>
                                      <?php if (!empty($v['id'])) : ?>
                                        <a id="link" href="../edit/recipe/release_recipe.php?id=
                                  <?php echo $v['id'] ?>" style="text-decoration:none;"><?php echo $v['recipe_name'] ?>
                                        </a>
                                      <?php endif ?>
                                    <?php } ?>
                                </td>
                                </span>
                                </td>
                              </tr>

                            </thead>
                          </table>
                        </div>
                      </div>

                    <?php endforeach ?>
                    <!-- precent -->
                  </div>


                </div>
              
              <?php } ?>

              <!-- //////////////// -->




                <!-- 表示欄 -->
                <?php if (!empty($recipeId)) { ?>

                    <!-- 🔎虫眼鏡のテキストフォームボックス検索 -->
                    <br><br>
                    <div class="margin-5 ">
                      <dt class="wf-sawarabimincho">
                        <img id="acd" src="../icon_img/3country.png" width="21px" height="auto">
                        <img id="acd" src="../icon_img/meet.png" width="24px" height="auto">
                        <span style="color:#ffffff">虫眼鏡の検索結果 レシピ一覧
                      </dt></span>
                      <?php foreach ($recipeId as $key => $v) : ?>
                        <?php
                        if (!empty($v['nickname'])) :
                          $membersNickname = $v['nickname'];
                        endif;
                        ?>
                      <?php endforeach ?>
                      <dt><span style="color:green;font-size:21px">
                          <!-- からのレシピ一です。</span></dt> -->
                          <!-- form送信でボタンを押したらセッションを削除できます -->
                          <br><br><br>

                          <!-- ボタン -->
                          <!-- form送信でボタンを押したらセッションを削除できます -->
                          <form action="" method="POST">
                            <!-- destroy ボタンが押されたら、セッションを削除し-->
                            <?php if (isset($_POST['clear'])) : ?>
                              <!-- もしname="destroy" がGET送信されたら、unset($_SESSION)する -->
                              <?php unset($_SESSION['serchRecipeId']); ?>

                            <?php endif ?>
                            <!-- セッション破棄（結果の削除） -->
                            <input type="submit" name="clear" value="クリア" />
                          </form>
                    </div>

                    </dt>

                    <!-- 虫眼鏡検索 🔎テキストフォームの検索結果を表示/そのレシピ名・材料名・ニックネームでレシピを検索 -->
                    <div class="parent">
                      <!-- データの数だけ繰り返し -->
                      <?php foreach ($recipeId as $v) : ?>

                        <div class="div_100p">
                          <div class="div_100">
                            <!-- テーブルのデータベースからFETCH()した  完成レシピのイメージ画像 -->
                            <?php if (empty($_SESSION['member'])) { ?>

                              <a href="../edit/recipe/release_recipe3.php?id=
                              <?php echo $v['id'] ?>">
                                <img id="completeimg" src="../create/recipe/images/<?php echo $v['complete_img'] ?>">
                              </a>
                            <?php } elseif (!empty($_SESSION['member'])) { ?>
                              <?php if (!empty($v['id'])) : ?>
                                <a href="../edit/recipe/release_recipe.php?id=
                                <?php echo $v['id'] ?>">
                                <?php endif ?>
                                <?php if (!empty($v['complete_img'])) : ?>
                                  <img id="completeimg" src="../create/recipe/images/<?php echo $v['complete_img'] ?>">
                                <?php endif ?>
                                </a>
                              <?php } ?>
                          </div>
                          <div class="div_100">
                            <table>
                              <tbody>
                                <tr>
                                  <th></th>
                                </tr>
                                <tr>
                                  <th></th>
                                </tr>
                                <tr>
                                  <th></th>
                                </tr>
                              </tbody>

                              <thead>
                                <tr>
                                  <?php if (!empty($v['id'])) : ?>
                                    <td><span style="color:green;font-size:11px">レシピID:<?php echo $v['id'] ?></span></td>
                                  <?php endif ?>
                                </tr>

                                <tr>
                                  <?php if (!empty($v['categories_name'])) : ?>
                                    <td><span style="color:green;font-size:11px">カテゴリー:<?php echo $v['categories_name'] ?></span></td>
                                  <?php endif ?>
                                <tr>
                                <tr>
                                  <td><img id='serch' src="../icon_img/members.png">&nbsp;
                                    <?php if (!empty($v['nickname'])) : ?>
                                      <!-- メンバー・あいこん画像 --><span style="font-size:13px"><?php echo $v['nickname'] ?>
                                      <?php endif ?>
                                  </td>
                                </tr>

                                <tr>
                                  <!-- マイレシピテーブルのデータベースからFETCH()した レシピ名  -->
                                  <td><span style="color:green;font-size:15px">
                                      <!-- 未ログインなら -->
                                      <?php if (empty($_SESSION['member'])) { ?>
                                        <a id="link" href="../edit/recipe/release_recipe3.php?id=
                                  <?php echo $v['id'] ?>" style="text-decoration:none;"><?php echo $v['recipe_name'] ?>
                                        </a>
                                        <!-- 既にログイン中なら -->
                                      <?php } elseif (!empty($_SESSION['member'])) { ?>
                                        <?php if (!empty($v['id'])) : ?>
                                          <a id="link" href="../edit/recipe/release_recipe.php?id=
                                    <?php echo $v['id'] ?>" style="text-decoration:none;"><?php echo $v['recipe_name'] ?>
                                          </a>
                                        <?php endif ?>
                                      <?php } ?>
                                  </td>
                                  </span>
                                  </td>
                                </tr>

                              </thead>
                            </table>
                          </div>
                        </div>

                      <?php endforeach ?>
                      <!-- precent -->
                    </div>


                    </div>
            

                    <?php } ?>

                    <!-- //////////////// -->





              <div class="space_visible">
                <div>

                </div>
              </div>

              <div class="space_visible">
                <div>

                </div>
              </div>



              <!-- カテゴリ検索結果表示 おわり -->
              <!-- DIV auto_childrenおわり -->
              </div>
              <!-- DIV comprehensiveおわり -->
          </div>
          <!-- DIV autoおわり -->
        </div>


</body>
</html>