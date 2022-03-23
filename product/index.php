<?php

session_start();



// 必要なファイルを読み込む
require_once('../class/db/Base.php');
require_once('../class/db/CreateRecipes.php');

// 1ページの$list でFETCH ALL の表示数
define('max_view', 6);


// ログインメンバーが管理者でなかったら、ログイン画面に遷移する
if ($_SESSION['member'] !== 104) {

    try {

        $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
        $date = $dt->format('Y-m-d');

        // データベースに接続するための文字列（DSN・接続文字列）
        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

        // エラーが起きたときのモードを指定する
        // 「PDO::ERRMODE_EXCEPTION」を指定すると、エラー発生時に例外がスローされる
        $dbh = new PDO($dsn, 'root', '');


        //  membersテーブルmenbers.id=104 の管理者の「アイコン画像」だけ表示)
        //  このページは、members.id=104の人だけしか入出不可（管理者特権！）。
        //  ※詳細は、/login/process.phpで説明。
        $sql = "SELECT * FROM members WHERE id = 104";

        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $member = $result->fetchAll(PDO::FETCH_ASSOC);


        // 商品アイテムリストに、メーカー名を表示するためにリレーションします。
        // 商品テーブルの：メーカーid と、メーカーテーブルの：id をリレーション
        $sql = "SELECT * FROM product_lists lEFT JOIN makers ON product_lists.maker_id = makers.id
                ";
        // left JOIN は、左側が条件に合致しなくても（空）でも、表示します
        // JOINの前に記述したテーブルを左、JOINの後に記述したテーブルを右としてかんがえます
        // 一方、内部結合だと、INNER JOIN の場合、どちらかが（空）だと表示してくれません

        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $list = $result->fetchAll(PDO::FETCH_ASSOC);



        // ここから、マイレシピのページングの処理
        $total_count = count($list);


        // ページ数= 全商品数/1ページの表示数
        // トータルページ数※ceilは小数点を切り捨てる関数
        $pages = ceil($total_count / max_view);



        //現在いるページのページ番号を取得
        if (!isset($_GET['page_id'])) {
            $now = 1;
        } else {
            $now = $_GET['page_id'];
        }

        // ページネーションの1ページ目のsqlの処理・1ページ以外のsqlの処理
        //表示するページを取得するSQLを準備

        $select = $dbh->prepare("SELECT *
                FROM product_lists
                LEFT OUTER JOIN makers ON product_lists.maker_id = makers.id
                WHERE product_lists.is_deleted = 0 ORDER BY product_lists.id DESC LIMIT :start,:max ");
        // 最新のものをトップで１番最初に表示させる！

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

// もしログインメンバーidが101では無ければ、
// マイページへ遷移する
} else {
    header('Location: ../../member/login/process.php');
    exit();
}



//  もしも、新規レシピ作成のsentボタンが押されたら、name = "sent"
if (!empty($_POST['send'])) {

    //  エラーチェックを走らせます
    if ($_POST['product_name'] === '') {
        $error['product_name'] = 'blank';
    }

    if ($_POST['amount'] === '') {
        $error['amount'] = 'blank';
    }
    if ($_POST['coo'] === '') {
        $error['coo'] = 'blank';
    }
    if ($_POST['categorie_name'] === '') {
        $error['categorie_name'] = 'blank';
    }

    if ($_POST['handling_start_date'] === '') {
        $error['handling_start_date'] = 'blank';
    }


    $fileName = $_FILES['img']['name'];

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
            $_FILES['img']['tmp_name'],
            './images/' . $image
        );


        $_SESSION['product'] = $_POST;
        $_SESSION['product']['img'] = $image;




        // エラーが無ければ、確認画面に遷移する
        header('Location: add.php');
        exit();
    }
}





if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    // （1時間が経過していたら、）ログアウトし、ログイン画面に遷移する
    $_SESSION['time'] = time();
    // 現在の時刻で上書きします。こうすることで、何か行動したことで上書きすることで
    // 最後の時刻から１時間を記録することができるようになる。 
} elseif ($_SESSION['member'] = []) {
    header('Location: ../login/join.php');
    exit();
    // 更新時刻より１時間経過していなくとも、クッキーの削除でセッション情報が空になったら
    // ログイン画面に遷移する
} else {
    // 何か行動した更新時刻より１時間経過したら、自動的にログイン画面に遷移します
    header('Location: ../login/join.php');
    exit();
}


