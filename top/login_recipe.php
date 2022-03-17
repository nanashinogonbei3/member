<?php
session_start();

// 検索結果を受け取る serch_recipe.phpの検索結果
if (!empty($_SESSION['search_recipe'])) {
    $search_recipe = $_SESSION['search_recipe'];
}

// 検索結果を受け取る serch_precedure.phpの検索結果
if (!empty($_SESSION['serchprecedures'])) {
    $serchprecedures = $_SESSION['serchprecedures'];
}


// レシピ検索セッション
if (!empty($_SESSION['recipe_id'])) {
    $recipeId = $_SESSION['recipe_id'];
}
// レシピID
if (!empty($_SESSION['recipename'])) {
    $recipename = $_SESSION['recipename'];
}
// レシピ名
if (!empty($_SESSION['nickname'])) {
    $nickname = $_SESSION['nickname'];
}
// ニックネーム
if (!empty($_SESSION['icon_img'])) {
    $icon_img = $_SESSION['icon_img'];
}



// レシピ検索結果のセッション削除
unset($_SESSION['search_recipe']);
// 検索結果の削除

unset($_SESSION['serchprecedures']);



// 必要なファイルを読み込む
require_once('../class/db/Base.php');
require_once('../class/db/CreateRecipes.php');

// 送信データを受け取る.(ログインメンバーのid)
$id = $_POST;
// 1ページの$list でFETCH ALL の表示数
define('max_view', 5);


