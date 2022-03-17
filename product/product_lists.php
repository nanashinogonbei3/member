<?php

session_start();

// 必要なファイルを読み込む
require_once('../class/db/Base.php');
require_once('../class/db/CreateRecipes.php');
// 1ページの$list でFETCH ALL の表示数
define('max_view', 15);


try {

    $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
    $date = $dt->format('Y-m-d');

    // データベースに接続するための文字列（DSN・接続文字列）
    $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

    $dbh = new PDO($dsn, 'root', '');
    // エラーが起きたときのモードを指定する
    // 「PDO::ERRMODE_EXCEPTION」を指定すると、エラー発生時に例外がスローされる


    //  membersテーブルmenbers.id=104 の管理者の「アイコン画像」だけ表示)
    //  このページは、members.id=104の人だけしか入出不可（管理者特権！）。
    //  ※詳細は、/login/process.phpで説明。
    $sql = "SELECT * FROM members WHERE id = 104";

    $stmt = $dbh->prepare($sql);

    $stmt->execute();

    $result = $dbh->query($sql);

    $member = $result->fetchAll(PDO::FETCH_ASSOC);


    $sql = "SELECT * FROM product_lists lEFT JOIN makers ON product_lists.maker_id = makers.id
            ";

    $stmt = $dbh->prepare($sql);

    $stmt->execute();

    $result = $dbh->query($sql);

    $list = $result->fetchAll(PDO::FETCH_ASSOC);

    // ここから、マイレシピのページングの処理
    $total_count = count($list);
 

    // ページ数= 全商品数/1ページの表示数
    // トータルページ数※ceilは小数点を切り上げる関数1.6⇒2
    $pages = ceil($total_count / max_view);



    //現在いるページのページ番号を取得
    if (!isset($_GET['page_id'])) {
        $now = 1;
    } else {
        $now = $_GET['page_id'];
    }

    // ページネーションの1ページ目のsqlの処理・1ページ以外のsqlの処理
    //表示するページを取得するSQLを準備
    $select = $dbh->prepare("SELECT distinct *
            FROM product_lists
            INNER JOIN makers ON product_lists.maker_id = makers.id
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


if (!empty($_REQUEST['action'])) {
    if ($_REQUEST['action'] == 'rewrite' && isset($_SESSION['product'])) {
        $_POST = $_SESSION['product'];
    }
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


    <div class='div_p'><span style="font-size:18px;color:green;"></span>Product lists 商品リスト


        <?php if (!empty($_SESSION['member'])) { ?>
            <!-- ログアウト -->
            <div class="div_logout"><input type="button" value='ログアウト' class="logout_btn" onclick="location.href='../logout/logout.php'">
            </div>
        <?php } else { ?>
            <!-- ログアウト -->
            <div class="div_logout"><input type="button" value='ログイン' class="logout_btn" onclick="location.href='../login/join.php'">
            </div>
        <?php } ?>


        <?php if (!empty($_SESSION['member'])) { ?>
            <!-- マイページ -->
            <div class="div_logout"><input type="button" value='マイページ' class="logout_btn" onclick="location.href='../login/process.php'">
            </div>
        <?php } else { ?>
            <!-- ログアウト状態ならマイページは表示しない -->
        <?php } ?>


        <!-- もしログイン状態で -->
        <?php if (!empty($_SESSION['member'])) : ?>
            <!-- 管理者id === 104 なら商品の編集ボタンを表示する -->
            <?php if ($_SESSION['member'] === 104) { ?>
                <!-- 管理者ログイン -->
                <div class="div_logout"><input type="button" value='商品の編集' class="logout_btn" onclick="location.href='index.php'">
                </div>
                <!-- // ログインメンバーのid が104以外なら、「商品の編集」ボタンは表示しない。 -->
            <?php } elseif ($_SESSION['member'] !== 104) { ?>




            <?php } ?>
        <?php endif ?>


        <?php if (!empty($_SESSION['member'])) { ?>
            <!-- 買い物カゴボタン -->
            <div class="div_logout3">
                <input type="button" value="カートを見る" class="shop-order" onclick="
                        location.href='./cart/cart_show.php'">
            </div>
        <?php } ?>


        <!-- 虫眼鏡 検索バー はじまり-->

        <!-- ここにレシピアイテム検索ツールがはいります -->
        <form action="./material/serch_mushimegane.php" method="GET">
            <!-- 検索ワード入力画面 -->

            <!-- 帰りにこのページに戻ってこれるように、$idをhiddenにして渡す -->

            <!-- 虫眼鏡 検索バー -->


            <!-- 虫眼鏡検索で『商品名』が入力がされたら、 -->
            <?php if (!empty($_GET['recipe_name'])) { ?>
                <input type="text" class="form-input" name="product" value="<?php print(htmlspecialchars(
                                                                                $_GET['product_name'],
                                                                                //name=product(商品検索)    
                                                                                ENT_QUOTES
                                                                            )); ?>" placeholder='serch' maxlength="24" />
            <?php } else { ?>
                <input type="text" class="form-input" name="product" value="" placeholder='serch' maxlength="24" />
            <?php } ?>

            <!-- 虫眼鏡のボタン -->
            <button type="submit">
                <i class="fas fa-search"></i> 検索
            </button>


            <!-- ページングCSS -->
            <div class="div_w5">
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
                                    echo "<a href='./product_lists.php?page_id=$n' 
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
            <!-- ページネーション囲むDIVおわり -->                
            </div>



            <div class="inline_block_5">

                <div class="parent">
                    <?php foreach ($data as $v) : ?>
                        <div class="item_2">
                            <!-- もし未ログインなら -->
                            <?php if (empty($_SESSION['member'])) { ?>
                                <a href="./product_introduction_no_login.php?id=
                    <?php echo $v['id'] ?>">
                                    <img id="product" src="./images/<?php echo $v['img'] ?>" alt="">
                                </a>
                                <!-- もし既ログインなら ※既ログインなら「お気に入り登録できる」 -->
                            <?php } elseif (!empty($_SESSION['member'])) { ?>
                                <a href="./product_introduction.php?id=
                    <?php echo $v['id'] ?>">
                                    <img id="product" src="./images/<?php echo $v['img'] ?>" alt="">
                                </a>
                            <?php } ?>
                        </div>
                    <?php endforeach ?>
                </div>
                <!-- div_Comprehensive -->
            </div>



</body>

</html>