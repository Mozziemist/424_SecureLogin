<?php
// login.php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: welcome.php");
    exit;
}

// Include config file
require_once "config.php";
require_once "send_username_email.php";

// Define variables and initialize with empty values
$email = $first_name = $username = "";
$email_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Check if email is empty
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email.";
    } else{
        $email = trim($_POST["email"]);
    }
    
    if(empty($email_err)){
	// Check if email exists
	$check_email = mysqli_query($link, "SELECT * FROM users WHERE email = '$email' LIMIT 1");
	if(mysqli_num_rows($check_email) > 0){
	
	    // Get data from database 
	    $get_att = mysqli_query($link, "SELECT first_name, username FROM users WHERE email = '$email'");
	    $fetch_att = mysqli_fetch_array($get_att);
	    $first_name = $fetch_att[0];
	    $username = $fetch_att[1];

            // Send email
	    sendUsernameEmail($email, $first_name, $username);
	    
	    // Redirect to login
	    header("location: index.php");
	} else {
	    sleep(1); // stall in case no email
	    header("location: index.php");
	}
    }
    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Forgot Username</h2>
        <p>Please enter the email associated with your account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="needs-validation" method="post" novalidate>
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="email" name="email" class="form-control" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" value="<?php echo $email; ?>">
		<div class="invalid-feedback">Please enter a valid email.</div>
		<span class="form-text" style="color:red"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
		<input type="submit" class="btn btn-primary" value="Submit">
		<a class="btn btn-link" href="index.php">Back</a>
            </div>
        </form>
    </div>
</body>
</html>

