<?php

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

$prepare = $pdo->prepare('select id from user where id = :id');
$prepare->bindValue(':id', $_POST['id']);
$prepare->execute();
$data = $prepare->fetch(PDO::FETCH_ASSOC);

if (!isset($data['id'])) {
    print_r($data);
    $prepare = $pdo->prepare('insert into user values(:id,:pass)');
    $prepare->bindValue(':id', $_POST['id']);
    $prepare->bindValue(':pass', password_hash($_POST['pass'], PASSWORD_BCRYPT));
    $prepare->execute();
    $data = $prepare->fetch(PDO::FETCH_ASSOC);
    echo 'OK';

    //slackに通知
    $url = "https://slack.com/api/chat.postMessage";
    $data = array(
        "token" => "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
        "channel" => "#勤怠管理システム_通知",
        "text" => $_POST['id'] . "さんが登録されました"
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
} else {
    //IDが既に登録されている時
    echo 'Duplicate';
}
