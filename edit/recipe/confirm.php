<?php
    session_start();


    // (処理が完了したら画面に遷移する前に add_product_material.php)
    // 材料の親カテゴリーを削除する
    $_SESSION['parent_category_id'] = '';

    // 必要なファイルを読み込む
    require_once('../../class/db/Base.php');
    require_once('../../class/db/CreateRecipes.php');



    if (!empty($_GET['members_id'])) {
        $_SESSION['member'] = $_GET['members_id'];
    }


    // レシピid
    $id = $_GET['id'];



        if (!empty($_GET['recipe_id'])) {
            $id = $_GET['recipe_id'];
            echo $id;
        } elseif (!empty($_POST['recipe_id'])) {
            $id = $_POST['recipe_id'];
            echo $id;
        }

        // action_m.phpで材料削除後、リダイレクトさせる変数を代入する
        // $_SESSION['recipe_id'] = $id;
        // action_subtitle.phpから、./confirm.phpへリダイレクトするため
        // 削除したidだと戻れないのでセッションにいれておく。
        if (!empty($id)) {
            $_SESSION['recipe_id'] = $id;
        }

        // もしも、ログイン会員idが空なら、ログイン画面へリダイレクトさせる。
        if ($_SESSION['member'] == '') {

            header("Location: ../../login/join.php");
        }


