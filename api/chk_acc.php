<?php
include_once "./api/db.php";

// 利用count來檢查是否帳號存在
// User資料表的acc數量是 POST的 acc
$res=$User->count(['acc'=>$_POST['acc']]);
// 回傳檢查的結果
if($res>0){
    echo 1;
}else{
    echo 0;
}

?>