<?php
// 使用count來進行帳號與密碼的檢查
$res=$User->count($_POST);

// 如果$res為真 帳密正確，建立session
if($res){
    $_SESSION['user']=$_POST['acc'];
}

// 回傳結果 
echo $res;


?>