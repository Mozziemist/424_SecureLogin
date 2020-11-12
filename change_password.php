<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
 
// Include config file
require "config.php";
 
// Define variables and initialize with empty values
$current_password = $new_password = $confirm_password = "";
$current_password_err = $new_password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Validate current password
    if(empty(trim($_POST["current_password"]))){
        $current_password_err = "Please enter your current password."; 
    } else {
        $current_password = trim($_POST["new_password"]);
    }

    // Validate new password
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Please enter a new password.<br>"; 
    } if(strlen(trim($_POST["new_password"])) < 8){
        $new_password_err .= "Password must have at least 8 characters.<br>";
    } if(!preg_match("#[0-9]+#", trim($_POST["new_password"]))){
        $new_password_err .= "Password must contain at least 1 number.<br>";
    } if(!preg_match("#[A-Z]+#", trim($_POST["new_password"]))){
        $new_password_err .= "Password must contain at least 1 capital letter.<br>";
    } if(!preg_match("#[a-z]+#", trim($_POST["new_password"]))){
        $new_password_err .= "Password must contain at least 1 lowercase letter.<br>";
    } if(!preg_match('/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/', trim($_POST["new_password"]))){
        $new_password_err .= "Password must contain at least 1 of the following special characters: <b>~!@#$%^&*()-_=+{}\|:;'\",./<>?</b>";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm the password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
        
    // Check input errors before updating the database
    if(empty($new_password_err) && empty($confirm_password_err)){

        // Check if current password matches actual current password
        $result = "SELECT password FROM users WHERE id = ?";
        
        if($stmt1 = mysqli_prepare($link, $result)){
            mysqli_stmt_bind_param($stmt1, "s", $param_id);

            $param_id = $_SESSION["id"];
            
            // attempt run
            if(mysqli_stmt_execute($stmt1)){
                mysqli_stmt_store_result($stmt1);
                mysqli_stmt_bind_result($stmt1, $id, $hashed_password);
		if(password_verify($current_password, $hashed_password)){

       	    // Prepare an update statement
            $sql = "UPDATE users SET password = ? WHERE id = ?";
        
            if($stmt = mysqli_prepare($link, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "si", $param_password, $param_id);
            
                // Set parameters
                $param_password = password_hash($new_password, PASSWORD_DEFAULT);
                $param_id = $_SESSION["id"];
            
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    // Password updated successfully. Destroy the session, and redirect to login page
                    session_destroy();
                    header("location: login.php");
                    exit();
                } else{
                    echo "Oops! Something went wrong. Please try again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
	    }
       } else {
            $current_password_err = "Password does not match.";
       }
    }
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
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Change Password</h2>
        <p>Please enter the following to reset your password.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
	<div class="form-group <?php echo (!empty($current_password_err)) ? 'has-error' : ''; ?>">
                <label>Current Password</label>
                <input type="password" name="current_password" class="form-control" value="<?php echo $current_password; ?>">
                <span class="help-block"><?php echo $current_password_err; ?></span>
	</div>    
	<div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control" value="<?php echo $new_password; ?>">
                <span class="help-block"><?php echo $new_password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a class="btn btn-link" href="welcome.php">Cancel</a>
            </div>
        </form>
    </div>    
</body>
</html>
