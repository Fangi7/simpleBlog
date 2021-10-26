<?php

session_start();

// Define variables and initialize with empty values
if(isset($_SESSION['username']))$username = $_SESSION['username'];


define('server', 'localhost');
define('username', 'root');
define('pwd', '');
define('name', 'hw4');
define('port', '3306');
$link = mysqli_connect(server, username, pwd, name, port);

if ($link == false) {
	die("ERROR: could not connect" . mysqli_connect_error());
}
$act = $_POST["act"];

if($act == "getArtNum"){
	$author = $_POST['author'];
	$sql = "SELECT * from article where article.author = ? ";
	if($stmt = mysqli_prepare($link, $sql)){
		mysqli_stmt_bind_param($stmt, "s", $author);
		if(mysqli_stmt_execute($stmt)){
			mysqli_stmt_store_result($stmt);
			$num = mysqli_stmt_num_rows($stmt);
			$arr= array("Num" => $num);
			$jsonString = json_encode($arr,true);
			echo $jsonString;
		}
	}
}


//update article
if($act == "updateArticle"){
	$title = $_POST["title"];
	$author = $_POST["author"];
	$sql = "SELECT * FROM article WHERE title = '".$title."' AND author='".$author."'";
	$result = mysqli_query($link,$sql);
	if(mysqli_num_rows($result)){
		echo json_encode(array("error"=>"You have the same title before, please change one."));
	}
	else{
		$content = $_POST["content"];
			$oldTitle = $_POST["oldTitle"];
		$sql = "UPDATE comment SET title= '".$title."' WHERE title ='".$oldTitle."' AND author='".$author."'";
		mysqli_query($link,$sql);
	        $sql = "UPDATE presslike SET title='".$title."' WHERE title ='".$oldTitle."' AND author='".$author."'";
		mysqli_query($link,$sql);	
		$sql = "UPDATE article SET title= '".$title."' , content= '".$content."' WHERE title='".$oldTitle."' AND author= '".$author."'";
		mysqli_query($link,$sql);
		if(mysqli_affected_rows($link)){//update success
			$arr= array("title" => $title, "content" => $content);
			$jsonString = json_encode($arr,true);
			echo $jsonString;
		}
		else{//update error
			echo '{"error":"1","msg":"update error！"}';
		}
	}
}


if($act == "presslike"){
	$author = $_POST['author'];
	$title = $_POST['title'];
	$sql = "INSERT INTO presslike (author, title, username) VALUES (?, ?, ?)";
	if ($stmt = mysqli_prepare($link, $sql)) {
		$author = $_POST["author"];
		$title = $_POST["title"];
		$Num = $_POST["likenum"]+1;
		mysqli_stmt_bind_param($stmt, "sss", $author, $title, $username);
		// Attempt to execute the prepared statement
		if (mysqli_stmt_execute($stmt)) {
			$arr= array("Num"=>$Num);
			$jsonString = json_encode($arr,true);
			echo $jsonString;
		} else {
			$arr= array("errorMsg" => "input DB error");
			//$arr= json_encode(array("errorMsg" => "input DB error"));
			$jsonString = json_encode($arr,true);
			echo $jsonString;
		}
	}

}

if($act == "pressdislike"){
	$author = $_POST['author'];
	$title = $_POST['title'];
	$Num = $_POST["likenum"]-1;
	$sql ="DELETE FROM presslike WHERE author= '".$author."' AND title= '".$title."' AND username ='".$username."'";
	mysqli_query($link,$sql);

	if(mysqli_affected_rows($link)){//update success
		$arr= array("Num"=>$Num);
		$jsonString = json_encode($arr,true);
		echo $jsonString;
	}
	else{
		$arr= array("errorMsg" => "input DB error");
		$jsonString = json_encode($arr,true);
		echo $jsonString;
	}
}