?>



<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>スパイス店</title>



    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
    <!--  全体 CSS -->
    <link rel="stylesheet" href="css/stylesheet1.css">
    <!--  ページネーション -->
    <link rel="stylesheet" href="css/style_paging.css">



</head>


<body>
    <!-- 画像アップロードボタン の、「ファイル名を表示させる」ためにjs を使用します -->


    <!-- Javascript ファイルを読み込む -->
    <script src="./js/delete/delete.js"></script>
    <script src="./js/delete/delete.js"></script>

    </script>

    <?php foreach ($member as $v) : ?>
        <div class='div_p'><?php echo $v['nickname']; ?><span style="font-size:18px;color:green;">管理者用&nbsp;</span>MY Product lists 商品リスト
        <?php endforeach ?>
        <!-- マイページ -->
        <div class="div_logout"><input type="button" value='ログアウト' class="logout_btn" onclick="location.href='../logout/logout.php'">
            <!-- /member/logout/process.php -->
        </div>
        <!-- ログアウト -->
        <div class="div_logout"><input type="button" value='マイページ' class="logout_btn" onclick="location.href='../login/process.php'">
            <!-- /member/logout/process.php -->
        </div>

        <!-- みんなのレシピ -->
        <div class="div_logout"><input type="button" value='みんなのレシピ' class="logout_btn" onclick="location.href='../top/confirm.php'">
   
        </div>



        <!-- 商品一覧 -->
        <div class="div_logout"><input type="button" value='商品一覧' class="logout_btn" onclick="location.href='./product_lists.php'">
       
        </div>


        <!-- 虫眼鏡 検索バー はじまり-->

        <!-- ここにレシピアイテム検索ツールがはいります -->
        <form action="./material/serch_mushimegane.php" method="GET">


            <!-- 虫眼鏡 検索バー -->
            <!-- <div class="mushi-megane"> -->

            <!-- 虫眼鏡検索で『商品名』が入力がされたら、 -->
            <input type="text" class="form-input" name="product" value="
            " placeholder='serch' maxlength="24" />

            <!-- 虫眼鏡のボタン -->


            <button type="submit">
                <div>検索</div>
            </button>

        <!--  div_mushimegane おわり -->
        </div>
        </form>

        </div>


        <div class="comprehensive">

            <div class='inline_block_2'>
                <div class="div_img3">
                    <?php foreach ($member as $v) : ?>
                        <img class="img3" src="../member_picture/<?php echo $v['icon_img'] ?>" width="90px" height="auto">
                    <?php endforeach ?>
                </div>


                <br>
                <div class="clear-both">
                    <!-- フォーム -->
                    <form action="" method="post" enctype="multipart/form-data">
                        <dt class="wf-sawarabimincho">商品名<span style="color:red;font-size:11px">※必須</span></dt>

                        <?php if (isset($_POST['product_name'])) { ?>
                            <input type="text" name="product_name" class="input_height" maxlength="255" value="<?php print(htmlspecialchars(
                                                                                                                    $_POST['product_name'],
                                                                                                                    ENT_QUOTES
                                                                                                                )); ?>">
                        <?php } else { ?>
                            <input type="text" name="product_name" class="input_height" maxlength="255" value="">
                        <?php } ?>
                        <?php if (!empty($error['product_name'])) : ?>
                            <p class="error">* 商品名を入力してください</p>
                        <?php endif ?>
                </div>

                <!-- フォーム2 内容量 -->

                <dt class="wf-sawarabimincho">内容量<span style="color:red;font-size:11px">※必須</span></dt>
                <div class="clear-both">
                    <?php if (isset($_POST['amount'])) { ?>
                        <input type="text" name="amount" class="input_height" maxlength="255" value="<?php print(htmlspecialchars(
                                                                                                            $_POST['amount'],
                                                                                                            ENT_QUOTES
                                                                                                        )); ?>">
                    <?php } else { ?>
                        <input type="text" name="amount" class="input_height" maxlength="255" value="">
                    <?php } ?>
                    <?php if (!empty($error['amount'])) : ?>
                        <p class="error">* 内容量を入力してください</p>
                    <?php endif ?>
                </div>

                <!-- フォーム3 材料費 -->


                <dt class="wf-sawarabimincho">原産国<span style="color:red;font-size:11px">※必須</span></dt>
                <div class="clear-both">
                    <?php if (isset($_POST['coo'])) { ?>
                        <input type="text" name="coo" class="input_height" maxlength="255" value="<?php print(htmlspecialchars(
                                                                                                        $_POST['coo'],
                                                                                                        ENT_QUOTES
                                                                                                    )); ?>">
                    <?php } else { ?>
                        <input type="text" name="coo" class="input_height" maxlength="255" value="">
                    <?php } ?>
                    <?php if (!empty($error['coo'])) : ?>
                        <p class="error">* 原産国を入力してください</p>
                    <?php endif ?>

                </div>

                <!-- フォーム4   何人分 -->

                <dt class="wf-sawarabimincho">カテゴリー名<span style="color:red;font-size:11px">※必須</span></dt>
                <div class="clear-both">
                    <?php if (isset($_POST['categorie_name'])) { ?>
                        <input type="text" name="categorie_name" class="input_height" maxlength="255" value="<?php print(htmlspecialchars(
                                                                                                                    $_POST['categorie_name'],
                                                                                                                    ENT_QUOTES
                                                                                                                )); ?>">
                    <?php } else { ?>
                        <input type="text" name="categorie_name" class="input_height" maxlength="255" value="">
                    <?php } ?>
                    <?php if (!empty($error['categorie_name'])) : ?>
                        <p class="error">* カテゴリー名を入力してください</p>
                    <?php endif ?>
                </div>

                <!-- フォーム5 完成イメージ画像 -->

                <dt class="wf-sawarabimincho">商品画像<span style="color:red;font-size:11px">※必須</span></dt>
                <div class="clear-both">
                    <!-- 画像アップロードlabelボタン -->
                    <div class="div_file">
                        <label class="label_btn">
                            <input type="file" name="img">画像ファイルを選択
                        </label>
                    </div>
                </div>

                <!-- エラー -->
                <?php if (!empty($error['img'])) : ?>
                    <p class="error">* 完成画像を選択してください</p>
                <?php endif ?>
                <?php if (!empty($error['image'])) : ?>
                    <p class="error">* 写真などは「.gif」または「.jpg」「.png」の画像を指定してください</p>
                <?php endif ?>
                <?php if (!empty($error)) : ?>
                    <p class="error">*恐れ入りますが、画像を改めて指定してください</p>
                <?php endif ?>


                <!-- フォーム7 取扱い開始日 -->

                <div class="clear-both">
                    <dt class="wf-sawarabimincho">取扱い開始日<span style="color:red;font-size:11px">※必須</span></dt>
                    <label class="label_date">
                        <input type="date" name="handling_start_date" class="input_height" maxlength="255" value="">
                    </label>

                    <?php if (!empty($error['handling_start_date'])) : ?>
                        <p class="error">* 取扱い開始日を入力してください</p>
                    <?php endif ?>
                </div>
                <!-- "登録確認"ボタン -->
                <div class="div_f_left">
                    <input type="submit" class="update" name="send" style="width: 111px;
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




            </div class="wrap11">
            <div class="inline_block_3">
                <!-- product_listsテーブル*データベースに登録したテキストデータと画像データを出力表示する -->


                <table width="825px">
                    <tr class="table th">

                        <th></th>
                        <th>
                            <p class='wf-sawarabimincho'>商品ID</p>
                        </th>
                        <th>
                            <p class='wf-sawarabimincho'>商品名</p>
                        </th>

                        <th>
                            <p class='wf-sawarabimincho'>画像</p>
                        </th>
                        <th>
                            <p class='wf-sawarabimincho'>価格</p>
                        </th>
                        <th>
                            <p class='wf-sawarabimincho'>メーカー名</p>
                        </th>
                        <th>
                            <p class='wf-sawarabimincho'>内容量</p>
                        </th>
                    </tr>


                    <?php
                    foreach ($data as $v) :
                    ?>

                        <!-- *データベースからSELECTして配列に保管してある、$v['is_deleted'] == 0 であったら、レシピを表示 -->
                        <?php if (empty($v['is_deleted'])) { ?>
                            <!-- is_deleted == 1 で1が入って削除、0なら表示 -->



                            <tr>
                                <!-- フォーム GET 編集画面へ -->
                                <!-- リンク・商品product_listsテーブルの商品の編集 -->
                                <form method="GET" action="./edit/confirm.php">
                                    <!-- 'フォーム Hidden' 商品ID -->

                                    <input type='hidden' name='id' value="<?= $v['id'] ?>">

                                    <td><input type="submit" value="編集" class="btn-border" /></td>

                                </form>
                                <!-- foreach でのレシピ表示 -->

                                <!-- product_lists   商品ID -->
                                <td width="80px">
                                    <p><span style="color:green"><?= (htmlspecialchars($v['id'], ENT_QUOTES)); ?></span></p>
                                </td>
                                <!-- product_listsテーブル   アイテム名 -->
                                <td width="80px">
                                    <p><span style="color:green"><?= (htmlspecialchars($v['product_name'], ENT_QUOTES)); ?></span></p>
                                </td>
                                <!-- product_listsテーブル   商品画像  -->
                                <!-- <a href="../edit/recipe/release_recipe.php?id= -->

                                <td>
                                    <a href="product_introduction.php?id=<?php echo $v['id'] ?>">
                                        <img id="compimg" class="img" src="./images/<?php echo $v['img']
                                                                                    ?>" width="101" height="auto"></p>
                                    </a>
                                </td>

                                <!-- product_listsテーブル   価格 -->
                                <td>
                                    <p><span style="color:green"><?= (htmlspecialchars($v['price'], ENT_QUOTES)); ?></span>円</p>
                                </td>
                                <!-- product_listsテーブル   メーカー名-->
                                <td>
                                    <p><span style="color:green"><?= (htmlspecialchars($v['names'], ENT_QUOTES)); ?></span></p>
                                </td>
                                <!-- product_listsテーブル   カテゴリー -->
                                <td>
                                    <p><span style="color:green"><?= (htmlspecialchars($v['amount'], ENT_QUOTES)); ?></span></p>
                                </td>




                                <!-- 削除・処理 -->
                                <form method="POST" action="update_is_del.php">
                                    <!-- 商品テーブルの、$['is_deleted'] = 1 の時、'WHERE is_deleted = 1;' 表示を切り替えることができる -->
                                    <!-- <form method="POST" action="action.php">完全削除する時 action.phpへPOSTします。 -->

                                    <input type="hidden" name="id" value="<?= $v['id'] ?>">
                                    <input type="hidden" name="id" value="<?php echo $id ?>">


                                    <td><input type='hidden' name='id' value="<?= $v['id'] ?>"></td>
                                    <td><input type="hidden" name="is_deleted" value="1" <?= $v['is_deleted'] == 1 ?>>


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

            </div>
            <!-- div_wrap -->
        <?php } ?>
        <!-- endif -->
    <?php endforeach ?>
    <!-- endforeach -->


 
    <!-- ページング -->
    <div class="div_w2">


        <!-- ページングCSS -->
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
                        <img src="../icon_img/pre.png"
                        alt="前へ" width="25" height="25" border="0">
                    </a>';
                    } else {
                        //  1ページよりも小さい＝ページが無い、場合は矢印は表示させない。
                    }
                    ?>

                    <?php
                    // 1 2 3 と、表示するページの数を$pagesを今回は使わず、1 2 3 4 5 と、'5'つにする。
                    for ($n = 1; $n <= 5; $n++) {
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
                        <img src="../icon_img/next.png"
                            alt="次へ" width="25" height="25" border="0" margin-top:1px>
                        </a>';
                    }
                    ?>
                </li>
            </ul>

        </div>

    </div>
    <!-- ページネーション囲むDIVおわり -->

 

        </div>
        </div>
        <!-- div_Comprehensive -->
        </div>

        </div>


</body>

</html>