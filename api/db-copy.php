<?php
date_default_timezone_set("Asia/Taipei");
session_start();
class DB
{

    // $dsn 用來作為 PDO的資料庫設定 dbname為使用的資料庫名稱
    // $table 使用的資料表明稱
    // $pdo PDO的物件名稱

    protected $dsn = "mysql:host=localhost;charset=utf8;dbname=web03";
    protected $pdo;
    protected $table;

    // 建立建構式，在建構時帶入table名稱，會建立資料庫的連線
    // 建構式為物件被實例化(new DB)時會先執行的方法

    public function __construct($table)
    {
        // 將物件內部的$table值設為帶入的$table 
        $this->table = $table;

        // 將物件內部的$pdo值設定為，PDO建立的資料庫連建物件
        $this->pdo = new PDO($this->dsn, 'root', '');
    }

    // 此方法僅供類別內部使用，外部無法呼叫
    // 帶入的參數必須為key-value型態的陣列
    // 陣列透過foreach轉化為`key`=`value`的字串存入陣列中
    // 回傳此字串陣列供其他方法使用

    protected function a2s($array)
    {
        foreach ($array as $key => $value) {
            // 如果陣列的key名有id的，則跳過不處理
            if ($key != 'id') {
                // 將$key和$value組成SQL語法的字串後加入到一個暫存的陣列中
                $tmp[] = "`$key`='$value'";
            }
        }
        // 回傳暫存的陣列
        return $tmp;
    }

    // 此方法僅供類別內部使用，外部無法呼叫
    // $sql 一個sql的字串，主要是where 前的語法
    // $array sql語句需要的欄位和值
    // $other sql特殊語句

    private function sql_all($sql, $array, $other)
    {
        // 如果有設定資料表且不為空
        if (isset($this->table) && !empty($this->table)) {

            // 如果參數為陣列
            if (is_array($array)) {

                // 如果陣列不為空
                if (!empty($array)) {
                    $tmp = $this->a2s($array);
                    $sql .= " where " . join(" && ", $tmp);
                }
            } else {
                $sql .= " $array";
            }

            $sql .= $other;

            // 回傳sql字串
            return $sql;
        }
    }

    // SELECT 查詢SQL語句:
    // select score as ‘成績’, avg(score) as ‘平均’ from students ……
    protected function math($math, $col, $array = '', $other = '')
    {
        $sql = "select $math($col) from $this->table";
        $sql = $this->sql_all($sql, $array, $other);

        // 因為這類方法大多是只會回傳一個值，所以使用fetchColumn()的方法來回傳
        return $this->pdo->query($sql)->fetchColumn();
    }

    // 直接呼叫內部的方法math()，帶入需要的參數即可
    // 這樣設計的目的是為了讓外部呼叫時方法名稱比較直覺
    // 同時也減少需要帶入的參數
    function sum($col, $where = '', $other = '')
    {
        return $this->math('sum', $col, $where, $other);
    }
    function max($col, $where = '', $other = '')
    {
        return $this->math('max', $col, $where, $other);
    }
    function min($col, $where = '', $other = '')
    {
        return $this->math('min', $col, $where, $other);
    }

    // count()...使用聚合函式來計算查詢到的資料筆數
    function count($where = '', $other = '')
    {
        // 查詢資料筆數的 SQL 語句
        $sql = "select count(*) from `$this->table`";
        // 拼接 WHERE 條件和其他條件
        $sql = $this->sql_all($sql, $where, $other);
        return $this->pdo->query($sql)->fetchColumn();

        // return 關鍵字表示這行代碼將執行的結果返回給調用者。
        // $this->pdo 指的是當前類中的一個 PDO 物件，這個物件用於執行資料庫操作。
        // ->query($sql) 是 PDO 物件的一個方法，用於執行一個 SQL 查詢，$sql 變數包含了要執行的 SQL 語句。
        // ->fetchColumn() 是 PDOStatement 物件的一個方法，用於從結果集中取得一行的一個欄位的值。
        // 在這裡，它用於取得數學聚合函數的結果或資料筆數的結果。
    }