if($act == "deleteArticle"){
	$author = $_POST['author'];
	$title = $_POST['title'];
	$sql ="DELETE FROM comment WHERE author= '".$author."' AND title= '".$title."'";
	mysqli_query($link,$sql);

	$sql ="DELETE FROM article WHERE author= '".$author."' AND title= '".$title."'";
	mysqli_query($link,$sql);
	if(mysqli_affected_rows($link)){//update success
		$arr= array("message" => "success");
		$jsonString = json_encode($arr,true);
		echo $jsonString;
	}
	else{
		$arr= array("errorMsg" => "input DB error");
		$jsonString = json_encode($arr,true);
		echo $jsonString;
	}
}
if($act == "addcomment"){
	// Prepare an insert statement
	$sql = "INSERT INTO comment (author, title, time, comment,username) VALUES (?, ?, ?, ?, ?)";
	if ($stmt = mysqli_prepare($link, $sql)) {
		// Bind variables to the prepared statement as parameters
		$author = $_POST["author"];
		$title = $_POST["title"];
		$today = date('Y-m-d H:i:s');
		$comment = $_POST["comment"];
		$Num = $_POST["commentNum"]+1;
		mysqli_stmt_bind_param($stmt, "sssss", $author, $title, $today, $comment, $username);
		// Attempt to execute the prepared statement
		if (mysqli_stmt_execute($stmt)) {
			// echo json_encode(array("title" => $title, "today" => $today, "content" => $content));
			$arr= array("title" => $title, "username" => $username, "comment" => $comment,"time"=>$today,"Num"=>$Num);
			$jsonString = json_encode($arr,true);
			echo $jsonString;
		} else {
			$arr= array("errorMsg" => "input DB error");
			//$arr= json_encode(array("errorMsg" => "input DB error"));
			$jsonString = json_encode($arr,true);
			echo $jsonString;
		}
	}
}



if($act=="lottery"){
	$ticket = $_POST["ticket"]-1;
	$account = $_POST["account"] + $_POST["reward"];
	$sql = "UPDATE userinfo4 SET ticket= '".$ticket."' , account= '".$account."' WHERE username='".$username."'";
	mysqli_query($link,$sql);
	if(mysqli_affected_rows($link)){//update success
		$arr= array("ticket" => $ticket, "account" => $account);
		$jsonString = json_encode($arr,true);
		echo $jsonString;
	}
	else{//update error
		echo '{"error":"1","msg":"update error！"}';
	}
}

if($act == "add"){
	$title = $_POST["title"];

	$sql = "SELECT * FROM article WHERE author= '".$username."' AND title= '".$title."'";
	$result = mysqli_query($link,$sql);
	if(mysqli_num_rows($result)>0){
		echo json_encode(array("error"=>"You use same title before, please change a title."));

	}
	else
	{

		$account = $_POST["account"]-5;
		$sql = "UPDATE userinfo4 SET account= '".$account."' WHERE username='".$username."'";
		mysqli_query($link,$sql);
		mysqli_affected_rows($link);

		// Prepare an insert statement

		$sql = "INSERT INTO article (author, title, time, content) VALUES (?, ?, ?, ?)";
		if ($stmt = mysqli_prepare($link, $sql)) {
			// Bind variables to the prepared statement as parameters
			$title = $_POST["title"];
			$today = date('Y-m-d H:i:s');
			$content = $_POST["content"];
			mysqli_stmt_bind_param($stmt, "ssss", $username, $title, $today, $content);
			// Attempt to execute the prepared statement
			if (mysqli_stmt_execute($stmt)) {
				// Redirect to login page
				$createSuccess = "Post Success.";
				// echo json_encode(array("title" => $title, "today" => $today, "content" => $content));
				$arr= array("title" => $title, "today" => $today, "content" => $content, "author"=> $username,"account"=>$account);
				$jsonString = json_encode($arr,true);
				echo $jsonString;
			} else {
				$arr= array("errorMsg" => "input DB error");
				//$arr= json_encode(array("errorMsg" => "input DB error"));
				$jsonString = json_encode($arr,true);
				echo $jsonString;
			}
		}
	}  
}
?>
