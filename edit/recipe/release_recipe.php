<?php
session_start();

require_once('../../class/db/Base.php');
require_once('../../class/db/CreateRecipes.php');

// レシピid
$id = $_GET['id'];


// $id = $_POST['recipe_id'];
// action_m.phpで材料削除後、リダイレクトさせる変数を代入する
// action_subtitle.phpから、./confirm.phpへリダイレクトするため
// 削除したidだと戻れないのでセッションにいれておく。
if (!empty($id)) {
    $_SESSION['recipe_id'] = $id;
}



try {


    if (empty($_GET['id'])) {

        header("Location: ../../create/recipe/index.php");
    } else {

        // 送信データを受け取る レシピId
        $id = $_GET["id"];

        $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
        $date = $dt->format('Y-m-d');

        //データに接続するための文字列
        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

        $dbh = new PDO($dsn, 'root', '');

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT * FROM my_recipes
          
            WHERE id=" . $id;


        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $record = $result->fetch(PDO::FETCH_ASSOC);


        // このページで表示するため、短く書ける変数に格納します
        // テーブルからFETCHした、各カラムの情報を、作成した変数にそれぞれ格納します
        // $idは、my_recipes テーブルのid(どのレシピか？)判別するためのキーとして、
        // 今後、material のrecipe_idと、proceduresのp_recipe_ie らと関連づけるためのたいせつなものです。
        $id = $record["id"];


        $recipe_name = $record["recipe_name"];
        $complete_img = $record["complete_img"];
        $cooking_time = $record["cooking_time"];
        $cost = $record['cost'];

        $how_many_servings = $record["how_many_servings"];
        $created_date = $record['created_date'];


        // メンバーのニックネームを取り出す
        $sql = "SELECT nickname FROM members
            JOIN my_recipes ON members.id = 
            my_recipes.members_id
            WHERE my_recipes.id =" . $id;


        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $user = $result->fetch(PDO::FETCH_ASSOC);

        $nickname = $user['nickname'];


        // left JOIN material_parent_categoriesだと、親材料カテゴリに未登録でも表示できる
        // 親材料カテゴリーから登録済みの親カテゴリーテーブルに紐づいた、材料・分量を表示します
        $sql = 'SELECT materials.id, materials.material_name, materials.amount, 
            material_parent_categories.materials_parent_category_name
            FROM materials
            left JOIN material_parent_categories ON materials.parent_category_id = 
            material_parent_categories.id   
            left outer JOIN material_categories ON material_parent_categories.id =
            material_categories.parent_category_id
            WHERE materials.recipe_id = ' . $id . '
            ';

        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $list = $result->fetchAll(PDO::FETCH_ASSOC);


        // リレーションしたテーブルに同名カラムがあった時の対処法
        // SELECT {カラム名} AS {変更するカラム名} FROM {テーブル名};
        // カルーセルで表示するレシピで使った商品をFETCHする。
        $sql = 'SELECT product_lists.id, product_lists.img, product_lists.price,
            product_lists.amount as product_amount, materials.amount, 
            product_lists.product_name,
            makers.names
            FROM product_lists
            JOIN materials ON product_lists.id = materials.product_id
            INNER JOIN makers ON product_lists.maker_id = makers.id 
            WHERE materials.recipe_id = ' . $id . '
            AND product_lists.is_released = 1
            ';

        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $product = $result->fetchAll(PDO::FETCH_ASSOC);


        // 調理手順
        $sql2 = 'SELECT id,descriptions,p_img,p_recipe_id, created_date, update_date 
            FROM procedures WHERE p_recipe_id=' . $id . ' ';
        $sql2 .= 'ORDER BY created_date ASC';


        $stmt2 = $dbh->prepare($sql2);

        $stmt2->execute();

        $result2 = $dbh->query($sql2);

        $report = $result2->fetchAll(PDO::FETCH_ASSOC);


        // カテゴリー
        // ログインユーザーがこのレシピに登録済みのカテゴリー一覧を表示するためのFETCHをする
        $sql = "SELECT categories.id, categories.categories_name,
            recipe_categories.category_id, recipe_categories.my_recipe_id
            FROM recipe_categories 
            JOIN categories ON recipe_categories.category_id = categories.id
            JOIN my_recipes ON recipe_categories.my_recipe_id = my_recipes.id
            WHERE recipe_categories.my_recipe_id = '.$id.' ";


        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $data = $result->fetchAll(PDO::FETCH_ASSOC);



        // 材料のアドバイス「一口メモ」を表示するためのFETCHです。
        //material_categoriesテーブル
        $sql = "SELECT advice
            FROM advices JOIN my_recipes 
            ON advices.recipe_id = my_recipes.id
            WHERE my_recipes.id = " . $id . "
            ";

        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $advice = $result->fetchAll(PDO::FETCH_ASSOC);

        // 処理が終わったあとだから、冒頭でセッションに代入不可だったのでここでセッションにレシピIDを代入する。
        $_SESSION['recipe_id'] = $id;


        // レシピのサブタイトル・コメントを表示する

        $sql = "SELECT id, sub_title, comment FROM recipe_subtitles WHERE recipe_id=" . $id;

        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $subtitle = $result->fetchAll(PDO::FETCH_ASSOC);

        foreach ($subtitle as $v) {
            $subtitle = $v['sub_title'];
            $comment = $v['comment'];
        }


        //favorite_recipesテーブル
        // お気に入り商品の重複チェック
        // $array_count = array_count_values ( $favorite );

        // 一回目のお気に入りは、INSERT（add_favorite_recipe.php)で。
        // 2回目以降は、UPDATE（update_favorite_recipe.php）で。
        // ※2回目以降は、completed==1なら、value=0,completed==0なら、value=1を代入するだけ。
        $sql = "SELECT favorite_recipe_id, is_completed
             FROM favorite_recipes
            INNER JOIN my_recipes ON favorite_recipes.favorite_recipe_id = my_recipes.id
            WHERE favorite_recipes.favorite_recipe_id = '" . $id . "' 
            AND favorite_recipes.members_id = '" . $_SESSION['member'] . "'
            ";

        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $favorite = $result->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    echo 'proceduresのDBに接続できません: ',  $e->getMessage(), "\n";
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>レシピ・ノート</title>


    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />

    <!-- 全体 -->
    <link rel="stylesheet" href="./css/css/style_recipe.css">
    <!-- カルーセルのCSS/javascriptjQuery/ -->
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/my_script.js"></script>
   
    <link rel="stylesheet" href="css/style.css">
    <!-- 一口レシピ用javascript用 CSS -->
    <link rel="stylesheet" href="css/stylesheet.css">
    


</head>
</head>

<body>
    <div class="div_p">
        <dt class="wf-sawarabimincho"><span style="font-size:18px;color:green"><?php echo $nickname ?></span>さんの</span></dt>
        <dt class="title_font"><span style="font-size:33px"><a name="#">Recipe Note </a></span></dt>

        <!-- ログアウト -->

        <div class="div_logout">
            <!-- 既ログインなら -->
            <?php if (!empty($_SESSION['member'])) { ?>
                <input type="button" value='ログアウト' class="logout_btn" onclick="location.href='../../logout/process.php'">
            <?php } else { ?>
                <!-- 非表示 -->
            <?php } ?>
        </div>
        <!-- マイページ -->
        <div class="div_logout">
            <!-- 未ログインなら -->
            <?php if (empty($_SESSION['member'])) { ?>
                <input type="button" value='マイページ' class="logout_btn" onclick="location.href='../../login/join.php'">
            <?php } else { ?>
                <input type="button" value='マイページ' class="logout_btn" onclick="location.href='../../login/process.php'">
            <?php } ?>

        </div>
        <!-- みんなのレシピ -->
        <div class="div_logout">
            <!-- 未ログインなら -->
            <?php if (empty($_SESSION['member'])) { ?>
                <input type="button" value='みんなのレシピ' class="logout_btn" onclick="location.href='../../top/index.php'">
            <?php } else { ?>
                <!-- 既ログインなら -->
                <input type="button" value='みんなのレシピ' class="logout_btn" onclick="location.href='../../top/confirm.php'">
            <?php } ?>
        </div>


        <!-- お気に入り登録 -->
        <div class="div_favorite">
            <!-- 該当レシピが、favorite_recipesテーブルに登録が無ければ、 -->
            <?php if (empty($favorite['favorite_recipe_id'])) { ?>
                <form action="./favorite/add_favorite_recipe.php" method="POST">
                    <!-- 条件式の値をname="is_completed"へ代入する -->
                    <input type="hidden" name="favorite_recipe_id" value="<?php echo $id ?>">
                    <input type="hidden" name="members_id" value="<?php echo $_SESSION['member'] ?>">
                    <input type="hidden" name="is_completed" value=1>

                    <!-- ボタン -->
                    <div class="favorite_btn">
                        <!-- 登録する -->
                        <input type="image" src="../../icon_img/my_favorite0.png" alt="お気に入りに追加" width="50%">
                    </div>
                </form>

                <!-- 当該商品が、fovorite_productsテーブルに登録があれば -->
            <?php } else { ?>
                <!-- お気に入りの重複チェック -->
                <?php
                $array_count = array_count_values($favorite);
                //  重複していません。だからINSERTしましょう。 -->    
                if ($array_count == 0) {

                ?>
                    <form action="./favorite/add_favorite_recipe.php" method="POST">
                        <!-- 条件式の値をname="is_completed"へ代入する -->
                        <input type="hidden" name="favorite_recipe_id" value="<?php echo $id ?>">
                        <input type="hidden" name="members_id" value="<?php echo $_SESSION['member'] ?>">
                        <input type="hidden" name="is_completed" value=1>

                        <!-- ボタン -->
                        <div class="favorite_btn">
                            <!-- 登録する画像 -->
                            <input type="image" src="../../icon_img/my_favorite0.png" alt="お気に入りに追加" width="50%">
                        </div>
                    </form>

                    <!-- 重複しています。 -->
                <?php } elseif ($array_count >= 1) { ?>
                    <!-- ↓Updateソース -->
                    <form action="./favorite/update_favorite_recipe.php" method="POST">

                        <input type="hidden" name="favorite_recipe_id" value="<?php echo $favorite['favorite_recipe_id'] ?>">
                        <input type="hidden" name="members_id" value="<?php echo $_SESSION['member'] ?>">
                        <?php if ($favorite['is_completed'] == 0) {
                            $value_num = 1;
                        } elseif ($favorite['is_completed'] == 1) {
                            $value_num = 0;
                        }
                        ?>
                        <input type="hidden" name="is_completed" value=<?php echo $value_num ?>>

                        <!-- ボタン -->
                        <div class="favorite_btn">
                            <!-- ボタンのイラストの切り替え -->
                            <?php if ($favorite['is_completed'] == 0) { ?>
                                <input type="image" src="../../icon_img/my_favorite0.png" alt="お気に入りに追加" width="50%">
                            <?php } elseif ($favorite['is_completed'] == 1) { ?>
                                <input type="image" src="../../icon_img/my_favorite1.png" alt="お気に入りです" width="50%">
                            <?php } ?>
                        </div>
                    </form>
                <?php } ?>

            <?php } ?>
        </div>
        <!-- お気に入り登録終わり -->
    </div>
    <!-- End div_p -->
    <div class="comprehensive">
        <div class="hidari">
            <!-- --------------- -->
            <div class="left_container">
                <dt class="wf-sawarabimincho">
                <dt class="p_font_rarge"><span style="color:green"><?php echo $recipe_name ?>

                        <?php if (empty($subtitle)) { ?>
                            <div class="line"></div>
                            <!-- 区切り線 -->
                        <?php } elseif (!empty($subtitle)) { ?>
                            <!-- 区切り線は非表示に。 -->
                        <?php } ?>


                        <!-- もしもサブタイトルがあれば、区切り線も含めてサブタイトルを表示。 -->
                        <?php if (!empty($subtitle)) { ?>
                            <!-- サブタイトル↓ -->

                <dt class="wf-sawarabimincho"><span style="font-size:23px"><?php echo $subtitle ?></span></dt>

                <div class="line"></div>
                <!-- ↑区切り線 -->

            <?php } elseif (empty($subtitle)) { ?>
                <!-- サブタイトルが無ければ、区切り線も非表示に。 -->
            <?php } ?>


            <?php if (!empty($comment)) { ?>
                <!-- レシピのコメント↓ -->
                <dt class="wf-sawarabimincho"><span style="font-size:15px"><?php echo $comment ?></span></dt>


                <br>

                <!-- ↑区切り線 -->

            <?php } elseif (empty($comment)) { ?>
                <!--  -->
            <?php } ?>


            <!-- データベースからFETCH()した、完成画像 -->
            <span style="color:green">

                <img id="cimg" src="../../create/recipe/images/<?php echo $complete_img
                                                                ?>">
                <!-- データベースからFETCH()した、  レシピID -->
                <dt class="wf-sawarabimincho"><span style="color:#000000">ID：</span>
                    <span style="color:green"><?php echo $id ?>

                        <!-- データベースからFETCH()した、  登録日 -->
                        登録日：
                    </span>
                    <span style="color:green"><?php echo $created_date ?></span>
                </dt>
            </div>
            <!-- END left_container -->
            <!-- --------------------------------------------------------- -->
            <!-- カルーセル -->

            <!-- https://www.php.net/manual/ja/function.is-countable.php -->
            <?php

            is_countable($product);
            //配列の数が3以上あるか判定するためにカウントする。
            $cnt = count($product);


            ?>
            <!-- もし配列が3セット以上あれば、カルーセルを表示する。
            つまり、材料のなかに商品の登録が3つ以上あれば、カルーセル表示させる。 -->
            <?php if (!empty($product) && $cnt > 3) { ?>

                <div class="div_carousel">

                    <div id="carousel">
                        <p id="carouselPrev"><img src="./images/prev3.png" alt="前へ" width="50px"></p>
                        <p id="carouselNext"><img src="./images/next3.png" alt="次へ" width="50px"></p>
                        <dt>&nbsp;&nbsp;</dt>

                        <div id="carouselInner">

                            <?php foreach ($product as $v) : ?>

                                <ul class="column">
                                    <!-- リンク先 商品詳細ページ -->
                                    <li><a href="../../product/product_introduction.php?id=<?php echo $v['id'] ?>">
                                            <img id="img" src="../../product/images/<?php echo $v['img'] ?>" alt=""></a></li>
                                    <!-- 画像おわり  -->
                                    <li>
                                        <?php echo $v['product_name'] ?><br>
                                    </li>
                                    <li>
                                        <!-- ボタン（カルーセル内’商品詳細’） -->
                                        <div class="btn_carousel">
                                            <button id="carousel_btn" type=“button” class="order" onclick="
                            location.href='../../product/product_introduction.php?id=<?php echo $v['id'] ?>'">商品の詳細</button>
                                        </div>
                                    </li>
                                </ul>

                            <?php endforeach ?>


                        </div>
                        <!-- DIV carouselInnerおわり -->

                    </div>
                    <!-- DIV carouselおわり -->
                </div>
                <!-- DIV containerおわり -->
                <!-- カルーセルおわり -->
            <?php } else {
                //    なんにも<表示>しないデス。
            } ?>

        </div>
        <!-- End hidari -->

        <!-- ---右----------------------------------------------- -->
        <div class="migi">
            <div class="right_container">
                <!-- 登録したカテゴリをFETCHする$dataが在れば,FETCHデータを表示 -->
                <?php if (!empty($data)) { ?>


                    <?php foreach ($data as $key => $v) { ?>

                        <label class="acd-label3" for="acd-check1">
                            <a href="../../top/serch_mushimegane.php?id=<?php echo $id ?>
                        &serch=<?php echo $v['categories_name'] ?>"><?php echo $v['categories_name'] ?>
                            </a>
                        </label>

                    <?php } ?>
                <?php } else {
                    // FETCHデータが無ければメッセージを表示
                    echo '<dt>カテゴリーは未登録です</dt>';
                } ?>
            </div>
            <!-- END left_container -->

            <!-- -------------------------------------------------------------- -->
            <div class="div_w">

                <table>
                    <thead>
                        <tr>
                            <th>
                                <dt class="wf-sawarabimincho">調理時間
                            </th>
                            <th>
                                <dt class="wf-sawarabimincho">材料費
                            </th>
                            <th>
                                <dt class="wf-sawarabimincho">何人分
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <!-- データベースからFETCH()した、 調理時間 -->
                            <td><span style="color:green;font-size:16px"><?php echo $cooking_time ?>分</span></dt>
                            </td>

                            <!-- データベースからFETCH()した、 材料費 -->
                            <td><span style="color:green;font-size:16px"><?php echo $cost ?>円</span></dt>
                            </td>

                            <!-- データベースからFETCH()した、 何人分 -->
                            <td>&nbsp;&nbsp;&nbsp;<span style="color:green;font-size:16px">(<?php echo $how_many_servings ?>人分)</span></dt>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- 材料の表示欄 -->
                <!-- div_w -->
            </div>

            <div class="div_w_under">

                <table width="560px">
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
                        <!-- 区切り 既存の親材料カテゴリーで作った材料表示はじまり -->
                        <tr>
                            <!-- データの数だけ繰り返し -->
                            <?php foreach ($list as $v) : ?>
                    <tbody>
                        <tr>
                            <?php if ($v['materials_parent_category_name'] == '■ホールスパイス') { ?>
                                <td width="160px" align="right" id="material_name"><span style="color:green;font-size:13px">
                                        <!-- 親材料カテゴリーテーブルのデータベースからFETCH()した  材料カテゴリー名 -->
                                        <span style="font-color:pink"><?php echo $v['materials_parent_category_name'] ?></span></td>
                            <?php } elseif ($v['materials_parent_category_name'] == '●パウダースパイス') { ?>
                                <td width="160px" align="right" id="material_name"><span style="color:green;font-size:13px">
                                        <!-- 親材料カテゴリーテーブルのデータベースからFETCH()した  材料カテゴリー名 -->
                                        <span style="font-color:blue"><?php echo $v['materials_parent_category_name'] ?></span></td>
                            <?php } else { ?>
                                <td width="165px" align="right" id="material_name"><span style="color:green;font-size:13px">
                                        <!-- 親材料カテゴリーテーブルのデータベースからFETCH()した  材料カテゴリー名 -->
                                        <?php echo $v['materials_parent_category_name'] ?>
                                    </span></td>
                            <?php } ?>


                            <!-- 材料テーブルのデータベースからFETCH()した  材料名 -->
                            <td>&nbsp;&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <!-- 材料検索 /../../top/serch_material_ahref.phpここから -->
                            <td width="265px">
                                <span style="color:green;font-size:13px">
                                    <a href="../../top/serch_material_ahref.php?id=<?php echo $id ?>
                                &material=<?php echo $v['material_name'] ?>"><?php echo $v['material_name'] ?></span>
                                </a>
                            </td>

                            <td>&nbsp;</td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <!-- 材料テーブルのデータベースからFETCH()した  分量 -->
                            <td width="132px"><span style="color:green;font-size:13px"><?php echo $v['amount'] ?></span></td>
                            <!-- 材料検索 -->
                    </div>
                    </tbody>
        <?php endforeach ?>

        <!-- 区切り 既存の親材料カテゴリーおわり -->
        </div>
        </tbody>
        </tbody>
        </table>
    </div>
    <!-- -------------------------------------------------------------------- -->
    <?php if (!empty($advice)) : ?>
        <div class="space_div">
            <dt class="wf-sawarabimincho"><span style="color:green;font-size:16px">

                    <h5><img src="./images/hint.png" alt="" width="5%">レシピの一口メモ</p>
                </span></h5>
            <dt><span style="color:#58555A">このレシピのおいしさの秘密、材料のヒントをこっそり教えます。</span></dt>
            <!-- ↓dl/div は「レシピの一口メモ」を非表示/表示する、表示を切り替えるための部品です。 -->
            <dl id="memo">
                <?php foreach ($advice as $v) : ?>
                    <!-- DIV 1 -->
                    <div id="memo">
                        <dt id="memo"></dt>
                        <dd id="memo">
                            <?php echo $v['advice'] ?>
                        </dd>
                    </div>
                <?php endforeach ?>
            </dl>

        <?php endif ?>
        </div>
        <!-- End migi -->
        </div>
        <!-- End comprehensive -->
        <!-- --------------------------------------------------------------- -->
        <div class="bottom">
            <dt class="wf-sawarabimincho"><span style="color:green;font-size:24px">🔲作り方</p>
            <dt>
                <!-- 材料テーブルのデータベースからFETCH()した  レシピID -->
                <span style="color:green;font-size:13px"></span>
            </dt>

            <!-- レシピを選び直すボタン -->
            <div class="div_re-order">
                <input type="button" class="re-order" onclick="
                         location.href='../../create/recipe/index.php?id=<?php echo $_SESSION['member'] ?> action=rewrite'" value='レシピを選び直す'>
            </div>
            <div class="parent">


                <?php foreach ($report as $p) : ?>
                    <div class="div_100p">
                        <div class="div_100">
                            <dt>
                                <!-- 材料テーブルのデータベースからFETCH()した  調理手順のイメージ画像 -->
                                <img class="img" id="pimg" src="../../create/recipe/pimg/<?php echo $p['p_img'] ?>">
                            </dt>
                        </div>

                        <div class="div_102">
                            <dt>
                                <!-- 材料テーブルのデータベースからFETCH()した 調理説明  -->
                                <span style="color:green;font-size:13px">
                                    <td><?php echo $p['descriptions'] ?></td>
                                </span>
                            </dt>
                        </div>
                    </div>
                <?php endforeach ?>
                <!-- End parent -->
            </div>
            <!-- End parent -->
        </div>
        <!-- End bottom -->
        <div class="footer">footer</div>
        <script src="js/main.js"></script>
        <!-- 一口レシピを表示/非表示をコントロールするjavaScript -->
        
</body>

</html>