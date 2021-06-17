<?php
session_start();
session_regenerate_id(true);
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
//チャレンジを生成
$challenge = substr(bin2hex(random_bytes(12)), 0, 12);
$_SESSION['challenge'] = $challenge;
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>勤怠管理システム マイページ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Sawarabi+Gothic" rel="stylesheet">
    <link href="CSS/mypage.css" rel="stylesheet">
</head>

<body>
    <div class="logout">
        <a href="./logout.php"><button type="button" class="btn btn-outline-danger">ログアウト</button></a>
    </div>
    <div class="page-header title">
        勤怠管理システム
    </div>
    <br>
    <div class="content">
        <input type="hidden" id="id" value="<?php echo $_SESSION['id'] ?>">
        <input type="hidden" id="challenge" value="<?php echo $_SESSION['challenge'] ?>">
        <button id="submit" class="btn btn-primary stanping" onclick="stamp()">打刻</button><br><br>

        <h5 class="card-title"><?php echo '<p class="history">'. $_SESSION['id'] . 'さんの打刻履歴</p>' ?></h5>
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
        $fetchstamp = $pdo->prepare('select * from stamp where id=:id order by time DESC');
        $fetchstamp->bindValue(':id', $_SESSION['id']);
        $fetchstamp->execute();
        echo '<table class="table user">';
        echo '<thead><tr><th scope="col">打刻時間</th><th scope="col">出勤/退勤</th></thead><tbody>';
        while ($stamp = $fetchstamp->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr>';
            echo '<td>' . $stamp['time'] . '</td>';
            if ($stamp['att'] === '1') {
                $att = '出勤';
            } else {
                $att = '退勤';
            }
            echo '<td>' . $att . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        ?>
        </p>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="./js/stamp.js"></script>
</body>

</html>