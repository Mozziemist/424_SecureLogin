<?php
// Include config file
require_once "config.php";
//require_once "send_email.php";
 
// Define variables and initialize with empty values
$first_name = $last_name = $email = $birth_date = $username = $password = $confirm_password =  "";
$first_name_err = $last_name_err = $email_err = $birth_date_err = $username_err = $password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate first name
    if(empty(trim($_POST["first_name"]))){
        $first_name_err = "Please enter a name.";
    } else{
        $first_name = trim($_POST["first_name"]);
    }

    // Validate last name
    if(empty(trim($_POST["last_name"]))){
        $last_name_err = "Please enter a name.";
    } else {
	$last_name = trim($_POST["last_name"]);
    }

    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);

            // Set parameters
            $param_email = trim($_POST["email"]);

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);

                if (!filter_var($param_email, FILTER_VALIDATE_EMAIL)){
                    $email_err = "Invalid email format";
                } else if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "This email is already in use.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Validate date of birth
    if(empty(trim($_POST["birth_date"]))){
        $birth_date_err = "Please enter your date of birth.";
    } else{
        $birth_date = trim($_POST["birth_date"]);
    }

    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.<br>"; 
    } if(strlen(trim($_POST["password"])) < 8){
        $password_err .= "Password must have at least 8 characters.<br>";
    } if(!preg_match("#[0-9]+#", trim($_POST["password"]))){
        $password_err .= "Password must contain at least 1 number.<br>";
    } if(!preg_match("#[A-Z]+#", trim($_POST["password"]))){
        $password_err .= "Password must contain at least 1 capital letter.<br>";
    } if(!preg_match("#[a-z]+#", trim($_POST["password"]))){
        $password_err .= "Password must contain at least 1 lowercase letter.<br>";
    } if(!preg_match('/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/', trim($_POST["password"]))){
        $password_err .= "Password must contain at least 1 of the following special characters: <b>~!@#$%^&*()-_=+{}\|:;'\",./<>?</b>";
    } else{
        $password = trim($_POST["password"]);
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
    if(empty($first_name_err) && empty($last_name_err) && empty($email_err) && empty($birth_date_err) && empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
	// Prepare an insert statement
	$sql = "INSERT INTO users (first_name, last_name, email, birth_date, username, password) VALUES (?, ?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssss", $param_first_name, $param_last_name, $param_email, $param_birth_date, $param_username, $param_password);
            
	    // Set parameters
	    $param_first_name = $first_name;
	    $param_last_name = $last_name;
            $param_email = $email;
            $param_birth_date = $birth_date;
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
	    if(mysqli_stmt_execute($stmt)){
		//$_SESSION["register_success"] = "Account successfully registered. Please check your email to activate your account.";
		$hash = md5( rand(0, 1000) ); // Generates random 32 character hash
		$insert_hash = "UPDATE users SET hash = '$hash' WHERE username = '$username'";
		mysqli_query($link, $insert_hash);	
		
		// Send email
		/*$to = "$email";
		$subject = "Activate Your Account";
		$message = "

		Thanks for signing up! Please click the link below to activate your account.
		https://ec2-34-198-69-85.compute-1.amazonaws.com/verify.php?email='.$email.'&hash='.$hash.'

		";
		$headers = "From: noreply@ec2-34-198-69-85.compute-1.amazonaws.com" . "\r\n";
		mail($to, $subject, $message, $headers);
		*/		
                // Redirect to login page
                header("location: index.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
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
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <style type="text/css">
        body{ font: 14px sans-serif; }
	.wrapper{ width: 350px; padding: 20px; }
        form label{ font-weight: bold; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="needs-validation" method="post" novalidate>
	    <div class="form-group <?php echo (!empty($first_name_err)) ? 'has-error' : ''; ?>">
                <label>First Name</label>
                <input type="text" name="first_name" class="form-control" placeholder="First Name" value="<?php echo $first_name; ?>"required>
		<div class="invalid-feedback">Please enter a name.</div>
	    </div>
            <div class="form-group <?php echo (!empty($last_name_err)) ? 'has-error' : ''; ?>">
                <label>Last Name</label>
		<input type="text" name="last_name" class="form-control" placeholder="Last Name" value="<?php echo $last_name; ?>" required>
		<div class="invalid-feedback">Please enter a name.</div>
	    </div>
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="email" name="email" class="form-control" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"  placeholder="Email" value="<?php echo $email; ?>" required>
		<div class="invalid-feedback">Please enter a valid email.</div>
		<span class="form-text" style="color:red;"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($birth_date_err)) ? 'has-error' : ''; ?>">
                <label>Date of Birth</label>
		<input type="date" name="birth_date" class="form-control" min="1900-01-01" max="<?php echo date('Y-m-d');?>"  placeholder="Date of Birth" value="<?php echo $birth_date; ?>" required>
		<div class="invalid-feedback">Please enter a valid date of birth.</div>
            </div>
	    <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
		<input type="text" name="username" class="form-control" pattern="[a-zA-z0-9]+$" placeholder="Username"value="<?php echo $username; ?>" required>
		<div class="invalid-feedback">Please enter a valid username.</div>
                <span class="form-text" style="color:red;"><?php echo $username_err; ?></span>
	    </div>   
	    <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
		<label>Password</label>
		<input type="password" name="password" id="password" class="form-control" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}" placeholder="Password" value="<?php echo $password; ?>" aria-describedby="passwordHelpBlock" required>
                <small id="passwordHelpBlock" class="form-text text-muted">Must be at least 8 characters long and contain at least 1 capital letter, 1 lowercase letter, 1 number, and 1 special character from the following: ~!@#$%^&amp;*()-_=+{}\|:;'",./&lt;&gt;?</small>
		<div class="invalid-feedback">Please enter a valid password.</div>
	    </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm" class="form-control" placeholder="Confirm Password" value="<?php echo $confirm_password; ?>" required>
		<div id="confirm_password" class="invalid-feedback">Please confirm your password.</div>
		<span class="form-text" style="color:red;"><?php echo $confirm_password_err; ?></span>
	    </div>
	    <div class="g-recaptcha" data-sitekey="6Lc0MOEZAAAAAOs8VuvUx-vmSo2FcviImt7bImx4" required></div><br/>
            <div class="invalid-feedback">Please complete the reCAPTCHA.</div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p>Already have an account? <a href="index.php"><b>Login here</b></a>.</p>
	</form>
	<script>
	    /*(function() {
		if ($('#password').val() != $('#confirm').val()) {
		    document.getElementById("confirm_password").innerHTML = "Passwords do not match";
		} else {
		    document.getElementById("confirm_password").innerHTML = "Please confirm your password.";
		}
	    }*/
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
    </div>    
</body>
</html>
