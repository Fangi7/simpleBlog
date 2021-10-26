<?php

session_start();
if(isset($_SESSION['username']))$username = $_SESSION['username'];

?>
<!DOCTYPE html>
<html lang="zh-tw">

<head>
    <meta charset="UTF-8">
    <title>HW4</title>
    <script src="sweetalert.js"></script>
    <script src="main.js" defer></script>
    <link rel="stylesheet" href="main.css" />
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css"
        integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.4.1.js"
        integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
    <style>
    table {
        margin: 10px;
    }
    </style>
</head>

<body>

    <!-- main page -->
    <div id="main_page">

        <h2> Article Page </h2>
        <div id="headerBtn">
            <input type="button" id="articleBtn" value="Article" onclick="window.location.href='index.php'">
        <?php
            
            if(isset($_SESSION['username'])){
                $username = $_SESSION['username'];
                print '
                <input type="button" id="mainBtn" value="Main Page" onclick="window.location.href=\'main.php\'">
                <input type="button" id="logoutBtn" value="Logout" onclick="window.location.href=\'logout.php\'">
                ';
            }
            else{
                print '
                <input type="button" id="mainBtn" value="Login" onclick="window.location.href=\'login.php\'">
                <input type="button" id="logoutBtn" value="Register" onclick="window.location.href=\'register.php\'">
                ';
            }
        ?>
            
        </div>


        <div class="container">
            <input type="search" id="search_text" class="form-control me-2" style="margin:0" name="search_text" placeholder="Search" />
            <table class='table'><tbody><tr><td>#</td><td>Author</td><td>Title</td><td>Time</td></tr></tbody><tbody id="search_result"></tbody></table>

            <script>
            $(document).ready(function() {

                load_data();

                function drawTable(data){
                    for(let i = 0 ; i < data.total; i++){
                        console.log(data.info[i]);
                        $('#search_result').append("<tr><td>"+(i+1)+"</td><td>"+data.info[i].author+"</td><td><a href='titlePage.php?author="+data.info[i].author+"&title="+data.info[i].title+"'>"+data.info[i].title+ "</a></td><td>"+data.info[i].time+"</td></tr>");
                    }
                }

                function load_data(query) {
                    $.ajax({
                        url: "search.php",
                        dataType: "json",
                        method: "GET",
                        data: {
                            s: query
                        },
                        success: function(data) {
                            $('#search_result').html("");
                            drawTable(data);
                            //console.log(data);
                        },
                        error: function(jqXHR,xhr,status,error) {
                            $("#search_result").html('<font color="#ff0000">發生錯誤：' + jqXHR.status + '</font>');
                            //alert(xhr.responseText);
                            console.log(jqXHR);
                            console.log(xhr);
                            console.log(error);
                            console.log(status);
                        }
                    });
                }
                $('#search_text').keyup(function() {
                    var search = $(this).val();
                    if (search != '') {
                        load_data(search);
                    } else {
                        load_data();
                    }
                });
            });
            </script>


        </div>

</body>

</html>