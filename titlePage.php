<?php

session_start();
if(isset($_SESSION['username']))$username = $_SESSION['username'];
$author = $_GET["author"];
$title = $_GET["title"];


define('server', 'localhost');
define('username', 'root');
define('pwd', '');
define('name', 'hw4');
define('port','3306');
$link = mysqli_connect(server, username, pwd, name, port);
if($link==false){die("ERROR: could not connect" . mysqli_connect_error());}


?>
<!DOCTYPE html>
<html lang="zh-tw">

<head>
    <meta charset="UTF-8">
    <title>HW4</title>
    <link rel="stylesheet" href="main.css" />
    <link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://cdn.staticfile.org/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="sweetalert.js"></script>
    <script src="https://use.fontawesome.com/0a440c7864.js"></script>
    <script src="main.js"></script>
    </script>

    <style>
    table {
        margin: 10px;
    }
    </style>
</head>
<script type="text/javascript">
function getArtNum(author){
    $.ajax({
            type: "POST",
            url: "postArticle.php",
            dataType: "json",
            data: {
                author: author,
                act: "getArtNum"
            },
            success: function(data) {
                $("#articleNum").html("<div class='panel panel-primary'><div class='panel-heading'>Information</div><div class='panel-body'>發文數量:"+data.Num+"</div></div>");
                    
            },
            error: function(jqXHR,xhr,status,error) {
                console.log(jqXHR);
                console.log(xhr);
                console.log(error);
                console.log(status);
            }
        })
}

//pressLike
function pressLike(logged,aticleIdObj,author,title){
    console.log(logged);
    if(logged == 'Nologined'){
        Swal.fire({
            icon: 'error',
            title: 'You can not use this function',
            text: 'Please Login First.'
        })
        return;
    }
    var targetNode = $("#"+aticleIdObj.id+"presslike");
    var likenum = $("#"+aticleIdObj.id+"likenum").text();
    if(targetNode.hasClass('fa fa-thumbs-o-up')){
        //like!
        $.ajax({
            type: "POST", //傳送方式
            url: "postArticle.php", //傳送目的地
            dataType: "json", //資料格式
            data: { //傳送資料
                title: title, //表單欄位 ID nickname
                author: author,
                likenum: likenum,
                act: "presslike"
            },
            success: function(data) {
                targetNode.removeClass('fa fa-thumbs-o-up');
                targetNode.addClass('fa fa-thumbs-up');
                $("#"+aticleIdObj.id+"likenum").html(data.Num);
            },
            error: function(jqXHR,xhr,status,error) {
                console.log(jqXHR);
                console.log(xhr);
                console.log(error);
                console.log(status);
            }
        })
    }
    else{
        //dislikeQQ
        $.ajax({
            type: "POST", //傳送方式
            url: "postArticle.php", //傳送目的地
            dataType: "json", //資料格式
            data: { //傳送資料
                title: title, //表單欄位 ID nickname
                author: author,
                likenum: likenum,
                act: "pressdislike"
            },
            success: function(data) {
                targetNode.removeClass('fa fa-thumbs-up');
                targetNode.addClass('fa fa-thumbs-o-up');
                $("#"+aticleIdObj.id+"likenum").html(data.Num);
            }
        })
    }
}

//add conmment
function messageGo(aticleIdObj,author,title){
    var articleId = $("#"+aticleIdObj.id); //input id element
    var comment = articleId.val(); //comment
    var commentNum = $("#"+aticleIdObj.id+"commentnum").text();
    //console.log(title,author,comment);
    $.ajax({
            type: "POST", //傳送方式
            url: "postArticle.php", //傳送目的地
            dataType: "json", //資料格式
            data: { //傳送資料
                title: title, //表單欄位 ID nickname
                comment: comment, //表單欄位 ID gender
                author: author,
                commentNum: commentNum,
                act: "addcomment"
            },
            success: function(data) {
                if (data.comment) {
                    $("#"+aticleIdObj.id+"result").append("<div class='panel panel-default' style='text-align: left;'><div class='panel-body'><strong>"+data.username+"</strong><div style='text-align: right;'>"+data.time+"</div><div>"+data.comment+"</div></div></div>");
                    $("#"+aticleIdObj.id+"commentnum").html(data.Num);
                }
            },
            error: function(jqXHR,xhr,status,error) {
                $("#"+aticleIdObj.id+"result").html('<font color="#ff0000">發生錯誤：' + jqXHR.status + '</font>');
                //alert(xhr.responseText);
                console.log(jqXHR);
                console.log(xhr);
                console.log(error);
                console.log(status);
            }
        })
}

</script>

