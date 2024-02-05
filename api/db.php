<?php
// 設定時區
date_default_timezone_get("Asia/Taipei");

// 啟動session
session_start();

// 宣告類別
class DB
{

    /**
     * $dsn 用來作為PDO的資料庫設定dbname為使用的資料庫名稱
     * $table 使用的資料表名
     * $pdo PDO的物件變數
     */

    protected $dsn = 'mysql:host=localhost;charset=utf8;dbname=web03';
    protected $table;
    protected $pdo;

    /**
     * 建立建構式，在建構時帶入table名稱會建立資料庫的連線
     * 建構式為物件被實例化(new DB)時會先執行的方法
     */

    function __construct($table)
    {
        // 將物件內部的$table值設為帶入的$table 
        $this->table = $table;

        // 將物件內部的$pdo值設為PDO建立的資料庫連線物件
        $this->pdo = new PDO($this->dsn, 'root', '');
    }


    //  a2s() 用來簡化陣列參數的字串轉換
    /**
     * 此方法引僅供類別內部使用，外部無法呼叫
     * 帶入的參數必須為key-value型態的陣列
     * 陣列透過foreach轉化為`key`='value'的字串存入陣列中
     * 回傳此字串陣列供其他方法使用
     */
    protected function a2s($array)
    {
        foreach ($array as $key => $value) {

            // 如果陣列的key名有id的，則跳過不處理
            if ($key != 'id') {

                // 將$key和$value組成SQL語法的字串後，加入到一個暫存的陣列中
                $tmp[] = "`$key` = '$value'";
            }
        }
        // 回傳暫存的陣列
        return $tmp;
    }

    // sql_all() 用來組合有特定條件，並且為多筆結果的sql語句
    /**
     * 此方法僅供類別內部使用，外部無法呼叫
     * $sql 一個sql的字串，主要是where前的語法
     * $array sql語句需要的欄位和值
     * $other sql特殊語句
     */

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

    // math() 內部函式，用來做為聚合函式的sql語法轉換
    protected function math($math, $col, $array = '', $other = '')
    {
        $sql = "select $math($col) from $this->table ";
        $sql = $this->sql_all($sql, $array, $other);

        // 因為這類方法大多是只會回傳一個值，所以使用fetchColumn()的方法來回傳
        return $this->pdo->query($sql)->fetchColumn();
    }


    // sum()/max()/min()... 使用聚合函式來計算某個欄位的結果
    /**
     * 直接呼叫內部的方法math()，帶入需要的參數即可
     * 這樣設計的目的是為了讓外部呼叫時方法名稱比較直覺，
     * 同時也減少需要帶入的參數
     */
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

    // count()... 使用聚合函式來計算查詢到的資料筆數
    /**
     * 執行 SQL 語句並回傳結果
     * 
     * param string $sql SQL語句
     * param string $where WHERE條件
     * param string $other 其他條件
     * return int 回傳結果
     */
    function count($where = '', $other = '')
    {
        // 建立查詢資料筆數的SQL語句
        $sql = "select count(*) from `$this->table` ";
        // 拼接 WHERE 條件和其他條件
        $sql = $this->sql_all($sql, $where, $other);
        // 執行 SQL語句並回傳結果
        return $this->pdo->query($sql)->fetchColumn();
    }

    // 撰寫外部空開方法
    // $table->all() 查詢符合條件的全部資料
    /**
     * 此方法主要用來取得符合條件的所有資料
     */
    function all($where = '', $other = '')
    {
        // 建立一個基礎語法字串
        $sql = "select * from $this->table ";

        // 將語法字串及參數帶入到類別內部的sql_all()方法中，結果會得到一個完整的SQL句子
        $sql = $this->sql_all($sql, $where, $other);

        // 將sql句子帶進pdo的query方法中，並以fetchAll的方式回傳所有的結果
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


    // $able->find($id) 查詢符合條件的單筆資料
    function find($id)
    {
        // 建立一個基礎語法字串
        $sql = "select * from $this->table ";

        // 如果 $id 是陣列
        if (is_array($id)) {
            // 執行內部方法a2s
            $tmp = $this->a2s($id);
            // 拼接sql語句
            $sql .= " where " . join(" && ", $tmp);
        }
        // 如果 $id 是數字
        else if (is_numeric($id)) {
            // 拼接sql語句
            $sql .= " where `id`='$id'";
        }
        // 將sql句子帶進pdo的query方法中，並以fetch的方式回傳一筆資料結果
        return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    // $table->del($id) 刪除資料
    function del($id)
    {
        // 建立一個基礎語法字串
        $sql = "delete from $this->table ";

        if (is_array($id)) {
            $tmp = $this->a2s($id);
            $sql .= " where " . join(" && ", $tmp);
        } else if (is_numeric($id)) {
            $sql .= " where `id`='$id'";
        }
        // 將sql句子帶進pdo的exec方法中，回傳的結果是影響了幾筆資料
        return $this->pdo->exec($sql);
    }


    // $table->save($save) 新增/更新資料
    /**
     * $arg 必須是個陣列，但考慮速度，所以程式中沒有特別檢查是否為陣列
     * 依據 $arg 是否帶有'id'這個key名，來決定是更新(有id)還是新增
     */

    function save($array)
    {
        // 如果 $array 中有 'id' 鍵--更新update
        if (isset($array['id'])) {
            // 如果有'id'，建立更新資料的 SQL 語句
            $sql = "update `$this->table` set ";

            // 如果 $array 不為空
            if (!empty($array)) {
                // 調用a2s()方法，將陣列轉換為SQL語法的字串
                $tmp = $this->a2s($array);
            }

            // 將轉換後的字串用逗號拼接到SQL語句中
            $sql .= join(",", $tmp);
            // 在SQL語句的末尾添加一個where條件，指定要更新紀錄的id
            $sql .= " where `id`='{$array['id']}'";
        } else {
            // 如果$array沒有'id'鍵，建立一個插入「新」紀錄的SQL語句
            $sql = "insert into `$this->table` ";
            // 將陣列的鍵名轉換為SQL語句的key鍵名稱部分
            $cols = "(`" . join("`,`", array_keys($array)) . "`)";
            // 將陣列的值轉換為SQL語句值的部分
            $vals = "('" . join("','", $array) . "')";

            // 將key和value拼接到SQL語句中
            $sql = $sql . $cols . " values " . $vals;
        }
        // 執行 SQL 語句並回傳結果
        return $this->pdo->exec($sql);
    }

    // q($sql) 複雜SQL語法的簡化函式
    // 用來解決聯表查詢或是子查詢之類較為複雜的語法
    public function q($sql)
    {
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}

// 此兩個函式會獨立在DB類別之外
function to($url){
    header("location:" .$url);
}
function dd($array){
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}
