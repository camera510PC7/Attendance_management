function disabled() {
    button.classList.add("btn_disable");
    clearInterval(statusDis);
}
function able() {
    button.classList.remove("btn_disable");
    clearInterval(statusAble);
}

function registration() {
    const id = document.getElementById("id").value;
    const pass = document.getElementById("pass").value;
    const pass_again = document.getElementById("pass_again").value;
    if ((id === "") || (pass === "") || (pass_again === "")) {
        alert("すべての項目を入力してください");
        return false;
    }

    if (pass != pass_again) {
        alert("パスワードが一致しません");
        return false;
    }
    if (pass.length <= 5) {
        alert("パスワードを6文字以上にしてください");
        return false;
    }
    button = document.getElementById("submit");
    statusDis = setInterval(disabled, 1);
    statusAble = setInterval(able, 1500);
    $.ajax({
        type: "POST",
        url: "registration.php",
        data: { id: id, pass: pass },
        success: function (data, dataType) {
            if (data === 'Duplicate') {
                alert("そのIDは既に登録されています");
            }
            if (data === 'OK') {
                alert("登録しました");
                window.location.href = 'index.html';
            }

        },
        error: function (request, status, msg) {
            alert("エラーが発生しました\n" + msg);
        }
    })
}