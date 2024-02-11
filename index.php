<?php include_once "./api/db.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- saved from url=(0039) -->
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<title>健康促進網</title>
	<link href="./css/css.css" rel="stylesheet" type="text/css">
	<script src="./js/jquery-1.9.1.min.js"></script>
	<script src="./js/js.js"></script>
</head>

<body>
	<div id="alerr" style="background:rgba(51,51,51,0.8); color:#FFF; min-height:100px; width:300px; position:fixed; display:none; z-index:9999; overflow:auto;">
		<pre id="ssaa"></pre>
	</div>
	<div id="all">
		<div id="title">
			<!-- 用php顯示幕前日期 -->
			<?= date("m月d日 l"); ?> |
			<!-- 在total資料表中date欄位是目前的日期，中的total的欄位，存儲當日的瀏覽量 -->
			今日瀏覽: <?= $Total->find(['date' => date("Y-m-d")])['total']; ?>
			累積瀏覽: <?= $Total->sum('total'); ?>
			<!-- 回首頁的連結在目前日期的右邊 -->
			<a href="./index.php" style="float:right">回首頁</a>
		</div>
		<!-- title2的地方設定標題圖片 -->
		<div id="title2">
			<a href="./index.php"><img src="./icon/02B01.jpg" alt="健康促進網－回首頁"></a>
		</div>
		<div id="mm">
			<div class="hal" id="lef">
				<a class="blo" href="?do=po">分類網誌</a>
				<a class="blo" href="?do=news">最新文章</a>
				<a class="blo" href="?do=pop">人氣文章</a>
				<a class="blo" href="?do=know">講座訊息</a>
				<a class="blo" href="?do=que">問卷調查</a>
			</div>
			<div class="hal" id="main">
				<div style="display:flex;">
					<marquee style="width:80%">請民眾踴躍投稿電子報，讓電子報成為大家相互交流、分享的園地！詳見最新文章</marquee>
					<span style="width:20%; display:inline-block;">
						<a href="?do=login">會員登入</a>
					</span>
					<div class="">
						<?php
						// 取得網址參數
						$do = $_GET['do'] ?? 'main';

						// 建立頁面檔案所在路徑
						$file = "./front/{$do}.php";

						// 判斷檔案及變數
						if (file_exists($file)) {

							// 載入對應的頁面檔案
							include $file;
						} else {
							// 如果沒有對應的頁面檔案，則直接載入main.php 
							include "./front/main.php";
						}

						?>
					</div>
				</div>
			</div>
		</div>
		<div id="bottom">
			本網站建議使用：IE9.0以上版本，1024 x 768 pixels 以上觀賞瀏覽 ， Copyright © 2024健康促進網社群平台 All Right Reserved
			<br>
			服務信箱：health@test.labor.gov.tw<img src="./icon/02B02.jpg" width="45">
		</div>
	</div>

</body>

</html>