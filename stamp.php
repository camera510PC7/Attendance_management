<?php
session_start();
if (!isset($_SESSION['login'])) {
    echo "不正な操作が検知されました";
    return false;
}
try {
    $pdo = new PDO(
        "mysql:host=hostname;dbname=am",
        "username",
        "password",
        array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET'utf8'"
        )
    );
} catch (PDOException $e) {
    die($e->getMessage());
}

//チャレンジに対するレスポンスを確認
if (strcmp($_SESSION['challenge'], $_POST['challenge']) != 0) {
    echo "不正な操作が検知されました";
    header("HTTP/1.1 403 Fobidden");
    return false;
}

//出勤or退勤を確認
$fetchstamp = $pdo->prepare('select * from stamp where id=:id order by time DESC LIMIT 1');
$fetchstamp->bindValue(':id', $_POST['id']);
$fetchstamp->execute();
$data = $fetchstamp->fetch(PDO::FETCH_ASSOC);
//echo print_r($data);
$count = $fetchstamp->rowCount();
if ($count === 0) {
    $set_att = 1;
} else {
    if (strcmp($data['att'], 0) === 0) {
        $set_att = 1;
    } else {
        $set_att = 0;
    }
}

//打刻
$prepare = $pdo->prepare('insert into stamp values(:id,:timestamp,:att)');
$prepare->bindValue(':id', $_POST['id']);
date_default_timezone_set("Asia/Tokyo");
$prepare->bindValue(':timestamp', date("Y/m/d H:i:s", time()));
$prepare->bindValue(':att', $set_att);
$prepare->execute();

//使用済みチャレンジを無効にする
$_SESSION['challenge'] = '';

//slackに通知
if($set_att === 1){
    $set_notify = "出勤";
}else{
    $set_notify = "退勤";
}
$url = "https://slack.com/api/chat.postMessage"; 
$data = array(
    "token" => "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "channel" => "#勤怠管理システム_通知", 
    "text" => $_POST['id']."さんが".$set_notify."されました"
);

$context = array(
    "http" => array(
        "method" => "POST",
        'header' => implode("\r\n", array(
            'Content-Type: application/x-www-form-urlencoded'
        )),
        'content' => http_build_query($data)
    )
);

$html = file_get_contents($url, false, stream_context_create($context));

$pdo = null;
$fetchstamp = null;
$data = null;
$prepare = null;
