<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}

?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
    </style>
</head>
<body>
    <div class="page-header">
    <h1>Hi, <b><?php echo "{$_SESSION['first_name']} {$_SESSION['last_name']}"; ?></b>. Welcome to our site.</h1>
    <h2>Your last login was on <b><?php echo "{$_SESSION['last_login']}"; ?> PST.</b></h2>
    <h3>Times logged in: <b><?php echo "{$_SESSION['login_count']}"; ?></b></h3>
    </div>
    <p>
	<a href="company_confidential_file.txt" download class="btn btn-primary">Download company_confidential_file.txt</a>
        <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
    </p>
</body>
</html>
