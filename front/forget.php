<fieldset>
    <legend>忘記密碼</legend>
    <div>請輸入信箱以查詢密碼</div>
    <div>
        <input type="text" name="email" id="email">
    </div>
    <div>
        <div id="result"></div>
    </div>
    <div>
    <button onclick="forget()">尋找</button>
    </div>
</fieldset>
<!-- <a href="../api/forget.php"></a> -->
<script>
    // 撰寫ajax取回結果函式
    function forget(){
        $.get("../api/forget.php", {email:$('#email').val()},(res)=>{
            // 在回調函數中，使用jQuery的.text方法
            // 將響應內容(res)設置為id為result的元素的本文內容
            // 這樣就可以在頁面上看到請求的結果
            $('#result').text(res)
        })

    }
</script>