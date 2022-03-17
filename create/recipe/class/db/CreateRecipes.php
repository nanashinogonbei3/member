<?php
// PHP練習問題12参照

echo('<pre>');
var_dump($_SESSION['recipe']);
echo('<pre>');
// exit;
// 実行結果
// ["how_many_servings"]=>
// string(3) "２"
// ２がstring(1)
// ["cooking_time"]=>
// string(2) "60"
// 半角で、再入力
// ["how_many_servings"]=>
// string(1) "2" stringがようやく1になった

// *********｛「マイ・レシピ」テーブル ｝***********************************************************

/**
 * my_recipesテーブルクラス
 */
class MyRecipes extends Base
{
    /**
     * コンストラクタ
     */
    public function __construct()
    {
        // 親クラスのコンストラクタの呼び出し
        parent::__construct();
    }

    /**
     * レコードを全件取得する（期限日の古いものから並び替える）
     *
     * @return array
     */
    public function selectAll()
    {
        $sql = 'SELECT * FROM my_recipes ORDER BY created_date';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQLを実行する
        $stmt->execute();

        // 取得したレコードを連想配列として返却する
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
/**


     * レコードをアップデートする
     *
     * @param int $id
     * @param string $recipe_name
     * @param int $members_id
     * @param string $complete_img
     * @param int $cooking_time
     * @param int $cost
     * @param int $how_many_servings
     * @param string $video
     * @param string $created_date
     * @param string $update_time
     * @param string $release_date

     * @return bool
     */
    public function update(int $id, string $recipe_name, int $members_id, string $complete_img, int $cooking_time, int $cost, int $how_many_servings, 
    string $video, string $create_date, string $update_time, string $release_date)
    {
        // レコードをアップデートするSQL文
        $sql = 'UPDATE my_recipes set id=:id, recipe_name=:recipe_name, members_id=:members_id, complete_img=:complete_img, cooking_time=:cooking_time, cost=:cost, 
        how_many_servings=:how_many_servings, video=:video, created_date=:created_date, update_time=:update_time, 
        release_date=:release_date
        -- is_deleted=:is_deleted ';
        $sql .= 'where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);
        
        
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':recipe_name', $recipe_name, PDO::PARAM_STR);
        $stmt->bindValue(':members_id',$members_id, PDO::PARAM_INT);
        $stmt->bindValue(':complete_img',$complete_img, PDO::PARAM_STR);
        $stmt->bindValue(':cooking_time',$cooking_time, PDO::PARAM_INT);
        $stmt->bindValue(':cost',$cost, PDO::PARAM_INT);
        $stmt->bindValue(':how_many_servings',$how_many_servings, PDO::PARAM_INT);
        $stmt->bindValue(':video',$video, PDO::PARAM_STR);
        $stmt->bindValue(':created_date',$created_date, PDO::PARAM_STR);
        // $stmt->bindValue(':is_deleted',$is_deleted, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 指定IDのレコードを完了フラグを切り替える
     *
     * @param int $id
 
     * @return void
     */

  
    public function updateIsCompletedByID(int $id)
    {
        // レコードをアップデートするSQL文
        $sql = 'update my_recipes set id ';
        $sql .= 'where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        // $stmt->bindValue(':isDeleted', (int) $isDeleted, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 指定IDのレコードを削除する
     *
     * @param integer $id
     * @return void
     */
    public function delete(int $id)
    {
        $sql = 'delete from my_recipes where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQL文の該当箇所に、変数の値を割り当て（バインド）する
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 新規レコードをインサートする
  
     * @param string $recipe_name
     * @param integer $members_id
     * @param string $complete_img
     * @param integer $cooking_time
     * @param integer $cost
     * @param integer $how_many_servings
     * @param string $video
     * @param string $created_date
     * @return void
     */
    // add.phpのインサート文の数と、このインサート文の引数の数があっていないとエラー
    public function insert(string $recipe_name, int $members_id, string $complete_img, int $cooking_time, int $cost, int $how_many_servings, 
    ?string $video, 
    string $created_date)
    {
        $sql = 'insert into my_recipes (';
        $sql .= 'recipe_name,';
        $sql .= 'members_id,';
        $sql .= 'complete_img,';
        $sql .= 'cooking_time,';
        $sql .= 'cost,';
        $sql .= 'how_many_servings,';
        $sql .= 'video,';
        $sql .= 'created_date';
        // $sql .= 'update_time,';
        // $sql .= 'release_date';
        // $sql .= 'is_deleted';
        $sql .= ') values (';
        $sql .= ':recipe_name,';
        $sql .= ':members_id,';
        $sql .= ':complete_img,';
        $sql .= ':cooking_time,';
        $sql .= ':cost,';
        $sql .= ':how_many_servings,';
        $sql .= ':video,';
        $sql .= ':created_date';
        // $sql .= ':update_time,';
        // $sql .= ':release_date';
        // $sql .= ':is_deleted';
        $sql .= ')';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQL文の該当箇所に、変数の値を割り当て（バインド）する
    
        // $stmt->bindValue(':id', $_SESSION['recipe']['id'], PDO::PARAM_INT);
        $stmt->bindValue(':recipe_name', $_SESSION['recipe']['recipe_name'], PDO::PARAM_STR);
        $stmt->bindValue(':members_id', $_SESSION['recipe']['members_id'], PDO::PARAM_INT);
        $stmt->bindValue(':complete_img',$_SESSION['recipe']['complete_img'],PDO::PARAM_STR);
        $stmt->bindValue(':cooking_time',$_SESSION['recipe']['cooking_time'],PDO::PARAM_INT);
        $stmt->bindValue(':cost',$_SESSION['recipe']['cost'],PDO::PARAM_INT);
        $stmt->bindValue(':how_many_servings',$_SESSION['recipe']['how_many_servings'],PDO::PARAM_INT);
        $stmt->bindValue(':video',$_SESSION['recipe']['video'],PDO::PARAM_STR);
        $stmt->bindValue(':created_date',$_SESSION['recipe']['created_date'],PDO::PARAM_STR);


        // SQLを実行する
        $stmt->execute();

    }
}

// *********｛「材料」テーブル ｝***********************************************************

/**
 * materialテーブルクラス
 */
class Materials extends Base
{
    /**
     * コンストラクタ
     */
    public function __construct()
    {
        // 親クラスのコンストラクタの呼び出し
        parent::__construct();
    }

    /**
     * レコードを全件取得する（期限日の古いものから並び替える）
     *
     * @return array
     */
    public function selectAll()
    {
        $sql = 'SELECT * FROM materials ORDER BY created_date';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQLを実行する
        $stmt->execute();

        // 取得したレコードを連想配列として返却する
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * レコードをアップデートする
     *
     * @param int $id
     * @param int $recipeId
     * @param string $materialName
     * @param string $amount
     * @param string $createdDate
     * @param string $updateDate
     * @param int $isDeleted
     * @return bool
     */
    public function update(int $id, int $recipeId, string $materialName, string $amount, string $createdDate, string $updateDate, 
    int $isDeleted)
    {
        // レコードをアップデートするSQL文
        $sql = 'update materials set recipe_id=:recipe_id, material_name=:material_name, amount=:amount, created_date=:created_date, 
        update_date=:update_date, is_deleted=:is_deleted ';
        $sql .= 'where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':recipe_id', $recipeId, PDO::PARAM_INT);
        $stmt->bindValue(':material_name', $materialName, PDO::PARAM_STR);
        $stmt->bindValue(':amount', $amount, PDO::PARAM_STR);
        $stmt->bindValue(':created_date', $createdDate, PDO::PARAM_STR);
        $stmt->bindValue(':update_time', $updateTime, PDO::PARAM_STR);
        $stmt->bindValue(':is_deleted', $isDeleted, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 指定IDのレコードを削除フラグを切り替える
     *
     * @param int $id
     * @param bool $isDeleted
     * @return void
     */
    public function updateIsCompletedByID(int $id, bool $isDeleted)
    {
        // レコードをアップデートするSQL文
        $sql = 'update materials set is_deleted=:isDeleted ';
        $sql .= 'where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':isDeleted', (int) $isDeleted, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 指定IDのレコードを削除する
     *
     * @param integer $id
     * @return void
     */
    public function delete(int $id)
    {
        $sql = 'delete from materials where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQL文の該当箇所に、変数の値を割り当て（バインド）する
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 新規レコードをインサートする
     *
     * @param integer $recipe_id
     * @param string $material_name
     * @param string $amount
 
     * @return void
     */
    public function insert(int $recipe_id, string $material_name, string $amount)
    {
        $sql = 'insert into materials (';
        $sql .= 'recipe_id,';
        $sql .= 'material_name,';
        $sql .= 'amount';
        // $sql .= 'created_date';
        // $sql .= 'update_time,';
        // $sql .= 'is_deleted';
        $sql .= ') values (';
        $sql .= ':recipe_id,';
        $sql .= ':material_name,';
        $sql .= ':amount';
        // $sql .= ':created_date';
        // $sql .= ':update_time,';
        // $sql .= ':is_deleted';
        $sql .= ')';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQL文の該当箇所に、変数の値を割り当て（バインド）する
        $stmt->bindValue(':recipe_id', $recipe_id, PDO::PARAM_INT);
        $stmt->bindValue(':material_name', $material_name, PDO::PARAM_STR);
        $stmt->bindValue(':amount', $amount, PDO::PARAM_STR);
        // $stmt->bindValue(':created_date', $created_date, PDO::PARAM_STR);
        // $stmt->bindValue(':update_time', $update_time, PDO::PARAM_STR);
        // $stmt->bindValue(':is_deleted', $is_deleted, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }
}

// *********｛「調理手順」テーブル ｝***********************************************************

/**
 * proceduresテーブルクラス
 */
class Procedures extends Base
{
    /**
     * コンストラクタ
     */
    public function __construct()
    {
        // 親クラスのコンストラクタの呼び出し
        parent::__construct();
    }

    /**
     * レコードを全件取得する（期限日の古いものから並び替える）
     *
     * @return array
     */
    public function selectAll()
    {
        $sql = 'SELECT * FROM procedures ORDER BY created_date';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQLを実行する
        $stmt->execute();

        // 取得したレコードを連想配列として返却する
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * レコードをアップデートする
     *
     * @param int $id
     * @param int $p_reciped_id
     * @param string $p_img
     * @param string $description
     * @return bool
     */
    public function update(int $id, int $p_recipe_id, string $p_img, string $description)
    {
        // レコードをアップデートするSQL文new Procedures(); 
        $sql = 'UPDATE procedures set p_recipe_id=:p_recipe_id, p_img=:p_img, description=:description ';
        $sql .= 'where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':p_recipe_id', $p_recipe_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_img', $p_img, PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
    

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 指定IDのレコードを削除フラグを切り替える
     *
     * @param int $id
     * @param bool $isDeleted
     * @return void
     */
    public function updateIsCompletedByID(int $id, bool $isDeleted)
    {
        // レコードをアップデートするSQL文
        $sql = 'update procedures set is_deleted=:isDeleted ';
        $sql .= 'where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':isDeleted', (int) $isDeleted, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 指定IDのレコードを削除する
     *
     * @param integer $id
     * @return void
     */
    public function delete(int $id)
    {
        $sql = 'delete from procedures where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQL文の該当箇所に、変数の値を割り当て（バインド）する
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 新規レコードをインサートする
  
     * @param int $p_recipe_id
     * @param string $descriptions
     * @param string $p_img
    
    

     * @return void
     */
    // add.php のインサート文と、このインサートの引数の数が一致していないとエラー
    public function insert(int $p_recipe_id, string $descriptions, string $p_img)
    {
        $sql = 'insert into procedures (';
        $sql .= 'p_recipe_id,';
        $sql .= 'descriptions,';
        $sql .= 'p_img';
        // $sql .= 'created_date,';
        // $sql .= 'update_time,';
        // $sql .= 'is_deleted';
        $sql .= ') values (';
        $sql .= ':p_recipe_id,';
        $sql .= ':descriptions,';
        $sql .= ':p_img';
        // $sql .= ':created_date,';
        // $sql .= ':update_time,';
        // $sql .= ':is_deleted';
        $sql .= ')';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQL文の該当箇所に、変数の値を割り当て（バインド）する
       
        $stmt->bindValue(':p_recipe_id', $p_recipe_id, PDO::PARAM_INT);
        $stmt->bindValue(':descriptions', $descriptions, PDO::PARAM_STR);
        $stmt->bindValue(':p_img', $p_img, PDO::PARAM_STR);
        // $stmt->bindValue(':created_date', $_SESSION['recipe']['createdDate'], PDO::PARAM_STR);
        // $stmt->bindValue(':update_time', $updateTime, PDO::PARAM_STR);
        // $stmt->bindValue(':is_deleted', $isDeleted, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }
}

