<?php

session_start();

if (empty($_SESSION['member'])) {
    header('Location: ../../login/join.php');
    exit();
}

// 必要なファイルを読み込む
require_once('../../class/db/Base.php');
require_once('../../class/db/CreateRecipes.php');

if (empty($_SESSION['member'])) {
}

// 1ページの$list でFETCH ALL の表示数
define('max_view', 3);


// confirm.php （編集画面からリダイレクトされたら、このindex.php に戻ってこれるようにする処理
// パラメータまたは$_POSTが入っていたら処理を始める
if (!empty($_POST['id']) || $_SESSION['member'] || !empty($_GET['name']) || !empty($_GET['id']) || empty($_POST)) {



    try {

        $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
        $date = $dt->format('Y-m-d');

        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

        $dbh = new PDO($dsn, 'root', '');

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // $members = $dbh->prepare('SELECT * FROM members WHERE id=?');
        // $members ->execute(array($_SESSION['id']));
        // $members という複数形の’メンバー’のidの配列から、
        // $member = $members->fetch();
        // // $memberという単数形で且つ今ログインしている会員の情報をfetch（取り出す）して引き出します。
        // $_SESSION['member'] = $member['id'];

        $sql3 = "SELECT nickname, icon_img
            FROM members WHERE members.id = '" . $_SESSION['member'] . "' ";

        $stmt3 = $dbh->prepare($sql3);

        $stmt3->execute();

        $member = $stmt3->fetch(PDO::FETCH_ASSOC);



        // my_recipeテーブルからcount($)レシピ数をカウントするFETCH
        // カウント$pages(ページ数)が2ページ未満の時はこの$listをforeachする
        $sql3 = "SELECT *
            FROM my_recipes WHERE is_deleted = 0
            AND members_id = '" . $_SESSION['member'] . "' ";

        $stmt3 = $dbh->prepare($sql3);

        $stmt3->execute();

        $list = $stmt3->fetchAll(PDO::FETCH_ASSOC);

        // ここから、マイレシピのページングの処理
        $total_count = count($list);

        // ページ数= 全商品数/1ページの表示数
        // トータルページ数※ceilは小数点を切り捨てる関数
        $pages = ceil($total_count / max_view);


        // ページ数が２ページ以上なら以下のsqlを実行し、２ページ未満なら実行しない。
        // if ($pages >= 2) {
        //現在いるページのページ番号を取得
        if (!isset($_GET['page_id'])) {
            $now = 1;
        } else {
            $now = $_GET['page_id'];
        }

        // ページネーションの1ページ目のsqlの処理・1ページ以外のsqlの処理
        //表示するページを取得するSQLを準備
        // members.idとmy_recipes.idの同名カラムの重複を防ぐためSQL文のas句を使用する。
        $select = $dbh->prepare("SELECT 
            my_recipes.id AS recipe_id, my_recipes.complete_img, my_recipes.recipe_name,
            my_recipes.cooking_time, my_recipes.cost, my_recipes.how_many_servings,
            my_recipes.created_date, members.id
            FROM my_recipes
            INNER JOIN members ON my_recipes.members_id = members.id
            WHERE my_recipes.members_id = '" . $_SESSION['member'] . "' 
            AND my_recipes.is_deleted = 0
            ORDER BY my_recipes.id  DESC LIMIT :start,:max ");


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
    } catch (Exception $e) {
        echo 'DBに接続できません: ',  $e->getMessage(), "\n";
    }

    //  もしも、新規レシピ作成のsentボタンが押されたら、name = "sent"
    if (!empty($_POST['send'])) {

        //  エラーチェックを走らせます
        if ($_POST['recipe_name'] === '') {
            $error['recipe_name'] = 'blank';
        }

        if ($_POST['cooking_time'] === '') {
            $error['cooking_time'] = 'blank';
        }
        if ($_POST['cost'] === '') {
            $error['cost'] = 'blank';
        }
        if ($_POST['how_many_servings'] === '') {
            $error['how_many_servings'] = 'blank';
        }
        if (strlen($_POST['how_many_servings']) >= 2) {
            $error['how_many_servings'] = 'string_over';
            // 「何人分」の、error設定
            // 全角数字"２"だと、string(2)でNG判定する。半角数字のばあいは正解で、string(1)とvar_dump($_POST['how_many_servings');実行結果が表示される
        }
        if ($_POST['created_date'] === '') {
            $error['created_date'] = 'blank';
        }


        $fileName = $_FILES['complete_img']['name'];

        // ファイルがアップロードされていれば、拡張子からエラーチェックを実行する
        if (!empty($fileName)) {
            $ext = substr($fileName, -3);
            if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png') {
                $error['image'] = 'type';
            }
        }


        if (empty($error)) {
            $image = date('YmdHis') .  $fileName;
            move_uploaded_file(
                $_FILES['complete_img']['tmp_name'],
                './images/' . $image
            );


            $_SESSION['recipe'] = $_POST;
            $_SESSION['recipe']['complete_img'] = $image;



            // エラーが無ければ、確認画面に遷移する
            header('Location: recipe_add.php');
            exit();
        }
    }


    // セッションに記録された時間が、今の時間よりも大きい、つまりログイン時間から
    // 1時間以上たっていた場合,という意味
    if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
        // （1時間が経過していたら、）ログアウトし、ログイン画面に遷移する
        $_SESSION['time'] = time();
        // 現在の時刻で上書きします。こうすることで、何か行動したことで上書きすることで
        // 最後の時刻から１時間を記録することができるようになる。 
    } elseif ($_SESSION['member'] = []) {
        header('Location: ../../login/join.php');
        exit();
        // 更新時刻より１時間経過していなくとも、クッキーの削除でセッション情報が空になったら
        // ログイン画面に遷移する
    } else {
        header('Location: ../../login/join.php');
        exit();
        // 何か行動した更新時刻より１時間経過したら、自動的にログイン画面に遷移します
    }
} else {
    header('Location: ../../login/join.php');
    exit();
}