try {


        if (empty($id)) {

            header("Location: ../../create/recipe/index.php");
        } else {

        $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
        $date = $dt->format('Y-m-d');

        //データに接続するための文字列
        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

        $dbh = new PDO($dsn, 'root', '');

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $sql = 'SELECT * FROM my_recipes WHERE id=' . $id;


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
        $cost = $record["cost"];
        $how_many_servings = $record["how_many_servings"];
        $created_date = $record['created_date'];



        // left JOIN material_parent_categoriesだと、
        //  親材料カテゴリににない、子供カテゴリーだけが表示できる。

        $sql = 'SELECT materials.id, materials.material_name, materials.amount, materials.is_deleted,
        materials.parent_category_id,
        material_parent_categories.materials_parent_category_name, material_categories.material_category_name,
        material_cat_products.material_category_id
        FROM materials
        left outer JOIN material_parent_categories ON materials.parent_category_id = 
        material_parent_categories.id   
        left outer JOIN material_categories ON materials.parent_category_id =
        material_categories.id
        left outer JOIN material_cat_products ON material_cat_products.material_category_id =
        material_parent_categories.id
        left outer JOIN product_lists ON product_lists.id =
        material_cat_products.product_id 
        WHERE materials.recipe_id = ' . $id . '
        ';

        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $list = $result->fetchAll(PDO::FETCH_ASSOC);




        // 調理手順テーブルのFETCH()を行う
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



        //  管理者(ID:56)が登録した親カテゴリー階層の子カテゴリーをFETCHする

        // 子カテゴリー (1) [カレー・国]
        // 管理者が作ったカテゴリーIDだけを表示する
        $sql = "SELECT  categories.id, categories.categories_name, 
        categories.parent_category_id
        FROM parent_categories JOIN categories ON parent_categories.id = 
        categories.parent_category_id 
        WHERE parent_categories.id = 1 AND categories.is_deleted = 0 
        AND categories.users_id = 56 ";



        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $category = $result->fetchAll(PDO::FETCH_ASSOC);


        // ログインメンバーが56以外ならsql文を実行する
        if ($_SESSION['member'] !== 56) {
        // 親カテゴリー階層の下に ログインユーザー作成した子供カテゴリーだけをFETCHする

        // 「カレー・国」(1)
        $sql = "SELECT  categories.id, categories.categories_name, 
        categories.parent_category_id, categories.users_id
        FROM parent_categories JOIN categories ON parent_categories.id = 
        categories.parent_category_id WHERE parent_categories.id = 1 AND categories.is_deleted = 0 
        AND categories.users_id = '" . $_SESSION['member'] . "'  ";


        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $category1 = $result->fetchAll(PDO::FETCH_ASSOC);
    }

        //  管理者(ID:56)が登録した親カテゴリー階層の子カテゴリーをFETCHする

        // 子カテゴリー (2) 副菜・おかず
        // 管理者が作ったカテゴリーIDだけを表示する";
        $sql = "SELECT  categories.id, categories.categories_name, 
        categories.parent_category_id
        FROM parent_categories JOIN categories ON parent_categories.id = 
        categories.parent_category_id WHERE parent_categories.id = 2 AND categories.is_deleted = 0 
        AND categories.users_id = 56";



        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $category2 = $result->fetchAll(PDO::FETCH_ASSOC);

        // ログインメンバーが56以外ならsql文を実行する
        if ($_SESSION['member'] !== 56) {
        // 親カテゴリーID(2)階層下に ログインユーザー作成した子供カテゴリーだけをFETCHする

        // 「副菜・おかず」(2)

        $sql = "SELECT  categories.id, categories.categories_name, 
        categories.parent_category_id, categories.users_id
        FROM parent_categories JOIN categories ON parent_categories.id = 
        categories.parent_category_id WHERE parent_categories.id = 2 AND categories.is_deleted = 0 
        AND categories.users_id = '" . $_SESSION['member'] . "'  ";


        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $category22 = $result->fetchAll(PDO::FETCH_ASSOC);
        }


        //  管理者(ID:56)が登録した親カテゴリー階層の子カテゴリーをFETCHする    
        // 管理者が作ったカテゴリーIDだけを表示する
        // カテゴリー (3) 具材・カレーの色
        $sql = "SELECT  categories.id, categories.categories_name, 
        categories.parent_category_id
        FROM parent_categories JOIN categories ON parent_categories.id = 
        categories.parent_category_id WHERE parent_categories.id = 3 AND categories.is_deleted = 0 
        AND categories.users_id = 56 ";
        


        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $category3 = $result->fetchAll(PDO::FETCH_ASSOC);

        // ログインメンバーが56以外ならsql文を実行する
        if ($_SESSION['member'] !== 56) {
        // 親カテゴリーID(3) 階層下に ログインユーザー作成した子供カテゴリーだけをFETCHする

        // 「具材・カレーの色」(3) 
        $sql = "SELECT  categories.id, categories.categories_name, 
        categories.parent_category_id, categories.users_id
        FROM parent_categories JOIN categories ON parent_categories.id = 
        categories.parent_category_id WHERE parent_categories.id = 3 AND categories.is_deleted = 0 
        AND categories.users_id = '" . $_SESSION['member'] . "'  ";


        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $category33 = $result->fetchAll(PDO::FETCH_ASSOC);
        }

        //  管理者(ID:56)が登録した親カテゴリー階層の子カテゴリーをFETCHする  

        //  (4) ナン/ライス
        // 管理者が作ったカテゴリーIDだけを表示する"
        $sql = "SELECT  categories.id, categories.categories_name, 
        categories.parent_category_id
        FROM parent_categories JOIN categories ON parent_categories.id = 
        categories.parent_category_id WHERE parent_categories.id = 4 AND categories.is_deleted = 0 
        AND categories.users_id = 56 ";



        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $category4 = $result->fetchAll(PDO::FETCH_ASSOC);

        // ログインメンバーが56以外ならsql文を実行する
        if ($_SESSION['member'] !== 56) {
        // 親カテゴリーID(4)階層下に ログインユーザー作成した子供カテゴリーだけをFETCHする

        // 「 ナン/ライス」(4) 
        $sql = "SELECT  categories.id, categories.categories_name, 
        categories.parent_category_id, categories.users_id
        FROM parent_categories JOIN categories ON parent_categories.id = 
        categories.parent_category_id WHERE parent_categories.id = 4 AND categories.is_deleted = 0 
        AND categories.users_id = '" . $_SESSION['member'] . "'  ";


        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $category44 = $result->fetchAll(PDO::FETCH_ASSOC);
        }


        // 管理者(ID:56)が登録した親カテゴリー階層の子カテゴリーをFETCHする  

        //  (5) スィーツ・飲み物
        // 管理者が作ったカテゴリーIDだけを表示する"
        $sql = "SELECT  categories.id, categories.categories_name, 
        categories.parent_category_id
        FROM parent_categories JOIN categories ON parent_categories.id = 
        categories.parent_category_id WHERE parent_categories.id = 5 AND categories.is_deleted = 0 
        AND categories.users_id = 56 ";



        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $category5 = $result->fetchAll(PDO::FETCH_ASSOC);


        // ログインメンバーが56以外ならsql文を実行する
        if ($_SESSION['member'] !== 56) {
        // 親カテゴリーID(5)階層下に登録済みのログインユーザーが作成した子供カテゴリーだけをFETCHする

        // 「 スィーツ・飲み物」(5)
        $sql = "SELECT  categories.id, categories.categories_name, 
        categories.parent_category_id, categories.users_id
        FROM parent_categories JOIN categories ON parent_categories.id = 
        categories.parent_category_id  WHERE parent_categories.id = 5 AND categories.is_deleted = 0 
        AND categories.users_id = '" . $_SESSION['member'] . "' ";


        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $category55 = $result->fetchAll(PDO::FETCH_ASSOC);
        }

        //  管理者(ID:56)が登録した親カテゴリー階層の子カテゴリーをFETCHする  

        //  (6) [趣向のカレー]
        // 管理者が作ったカテゴリーIDだけを表示する"
        $sql = "SELECT  categories.id, categories.categories_name, 
        categories.parent_category_id
        FROM parent_categories JOIN categories ON parent_categories.id = 
        categories.parent_category_id 
        WHERE parent_categories.id = 6 AND categories.is_deleted = 0 
        AND categories.users_id = 56 ";



        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $category6 = $result->fetchAll(PDO::FETCH_ASSOC);

        // ログインユーザーが作成した子供カテゴリーだけをFETCHする
        // 但しカテゴリーユーザーIDが56以外のカテゴリーだけを表示する
        // 「趣向のカレー」(6)

        if ($_SESSION['member'] !== 56) {
        $sql = "SELECT  categories.id, categories.categories_name, 
        categories.parent_category_id, categories.users_id
        FROM parent_categories JOIN categories ON parent_categories.id = 
        categories.parent_category_id WHERE parent_categories.id = 6 AND categories.is_deleted = 0 
        AND categories.users_id = '" . $_SESSION['member'] . "' ";

        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $category66 = $result->fetchAll(PDO::FETCH_ASSOC);


        } else {

        $sql = "SELECT  categories.id, categories.categories_name, 
        categories.parent_category_id, categories.users_id
        FROM parent_categories JOIN categories ON parent_categories.id = 
        categories.parent_category_id WHERE parent_categories.id = 6 AND categories.is_deleted = 0 
        AND categories.users_id = '" . $_SESSION['member'] . "' ";



        $stmt = $dbh->prepare($sql);

        $stmt->execute();
        }

        // 親材料カテゴリーをプルダウンで選択できるようにする
        // 親材料カテゴリーを追加・●ホールスパイス階層下に、クミン、コリアンダー、ターメリックがある
        // ８は、「ユーザー定義」材料カテゴリーなので表示から省く。「id=8の”ユーザー”定義」は、ユーザー材料カテゴリー追加画面でのみ使う。
        $sql = "SELECT id, materials_parent_category_name
        FROM material_parent_categories
        WHERE id <= 7
        ";


        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $count = $result->fetchAll(PDO::FETCH_ASSOC);


        // 表示レシピだけにひょうじする、アコーディオンプルダウンメニュー「材料カテゴリー」に＋プラスした
        // 「*インドのおかず」などのユーザー定義カテゴリーをmaterial_categoriesテーブルから引っ張り出す・表示させるためのFETCH

        $sql = "SELECT material_categories.id, material_categories.material_category_name
        FROM material_categories
        WHERE material_categories.users_id = '" . $_SESSION['member'] . "'
        AND material_categories.recipe_id = '" . $id . "'
        ";
        // ようするにユーザーが作ったレシピIDに付随した子供材料カテゴリーIDだけを表示する

        $stmt = $dbh->prepare($sql);


        $stmt->execute();

        $result = $dbh->query($sql);

        $children = $result->fetchAll(PDO::FETCH_ASSOC);



        // ログインメンバーのニックネームだけを取り出す
        // ユーザーが作った子供材料カテゴリーIDだけを表示する
        $sql = "SELECT nickname
        FROM members
        WHERE id = '" . $_SESSION['member'] . "' ";



        $stmt = $dbh->prepare($sql);


        $stmt->execute();

        $result = $dbh->query($sql);

        $nickname = $result->fetch(PDO::FETCH_ASSOC);



        foreach ($nickname as $v) {

            $nickname = $v;
        }
    }

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



        // セッションに記録された時間が、今の時間よりも大きい、つまりログイン時間から
        // 1時間以上たっていた場合,という意味
        if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
            // （1時間が経過していたら、）ログアウトし、ログイン画面に遷移する
            $_SESSION['time'] = time();
            // 現在の時刻で上書きします。こうすることで、何か行動したことで上書きすることで
            // 最後の時刻から１時間を記録することができるようになる。 
        } elseif ($_SESSION['member'] = '') {
            header('Location: ../../login/join.php');
            exit();
            // 更新時刻より１時間経過していなくとも、クッキーの削除でセッション情報が空になったら
            // ログイン画面に遷移する
        } else {
            header('Location: ../../login/join.php');
            exit();
            // 何か行動した更新時刻より１時間経過したら、自動的にログイン画面に遷移します
        }


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
            $subtitleId = $v['id'];
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
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>新規レシピノート登録確認</title>


    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
    <!-- ↓全体 -->
    <link rel="stylesheet" href="css/stylesheet2.css">
    <!-- アコーディオン  -->
    <link rel="stylesheet" href="css/stylesheet_a.css">
    <!-- 一口レシピ用javascript用 CSS -->
    <link rel="stylesheet" href="css/stylesheet.css">

    <style>

    </style>
