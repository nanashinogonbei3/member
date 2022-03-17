<?php
session_start();

require_once('../../class/db/Base.php');
require_once('../../class/db/CreateRecipes.php');


// どの商品アイテムか？
$id = $_GET['id'];



try {


    if (empty($_GET['id'])) {
        header("Location: ../index.php");
    } else {


        $id = $_GET['id'];

        $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
        $date = $dt->format('Y-m-d');

        //データに接続するための文字列
        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

        $dbh = new PDO($dsn, 'root', '');

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //  冒頭で作った変数の$id = $_GET['id'] 選んだ商品id をここで代入する
        $sql = "SELECT * FROM product_lists
        LEFT JOIN makers ON product_lists.maker_id = makers.id 
        WHERE product_lists.id= '" . $id . "' ";

        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $record = $result->fetch(PDO::FETCH_ASSOC);




        //   今、画面で選択している商品を表示する
        $product_id = $record['id'];

        // echo '今表示している商品のメーカーidは'. $record['maker_id'] . 'です';
        // $makerid = $record['maker_id'];

        // ここからメーカーテーブル
        // SELECT文で、全メーカーを選択できるように表示させるためのFETCH()ALLする。
        $sql = "SELECT id,names FROM makers WHERE id
        
        ";


        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);


        $date = $result->fetchAll(PDO::FETCH_ASSOC);




        // SELECT文で、全メーカーを選択できるように表示させるためのFETCH()ALLする。

        $sql = "SELECT id FROM makers WHERE id";


        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);


        $part = $result->fetchAll(PDO::FETCH_ASSOC);



        // 商品テーブルの：メーカーid と、メーカーテーブルの：id をリレーション
        $sql = "SELECT * FROM product_lists";

        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $list = $result->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    echo 'DBに接続できません: ',  $e->getMessage(), "\n";
    echo '<pre>';
    var_dump($e);
    echo '</pre>';
    echo $e->getMessage();
    exit;
}


?>

<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>商品アイテムの編集（管理者用）</title>


    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/stylesheet2.css">

</head>




