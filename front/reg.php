<fieldset>
    <legend>會員註冊</legend>
    <span style="color:red">*請設定您要註冊的帳號及密碼（最長 12 個字元）</span>
    <table>
        <tr>
            <td class="clo">Step1:登入帳號</td>
            <td><input type="text" name="acc" id="acc"></td>
        </tr>
        <tr>
            <td class="clo">Step2:登入密碼</td>
            <td><input type="password" name="pw" id="pw"></td>
        </tr>
        <tr>
            <td class="clo">Step3:再次確認密碼</td>
            <td><input type="password" name="pw2" id="pw2"></td>
        </tr>
        <tr>
            <td class="clo">Step4:信箱(忘記密碼時使用)</td>
            <td><input type="text" name="email" id="email"></td>
        </tr>
        <tr>
            <td>
                <input type="button" value="註冊" onclick="reg()">
                <input type="reset" value="清除" onclick="clean()">
            </td>
            <td></td>
        </tr>
    </table>
</fieldset>

<script>
    function reg() {
        // 取得使用者輸入的帳號、密碼、確認密碼和電子郵件
        let user = {
            acc: $('#acc').val(),
            pw: $('#pw').val(),
            pw2: $('#pw2').val(),
            email: $('#email').val(),
        }
        // 檢查使用者輸入是否完整
        if (user.acc != '' && user.pw != '' && user.pw2 != '' && user.email != '') {
            // 檢查密碼和確認密碼是否相符
            if (user.pw == user.pw2) {
                // 發送 POST 請求檢查帳號是否重覆
                $.post('./api/chk_acc.php',{acc:user.acc},(res)=>{
                    // 如果回傳的結果是1, 表示帳號重覆
                    if(parseInt(res)==1){
                        alert("帳號重覆")
                    }else{
                        // 發送 POST 請求進行註冊
                        $.post('./api/reg.php', user, (res)=>{
                            alert("註冊完成，歡迎加入")
                        })
                    }
                })
            }else{
                alert("密碼錯誤")
            }
        }else{
            alert("輸入欄位不可空白")
        }
    }
</script>