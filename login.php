<?php
session_start();
session_regenerate_id(true);
try{
    $pdo=new PDO("mysql:host=hostname;dbname=am","username","password",
    array(
        PDO::MYSQL_ATTR_INIT_COMMAND=>"SET CHARACTER SET'utf8'"
    ));
}catch(PDOException $e){
    die($e->getMessage());
}

$prepare = $pdo->prepare('select * from user where id = :id');
$prepare -> bindValue(':id',$_POST['id']);
$prepare -> execute();
$data = $prepare -> fetch(PDO::FETCH_ASSOC);

if(!isset($data['pass'])){
    echo "ログイン失敗<br>IDもしくはパスワードが違います<br><br>";
    echo "<a href='./index.html'>戻る</a>";
    return false;
}

if(password_verify($_POST['pass'],$data['pass'])){
    $_SESSION['login'] = 1;
    $_SESSION['id'] = $_POST['id'];
    if($_POST['id']==='admin'){
        $_SESSION['admin'] = 1;
        header("Location:./admin.php");
    }else{
        $_SESSION['admin'] = 0;
        header("Location:./mypage.php");
    };
    
}else{
    echo "ログイン失敗<br>IDもしくはパスワードが違います<br><br>";
    echo "<a href='./index.html'>戻る</a>";
    return false;
}
