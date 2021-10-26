<?php
extract($_POST);
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_COOKIE["logged"]) && $_COOKIE["logged"] == true) {
    header("location: main.php");
    exit;
}

// Define variables and initialize with empty values
$email = $hashed_password = $password = "";
$email_err = $password_err = $login_err = "";


define('server', 'localhost');
define('username', 'root');
define('pwd', '');
define('name', 'hw4');
define('port', '3306');
$link = mysqli_connect(server, username, pwd, name, port);

if ($link == false) {
    die("ERROR: could not connect" . mysqli_connect_error());
}
// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username is empty
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter Email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($usermail_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT username,email,password FROM userinfo4 WHERE email = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_usermail);

            // Set parameters
            $param_usermail = $email;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $username, $usermail, $hashed_password);

                    if (mysqli_stmt_fetch($stmt)) {

                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            session_start();
                            // Store data in session variables
                            $_SESSION["username"] = $username;

                            setcookie("loginName", $username);
                            setcookie("pwd", $password);
                            setcookie("logged", true);

                            // Redirect user to welcome page
                            header("location: main.php");
                        } else {
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid username or password.";

                        }
                    }
                } else {
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";
                }
            } else {
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
    <title>HW4 Login</title>
    <script src="sweetalert.js">
    < script src = "main.js"
    defer >
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="login.css" />
</head>

<body>
    <!-- login page -->

    <div id="login_page">
        <div class="container">
            <div class="loginHeader">
                <input type="button" class='btn btn-primary' id="loginBtn" value="Login" name="loginhBtn">
                <input type="button" class='btn btn-light' id="regBtn" value="Register" name="regBtn"
                    onclick="window.location.href='register.php'">
            </div>
            <hr size="1px" width="50%" color="#CFCFCF">
            <?php
if (!empty($login_err)) {
    echo '<div class="alert alert-danger">' . $login_err . '</div>';
    echo "<script>Swal.fire({
                    icon: 'error',
                    title: 'Login Fail',
                    text: '" . $login_err . "'
                })</script>";
} else if (!empty($email_err)) {
    echo '<div class="alert alert-danger">' . $username_err . '</div>';
    echo "<script>Swal.fire({
                    icon: 'error',
                    title: 'Login Fail',
                    text: '" . $username_err . "'
                    })</script>";
} else if (!empty($password_err)) {
    echo '<div class="alert alert-danger">' . $password_err . '</div>';
    echo "<script>Swal.fire({
                    icon: 'error',
                    title: 'Login Fail',
                    text: '" . $password_err . "'
                })</script>";
}
?>
            <form id="loginForm" style='Background:white;'
                action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="userPwd">
                    <input type="text" id="email" class="form-control me-2" name="email"
                        style="margin-top: 20px; border-style: solid;" placeholder="Email" />
                    <input type="password" id="pwdtext" class="form-control me-2" name="password"
                        style="margin-top: 20px; border-style: solid;" placeholder="Password" />
                </div>
                <hr size="1px" width="30%" color="#CFCFCF">
                <div><a href='/hw4/register.php'>No account? please click here.</a></div>
                <input type="submit" id="log_inBtn" class="btn btn-outline-success" value="LOG IN" name="login_hBtn"
                    style="background-color:transparent;
    border: 0;">
            </form>

        </div>
    </div>

</body>

</html>