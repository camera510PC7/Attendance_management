<?php
session_start();
session_regenerate_id(true);
if ((!isset($_SESSION['login'])) || ($_SESSION['admin'] != 1)) {
    echo "不正な操作が検知されました";
    return false;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>勤怠管理システム 管理者用画面</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Sawarabi+Gothic" rel="stylesheet">
    <link href="CSS/admin.css" rel="stylesheet">
</head>

<body>
    <div class="logout">
        <a href="./logout.php"><button type="button" class="btn btn-outline-danger">ログアウト</button></a>
    </div>
    <div class="content">
        <div class="page-header title">
            勤怠管理システム 管理者用画面
        </div>
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
        $fetchdata = $pdo->prepare('select * from stamp as a WHERE time=(select max(time) from stamp as b WHERE a.id=b.id) order by att desc;');
        $fetchdata->execute();
        date_default_timezone_set("Asia/Tokyo");
        echo '<p class="now">' . date("Y/m/d H:i:s", time()) . ' 現在の状況</p>';
        echo '<table class="table user">';
        echo '<thead><tr><th scope="col">ID</th><th scope="col">最終打刻時間</th><th scope="col">出勤/退勤</th></thead><tbody>';
        while ($stamp = $fetchdata->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr>';
            echo '<td>' . $stamp['id'] . '</td>';
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
    </div>
</body>

</html>