?>

<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>マイレシピ</title>


    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
    <!-- 全体CSS -->
    <link rel="stylesheet" href="css/stylesheet1.css">
    <!--  ページネーションCSS -->
    <link rel="stylesheet" href="css/style_paging.css">

</head>

<body>
    <!-- 画像アップロードボタン の、「ファイル名を表示させる」ためにjs を使用します -->
    <!-- Javascript ファイルを読み込む -->
    <script src="./js/delete/delete.js"></script>


    </script>
    <div class='div_p'><?php echo $member['nickname']; ?><span style="font-size:18px;color:green;">さんの</span>my recipes

        <!-- ログアウト -->
        <div class="div_logout"><input type="button" value='ログアウト' class="logout_btn" onclick="location.href='../../logout/process.php'">

        </div>
        <!-- マイページ -->
        <div class="div_logout"><input type="button" value='マイページ' class="logout_btn" onclick="location.href='../../login/process.php'">

        </div>
        <!-- みんなのレシピ -->
        <div class="div_logout"><input type="button" value='みんなのレシピ' class="logout_btn" onclick="location.href='../../top/confirm.php'">

        </div>

    </div>
    <div class="comprehensive">

        <div class='inline_block_2'>
            <div class="div_img3">
                <img class="img3" src="../../member_picture/<?php echo $member['icon_img'] ?>" width="90px" height="auto">
            </div>
            <!-- フォーム -->
            <form action="" method="post" enctype="multipart/form-data">

                <!-- 'フォーム Hidden' レシピID -->
                <div class="clear-both">

                </div>

                <!-- 'フォーム Hidden' メンバーID -->
                <div class="clear-both">
                    <input type='hidden' name='members_id' value="<?php echo $_SESSION['member'] ?>">
                    <!-- $idm メンバーid / 誰か？ -->
                </div>

                <!-- 各フォーム$_POST[]は、リダイレクト（書き直し）したときに元の入力の値を表示し、書き直しのない入力欄の再入力する余計な時間を省く。 -->
                <!-- フォーム1 レシピ名 -->

                <dt class="wf-sawarabimincho">レシピ名<span style="color:red;font-size:11px">※必須</span></dt>
                <div class="clear-both">
                    <input type="text" name="recipe_name" class="input_height" value="">
                    <?php if (!empty($error['recipe_name'])) : ?>
                        <p class="error">* レシピ名を入力してください</p>
                    <?php endif ?>
                </div>


                <!-- フォーム2 調理時間 -->

                <dt class="wf-sawarabimincho">調理時間<span style="color:red;font-size:11px">※必須</span></dt>
                <div class="clear-both">
                    <input type="text" name="cooking_time" size="11" class="input_height" maxlength="240" value="">&nbsp;分

                    <?php if (!empty($error['cooking_time'])) : ?>
                        <p class="error">* 調理時間を入力してください</p>
                    <?php endif ?>
                </div>

                <!-- フォーム3 材料費 -->


                <dt class="wf-sawarabimincho">材料費<span style="color:red;font-size:11px">※必須</span></dt>
                <div class="clear-both">
                    <input type="text" name="cost" size="11" class="input_height" maxlength="120" value="">&nbsp;円

                    <?php if (!empty($error['cost'])) : ?>
                        <p class="error">* 材料費を入力してください</p>
                    <?php endif ?>
                </div>

                <!-- フォーム4   何人分 -->

                <dt class="wf-sawarabimincho">何人分<span style="color:red;font-size:11px">※必須</span></dt>
                <div class="clear-both">
                    <input type="text" name="how_many_servings" size="11" class="input_height" maxlength="255" value="">&nbsp;人分

                    <?php if (!empty($error['how_many_servings'])) : ?>
                        <p class="error">* 何人分か入力してください</p>
                    <?php endif ?>
                    <?php if (!empty($error['how_many_servings'])) : ?>
                        <p class="error">* 全角数字が入っているか、もしくは2桁以上の数字が入力されています</p>
                    <?php endif ?>
                </div>

                <!-- フォーム5 完成イメージ画像 -->

                <dt class="wf-sawarabimincho">完成画像<span style="color:red;font-size:11px">※必須</span></dt>
                <div class="clear-both">
                    <!-- 画像アップロードlabelボタン -->

                    <div class="div_file">
                        <label class="label_btn">
                            <input type="file" name="complete_img">画像ファイルを選択
                        </label>
                    </div>
                </div>

                <!-- エラー -->
                <?php if (!empty($error['complete_img'])) : ?>
                    <p class="error">* 完成画像を選択してください</p>
                <?php endif ?>
                <?php if (!empty($error['image'])) : ?>
                    <p class="error">* 写真などは「.gif」または「.jpg」「.png」の画像を指定してください</p>
                <?php endif ?>
                <!-- <if (!empty($error['image'])) : > -->
                <?php if (empty($_POST['complete_img']) && !empty($_POST['send'])) : ?>
                    <!-- 確認画面からリライトして、もう一度ファイルを選びなおしてもらう時 -->
                    <p class="error">*恐れ入りますが、画像を改めて指定してください</p>
                <?php endif ?>

                <!-- フォーム6 イメージ動画ビデオ -->

                <dt class="wf-sawarabimincho">調理動画<span style="color:red;font-size:11px"></span></dt>
                <div class="clear-both">
                    <!-- 動画アップロードlabel動画ボタン -->

                    <div class="div_file">
                        <label class="label_btn">
                            <input type="file" name="video">動画ファイルを選択
                        </label>
                    </div>
                </div>
                <!-- フォーム7 作成日 -->

                <div class="clear-both">
                    <dt class="wf-sawarabimincho">作成日<span style="color:red;font-size:11px">※必須</span></dt>
                    <label class="label_date">
                        <input type="date" name="created_date" class="input_height" maxlength="255" value="">
                    </label>

                    <?php if (!empty($error['created_date'])) : ?>
                        <p class="error">* 作成日を入力してください</p>
                    <?php endif ?>
                </div>
                <!-- "登録確認"ボタン -->
                <div class="div_f_left">
                    <input type="submit" class="update" value="登録確認" name="send" style="width: 111px;
                color: #ffffff;
                height: 33px;
                font-size: 16px;
                border-radius: 10px;
                border: none;
                background-color: #E9C8A7;
                background-color: #8C6A03;
                ">
                </div>
            </form>

            <!-- レシピ表示欄 -->
        </div class="wrap11">
        <div class="inline_block_3">

            <table width="825px">
                <tr class="table th">

                    <th></th>
                    <th>
                        <p class='wf-sawarabimincho'>レシピID</p>
                    </th>
                    <th>
                        <p class='wf-sawarabimincho'>レシピネーム</p>
                    </th>
                    <th>
                        <p class='wf-sawarabimincho'>時間</p>
                    </th>
                    <th>
                        <p class='wf-sawarabimincho'>材料費</p>
                    </th>
                    <th>
                        <p class='wf-sawarabimincho'>何人分</p>
                    </th>
                    <th>
                        <p class='wf-sawarabimincho'>完成画像</p>
                    </th>
                    <th>
                        <p class='wf-sawarabimincho'>作成日</p>
                    </th>
                </tr>
                <?php

                // レシピの画面ページ数が２以上の時は、$recipes変数にsql文の$dataの配列データを代入する。
                foreach ($data as $v) {
                ?>
                    <tr>
                        <!-- フォーム GET 編集画面へ -->
                        <!-- リンク・マイレシピテーブル・材料・調理テーブルの編集 -->
                        <form method="GET" action="../../edit/recipe/confirm.php">
                            <!-- 'フォーム Hidden' メンバーID -->
                            <input type='hidden' name='id' value="<?= $v['recipe_id'] ?>">
                            <td><input type="submit" value="編集" class="btn-border" /></td>
                        </form>
                        <!-- フォーム編集画面へ、おわり -->
                        <!-- foreach でのレシピ表示 -->
                        <!-- my_recipeテーブル dbレシピID -->
                        <td width="80px">
                            <p><span style="color:green"><?= (htmlspecialchars($v['recipe_id'], ENT_QUOTES)); ?></span></p>
                        </td>
                        <!-- my_recipeテーブル dbレシピネーム -->
                        <td width="100px">
                            <p><span style="color:green"><?= (htmlspecialchars($v['recipe_name'], ENT_QUOTES)); ?></span></p>
                        </td>
                        <!-- my_recipeテーブル db調理時間 -->
                        <td>
                            <p><span style="color:green"><?= (htmlspecialchars($v['cooking_time'], ENT_QUOTES)); ?></span>分</p>
                        </td>
                        <!-- my_recipeテーブル dbコスト -->
                        <td>
                            <p><span style="color:green"><?= (htmlspecialchars($v['cost'], ENT_QUOTES)); ?></span>円</p>
                        </td>
                        <!-- my_recipeテーブル db 何人前 -->
                        <td>
                            <p><span style="color:green"><?= (htmlspecialchars($v['how_many_servings'], ENT_QUOTES)); ?></span>人分</p>
                        </td>
                        <!-- my_recipeテーブル db完成画像 -->
                        <td>
                            <p><span style="color:green"><?php if ($v['complete_img'] !== '') : ?>
                                        <!-- もしも画像があったら表示する -->
                                        <img id="compimg" class="img" src="./images/<?php print(htmlspecialchars(
                                                                                        $v['complete_img'],
                                                                                        ENT_QUOTES
                                                                                    ));  ?>" width="135px" height="auto">
                                    <?php endif; ?>
                            </p>
                        </td>
                        <!-- my_recipeテーブル db   作成日 -->
                        <td>
                            <p><span style="color:green"><?= (htmlspecialchars($v['created_date'], ENT_QUOTES)); ?></span></p>
                        </td>

                        <!-- レシピの公開 -->
                        <form method="GET" action="confirm.php">
                            <td><input type='hidden' name='id' value="<?= $v['recipe_id'] ?>"></td>
                            <td><input type="submit" value="レシピの公開" class="btn-border" /></td>
                        </form>

                        <!-- 削除・処理 -->
                        <form method="POST" action="update_r_d.php">
                            <!-- my_recipesテーブルの、$['is_deleted'] = 1 の時、'WHERE is_deleted = 1;' 表示を切り替えることができる -->

                            <input type="hidden" name="id" value="<?php echo $id ?>">
                            <input type='hidden' name='id' value="<?= $v['recipe_id'] ?>">
                            <input type="hidden" name="is_deleted" value="1">
                            <!-- 削除（Delete） ボタン -->

                            <td><input type="submit" class="update" name="del" value="Delete" onClick="return dispDelete();" style="
                            font-size: 12px;
                            width: 45px;
                            height: 20px; 
                            border-radius: 6px;
                            border:none;
                            color: #ffffff;
                            background: #000000;" /></td>
                    </tr>
                    </form>

        <!-- div_wrap -->
        </div>


    <!-- endforeach -->
    <?php } ?>


    <!-- endif -->
    <div class="div_w2">


        <!-- ページング はじまり -->
        <div class="flex">
            <ul class="bar">
                <li>
                    <!-- lighter細字にする。 -->
                    <span style="color:#000000;font-size:15px;font-weight:lighter">
                        全アイテム数:<?php echo $total_count ?>品</span>
                    &nbsp;&nbsp;

                    <?php
                    //ページネーションを表示    
                    if ($now > 1) {
                        // 1ページより大きいなら、「前へ」表示
                        echo '<a href="?page_id=', ($now - 1), '">
                             <img src="../../icon_img/pre3.png"
                             alt="前へ" width="20" height="20" border="0">
                            </a>';
                    } else {
                        //  1ページよりも小さい＝ページが無い、場合は矢印は表示させない。
                    }
                    ?>

                    <?php
                    // 1 2 3 と、表示するページの数を$pagesを今回は使わず、1 2 3 4 5 と、'5'つにする。
                    for ($n = 1; $n <= $pages; $n++) {
                        if ($n == $now) {
                            echo "<span style='padding: 5px;'>$now</span>";
                        } else {
                            echo "<a href='./index.php?page_id=$n' 
                                            style='padding:5px;
                                          '
                                            '>$n</a>";
                            // hrefのリンクは、表示現在表示するリンクに修正して使うこと。
                        }
                    }
                    ?>

                    <?php
                    if ($now < $pages) {
                        // 表示ページが最終ページより小さいなら、「次へ」表示
                        echo '<a href="?page_id=', ($now + 1), '">
                                <img src="../../icon_img/next3.png"
                                    alt="次へ" width="20" height="20" border="0" margin-top:1px>
                                </a>';
                    }
                    ?>
                </li>
            </ul>


        </div>
        <!-- ページネーション囲むDIVおわり -->
    </div>


    </div>
    <!-- div_Comprehensive -->
    </div>

    </div>

    </div>

</body>

</html>