try {


    $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
    $date = $dt->format('Y-m-d');

    //データに接続するための文字列
    $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

    $dbh = new PDO($dsn, 'root', '');

    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    // メンバーズ★テーブルに接続 ログインしているメンバーと関連付ける。
    $sql = "SELECT * FROM members WHERE id";

    $stmt = $dbh->prepare($sql);

    $stmt->execute();


    // 今セッションでログインしているメンバーをFETCHして取り出し、変数$member に格納する

    $members = $dbh->prepare('SELECT * FROM members WHERE id=?');

    $members->execute(array($_SESSION['id']));

    $member = $members->fetch();



    $sql3 = "SELECT my_recipes.id as recipe_id, my_recipes.recipe_name, my_recipes.complete_img,
            my_recipes.update_time  
            FROM my_recipes WHERE members_id = '" .  $_SESSION['member'] . "' 
            AND is_deleted = 0
            ";

    $stmt3 = $dbh->prepare($sql3);

    // sqlの実行
    $stmt3->execute();

    $result = $dbh->query($sql3);

    $list = $result->fetchAll(PDO::FETCH_ASSOC);


    // これを消すとレシピが表示できない
    foreach ($list as $v) {
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


    $select = $dbh->prepare("SELECT * FROM my_recipes WHERE members_id = '" . $_SESSION['member'] . "'  
            ORDER BY update_time DESC LIMIT :start,:max ");





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


    // ログインしてから何もしていない時間が６０分経過したら自動的にログイン画面へ遷移する



    // セッションに記録された時間が、今の時間よりも大きい、つまりログイン時間から1時間以上たっていた場合,という意味
    if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
        // （1時間が経過していたら、）ログアウトし、ログイン画面に遷移する
        $_SESSION['time'] = time();
        // 現在の時刻で上書きします。こうすることで、何か行動したことで上書きすることで最後の時刻から１時間を記録することができるようになる。 
    } else {
        // 何か行動した更新時刻より１時間経過したら、勝手にログイン画面に移動しますs
        header('Location: ../login/join.php');
        exit();
    }
} catch (Exception $e) {
    echo 'DBに接続できません: ',  $e->getMessage(), "\n";
}




// エラーチェックを走らせる[何か入力してください]
if (isset($_GET['serch1'])) {
    // エラーチェック項目
    if (
        $_GET['recipe_id'] === '' && $_GET['recipe_name'] === '' && $_GET['nickname'] === '' && $_GET['cooking_time_a'] === '' &&
        $_GET['cooking_time_b'] === '' && $_GET['cost_a'] === '' && $_GET['cost_b'] === ''
    ) {

        $error['serch1'] = 'blank';
    }
    // エラーがなければ、serch_recipe.php へ遷移する。
    if (empty($error)) {

        $_SESSION['serch1'] = $_GET;
        header('Location: ./serch_multiple_recipe.php');
        exit;
    }
}


// エラーチェックを走らせる[idを入力してください]
if (isset($_GET['serch2'])) {
    // エラーチェック項目： 
    if ($_GET['recipe_id'] === '') {
        $error['recipe_id'] = 'blank';
    }
    // エラーがなければ、serch_precedure.php へ遷移する。
    if (empty($error)) {

        $_SESSION['recipe_id'] = $_GET['recipe_id'];
        header('Location: ./serch_precedure.php');
        exit();
    }
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

    <!-- CSS -->
    <!-- 全体 -->
    <link rel="stylesheet" href="./css/login_r/stylesheet2.css">
    <!--  サムネイル画像 -->
    <link rel="stylesheet" href="css/stylesheet1_1.css">
    <!--  タグ -->
    <link rel="stylesheet" href="css/stylesheet3_1.css">
    <!-- ページネーション -->
    <link rel="stylesheet" href="css/style_paging.css">


</head>


<body>
    <!-- Javascript ファイルを読み込む -->
    <script src="js/backup614/javascript.js"></script>



    <div class='div_p'>my recipes

        <!-- ログアウト -->
        <div class="div_logout"><input type="button" value='ログアウト' class="logout_btn" onclick="location.href='../logout/process.php'">

        </div>
        <!-- マイページ -->
        <div class="div_logout"><input type="button" value='マイページ' class="logout_btn" onclick="location.href='../login/process.php'">

        </div>
    </div>




    <div class='inline_block_2'>

        <div class="comprehensive">

            <div class="block1">
                <!-- タブ -->
                <div class="tabs">
                    <input id="all" type="radio" value="./confirm.php" onclick="location.href=this.value" name="tab_item">
                    <label class="tab_item" for="all">みんなのレシピ</label>

                    <input id="programming" type="radio" value="./login_recipe.php" onclick="location.href=this.value" name="tab_item" checked>
                    <label class="tab_item" for="programming">わたしのレシピ</label>

                    <input id="design" type="radio" value="../create/recipe/index.php" onclick="location.href=this.value" name="tab_item">
                    <label class="tab_item" for="design">レシピヲつくる</label>


                    <!-- 表示 -->
                    <div class="tab_content" id="all_content">
                        みんなのレシピを紹介しています
                    </div>

                    <?php if (!empty($list)) { ?>
                        <div class="tab_content" id="programming_content">
                            わたしの作成したレシピ
                        </div>
                    <?php } elseif (empty($list)) { ?>
                        <div class="tab_content" id="programming_content">
                            レシピを登録しましょう！
                        </div>
                    <?php } ?>
                    <div class="tab_content" id="design_content">
                        レシピヲつくる
                    </div>
                    <!-- ↓消すな -->
                    <div class="btn_migi">

                        <div class="div_hidari">

                            <!-- データベースからFETCH()した、 公開レシピの、サムネイル画像 -->

                            <!-- もし$listがあれば、 -->
                            <?php if (!empty($list)) { ?>
                                <div class="item_l">
                                    <form action="../edit/recipe/confirm.php" method="post" enctype="multipart/form-data">
                                        <div class="imageList">
                                            <div class="imageList__view">
                                                <input type="hidden" name="members_id">
                                                <a href="../edit/recipe/confirm.php?id=
                                                    <?php echo $v['recipe_id'] ?>" style="text-decoration:none;">
                                                    <img id="img" src="../create/recipe/images/<?php echo $list[0]['complete_img'] ?>" onclick="changeimg('../create/recipe/images/<?php echo $list[0]['complete_img'] ?>')" />
                                                </a>
                                                <!-- 大きいサムネイル画像 -->
                                            </div>

                                            <div id="thumb_img" class="imageList__thumbs">

                                                <!-- 小さいサムネイル画像 -->
                                                <?php foreach ($list as $v) : ?>
                                                    <div class="imageList__thumbnail selected">

                                                        <img id="img_s" src="../create/recipe/images/<?php echo $v['complete_img'] ?>" onclick="changeimg('../create/recipe/images/<?php echo $v['complete_img'] ?>')" />
                                                        <!-- 小さい画像に「レシピ名」を追加・リンクを貼る -->
                                                        <a href="./index.php" target="blank"><span style="font-size=3px"><?php echo $v['recipe_name'] ?></span></a>

                                                    </div>

                                                <?php endforeach ?>

                                            </div>
                                        </div>
                                    </form>
                                </div>
                            <?php }  ?>
                        </div>
                        <!-- DIV 左側おわり -->

                        <!-- DIV 右側はじまり -->
                        <div class="div_migi">

                            <div class="div_w">
                                <!-- 入力フォーム 材料入力 -->
                                <?php
                                echo '<pre>';
                                echo '<span style="padding: 1%;">' . $now . '頁/' . $total_count . '件</span>';
                                echo '</pre>';
                                ?>
                                <table width="380px">
                                    <thead>
                                        <tr>
                                            <th>
                                                <dt class="wf-sawarabimincho">id
                                            </th>
                                            <td>&nbsp;&nbsp;&nbsp;</td>
                                            <th>
                                                <dt class="wf-sawarabimincho">レシピ
                                            </th>
                                            <th>
                                                <dt class="wf-sawarabimincho">更新日
                                            </th>
                                            <td>
                                                &nbsp;
                                            </td>
                                            <th>
                                                <dt class="wf-sawarabimincho">公開
                                            </th>


                                            <th>
                                                <dt class="wf-sawarabimincho">削除
                                            </th>
                                        </tr>
                                    </thead>
                                </table>

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
                                            <td><span style="color:green;font-size:13px"><?php echo $val['id'] ?></span>&nbsp;&nbsp;</td>

                                            <!-- マイレシピ・テーブルのデータベースからFETCH()した  レシピ名とリンク -->
                                            <td><span style="color:green;font-size:13px">
                                                    <a href="./recipes.php?id=
                        <?php echo  $val['id'] ?>" style="text-decoration:none;"><?php echo  $val['recipe_name'] ?></a></span></td>

                                            <!-- マイレシピ・テーブルのデータベースからFETCH()した  公開日（=更新日） -->
                                            <td>&nbsp;&nbsp;<span style="color:green;font-size:13px"><?php echo  $val['update_time'] ?></span></td>

                                            <!-- マイレシピ・テーブルのデータベースからFETCH()した  リリース（=1/公開: =0/未公開） -->
                                            <td>&nbsp;&nbsp;</td>
                                            <?php if ($val['is_released'] == 1) { ?>
                                                <td><span style="color:green;font-size:13px">公開</span></td>
                                            <?php } else { ?>
                                                <td><span style="color:green;font-size:13px">-</span></td>
                                            <?php } ?>

                                            <!-- マイレシピ・テーブルのデータベースからFETCH()した  リリース（=1/削除済: =0/登録） -->

                                            <?php if ($val['is_deleted'] == 0) { ?>
                                                <td><span style="color:green;font-size:13px"></span>-</td>
                                            <?php } else { ?>
                                                <td><span style="color:green;font-size:13px">delete</span></td>
                                            <?php } ?>

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
                                    // 表示ページが、1ページより大きいなら、「前へ」表示
                                    echo '<a href="?page_id=', ($now - 1), '">前へ</a>';
                                } else {
                                    // "前へ"非表示
                                }
                                ?>

                                <ul class="bar">
                                    <li>
                                        <?php
                                        for ($n = 1; $n <= $pages; $n++) {
                                            if ($n == $now) {
                                                echo "<span style='padding: 5px;'>$now</span>";
                                            } else {
                                                echo "<a href='./login_recipe.php?page_id=$n' style='padding: 5px;'>$n</a>";
                                            }
                                        }
                                        ?>
                                    </li>
                                </ul>

                                <?php
                                if ($now < $pages) {
                                    // 表示ページが最終ページより小さいなら、「次へ」表示
                                    echo '<a href="?page_id=', ($now + 1), '">次へ</a>';
                                }
                                ?>
                            </div>

                        </div>

                    </div>



                </div>
                <!-- div class="comprehensive" おわり-->
            </div>








            <div class="div_serch">
                <!-- レシピ検索 -->
                <dt class="p_font_rarge">🍳レシピと調理手順の検索</dt>
                <br>

                <div class="toolbar">
                    <!-- ここにレシピアイテム検索ツールがはいります -->
                    <form action="" method="GET">
                        <!-- 検索ワード入力画面 -->
                        <!-- <input type="hidden" name="id" value=""> -->
                        <!-- 帰りにこのページに戻ってこれるように、$idをhiddenにして渡す -->

                        <table>

                            <tr>
                                <td>レシピID :</td>
                                <td><input type="text" name="recipe_id" value="" /></td>
                            </tr>

                            <tr>
                                <td>レシピ名 :</td>
                                <td><input type="text" name="recipe_name" value="" /></td>
                            </tr>

                            <?php
                            // echo $_SESSION['null'];
                            if (empty($_GET['serch'])) {
                                '<p>レシピ名もしくはIDを入力してください</p>';
                            }
                            ?>

                            <tr>
                                <td>作った人 :</td>
                                <td><input type="text" name="nickname" value="" /></td>
                            </tr>

                            <tr>
                                <td>調理時間</td>
                                <td><input type="text" name="cooking_time_a" value="" />～
                                </td>
                                <td><input type="text" name="cooking_time_b" value="" /></td>
                            </tr>

                            <tr>
                                <td>材料費</td>
                                <td><input type="text" name="cost_a" value="" />～</td>
                                <td><input type="text" name="cost_b" value="" /></td>
                            </tr>

                            <tr>
                                <?php
                                if (!empty($error['serch1'])) : ?>
                                    <p class="error">* 何か入力してください</p>
                                <?php endif ?>
                            </tr>

                        </table>
                        <!-- 検索ボタン -->
                        <input type="submit" name="serch1" value="検索">
                        <input type="reset" value="リセット">
                    </form>


                    <div class="div_clear">

                        <!-- form送信でボタンを押したらセッションを削除できます -->
                        <form action="" method="GET">

                            <input type="hidden" name="destroy" id="destroy" value="destroy" />


                            <!-- destroy ボタンが押されたら、セッションを削除し-->
                            <?php if (isset($destroy)) : ?>
                                <!-- もしname="destroy" がGET送信されたら、unset($_SESSION)する -->
                                <?php unset($_SESSION['destroy']);
                                // 処理が終わったら同じページに戻る
                                header("Location: ./confirm.php?id=" . $_GET['id']);
                                ?>
                            <?php endif ?>
                            <!-- セッション破棄（結果の削除） -->
                            <input type="submit" value="クリア" />
                        </form>
                    </div>




                    <?php if (isset($search_recipe)  === '') { ?>
                        <p>検索結果はありません</p>



                    <?php } elseif (!empty($search_recipe)) { ?>



                        <!-- 検索結果 を、おなじDIVの中に表示したい-->
                        <p>[検索結果1]</p>

                        <table rules="all" size="90%">
                            <tr>
                                <td>レシピID</td>
                                <td>レシピ名</td>
                                <td>イメージ画像</td>
                                <td>作った人</td>
                                <td>調理時間</td>
                                <td>材料費</td>

                            </tr>
                            <?php

                            ?>

                            <?php foreach ($search_recipe as $key => $v) {
                                echo '<tr>';
                                echo '<td>No.' . $v['recipeid'] . '</td>';
                                echo '<td>' . $v['recipe_name'] . '<a href="../create/recipe/confirm.php=' . $v['recipe_name'] . '" </a></td>';

                                echo '<td>
                        <a href="../edit/recipe/release_recipe.php?id=' . $v['recipeid'] . '"
                        style="text-decoration:none;">
                        <img  class="img2" src="../create/recipe/images/' . $v['complete_img'] . '" width="75px" height="auto">
                        </td></a>';

                                echo '<td width="11%">' . $v['nickname'] . '</td>';
                                echo '<td width="11%">' . $v['cooking_time'] . '分<dt></dt></td>';
                                echo '<td width="11%">' . $v['cost'] . '円</td>';
                                echo '</tr>';
                            }

                            ?>




                            <!-- end if -->
                        <?php } ?>


                        </table>



                        <!-- 調理手順の検索 -->
                        <br><br>
                        <!-- ここにレシピアイテム検索ツールがはいります -->
                        <form action="" method="GET">
                            <!-- 検索ワード入力画面 -->

                            <table>

                                <td>レシピID :</td>
                                <td>
                                    <input type="text" name="recipe_id" value="" />
                                    <!-- もしPOSTされた時に -->
                                    <?php if (!empty($error['recipe_id'])) : ?>
                                        <p class="error">* レシピIDを入力してください</p>
                                    <?php endif ?>
                                </td>
                                <tr>

                            </table>
                            <!-- 検索ボタン -->
                            <input type="submit" name="serch2" value="検索">
                            <input type="reset" value="リセット">
                        </form>


                        <div class="div_clear">

                            <!-- form送信でボタンを押したらセッションを削除できます -->
                            <form action="" method="GET">

                                <input type="hidden" name="destroy" id="destroy" value="destroy" />

                                <!-- destroy ボタンが押されたら、セッションを削除し-->
                                <?php if (isset($destroy)) : ?>
                                    <!-- もしname="destroy" がGET送信されたら、unset($_SESSION)する -->
                                    <?php unset($_SESSION['destroy']);
                                    // 処理が終わったら同じページに戻る
                                    header("Location: ./confirm.php?id=" . $_GET['id']);
                                    ?>
                                <?php endif ?>
                                <!-- セッション破棄（結果の削除） -->
                                <input type="submit" value="クリア" />
                            </form>
                        </div>





                        <?php if (isset($serch)) { ?>

                            <!-- 検索結果 を、おなじDIVの中に表示したい-->
                            <p>[検索結果]</p>
                        <?php } ?>


                        <!-- div_serch DIVおわり -->

                        <!-- 調理手順の検索 おわり -->

                        <!-- 表示欄 -->

                        <?php if (!empty($serchprecedures)) { ?>

                            <div class="font_title">
                                <span style="font-color=green"><?php echo $recipename ?></span>
                                <span style="font-size:11px">id:<?php echo $recipeId ?></span>
                                <div class="creater">
                                    <p class="p_font_small">作った人:<br>
                                        <?php echo $nickname ?></p>
                                </div>
                                <div class="div_img3">
                                    <!-- 画像リンク -->

                                    <img class="img" id="mimg" src="../member_picture/<?php echo $icon_img ?>" class="img5">


                                </div>
                            </div>

                            <div class="parent">

                                <!-- データの数だけ繰り返し -->
                                <?php foreach ($serchprecedures as $v) : ?>

                                    <div class="div_100p">
                                        <div class="div_100">
                                            <!-- 材料テーブルのデータベースからFETCH()した  調理手順のイメージ画像 -->
                                            <img class="img" id="pimg" src="../create/recipe/pimg/<?php echo $v['p_img'] ?>"></p>

                                        </div>
                                        <div class="div_100">
                                            <!-- 材料テーブルのデータベースからFETCH()した 調理説明  -->
                                            <p><span style="color:green;font-size:13px">
                                                    <td><?php echo  $v['descriptions'] ?></td>
                                                </span></p>

                                        </div>
                                    </div>

                                <?php endforeach ?>

                            </div>
                            <!-- precent -->
                        <?php } ?>
                </div>
                <!-- div_serch DIVおわり -->



                <!-- 区切り線 -->
                <div class="line"></div>







                <!-- Javascript ファイルを読み込む -->
                <script src="js/backup614/javascript.js"></script>

</body>

</body>

</html>