</head>


<body>

    <div class='div_p'>
        <dt class="wf-sawarabimincho"><span style="font-size:18px;color:green"><?php echo $nickname ?></span>さんの</dt>
        <dt class="title_font"><span style="font-size:31px">Recipe Note<span style="font-size:16px">の編集をします</span></dt>

        <!-- ログアウト -->
        <div class="div_logout"><input type="button" value='ログアウト' class="logout_btn" onclick="location.href='../../logout/process.php'">
            
        </div>
        <!-- マイページ -->
        <div class="div_logout">
            <!-- 未ログインなら -->
            <?php if (empty($_SESSION['member'])) { ?>
                <input type="button" value='マイページ' class="logout_btn" onclick="location.href='../../login/join.php'">
                <!-- 既ログインなら -->
            <?php } else { ?>
                <input type="button" value='マイページ' class="logout_btn" onclick="location.href='../../login/process.php'">
            <?php } ?>
        </div>


        <!-- みんなのレシピ -->
        <div class="div_logout">
            <!-- 未ログインなら -->
            <?php if (empty($_SESSION['member'])) { ?>
                <input type="button" value='みんなのレシピ' class="logout_btn" onclick="location.href='../../top/confirm.php'">
                <!-- 既ログインなら -->
            <?php } else { ?>
                <input type="button" value='みんなのレシピ' class="logout_btn" onclick="location.href='../../top/index.php'">
            <?php } ?>
        </div>
    </div>


    <div class='inline_block_2'>


        <div class="comprehensive">
            <div class="block1">

                <!-- DIV トップの左側（レシピ完成画像） -->
                <div class="div_hidari">
                    <div class="div_edit_conf">


                        <!-- レシピを選び直すボタン -->
                        <div class="div_re-order">


                            <dt class="wf-sawarabimincho"><input type="button" class="re-order" onclick=" 
                            
                            
                            location.href='../../create/recipe/index.php?id=<?php echo $id ?> '" value='レシピを選び直す'></dt>
                        </div>


                        <dt class="wf-sawarabimincho">レシピ名:
                        <dt class="p_font_rarge"><span style="color:green"><?php echo $recipe_name ?>
                                <!-- 区切り線 -->
                                <div class="line"></div>

                                <!-- レシピのサブタイトル↓ -->
                                <!-- サブタイトル作成 -->
                                <!-- 作成ページ画面に遷移する -->

                                <form action="./edit_recipe_subtitle.php" method="GET">
                        <dt class="p_font_rarge">🔲<input type="submit" value="レシピのサブタイトルとコメントを追加/編集します">
                            <pre></pre>
                            <!-- レシピid の隠し送信 どのレシピか？判別 -->    
                            <input type="hidden" name="id" value=<?php echo $id ?>>
                        </dt>


                        
                        </form>


                        <!-- フォーム  -->
                        <!-- レシピ名・完成画像の編集を行います -->
                        <form action="update_r.php" method="post" enctype="multipart/form-data">
                            <!-- 隠し送信するレシピID -->
                            <input type="hidden" name="id" value="<?php echo $id ?>">
                            <!-- ログイン・メンバーズID -->
                            <input type="hidden" name="members_id" value="<?php echo $_SESSION['member'] ?>">



                            <!-- サブタイトル↓ -->
                            <dt class="wf-sawarabimincho"><span style="color:#000000">サブタイトル:</span>
                                <br>
                                <?php if (!empty($subtitle)) { ?>

                            <dt class="wf-sawarabimincho"><span style="font-size:23px"><?php echo $subtitle ?></span></dt>
                        <?php } ?>
                        <!-- 区切り線 -->
                        <div class="line"></div>


                        <!-- レシピのコメント↓ -->
                        <dt class="wf-sawarabimincho"><span style="color:#000000">レシピのコメント:</span>

                            <?php if (!empty($comment)) { ?>
                                <br><br>
                        <dt class="wf-sawarabimincho"><span style="font-size:15px"><?php echo $comment ?></span></dt>

                    <?php } ?>
                    <!-- 区切り線 -->
                    <div class="line"></div>

                    <!-- データベースからFETCH()した、  レシピID -->
                    <dt class="wf-sawarabimincho"><span style="color:#000000">レシピID:</span>
                        <span style="color:green"><?php echo $id ?></span>
                    </dt>

                    <!-- データベースからFETCH()した、  作成日 -->
                    <dt class="wf-sawarabimincho"><span style="color:#000000">作成日:</span>
                        <span style="color:green"><?php echo $created_date ?></span>
                    </dt>
                    <!-- 更新 入力フォーム/あらたなレシピ名 -->
                    <!-- あたらしいレシピ名を入力してください -->
                    <dt class="p_font_rarge">🔲<input type="text" name="recipe_name" size="40" placeholder='レシピ名を修正します。' maxlength="24"></dt>


                    <div class="item_l">
                        <!-- データベースからFETCH()した、完成画像 -->
                        <span style="color:green">
                            <dt class="wf-sawarabimincho">完成画像</dt>
                            <div class="photo_kadomaru">
                                <img id="cimg" src="../../create/recipe/images/<?php echo $complete_img
                                                                                ?>">
                            </div>
                            <!-- 更新 入力フォーム/あらたな完成画像 -->
                            <input type="file" name="complete_img">


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
                    </div>
                        </form>
                    <!-- div_editおわり -->
                    </div>



                    <!-- ------------------------------------------------------ -->

                    <!-- カテゴリー登録フォーム -->

                    <div class="categories_comprehensive">

                        <!-- アコーディオンバーはじまり -->
                        <!-- カテゴリを選ぶ✅ボックス -->

                        <!-- 左側 はじまり -->
                        <div class="div_width">

                            <!-- 1 -->
                            <input id="acd-check1" class="acd-check" type="checkbox">
                            <label class="acd-label" for="acd-check1">

                                カレー/ 国</label>
                            <div class="acd-content">

                                <!-- フォーム画面遷移⓵ -->
                                <form action="add_checkbox_categories.php" method="GET">
                                    <input type="hidden" name="recipe_id" value="<?php echo $id ?>">
                                    <!-- ↑レシピid の隠し送信 どのレシピか？判別 -->

                                    <dt>●国・地域</dt>
                                    <!-- 国・地域 -->
                                    <?php foreach ($category as $v) : ?>
                                        <table class="table">
                                            <tr>
                                                <td><input type="checkbox" name="category_id[]" value='<?php echo $v['id']  ?>'></td>
                                                <td><?php echo $v['categories_name'] ?></td>

                                            </tr>
                                        </table>
                                    <?php endforeach ?>

                                    <!-- ユーザーが作ったカテゴリだけ表示 -->
                                    <?php foreach ($category1 as $v) : ?>
                                        <table class="table">
                                            <tr>
                                                <td><input type="checkbox" name="category_id[]" value='<?php echo $v['id']  ?>' checked></td>
                                                <td><?php echo $v['categories_name'] ?></td>
                                            </tr>
                                        </table>
                                    <?php endforeach ?>


                                    <!-- 送信ボタン⓵ボタン -->
                                    <div class="btn">
                                        <input type="submit" a href="action.php" value="登録" class="btn-border"></a>
                                        <input type="reset" value="リセット" class="btn-border">
                                    </div>


                            </div>
                            <!-- ⓵のフォームおわり -->
                            </form>



                            <!-- 2 -->

                            <input id="acd-check2" class="acd-check" type="checkbox">
                            <label class="acd-label" for="acd-check2">
                                副菜とおかず
                            </label>
                            <div class="acd-content">

                                <!-- フォーム画面遷移⓶ -->
                                <form action="add_checkbox_categories.php" method="GET">
                                    <input type="hidden" name="recipe_id" value="<?php echo $id ?>" checked>
                                    <!-- ↑レシピid の隠し送信 どのレシピか？判別 -->

                                    <dt>●副菜とおかず</dt>
                                    <!-- 副菜とおかず -->
                                    <?php foreach ($category2 as $v) : ?>
                                        <table class="table">
                                            <tr>
                                                <td><input type="checkbox" name="category_id[]" value='<?php echo $v['id'] ?>'></td>
                                                <td><?php echo $v['categories_name'] ?></td>
                                            </tr>
                                        </table>
                                    <?php endforeach ?>

                                    <!-- ユーザーが作ったカテゴリだけ表示 -->
                                    <?php foreach ($category22 as $v) : ?>
                                        <?php if ($v['users_id'] === $_SESSION['member']) { ?>
                                            <table class="table">
                                                <tr>
                                                    <td><input type="checkbox" name="category_id[]" value='<?php echo $v['id'] ?>'></td>
                                                    <td><?php echo $v['categories_name'] ?></td>
                                                </tr>
                                            </table>
                                        <?php } ?>
                                    <?php endforeach ?>

                                    <pre></pre>
                                    <br>

                                    <!-- 送信ボタン⓶ボタン -->
                                    <div class="btn">
                                        <input type="submit" a href="action.php" value="登録" class="btn-border"></a>
                                        <input type="reset" value="リセット" class="btn-border">
                                    </div>

                            </div>
                            <!-- フォーム⓶おわり -->
                            </form>


                            <!-- 6 -->

                            <input id="acd-check6" class="acd-check" type="checkbox">
                            <label class="acd-label" for="acd-check6">

                                趣向のカレー
                            </label>
                            <div class="acd-content">


                                <!-- フォーム画面遷移⑥-->
                                <form action="add_checkbox_categories.php" method="GET">
                                    <!-- レシピid の隠し送信 どのレシピか？判別 -->
                                    <input type="hidden" name="recipe_id" value="<?php echo $id ?>">


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

                                    <!-- ユーザーが作ったカテゴリだけ表示 -->

                                    <?php foreach ($category66 as $v) : ?>
                                        <?php if ($v['users_id'] === $_SESSION['member']) { ?>
                                            <table class="table">
                                                <tr>
                                                    <td><input type="checkbox" name="category_id[]" value='<?php echo $v['id']  ?>' checked></td>
                                                    <td><?php echo $v['categories_name'] ?></td>
                                                </tr>
                                            </table>
                                        <?php } ?>
                                    <?php endforeach ?>


                                    <pre></pre>



                                    <!-- 送信ボタン⑥ボタン -->
                                    <div class="btn">
                                        <input type="submit" a href="action.php" value="登録" class="btn-border"></a>
                                        <input type="reset" value="リセット" class="btn-border">
                                    </div>

                            </div>
                            </form>
                            



                        <!-- div widthおわり -->

                        <!-- ✅左側カテゴリ おわり -->                    
                        </div>
                        





                        <!-- 右側カテゴリ 始まり  -->

                        <div class="div_width">

                            <!-- 3 -->
                            <input id="acd-check3" class="acd-check" type="checkbox">
                            <label class="acd-label" for="acd-check3">


                                具材 ・色</label>
                            <div class="acd-content">


                                <!-- フォーム画面遷移4 -->
                                <form action="add_checkbox_categories.php" method="GET">
                                    <!-- レシピid の隠し送信 どのレシピか？判別 -->
                                    <input type="hidden" name="recipe_id" value="<?php echo $id ?>">




                                    <!-- 具材 -->
                                    <!-- 色 -->

                                    <dt>●具材・色</dt>
                                    <?php foreach ($category3 as $v) : ?>
                                        <table class="table">
                                            <tr>
                                                <td><input type="checkbox" name="category_id[]" value='<?php echo $v['id']  ?>'></td>
                                                <td><?php echo $v['categories_name'] ?></td>
                                            </tr>
                                        </table>
                                    <?php endforeach ?>

                                    <!-- ユーザーが作ったカテゴリだけ表示 -->

                                    <?php foreach ($category33 as $v) : ?>
                                        <?php if ($v['users_id'] === $_SESSION['member']) { ?>
                                            <table class="table">
                                                <tr>
                                                    <td><input type="checkbox" name="category_id[]" value='<?php echo $v['id']  ?>'></td>
                                                    <td><?php echo $v['categories_name'] ?></td>
                                                </tr>
                                            </table>
                                        <?php } ?>
                                    <?php endforeach ?>

                                    <pre></pre>

                                    <!-- 送信ボタン③ボタン -->
                                    <div class="btn">
                                        <input type="submit" a href="action.php" value="登録" class="btn-border"></a>
                                        <input type="reset" value="リセット" class="btn-border">
                                    </div>


                            </div>
                            <!-- 3のフォームおわり -->
                            </form>




                            <!-- 4 -->

                            <input id="acd-check4" class="acd-check" type="checkbox">
                            <label class="acd-label" for="acd-check4">
                                ナン / ライス
                            </label>
                            <div class="acd-content">

                                <!-- フォーム画面遷移⓸ -->
                                <form action="add_checkbox_categories.php" method="GET">
                                    <!-- レシピid の隠し送信 どのレシピか？判別 -->
                                    <input type="hidden" name="recipe_id" value="<?php echo $id ?>">



                                    <!-- ナン・ライス -->

                                    <dt>●ナン / ライス</dt>
                                    <?php foreach ($category4 as $v) : ?>
                                        <table class="table">

                                            <tr>
                                                <td><input type="checkbox" name="category_id[]" value='<?php echo $v['id'] ?>'></td>
                                                <td><?php echo $v['categories_name'] ?></td>
                                            </tr>

                                        </table>
                                    <?php endforeach ?>

                                    <!-- ユーザーが作ったカテゴリだけ表示 -->

                                    <?php foreach ($category44 as $v) : ?>
                                        <?php if ($v['users_id'] === $_SESSION['member']) { ?>
                                            <table class="table">
                                                <tr>
                                                    <td><input type="checkbox" name="category_id[]" value='<?php echo $v['id'] ?>'></td>
                                                    <td><?php echo $v['categories_name'] ?></td>
                                                </tr>
                                            </table>
                                        <?php } ?>
                                    <?php endforeach ?>

                                    <pre></pre>

                                    <br>

                                    <!-- 送信ボタン⓸ボタン -->
                                    <div class="btn">
                                        <input type="submit" a href="action.php" value="登録" class="btn-border"></a>
                                        <input type="reset" value="リセット" class="btn-border">
                                    </div>

                            </div>
                            <!-- フォーム⓸おわり -->
                            </form>



                            <!-- 5 -->

                            <input id="acd-check5" class="acd-check" type="checkbox">
                            <label class="acd-label" for="acd-check5">

                                スイーツ / 飲み物
                            </label>
                            <div class="acd-content">


                                <!-- フォーム画面遷移⑤-->
                                <form action="add_checkbox_categories.php" method="GET">
                                    <!-- レシピid の隠し送信 どのレシピか？判別 -->
                                    <input type="hidden" name="recipe_id" value="<?php echo $id ?>">


                                    <!-- スィーツ・飲み物 -->

                                    <dt>●スィーツ・飲み物</dt>
                                    <?php foreach ($category5 as $v) : ?>
                                        <table class="table">
                                            <tr>
                                                <td><input type="checkbox" name="category_id[]" value='<?php echo $v['id'] ?>'></td>
                                                <td><?php echo $v['categories_name'] ?></td>
                                            </tr>
                                        </table>
                                    <?php endforeach ?>

                                    <!-- ユーザーが作ったカテゴリだけ表示 -->

                                    <?php foreach ($category55 as $v) : ?>
                                        <?php if ($v['users_id'] === $_SESSION['member']) { ?>
                                            <table class="table">
                                                <tr>
                                                    <td><input type="checkbox" name="category_id[]" value='<?php echo $v['id'] ?>'></td>
                                                    <td><?php echo $v['categories_name'] ?></td>
                                                </tr>
                                            </table>
                                        <?php } ?>
                                    <?php endforeach ?>


                                    <pre></pre>

                                    <pre></pre>

                                    <!-- 送信ボタン⑤ボタン -->
                                    <div class="btn">
                                        <input type="submit" a href="action.php" value="登録" class="btn-border"></a>
                                        <input type="reset" value="リセット" class="btn-border">
                                    </div>

                            </div>
                            <!-- フォーム5おわり -->
                            </form>


                        </div>
                        <!-- div widthおわり -->

                        <!-- ✅ボックスリスト おわり -->


                    <!-- div_categories_comprehensive -->
                    </div>


                    <!-- DIV カテゴリーおわり -->
                    <div class="space">



                        <!-- ------------------------------------------------------------ -->
                        <!-- 表示欄 / カテゴリーの削除-->
                        <p>🔲このレシピのカテゴリー</p>
                        <!-- 登録したカテゴリをFETCHする$dataが在れば,FETCHデータを表示 -->
                        <?php if (!empty($data)) { ?>
                            <table width="350px">
                                <dt class="wf-sawarabimincho"><span style="font:24px">このレシピの登録カテゴリー一覧</span></dt>
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

                                    <?php foreach ($data as $key => $v) { ?>
                                        <tr>
                                            <td>
                                                <dt><span style="font-size:13px">Id&nbsp;</span><?php echo $v['id'] ?></dt>
                                            </td>
                                            <td><?php echo $v['categories_name'] ?></td>
                                            <td>
                                                <!-- フォーム 登録カテゴリ削除 -->
                                                <form method="POST" action="action_categories.php">
                                                    <input type="hidden" name="category_id" value="<?php echo $v['category_id'] ?>">
                                                    <input type="hidden" name="my_recipe_id" value="<?php echo $v['my_recipe_id'] ?>">
                                                    <!-- 削除 Delete ボタン -->
                                                    <input type="submit" value="Delete" name="del" class="execution-btn">
                                                </form>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } else {
                            // FETCHデータが無ければメッセージを表示
                            echo '<dt>カテゴリーは未登録です</dt>';
                        } ?>


                    <!-- div_space おわり -->
                    </div>




                    <!-- 親カテゴリー階層にユーザーが好きに子供カテゴリーを作成する ↓ -->

                    <!-- カテゴリーの新規作成 -->
                    <div class="category_management">
                        <div class="f_left">
                            <form method="GET" action="./edit_mycategory.php">

                                <!-- ↓categoriesテーブルのusers_idカラムに登録する為に必要な、ログイン中のusers_idをカラム -->
                                <input type="hidden" name="users_id" value="<?php echo $_SESSION['member'] ?>">
                                <input type="hidden" name="id" value=<?php echo $id ?>>
                                <!-- ↑レシピid の隠し送信 どのレシピか？判別 -->

                                <!-- カテゴリの新規登録 * ボタン -->
                                <dt><input type="submit" a href="./edit_mycategory.php" class="update" value="カテゴリの新規作成" style="width: 155px;
                                color: #4F5902;
                                height: 33px;
                                font-size: 16px;
                                border-radius: 10px;
                                border: none;
                                background-color: #E9C8A7;
                                background-color: #D9CC1E
                                ">
                                </dt>
                            </form>
                        </div>
                    </div>
                <!-- div_hidariおわり -->        
                </div>
                




                <!-- DIV 右側はじまり -->
                <div class="div_migi">

                    <div class="div_edit_r">
                        <!-- フォーム  -->
                        <form action="update_r2.php" method="post" enctype="multipart/form-data">
                            <!-- 隠し送信するレシピID -->
                            <input type="hidden" name="id" value="<?php echo $id ?>">
                            <!-- ログイン・メンバーズID -->
                            <input type="hidden" name="members_id" value="<?php echo $_SESSION['member'] ?>">



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
                                        <td><span style="color:green"><?php echo $cooking_time ?></span>分</dt>
                                        </td>

                                        <!-- データベースからFETCH()した、 材料費 -->
                                        <td><span style="color:green"><?php echo $cost ?></span>円</dt>
                                        </td>

                                        <!-- データベースからFETCH()した、 何人分 -->
                                        <td><span style="color:green"><?php echo $how_many_servings ?></span>人分</dt>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- 区切り線 -->
                            <div class="line"></div>
                            <!-- あたらしい調理時間・材料費・何人分・調理時間を更新する入力フォーム -->
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
                                        <!-- フォーム 調理時間・材料費・何人分 -->
                                        <td>
                                            <dt class="wf-sawarabimincho">
                                                <input type="text" name="cooking_time" size="5" maxlength="10" placeholder='60'>分
                                            </dt>
                                        </td>
                                        <td>
                                            <dt class="wf-sawarabimincho">
                                                <input type="text" name="cost" size="5" maxlength="10" placeholder='1200'>円
                                            </dt>
                                        </td>
                                        <td>
                                            <dt class="wf-sawarabimincho">
                                                <input type="text" name="how_many_servings" size="5" maxlength="10" placeholder='2'>人分
                                            </dt>
                                        </td>

                                    </tr>
                                </tbody>
                            </table>



                            <!-- $list （materialsテーブルからfetchデータの表示 -->

                            <!-- データベースからFETCH()した、 調理ビデオ動画 -->
                            <div class="item_l">
                                <span style="color:green">
                                    <p class="wf-sawarabimincho">調理ビデオ動画</P>
                                    <!-- 動画があったら表示 -->
                                    <?php if (isset($v['video'])) { ?>

                                        <span style="color:green">
                                            <p class="wf-sawarabimincho">調理動画</P>
                                            <!-- <img class="img" src="./../create/recipe/images/<?php echo $video ?>" height="auto"> -->

                                        <?php } ?>
                                        <td><input type="file" name="video"></td>


                                        <!-- "更新する"ボタン -->
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



                            </div>


                        </form>
                        <!-- アンカー -->


                        <!-- アコーディオンバーはじまり -->
                        <!-- 親の材料カテゴリを選ぶ✅ボックス -->
                        <div class="div_w">

                            <div class="div_width">

                                <input id="acd-check7" class="acd-check" type="checkbox">
                                <label class="acd-label" for="acd-check7">
                                    <!-- 親・材料カテゴリー -->
                                    材料カテゴリー
                                </label>
                                <div class="acd-content">

                                    <!-- フォーム/親の材料カテゴリーと、その階下につく材料をインサートする -->
                                    <!-- フォーム/親の材料カテゴリーと、その階下につく材料をインサートする -->
                                    <form action="add_children_material.php" method="GET">
                                        <!-- 材料id の隠し送信 どの材料か？判別 -->    
                                        <input type="hidden" name="recipe_id" value="<?php echo $_SESSION['recipe_id'] ?>">
                                        

                                        <!-- 親の材料カテゴリー階層を、カテゴリー・テーブルへインサートする -->
                                        <dt>●材料カテゴリー</dt>

                                        <?php foreach ($count as $v) : ?>
                                            <table class="table">
                                                <tr>
                                                    <td><input id="form1" type="radio" name="id[]" value='<?php echo $v['id'] ?>'></td>
                                                    <td><?php echo $v['materials_parent_category_name'] ?></td>
                                                </tr>
                                            </table>
                                        <?php endforeach ?>

                                        <!-- 子供の材料カテゴリー -->
                                        <?php foreach ($children as $key => $v) : ?>
                                            <table class="table">
                                                <tr>
                                                    <td><input type="radio" name="id[]" value='<?php echo $v['id'] ?>'></td>
                                                    <td><?php echo $v['material_category_name'] ?></td>
                                                </tr>
                                            </table>
                                        <?php endforeach ?>




                                        <!-- リセットボタンボタン -->
                                        <div class="btn">
                                            <input type="reset" value="リセット" class="btn-border">
                                            <br>



                                        </div>
                                </div>
                            </div>
                        </div>


                        <!-- 子供材料カテゴリー作成おわり -->                    
                        <div class="div_w1">

                            



                            <table width="550px">
                                <thead>
                                    <tr>
                                        <th>
                                            <dt class="wf-sawarabimincho">
                                        </th>
                                        <th>
                                            <dt class="wf-sawarabimincho">
                                        </th>
                                        <th>
                                            <dt class="wf-sawarabimincho">
                                        </th>
                                        <th>
                                            <dt class="wf-sawarabimincho">
                                        </th>
                                        <th></th>
                                        <th>


                                        </th>
                                    </tr>
                                </thead>

                                <!-- 材料入力フォーム -->
                                <tbody>
                                    <tr>
                                        <td>

                                        </td>

                                        <!-- 隠し送信 Hidden レシピIDデータベースからFETCH()した   -->

                                        <td>
                                            <dt class="wf-sawarabimincho">
                                        </td>

                                        <!-- 入力フォーム値  材料名 -->

                                        <td>
                                            <input type="text" size='11' name="material_name" placeholder='牛乳' maxength="255">
                                        </td>

                                        <!-- 入力フォーム値  分量 -->
                                        <td width="20px"><input type="text" size='8' name="amount" placeholder='1ℓ' maxlength="255"></td>

                                        <td>
                                            <!-- 追加ボタン -->
                                            <input type="submit" value="追加" id="mySubmit" class="material_add_btn">
                                            <!-- <form id="parent_category" の値をmaterials.parent_category_idへINSERTする。-->
                                        </td>
                        </div>
                        </form>


                        <!-- フォーム⓸ [商品名で検索🔍] -->
                        <div class="listserch">
                            <form action="../../product/material/serch_material3_multiple.php" method="GET" onclick="open_preview();" width=600px heiht=500px>
                                <input type="hidden" name="recipe_id" value="<?php echo $id ?>">

                                <!-- 商品、product_lists.id -->
                                <input type="hidden" name="parent_category_id" id="material_category" value='<?php echo $parent_category_id ?>'>


                                <input type="text" name="product_name" style="border:none" size='10' maxlength="10" name="product_name" placeholder='材料名:クミン' maxlength="255"></td>

                                <!-- ボタン -->
                                <input id="button" id="material_category" type="submit" value="商品から選ぶ" name="send">


                            </form>

                        </div>



                        <td>
                            <!-- 材料カテゴリー作成 -->
                            <div class="list"></div>
                            <form action="./edit_parent_material.php" method="GET">
                                <dt><input type="submit" value="材料カテゴリー作成"></dt>
                                <input type="hidden" name="users_id" value="<?php echo $_SESSION['member'] ?>">
                                <input type="hidden" name="recipe_id" value="<?php echo $_SESSION['recipe_id'] ?>">
                            </form>

                        </td>

                        <td width="200px">
                            <!-- アンカー -->
                            <a href="#title"><img src="../../icon_img/anker.png" alt="材料最後へ" width="30px"></a>
                            <div class="to_top">


                            </div>
                        </td>
                        </tr>
                        </tbody>
                        </table>

                    <!-- 材料の表示欄 -->
                    <!-- div_w -->
                    </div>

                    <div class="div_w_under">

                        <table>
                            <thead>
                                <tr>
                                    <th width="120px"></th>
                                    <th>
                                        <dt class="wf-sawarabimincho">
                                    </th>
                                    <th>
                                        <dt class="wf-sawarabimincho">
                                    </th>
                                    <th>&nbsp;</th>
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
                                    <!-- 編集・処理 -->


                                    <td>
                                        <form action="edit_material_confirm.php" method="GET">
                                            <input type="hidden" name="recipe_id" value="<?php echo $id ?>">
                                            <input type="hidden" name="id" value="<?php echo $v['id'] ?>">

                                            <!-- 実行 ボタン -->
                                            <input type="submit" id="beforeunload" class="update" value="Edit" style="
                                        font-size: 11px;
                                        width: 38px;
                                        height: 19px; 
                                        margin-right: 3.5px;
                                        border-radius: 3px;
                                        border:none;
                                        color: #ffffff;
                                        background: #8C6A03;" />

                                        </form>
                                    </td>

                                    <?php if ($v['materials_parent_category_name'] == '■ホールスパイス') { ?>
                                        <td width="200px" id="material_name"><span style="color:green;font-size:13px">
                                                <!-- 親材料カテゴリーテーブルのデータベースからFETCH()した  材料カテゴリー名 -->
                                                <span style="font-color:pink"><?php echo $v['materials_parent_category_name'] ?></span></td>
                                    <?php } elseif ($v['materials_parent_category_name'] == '●パウダースパイス') { ?>
                                        <td width="200px" id="material_name"><span style="color:green;font-size:13px">
                                                <!-- 親材料カテゴリーテーブルのデータベースからFETCH()した  材料カテゴリー名 -->
                                                <span style="font-color:blue"><?php echo $v['materials_parent_category_name'] ?></span></td>

                                    <?php } elseif (!empty($v['material_category_name'])) { ?>
                                        <td width="150px" id="material_name"><span style="color:green;font-size:13px">
                                                <!-- 親材料カテゴリーテーブルのデータベースからFETCH()した  材料カテゴリー名 -->
                                                <?php echo $v['material_category_name'] ?>
                                            </span></td>

                                    <?php } else { ?>
                                        <td width="200px" id="material_name"><span style="color:green;font-size:13px">
                                                <!-- 親材料カテゴリーテーブルのデータベースからFETCH()した  材料カテゴリー名 -->
                                                <span style="font-color:blue"><?php echo $v['materials_parent_category_name'] ?></span></td>
                                    <?php } ?>


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


                                    <!-- 削除・処理 -->
                                    <form method="POST" action="action_m.php">
                                        <input type="hidden" name="recipe_id" value="<?php echo $id ?>">

                                        <td><input type='hidden' name='id' value="<?= $v['id'] ?>"></td>
                                        <td><input type="hidden" name="del" value="<?= 1 ?>"></td>

                                        <!-- 実行 ボタン -->
                                        <td width="24px"><input type="submit" id="beforeunload" class="update" value="Del" style="
                                        font-size: 11px;
                                        width: 38px;
                                        height: 19px; 
                                        margin-right: 3.5px;
                                        border-radius: 3px;
                                        border:none;
                                        color: #ffffff;
                                        background: #8C6A03;" /></td>
                                </tr>
                                </form>
                                <!-- 削除おわり -->

                    </div>

                    </tbody>


                <?php endforeach ?>

                <!-- 区切り 既存の親材料カテゴリーおわり -->
                </div>
                </tbody>
                </tbody>
                </table>
                </form>
            </div>

            <?php if (!empty($advice)) : ?>
                <div class="space_div">
                    <dt class="wf-sawarabimincho"><span style="color:green;font-size:16px">

                            <h5><img src="./images/hint.png" alt="" width="5%">レシピの一口メモ
                        </span></h5><span style="color:#58555A">※＋ボタンでメモが開きます。</span>
                    <dt><span style="color:#58555A">このレシピのおいしさの秘密や、材料へのアドバイスをコメントしましょう。</span></dt>
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
                <!-- div space_divおわり -->
                <!-- 一口メモの編集ボタン -->
                <br>
                <form action="./edit_advice.php" method="POST">
                    <dt><input type="submit" value="メモ編集"></dt>
                    <input type="hidden" name="users_id" value="<?php echo $_SESSION['member'] ?>">
                    <input type="hidden" name="recipe_id" value="<?php echo $_SESSION['recipe_id'] ?>">
                </form>
                <!-- アンカー -->
                <div class="to_top">
                    <a id="title"></a>
                </div>
                </div>


        <!-- div_w -->
        <!-- DIV 右側おわり -->
        </div>



    </div>
    <!-- div class="comprehensive" おわり-->
    </div>



    <!-- ↓ ここから、Proceser 調理手順 -->
    <div class="inline_block_4">

        <!-- レシピを選び直すボタン -->
        <div class="div_re-order">
            <input type="button" class="re-order" onclick="
                         location.href='../../create/recipe/index.php?id=<?php echo $id ?>'" value='レシピを選び直す'>
        </div>
        <dt class="wf-sawarabimincho"><span style="color:green;font-size:24px">🔲作り方</p>
        <dt>
            <!-- 材料テーブルのデータベースからFETCH()した  レシピID -->
            <p><span style="color:green;font-size:13px">レシピID:<?php echo $v['recipe_id'] ?></span>
        </dt>

        <!-- 調理手順の作成 -->
        <form action="add_edit_p.php" method="post" enctype="multipart/form-data">


            <!-- フォーム1  隠し送信 Hidden レシピIDは、SELECTでもってくる、my_recipeテーブルの
            ID をフォームでhiddenで隠して送信します -->
            <div class="flex">
                <input type="hidden" name="p_recipe_id" value="<?php echo $id ?>">
                <!-- フォーム2調理の説明（調理手順） -->
                <textarea name="descriptions" class="textarea3" placeholder='1.牛乳をお鍋に入れて温める'></textarea>
                <!-- フォーム3調理の画像 -->

                <label class="label_btn">
                    <!-- labelボタン -->
                    <div>画像アップロード</div>
                    <input type="file" name="p_img" class="file">

                </label>

                <!-- 保存ボタン -->
                <dt><input type="submit" id="mySubmit" value="保存" class="procedure_seve_btn"></dt>
            </div>


        </form>


        <!--  表示欄  -->
        <div class="parent">
            <!-- データの数だけ繰り返し -->
            <?php foreach ($report as $p) : ?>


                <div class="div_100p">


                    <div style="display:inline-flex">


                        <!-- materialsテーブルのrecipe_idを隠してＰＯＳＴするのは、action_m.phpからリダイレクトするため -->
                        <!-- value="echo $id ?> は、my_recipesテーブルのid.データベースからSELECTしたとき 連携している -->
                        <!-- $idは、このページにリダイレクトしてきた時にはすでに$_POST['id']は削除されて消えているので -->

                        <!-- フォーム / 削除 -->
                        <form method="POST" action="action_p.php">

                            <input type="hidden" name="p_recipe_id" value="<?php echo $id ?>">
                            <!-- ↑ materialsテーブルの name = p_recipe_idを隠してPOST送信するのは、action_m.phpからリダイレクトするため -->
                            <!-- value="echo $id ?> は、my_recipesテーブルのid.データベースからSELECTしたとき 連携している -->
                            <!-- $idは、このページにリダイレクトしてきた時にはすでにaction_p.php でデータベースで、$_POST['id']は削除されて消えているので -->
                            <!-- このページに戻ってこれるように、name="p_recipe_id" value="<echo $id ?> をPOST送信する（最初の?phpはコメントアウトのため省略） -->

                            <input type='hidden' name='id' value="<?= $p['id'] ?>">

                            <!-- 削除 Delete ボタン -->
                            <input type="submit" value="Delete" name="del" class="execution-btn">
                        </form>



                        <!-- 調理手順の各IDごと、1.肉を切る、などの調理手順ごとのテキストを編集 -->

                        <!-- 調理手順テーブルの編集をします -->
                        <form method="GET" action="./modify.php">
                            <!--調理手順テーブルの ID 例) id = "201" だけ export  -->
                            <input type="hidden" name="p_recipe_id" value="<?php echo $id ?>">

                            <input type="hidden" name="id" value="<?php echo $p['id'] ?>">
                            <!-- <input type="checkbox" name = "select"> -->
                            <input type="submit" value="編集" class="btn-border" />

                    </div>
                    <!-- <div style="display:inline-flex">おわり -->
                    <div class="div_pid">
                        <dt>id:<?php echo $p['id'] ?></dt>
                        <div class="div_100">

                            <!-- 材料テーブルのデータベースからFETCH()した  調理手順のイメージ画像 -->
                            <img id="pimg" src="../../create/recipe/pimg/<?php echo $p['p_img'] ?>">

                        </div>
                        <!-- 段落;改行１行スペース入れる -->
                        <pre></pre>


                        <div class="div_100">
                            <!-- 材料テーブルのデータベースからFETCH()した 調理説明  -->
                            <span style="color:green;font-size:13px">
                                <td><?php echo $p['descriptions'] ?></td>
                            </span></p>

                        </div>



                    </div>



                </div>
                <!-- フォームend -->
                </form>
            <?php endforeach ?>

        
        <!-- div_precent -->
        </div>


       
    </div>
    <!-- inline_block_4 -->
    </div>
    <!-- 一口レシピを表示/非表示をコントロールするjavaScript -->
    <script src="main.js"></script>

</body>

</html>