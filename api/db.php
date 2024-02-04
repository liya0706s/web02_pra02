<?php
// 設定時區
date_default_timezone_get("Asia/Taipei");

// 啟動session
session_start();

// 宣告類別
class DB{

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
        $this->table=$table;

        // 將物件內部的$pdo值設為PDO建立的資料庫連線物件
        $this->pdo=new PDO($this->dsn,'root','');
     }

    
     //  用來簡化陣列參數的字串轉換
     /**
      * 此方法引僅供類別內部使用，外部無法呼叫
      * 帶入的參數必須為key-value型態的陣列
      * 陣列透過foreach轉化為`key`='value'的字串存入陣列中
      * 回傳此字串陣列供其他方法使用
      */
      protected function a2s($array){
        foreach($array as $key => $value){

            // 如果陣列的key名有id的，則跳過不處理
            if($key!='id'){

                // 將$key和$value組成SQL語法的字串後，加入到一個暫存的陣列中
            $tmp[]="`$key`='$value'";
            }
        }
        // 回傳暫存的陣列
        return $tmp;

        // 用來組合有特定條件
      }
}

?>