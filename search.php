
<?php


define('server', 'localhost');
define('username', 'root');
define('pwd', '');
define('name', 'hw4');
define('port', '3306');
$link = mysqli_connect(server, username, pwd, name, port);

if ($link == false) {
    die("ERROR: could not connect" . mysqli_connect_error());
}


if (isset($_GET['s'])) {
    $s = mysqli_real_escape_string($link, $_GET['s']);
    $sql = "SELECT * FROM article WHERE title LIKE '%" . $s . "%'";
    $result = mysqli_query($link, $sql);
    $arr = [];//存查詢出來的資料
    $i = 0;
    while($row = mysqli_fetch_assoc($result)){
        array_unshift($arr,$row);
        $i=$i+1;
    }

    $resultArr = ["total"=>$i,"info"=>$arr];
    echo json_encode($resultArr);

} else {
    $sql = "SELECT author,title,time FROM article ";
    $result = mysqli_query($link, $sql);
    $arr = [];//存查詢出來的資料
    $i = 0;
    while($row = mysqli_fetch_assoc($result)){
        array_unshift($arr,$row);
        $i=$i+1;
    }

    $resultArr = ["total"=>$i,"info"=>$arr];
    echo json_encode($resultArr);
}

mysqli_close($link);
?>