<?php
// Include config file
//require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = $email = "";
$username_err = $password_err = $confirm_password_err = $email_err = "";

define('server', 'localhost');
define('username', 'root');
define('pwd', '');
define('name', 'hw4');
define('port','3306');
$link = mysqli_connect(server, username, pwd, name,port);

if($link == false)
{
    die("ERROR: could not connect" . mysqli_connect_error());
}
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{

        $username = trim($_POST["username"]);
	// Prepare a select statement
	$sql = "SELECT username FROM userinfo4 WHERE username='".$username."'";
	$result = mysqli_query($link,$sql);
	if(mysqli_num_rows($result)>0) $email_err ="This username is already taken.";
        $sql = "SELECT email FROM userinfo4 WHERE email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = trim($_POST["email"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "This Email is already taken.";
                } else{
                    $email = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            //mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Validate username
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter a email.";     
    } else{
        $email = trim($_POST["email"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO userinfo4 (username, password, email, ticket, account) VALUES (?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            $ticket = 50;
            $account = 100;
            mysqli_stmt_bind_param($stmt, "sssii", $param_username, $param_password,$param_email,$ticket,$account);
            
            // Set parameters
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                $registerSuccess = true;
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
           // mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
   // mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="zh-tw">

<head>
    <meta charset="UTF-8">
    <title>HW4 Register</title>
    <script src="sweetalert.js">
    <script src="main.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="login.css" />
</head>

<body>
    <!-- login page -->
    <div id="login_page">
        <div class="container">
            <div class="loginHeader">
                <input type="button" id="loginBtn" value="Login" name="loginhBtn" onclick="window.location.href='login.php'">
                <input type="button" id="regBtn" value="Register" name="regBtn" >
            </div>
            <hr size="1px" width="50%" color="#CFCFCF">   
            <?php 
                if(!empty($registerSuccess))
                {
                    echo "<script>Swal.fire({
                    icon: 'success',
                    title: 'Register success',
                    text: 'Register success'
                    }).then(function(){
                            window.location = 'login.php';
                        });
                    </script>";     
                }
                if(!empty($username_err)){
                    echo '<div class="alert alert-danger">' . $username_err . '</div>';
                    echo "<script>Swal.fire({
                        icon: 'error',
                        title: '" . $username_err . "',
                        text: '" . $username_err . "'
                    })</script>";
                }  
                if(!empty($email_err)){
                    echo '<div class="alert alert-danger">' . $email_err . '</div>';
                    echo "<script>Swal.fire({
                        icon: 'error',
                        title: '" . $email_err . "',
                        text: '" . $email_err . "'
                    })</script>";
                }  
                else if(!empty($password_err)){
                    echo '<div class="alert alert-danger">' . $password_err . '</div>';
                    echo "<script>Swal.fire({
                        icon: 'error',
                        title: '" . $password_err . "',
                     text: '" . $password_err . "'
                    })</script>";
                }  
                else if(!empty($confirm_password_err)){
                    echo '<div class="alert alert-danger">' . $confirm_password_err . '</div>';
                    echo "<script>Swal.fire({
                        icon: 'error',
                        title: '" . $confirm_password_err . "',
                        text: '" . $confirm_password_err . "'
                    })</script>";
                }        
        ?>     

            <form id="regForm" style='Background:white;' <?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post">
                <div class="userPwd">
                    <input type="text" id="usertext2" class="form-control me-2" name="username"  style="margin-top: 20px; border-style: solid;"
                        placeholder="Username" />
                    <input type="text" id="email2" class="form-control me-2" name="email"  style="margin-top: 20px; border-style: solid;"
                        placeholder="Email" />
                    <input type="password" id="pwdtext2" class="form-control me-2" name="password" style="margin-top: 20px; border-style: solid;"
                        placeholder="Password" />
                    <input type="password" id="pwdconfirm" name="confirm_password" class="form-control me-2" style="margin-top: 20px; border-style: solid;"
                        placeholder="Retype Password" />
                </div>
                <hr size="1px" width="30%" color="#CFCFCF">
                <div><a href='/hw4/login.php'>Already have account? please click here.</a></div>
                <input type="submit" id="reg_Btn" class="btn btn-outline-success" value="REGISTER NOW" name="reg_Btn" style="background-color:transparent;
    border: 0;">
            </form>
        </div>
    </div>

</body>

</html>
