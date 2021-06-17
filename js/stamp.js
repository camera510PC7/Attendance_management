function disabled() {
    button.classList.add("btn_disable");
    clearInterval(statusDis);
}
function able() {
    button.classList.remove("btn_disable");
    clearInterval(statusAble);
    location.reload();
}
function stamp() {
    const user = $('#id').val();
    const challenge = $('#challenge').val();
    $.ajax({
        type: "POST",
        url: "stamp.php",
        data: { id: user, challenge: challenge },
        success: function (data, dataType) {
            alert("打刻しました");
        },
        error: function () {
            alert("エラーが発生しました");
        }
    });

    button = document.getElementById("submit");
    statusDis = setInterval(disabled, 1);
    statusAble = setInterval(able, 1500);
}