<body>

    <div class='div_p'><a name="#">
            <p class="title_font">商品アイテムの編集（管理者用）</p>
            <!-- 商品の詳細 -->
            <div class="div_logout2"><input type="button" value='商品の詳細' class="logout_btn" onclick="location.href='../product_introduction.php?id=<?php echo $product_id ?>'">

            </div>
        </a>

    </div>
    <!-- </a>は、a name="#"page topのリンク。don't delete. -->
    <div class='inline_block_2'>


        <div class="comprehensive">
            <div class="block1">

                <!-- DIV トップの左側（商品画像） -->
                <div class="div_hidari">
                    <div class="div_edit">


                        <!-- 商品アイテムを選び直すボタン -->
                        <div class="div_re-order">


                            <input type="button" class="re-order" onclick=" 
                            

                            location.href='../index.php?idm=<?php echo $record['id'] ?> action=rewrite'" value='商品アイテムを選び直す'>

                        </div>


                        <!-- フォーム  -->
                        <form action="update.php" method="post" enctype="multipart/form-data">
                            <!-- 隠し送信する商品ID -->
                            <input type="hidden" name="id" value="<?= $record['id'] ?>">


                            <!-- データベースからFETCH()した、商品名） -->
                            <dt class="wf-sawarabimincho">今の商品アイテム名:
                            <dt class="p_font_rarge"><span style="color:green"><?php echo $record['product_name'] ?></span></dt>
                            </dt>


                            <!-- 更新 入力フォーム / 修正する商品アイテム名 -->
                            <!-- あたらしい商品アイテム名を入力してください -->
                            <dt>
                                <p class="p_font_rarge">🔲<input type="text" name="product_name" size="40" maxlength="20" placeholder='修正後の商品アイテム名' maxength="255"></p>
                            </dt>


                            <!-- データベースからFETCH()した、商品カテゴリー名） -->
                            <dt class="wf-sawarabimincho">今のカテゴリー名:&nbsp;<span style="color:green"><?php echo $record['categorie_name'] ?></span></dt>


                            <!-- 更新 入力フォーム / 修正する商品カテゴリー名 -->
                            <!-- あたらしい商品カテゴリー名を入力してください -->
                            <dt>
                                <p class="p_font_rarge">🔲<input type="text" name="categorie_name" size="40" maxlength="20" placeholder='修正後の商品カテゴリー名' maxength="255"></p>
                            </dt>

                            <!-- データベースからFETCH()した、  取扱い開始日 -->
                            <dt class="wf-sawarabimincho">取扱い開始日：
                                <span style="color:green"><?php echo $record['handling_start_date'] ?></span>
                            </dt>


                            <!-- 更新 入力フォーム / 修正後の取扱い開始日-->
                            <!-- 取扱い開始日を入力してください -->
                            <dt>
                                <p class="p_font_rarge">🔲<input type="date" name="handling_start_date" size="40" maxlength="20" placeholder='取扱い開始日'></p>
                            </dt>



                            <div>
                                <!-- データベースからFETCH()した、商品ID -->
                                <dt class="wf-sawarabimincho">商品ID：
                                    <span style="color:green"><?php echo $record['id'] ?></span>
                                </dt>

                            </div>


                            <div class="item_l">
                                <!-- データベースからFETCH()した、商品画像 -->
                                <span style="color:green">
                                    <p class="wf-sawarabimincho">
                                        <img height="150px" src="../images/<?php echo $record['img']
                                                                            ?>">


                                        <!-- 更新 入力フォーム / あらたな商品画像 -->
                                        <input type="file" name="img">
                            </div>

                            <!-- ボタンを２つ横並びに配列させる。 -->
                            <div class="comprehensive">
                                <!-- "商品情報   更新する"ボタン -->
                                <div class="f_left">
                                    <dt><input type="submit" class="update" value="更新する" style="width: 120px;
                            color: #4F5902;
                            height: 33px;
                            font-size: 16px;
                            border-radius: 10px;
                            border: none;
                            background-color: #E9C8A7;
                            background-color: #D9CC1E
                            ">
                                    </dt>
                                </div>
                                <div class="test">
                                    <!-- 戻る -->
                                    <input type="button" class="re-order" onclick="window.history.back();" value="前のページに戻る">

                                </div>
                            </div>

                    </div>
                    <!-- div_editおわり -->


                </div>
                <!-- div_hidariおわり -->
                </form>




                <!-- DIV 右側はじまり -->
                <div class="div_migi">

                    <div class="div_edit_r">

                        <!-- フォーム  -->
                        <form action="update_prices.php" method="post" enctype="multipart/form-data">
                            <!-- 隠し送信する商品ID -->
                            <input type="hidden" name="id" value="<?= $record['id'] ?>">

                            <table>
                                <thead>
                                    <tr>
                                        <th>
                                            <dt class="wf-sawarabimincho">販売価格
                                        </th>
                                        <th width="124px">
                                            <dt class="wf-sawarabimincho">仕入価格
                                        </th>
                                        <th width="80px">
                                            <dt class="wf-sawarabimincho">内容量
                                        </th>
                                        <th width="80px">
                                            <dt class="wf-sawarabimincho">原産国
                                        </th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <!-- データベースからFETCH()した、 販売価格 -->
                                        <td><span style="color:green"><?php echo $record['price'] ?></span>円</dt>
                                        </td>

                                        <!-- データベースからFETCH()した、 原価  -->
                                        <td width="111px"><span style="color:green">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $record['cost_price'] ?></span>円</dt>
                                        </td>

                                        <!-- データベースからFETCH()した、 内容量 -->
                                        <td>&nbsp;&nbsp;&nbsp;<span style="color:green"><?php echo $record['amount'] ?></span></dt>
                                        </td>

                                        <!-- データベースからFETCH()した、 原産国 -->
                                        <td>&nbsp;&nbsp;&nbsp;<span style="color:green"><?php echo $record['coo'] ?></span></dt>
                                        </td>

                                    </tr>
                                </tbody>
                            </table>
                            <!-- 区切り線 -->
                            <div class="line"></div>



                            <!-- 販売価格・仕入価格・内容量を更新する入力フォーム -->
                            <table>
                                <thead>
                                    <tr>
                                        <th>
                                            <dt class="wf-sawarabimincho">販売価格
                                        </th>
                                        <th>
                                            <dt class="wf-sawarabimincho">仕入価格
                                        </th>
                                        <th>
                                            <dt class="wf-sawarabimincho">内容量
                                        </th>
                                        <th>
                                            <dt class="wf-sawarabimincho">原産国
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>

                                        <!-- フォーム 販売価格 -->
                                        <td>
                                            <dt class="wf-sawarabimincho">
                                                <input type="text" name="price" size="5" maxlength="10" placeholder='380'>円
                                            </dt>
                                        </td>
                                        <!-- フォーム  仕入価格 -->
                                        <td>
                                            <dt class="wf-sawarabimincho">
                                                <input type="text" name="cost_price" size="5" maxlength="10" placeholder='150'>円
                                            </dt>
                                        </td>
                                        <!-- フォーム  内容量 -->
                                        <td>
                                            <dt class="wf-sawarabimincho">
                                                <input type="text" name="amount" size="5" maxlength="10" placeholder='100g'>
                                            </dt>
                                        </td>
                                        <!-- フォーム  原産国 -->
                                        <td>
                                            <dt class="wf-sawarabimincho">
                                                <input type="text" name="coo" size="8" maxlength="13" placeholder='インド'>
                                            </dt>
                                        </td>
                    </div>
                    </tr>
                    </tbody>
                    </table>

                    <!-- "商品情報 "ボタン -->
                    <div class="f_left">
                        <dt><input type="submit" class="update2" value="更新する" style="width: 120px;
                            ">
                        </dt>
                    </div>
                    </form>




                    <div class="div_w">
                        <div class="clear_both">

                            <!-- 入力フォーム メーカー -->
                            <form action="./update_maker.php" method="GET">


                                <input type="hidden" name="pid" value="<?php echo $product_id ?>">
                                <!-- 商品idを隠して送る  -->


                                <table width>
                                    <thead>
                                        <tr>
                                            <dt>🔲メーカー名 &nbsp;&nbsp;&nbsp;&nbsp;
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;🔲商品リストにアップする</dt>
                                            <th>
                                                <dt class="wf-sawarabimincho">メーカー
                                            </th>
                                            <th></th>
                                            <th>
                                                <dt class="wf-sawarabimincho">商品の公開
                                            </th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <!-- 隠し送信 Hidden メーカーID -->


                                                <select name="maker_id">

                                                    <?php
                                                    // メーカーを選ぶセレクトボックス 

                                                    foreach ($date as $v) { ?>


                                                        <option value="<?php echo $v['id'] ?>"><?php echo $v['names'] ?></option>


                                                    <?php } ?>


                                                </select>
                                            </td>

                                            <td>
                                                <!-- 保存ボタン -->
                                                <input type="submit" value="保存" class="material_add_btn">
                                            </td>
                            </form>

                            <!-- 入力フォーム 商品テーブル・公開・非公開 -->
                            <form action="./update_releace.php" method="POST">
                                <input type="hidden" name="id" value="<?php echo $id ?>">
                                <!-- 隠し送信 Hidden 商品ID   -->


                                <td>
                                    <select name="is_released">
                                        <option value="1" <?php echo $record['is_released'] ?>>商品リストにアップ</option>
                                        <option value="0" <?php echo $record['is_released'] ?>>未公開</option>
                                    </select>
                                </td>

                                <td>
                                    <!-- 保存ボタン -->
                                    <input type="submit" value="保存" id="mySubmit" class="material_add_btn">
                                </td>

                                </tr>
                                </tbody>

                                </table>
                            </form>

                        </div>

                        <!-- div_w おわり -->
                    </div>




                    <!-- メーカー名の再設定、商品アイテムの公開・非公開の設定 -->
                    <div class="div_w_under">

                        <table width="375px">
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


                            <tbody>
                                <tr>
                                    <!-- メーカーテーブルのデータベースからFETCH()した  メーカー名 -->
                                    <td><span style="color:green;font-size:13px">
                                            <?php echo $record['names'] ?></span></td>

                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                    <!-- 商品テーブルのデータベースからFECTCH()した リリース結果 -->
                                    <?php if ($record['is_released'] == 0) :
                                        // コメント表示
                                        $non_released = '未公開です';
                                    ?>
                                        <td width="72"><span style="color:green;font-size:13px">
                                                <dt><?php echo $non_released ?></dt>
                                            </span></td>

                                    <?php elseif ($record['is_released'] == 1) :
                                        // コメント表示 
                                        $released = '公開済です';
                                    ?>
                                        <td width="72"><span style="color:green;font-size:13px">
                                                <dt><?php echo $released ?></dt>
                                            </span></td>
                                    <?php endif ?>
                    </div>

                    </tbody>
                    </tbody>
                    </table>
                    </form>
                </div>

            </div>





            <!-- div class="comprehensive" おわり-->
        </div>



    </div>
    </div>


    <!-- ↓ ここから、商品説明入力画面 -->

    <div class="inline_block_5">
        <dt class="p_font_rarge">🔲商品説明
            <span style="color:green;font-size:20px">商品名:<?php echo $record['product_name'] ?></span>
        </dt>

        <!-- フォーム -->
        <form action="add_update_describ.php" method="POST" enctype="multipart/form-data">

            <!-- 商品説明 -->

            <div class="howto_use">
                <dt>🔲商品の特徴
                    <input type="hidden" name="id" value="<?php echo $id  ?>">
                </dt>

                <!-- フォーム1 商品説明を入力する-->
                <?php if (!empty($_POST['describes'])) { ?>
                    <textarea name="describes" value="
    <?php print(htmlspecialchars($_POST['describes'], ENT_QUOTES)); ?>" cols="35" rows="15" placeholder='清涼感のある強い香りです'></textarea>
                <?php } else { ?>
                    <textarea name="describes" value="
    " cols="35" rows="15" placeholder='清涼感のある強い香りです'></textarea>
                <?php } ?>

                <!-- 文字入力数エラー表示欄 -->
                <?php if (!empty($_SESSION['errrMsg1'])) { ?>
                    <dt class="errorMsg">
                        <?php echo $_SESSION['errrMsg1']; ?>
                    </dt>
                    <dt class="strLength">入力文字数は、
                        <?php echo $_SESSION['describeLength']; ?>文字でした。
                    </dt>
                <?php } ?>
                <!-- 文字入力数エラーおわり -->

                <!-- "商品情報   更新する"ボタン -->
                <div class="f_left">
                    <dt><input type="submit" class="update" value="更新する" style="width: 120px;">
                    </dt>
                </div>
            </div>

        </form>

        <!-- フォーム -->
        <form action="add_update_effic.php" method="POST" enctype="multipart/form-data">
            <!-- 効能 -->

            <div class="howto_use">
                <dt>🔲成分・効能</dt>
                <input type="hidden" name="id" value="<?php echo $id ?>">

                <!-- フォーム2 効能を入力する -->
                <?php if (!empty($_POST['efficacy'])) { ?>
                    <textarea name="efficacy" value="
    <?php print(htmlspecialchars($_POST['efficacy'], ENT_QUOTES)); ?>" cols="35" rows="15" placeholder='暑い日に体の調子を整えます'></textarea>
                <?php } else { ?>
                    <textarea name="efficacy" value="
    " cols="35" rows="15" placeholder='暑い日に体の調子を整えます'></textarea>
                <?php } ?>

                <!-- 文字入力数エラー表示 -->
                <?php if (!empty($_SESSION['errrMsg2'])) { ?>
                    <dt class="errorMsg">
                        <?php echo $_SESSION['errrMsg2']; ?>
                    </dt>
                    <dt class="strLength">入力文字数は、
                        <?php echo $_SESSION['efficLength']; ?>でした。
                    </dt>
                <?php } ?>
                <!-- 文字入力数エラー表示おわり -->

                <!-- 更新ボタン -->
                <!-- "商品情報   更新する"ボタン -->
                <div class="f_left">
                    <dt><input type="submit" class="update" value="更新する" style="width: 120px; ">
                    </dt>

                </div>
            </div>

        </form>

        <!-- フォーム -->
        <form action="add_update_howto.php" method="POST" enctype="multipart/form-data">
            <!-- 使用方法 -->

            <div class="howto_use">
                <dt>🔲使用方法</dt>
                <input type="hidden" name="id" value="<?php echo $id ?>">

                <!-- フォーム3 使用方法を入力する -->
                <textarea name="howto_use" value="
    <?php if (!empty($_POST['howto_use'])) {
        echo $_POST['howto_use'];
    } ?>" cols="35" rows="15" placeholder='テンパリングの際に弱火で炒めます'></textarea>
                <?php if (!empty($_SESSION['errrMsg3'])) { ?>
                    <dt class="errorMsg">
                        <?php echo $_SESSION['errrMsg3']; ?>
                    </dt>
                    <dt class="strLength">入力文字数は、
                        <?php echo $_SESSION['howtoLength']; ?>でした。
                    </dt>
                <?php } ?>
                </dt>

                <!-- 更新ボタン -->
                <!-- "商品情報   更新する"ボタン -->
                <div class="f_left">
                    <dt><input type="submit" class="update" value="更新する" style="width: 120px;">
                    </dt>

                </div>

            </div>

        </form>

        <!--  表示欄  -->
        <div class="parent">

            <div class="div_100p">

                <div style="display:inline-flex">

                    <!-- 表示欄   商品説明 -->
                    <form method="POST" action="update.php">
                </div>

                <div class="contents">
                    <div class="howto_use2">
                        <!-- 商品テーブルのデータベースからFETCH()した 使い方の説明  -->
                        <dt><span style="color:green;font-size:13px">
                                <td><?php echo  $record['describes'] ?></td>
                            </span></dt>

                    </div>
                    <div class="howto_use_right">
                        <!-- 商品テーブルのデータベースからFETCH()した 使い方の説明  -->
                        <dt><span style="color:green;font-size:13px">
                                <td><?php echo  $record['efficacy'] ?></td>
                            </span></dt>

                    </div>
                    <div class="howto_use_right">
                        <!-- 商品テーブルのデータベースからFETCH()した 使い方の説明  -->
                        <dt><span style="color:green;font-size:13px">
                                <td><?php echo  $record['howto_use'] ?></td>
                            </span></dt>
                    </div>
                </div>
                <!-- contentsおわり -->
                <div class="to_top">
                    <a href="#"><img src="../../icon_img/top.png" alt="topへ" width="80%"></a>
                </div>
            </div>

        </div>
        </form>

        <!-- inline_block_4 -->
    </div>
    <!-- div_precent -->
    </div>



    <script src="./js/movepage.js"></script>
</body>

</html>