    // 此方法主要是用來取得符合條件的所有資料
    function all($where = '', $other = '')
    {
        // 建立一個基礎語法字串
        $sql = "select * from $this->table ";

        // 將語法字串及參數帶入到類別內部的sql_all()方法中，結果會得到一個完整的SQL句子
        $sql = $this->sql_all($sql, $where, $other);

        // 將sql句子帶進pdo的query方法中，並以fetchAll的方式回傳所有的結果
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    function find($id)
    {
        // 建立一個基礎語法字串
        $sql = "select * from $this->table";

        // 如果 $id 是陣列
        if (is_array($id)) {

            // 執行內部方法a2s
            $tmp = $this->a2s($id);

            // 拼接sql語句
            $sql .= " where " . join(" && ", $tmp);
        }
        // 如果 $id 是數字
        else if (is_numeric($id)) {

            // 拼接 sql語句
            $sql .= " where `id`='$id'";
        }

        // 將sql句子帶進pdo的query方法中，並以fetch的方式回傳一筆資料結果
        return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    function del($id)
    {
        // 建立一個基礎與法字串
        $sql = "delete from $this->table";

        if (is_array($id)) {
            $tmp = $this->a2s($id);
            $sql .= " where " . join(" && ", $tmp);
        } else if (is_numeric($id)) {
            $sql .= " where `id`='$id'";
        }

        // 將sql句子帶進pdo的exec方法中，回傳的結果是影響了幾筆資料
        return $this->pdo->exec($sql);
    }


    // $array 必須是個陣列，但考量速度，所以城市中沒有特別檢查是否為陣列
    // 依據$arg是否帶有'id'這個key名，來決定是更新(有id)還是新增(沒id)

    function save($array)
    {
        // 如果 $array 中有'id'鍵
        if (isset($array['id'])) {
            // 建立更新資料的 SQL 語句，則準備一個更新 (update) 的 SQL 語句，
            // 用於更新資料庫中的現有記錄
            $sql = "update `$this->table` set "; // $this->table 是一個變數，儲存了當前要操作的資料表名稱

            // 如果 $array 不為空
            if (!empty($array)) {
                // 將陣列轉換為字串
                $tmp = $this->a2s($array);
            }

            // 拼湊 SQL 語句
            $sql .= join(",", $tmp);
            // 在 SQL 語句的末尾加上一個條件，
            // 指定要更新哪一條記錄，即那些 id 等於 $array['id'] 的記錄
            $sql .= " where `id`='{$array['id']}'";

            // update更新的SQL語句:
            // UPDATE `table` SET `col1`='value1',`col2`='value2',...　WHERE ...
        } else {
            // 如果 $array 中不存在 'id' 鍵，
            // 則認為是一個新的記錄，準備一個插入 (insert) 的 SQL 語句
            $sql = "insert into `$this->table`"; // 插入到 $this->table 指定的資料表中
            // 使用 array_keys 函數獲取 $array 陣列中所有鍵名，
            // 這個字串代表 SQL 語句中的列名稱
            $cols = "(`" . join("`,`", array_keys($array)) . "`)";
            // 使用 join 函數將 $array 陣列中的所有值用逗號和單引號連接成一個字串
            // 這個字串代表 SQL 語句中的值
            $vals = "('" . join("','", $array) . "')";

            // 將列名稱和值拼接到 $sql 變數中，完成插入語句的構造
            $sql = $sql . $cols . " values " . $vals;

            // insert into語句如下:
            // INSERT INTO `table`(`col1`,`col2`,`col3`,`col4`,`col5`) 
            // VALUES('value1','value1','value1','value1','value1','value1');

        }
        // 執行 SQL 語句並回傳結果
        return $this->pdo->exec($sql);
    }

    public function q($sql)
    {
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}

// 此兩個函式會獨立在DB類別之外
function to($url)
{
    header("location:" . $url);
}

function dd($array)
{
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}

// 建議使用首字母大寫來代表這是資料表的變數， 方便和拳小寫的變數做區隔
$Total = new DB('total');
$User = new DB('user');
$News = new DB('news');
$Que = new DB('que');
$Log = new DB('log');

// 增加一個方法來判斷訪客的拜訪狀態，
// 用來決定當日訪客人次是否需要增加。

// 如果沒有被設置（即不存在或其值為 NULL） $_SESSION['visited'], 則執行以下程式碼
if (!isset($_SESSION['visited'])) {
    // 如果今天的日期在資料庫中已存在，則取得該筆資料
    if ($Total->count(['date' => date('Y-m-d')]) > 0) {
        $total = $Total->find(['date' => date('Y-m-d')]);
        // 將該筆資料的 total 欄位加一
        $total['total']++;
        // 儲存更新後的資料
        $Total->save($total);
    } else {
        // 如果今天的日期在資料庫中不存在，則新增一筆資料
        $Total->save(['total' => 1, 'date' => date('Y-m-d')]);
    }
    // 設定 $_SESSION['visited'] 為 1, 表示已經訪問過了
    $_SESSION['visited'] = 1;
}
