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
}