<body>

    <!-- main page -->
    <div id="main_page">

        <h2> Main Page </h2>
        <div id="headerBtn">
        <?php
            
            if(isset($_SESSION['username'])){
                $username = $_SESSION['username'];
                print '
                <input type="button" id="articleBtn" value="Article     /" onclick="window.location.href=\'index.php\'">
                <input type="button" id="logoutBtn" value="Logout" onclick="window.location.href=\'logout.php\'">
                ';
            }
            else{
                print '
                <input type="button" id="articleBtn" value="Article" onclick="window.location.href=\'index.php\'">
                ';
            }
        ?>
            
        </div>

        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class='panel panel-primary '>
                        <div class='panel-heading'></div>
                        <div class='panel-body'>
                            <img src="userImg.png" alt="Your Picture" style="border-radius: 50%;" width="100" height="100">
                            <div><input type='button' value=<?php  echo $author; ?> onclick = 'getArtNum("<?php  echo $author; ?>")'></input></div>
                        </div>
                    </div>
                    <div id='articleNum'></div>
                    
                </div>

                <div class="col-md-8">
                    <div id="result"></div> <!-- 顯示回傳資料 -->
                <?php
                    function getAllComment($author,$title)
                    {
                        $clink = mysqli_connect(server, username, pwd, name, port);
                        $comsql = "SELECT * from comment where comment.author = ? AND comment.title = ? ";
                        if($cstmt = mysqli_prepare($clink, $comsql)){
                            mysqli_stmt_bind_param($cstmt, "ss", $author,$title);
                            if(mysqli_stmt_execute($cstmt)){
                                mysqli_stmt_store_result($cstmt);
                                mysqli_stmt_bind_result($cstmt, $title,$comment,$time,$author,$username);
                                while(mysqli_stmt_fetch($cstmt)){
                                    echo "<div class='panel panel-default' style='text-align: left;'><div class='panel-body'><strong>".$username."</strong><div style='text-align: right;'>".$time."</div><div>".$comment."</div></div></div>";
                                }
                            }
                        }
                    }
                    function getCommentNum($author,$title)
                    {
                        $clink = mysqli_connect(server, username, pwd, name, port);
                        $comsql = "SELECT * from comment where comment.author = ? AND comment.title = ? ";
                        if($cstmt = mysqli_prepare($clink, $comsql)){
                            mysqli_stmt_bind_param($cstmt, "ss", $author,$title);
                            if(mysqli_stmt_execute($cstmt)){
                                mysqli_stmt_store_result($cstmt);
                                echo mysqli_stmt_num_rows($cstmt);
                            }
                        }
                    }
                    function getLikeNum($author,$title)
                    {
                        $clink = mysqli_connect(server, username, pwd, name, port);
                        $comsql = "SELECT * from presslike where presslike.author = ? AND presslike.title = ? ";
                        if($cstmt = mysqli_prepare($clink, $comsql)){
                            mysqli_stmt_bind_param($cstmt, "ss", $author,$title);
                            if(mysqli_stmt_execute($cstmt)){
                                mysqli_stmt_store_result($cstmt);
                                echo mysqli_stmt_num_rows($cstmt);
                            }
                        }
                    }
                    function checkLike($author,$title){
                        $username = $_SESSION['username'];
                        $clink = mysqli_connect(server, username, pwd, name, port);
                        $comsql = "SELECT * from presslike where presslike.author = ? AND presslike.title = ? AND presslike.username = ? ";
                        if($cstmt = mysqli_prepare($clink, $comsql)){
                            mysqli_stmt_bind_param($cstmt, "sss", $author,$title,$username);
                            if(mysqli_stmt_execute($cstmt)){
                                mysqli_stmt_store_result($cstmt);
                                if(mysqli_stmt_num_rows($cstmt)){
                                    echo "'fa fa-thumbs-up'";
                                }
                                else echo "'fa fa-thumbs-o-up'";
                            }
                        }
                    }
                    

                    $sql = "SELECT * from article where article.author = ? AND article.title = ? ";
                    
                    if($stmt = mysqli_prepare($link, $sql)){
                        mysqli_stmt_bind_param($stmt, "ss", $author,$title);
                        if(mysqli_stmt_execute($stmt)){
                            mysqli_stmt_store_result($stmt);
                            mysqli_stmt_bind_result($stmt, $author, $title, $time, $content);
                            while(mysqli_stmt_fetch($stmt)){
                                if(isset($_SESSION['username'])){
                                    echo "<div class='panel panel-default'><div class='panel-heading'>
                                    <h2  class='panel-title'>".$title."</h2><div style='text-align:right;'>".$time."
                                    </div></div><div class='panel-body'>".$content."</div>
                                    <button type='button' style='border:0;' class=";
                                    echo checkLike($author,$title);
                                    echo "id='".$author.$title."presslike' onClick=pressLike('logined',".$author.$title.",'".$author."','".$title."')>Like</button>
                                    <span id='".$author.$title."likenum'>";
                                    echo getLikeNum($author,$title);
                                    echo "</span>-likes<span> <span id='".$author.$title."commentnum'>";
                                    echo getCommentNum($author,$title);
                                    echo "</span>-comments
                                    <div class='well'><input type='text' class='form-control me-2' id='".$author.$title."'></input>
                                    <input type='button' class='btn btn-success' value='Send' onClick=messageGo(".$author.$title.",'".$author."','".$title."')></input><div id='".$author.$title."result'>
                                    ";
                                    echo getAllComment($author,$title);
                                    echo "</div></div></div>";
                                }
                                else{
                                    echo "<div class='panel panel-default'><div class='panel-heading'>
                                    <h2  class='panel-title'>".$title."</h2><div style='text-align:right;'>".$time."
                                    </div></div><div class='panel-body'>".$content."</div>
                                    <button type='button' style='border:0;' class='fa fa-thumbs-o-up'";
                                    echo "id='".$author.$title."presslike' onClick=pressLike('Nologined','".$author.$title."','".$author."','".$title."')>Like</button>
                                    <span id='".$author.$title."likenum'>";
                                    echo getLikeNum($author,$title);
                                    echo "</span>-likes<span> <span id='".$author.$title."commentnum'>";
                                    echo getCommentNum($author,$title);
                                    echo "</span>-comments<div class='well'>";
                                    echo getAllComment($author,$title);
                                    echo "</div></div></div>";

                                }

                            }
                        }
                    }
                ?>
                </div>
            </div>

        </div>

</body>

</html>