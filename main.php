<?php

session_start();
if(isset($_SESSION['username'])) $username = $_SESSION['username'];


define('server', 'localhost');
define('username', 'root');
define('pwd', '');
define('name', 'hw4');
define('port','3306');
$link = mysqli_connect(server, username, pwd, name, port);
if($link==false){die("ERROR: could not connect" . mysqli_connect_error());}
// take account and ticket from DB
$sql = "SELECT ticket,account from userinfo4 where userinfo4.username = ?";

if($stmt = mysqli_prepare($link, $sql)){
    mysqli_stmt_bind_param($stmt, "s", $username);
    if(mysqli_stmt_execute($stmt)){
        mysqli_stmt_store_result($stmt);
        mysqli_stmt_bind_result($stmt, $ticket, $account);
        mysqli_stmt_fetch($stmt);
    }
}

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
    <script src="main.js"></script>
    <script src="https://use.fontawesome.com/0a440c7864.js"></script>
    </script>

    <style>
    table {
        margin: 10px;
    }
    </style>
</head>
<script type="text/javascript">
//pressLike
function pressLike(aticleIdObj,author,title){
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
//delete article
function deleteArticle(aticleIdObj,author,title){
    var deleteTarget = aticleIdObj.parentNode.parentNode;
    //var articleId = $("#"+aticleIdObj+"delete".id); //input id element
    //console.log(aticleIdObj.parentNode.parentNode);
    $.ajax({
            type: "POST", //傳送方式
            url: "postArticle.php", //傳送目的地
            dataType: "json", //資料格式
            data: { //傳送資料
                title: title, //表單欄位 ID nickname
                author: author,
                act: "deleteArticle"
            },
            success: function(data) {
                deleteTarget.parentNode.parentNode.removeChild(deleteTarget.parentNode);
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

//turntable turn
$(document).ready(function() {
    $("#pointer").click(function() {
        var angle = 36; //360/10
        var turntable = $("#circle");
        var timer = null;
        var finalAngle = Math.floor(Math.random() * 360);
        while(!finalAngle%36){
            finalAngle = Math.floor(Math.random() * 360);
        }

        turntable.css('transform','rotate(18deg)');
        $({rotation: 18}).animate({rotation: finalAngle+1098},
        {
            duration: 3000,
            step: function(now){
                turntable.css({transform: 'rotate(' + now + 'deg)'});
            }
        });

        var reward = 0;
        if(finalAngle <= (angle * 10) && finalAngle > (angle * 9)) {console.log("$0"); reward = 0;}
        else if(finalAngle <= (angle * 9) && finalAngle > (angle * 8)) {console.log("$50"); reward = 50;}
        else if(finalAngle <= (angle * 8) && finalAngle > (angle * 7)) {console.log("$0"); reward = 0;}
        else if(finalAngle <= (angle * 7) && finalAngle > (angle * 6)) {console.log("$150"); reward = 150;}
        else if(finalAngle <= (angle * 6) && finalAngle > (angle * 5)) {console.log("$500"); reward = 500;}
        else if(finalAngle <= (angle * 5) && finalAngle > (angle * 4)) {console.log("$0"); reward = 0;}
        else if(finalAngle <= (angle * 4) && finalAngle > (angle * 3)) {console.log("$20"); reward = 20;}
        else if(finalAngle <= (angle * 3) && finalAngle > (angle * 2)) {console.log("$0"); reward = 0;}
        else if(finalAngle <= (angle * 2) && finalAngle > (angle * 1)) {console.log("$50"); reward = 50;}
        else if(finalAngle <= (angle * 1) && finalAngle > (angle * 0)) {console.log("$20"); reward = 20;}
        else {console.log("??");}

        $.ajax({
            type: "POST", //傳送方式
            url: "postArticle.php", //傳送目的地
            dataType: "json", //資料格式
            data: { //傳送資料
                ticket: $("#ticket").text(),
                account:$("#account").text(),
                reward: reward,
                act: "lottery"
            },
            success: function(data) {
                $("#ticket").html(data.ticket);
                $("#account").html(data.account);
               
            },
            error: function(jqXHR,xhr,status,error) {
                $("#ticket").html('<font color="#ff0000">發生錯誤：' + jqXHR.status + '</font>');
                //alert(xhr.responseText);
                console.log(jqXHR);
                console.log(xhr);
                console.log(error);
                console.log(status);
            }
        })
    });

});
//add new post
$(document).ready(function() {
    $("#submitCreate").click(function() { //ID 為 submitExample 的按鈕被點擊時
        //console.log($("#title").val());
        $.ajax({
            type: "POST",
            url: "postArticle.php",
            dataType: "json",
            data: {
                title: $("#title").val(),
                content: $("#content").val(),
                account: $("#account").text(),
                act: "add"
            },
            success: function(data) {
                if (data.title) {
                    $("#result").prepend("<div class='panel panel-default'><div class='panel-heading'><h2  class='panel-title'>" + data.title
                     + "</h2><div style='text-align:right;'>"+ data.today+"<button type='button' style='border:0;' class=\"fa fa-trash\"  id='"+data.author+data.title+"delete' onClick=deleteArticle("+data.author+data.title+",'"+data.author+"','"+data.title+"')></button><button type='button'style='border:0;' class=\"fa fa-edit\" data-toggle=\"modal\" data-target=\"#editModal\" id='"+data.author+data.title
                     +"edit' onClick=editArticle("+data.author+data.title+",'"+data.author+"','"+data.title+"')></button></div></div><div class='panel-body'>"
                     +data.content+"</div><button type='button' style='border:0;' class=\"fa fa-thumbs-o-up\"  id='"+data.author+data.title+"presslike' onClick=pressLike("+data.author+data.title+",'"+data.author+"','"+data.title
                     +"')>Like</button><span id='"+data.author+data.title+"likenum'>0</span> -likes<span> <span id='"+data.author+data.title+"commentnum'>0</span>-comments<div class='well'><input type='text' class='form-control me-2' id='"+data.author+data.title+
                     "'></input><input type='button' class='btn btn-success' value='Send' onClick=messageGo("+data.author+data.title+",'"+data.author+"','"+data.title+"')></input><div id='"+data.author+data.title+"result'></div></div></div></div>");
                     $("#account").html(data.account);
                     $("#title").val("");
                     $("#content").val("");
                } else {
                    alert(data.error);
                }
            },
            error: function(jqXHR,xhr,status,error) {
                $("#result").html('<font color="#ff0000">發生錯誤：' + jqXHR.status + '</font>');
                //alert(xhr.responseText);
                console.log(jqXHR);
                console.log(xhr);
                console.log(error);
                console.log(status);
            }
        })
    })
});
//update post
function editArticle(aticleIdObj,author,title){
    var targetObj = aticleIdObj.parentNode.parentNode.parentNode;
    var oldTitle = $('h2', targetObj).text();
    //console.log(targetObj.childNodes[1].innerText);
    //console.log(targetObj.childNodes[4].nodeValue);
    $("#editTitle").val($('h2', targetObj).text());  //origin title
    $("#editContent").val(targetObj.childNodes[1].innerText);  //origin content
    $("#editCreate").click(function() { //ID 為 submitExample 的按鈕被點擊時
        console.log($("#editTitle").val());
        $.ajax({
            type: "POST", //傳送方式
            url: "postArticle.php", //傳送目的地
            dataType: "json", //資料格式
            data: { //傳送資料
                title: $("#editTitle").val(), //表單欄位 ID nickname
                content: $("#editContent").val(), //表單欄位 ID gender
		oldTitle: oldTitle,
		author: author,
                act: "updateArticle"
            },
            success: function(data) {
		    if(data.title){
			    $('h2', targetObj).text(data.title);
			    targetObj.childNodes[1].innerText = data.content;
		    }
		    else{
			    alert(data.error);
		    }

            },
            error: function(jqXHR,xhr,status,error) {
                $("#result").html('<font color="#ff0000">發生錯誤：' + jqXHR.status + '</font>');
                //alert(xhr.responseText);
                console.log(jqXHR);
                console.log(xhr);
                console.log(error);
                console.log(status);
            }
        })
    })
}


</script>

<body>

    <!-- main page -->
    <div id="main_page">

        <h2> Main Page </h2>
        <div id="headerBtn">
            <input type="button" id="articleBtn" value="Article     /" onclick="window.location.href='index.php'">
            <input type="button" id="logoutBtn" value="Logout" onclick="window.location.href='logout.php'">
        </div>
        <?php
if (!empty($createSuccess)) {
    echo "<script>Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Post success'
                    }).then(function(){

                        });
                    </script>";
}
?>

        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class='panel panel-primary '>
                        <div class='panel-heading'></div>
                        <div class='panel-body'>
                            <img src="userImg.png" alt="Your Picture" style="border-radius: 50%;" width="100" height="100">
                            <div><?php  echo $username; ?></div>
                        
                    <!-- target button -->
                    <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
                        POST
                    </button>
                    <!-- POST Modal -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h4 class="modal-title" id="myModalLabel">POST</h4>
                                </div>
                                <div class="modal-body">
                                    <form role="form" action="" method="post">
                                        <div class="form-group">
                                            <label for="name">Title(cannot include special character, and your title should be unique)</label>
                                            <input type="text" class="form-control" id="title">
                                            <label for="name">Content</label>
                                            <textarea class="form-control" rows="20" id="content"></textarea>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default"
                                                data-dismiss="modal">Close</button>
                                            <button type="button" id="submitCreate"
                                                class="btn btn-primary" data-dismiss="modal">Create</button>
                                        </div>
                                    </form>
                                </div>

                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                    <!-- create modal -->
                    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h4 class="modal-title" id="myModalLabel">POST</h4>
                                </div>
                                <div class="modal-body">
                                    <form role="form" action="" method="post">
                                        <div class="form-group">
                                            <label for="name">Title(cannot include special character)</label>
                                            <input type="text" class="form-control" id="editTitle">
                                            <label for="name">Content</label>
                                            <textarea class="form-control" rows="20" id="editContent"></textarea>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default"
                                                data-dismiss="modal">Close</button>
                                            <button type="button" id="editCreate"class="btn btn-success" data-dismiss="modal">Edit</button>
                                        </div>
                                    </form>
                                </div>

                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->


                    </div>
                    </div>
                    <div class='panel panel-primary '>
                        <div class='panel-heading'>Information</div>
                        <div class='panel-body'>
                            <div>Ticket:</div><div id="ticket"><?php echo $ticket; ?></div>
                            <div>Account:</div><div id="account"><?php echo $account; ?></div>
                        </div>
                    </div>
                    <!-- lottery turntable -->
                    <input type="image" src="circle.png" alt="circle" id="circle" style="border-radius: 50%;" width="200" height="200">
                    <input type="image" src="circlecenter.png" id="pointer" name="pointer" width="60" height="50">
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
                    

                    $sql = "SELECT * from article where article.author = ? ORDER BY time DESC ";

                    if($stmt = mysqli_prepare($link, $sql)){
                        mysqli_stmt_bind_param($stmt, "s", $username);
                        if(mysqli_stmt_execute($stmt)){
                            mysqli_stmt_store_result($stmt);
                            mysqli_stmt_bind_result($stmt, $author, $title, $time, $content);
                            while(mysqli_stmt_fetch($stmt)){
                                echo "<div class='panel panel-default'><div class='panel-heading'>
                                <h2  class='panel-title'>".$title."</h2><div style='text-align:right;'>".$time.
                                "<button type='button' style='border:0;' class=\"fa fa-trash\"  id='".$author.$title."delete' onClick=deleteArticle(".$author.$title.",'".$author."','".$title."')></button>
                                <button type='button' style='border:0;' class=\"fa fa-edit\" data-toggle=\"modal\" data-target=\"#editModal\" id='".$author.$title."edit' onClick=editArticle(".$author.$title.",'".$author."','".$title."')></button>
                                </div></div><div class='panel-body'>".$content."</div>
                                <button type='button' style='border:0;' class=";
                                echo checkLike($author,$title);
                                echo "id='".$author.$title."presslike' onClick=pressLike(".$author.$title.",'".$author."','".$title."')>Like</button>
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
                        }
                    }
                    ?>

                </div>
            </div>

        </div>

</body>

</html>
