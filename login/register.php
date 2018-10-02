<div class="section-vcardbody">
<?php

// Define variables and initialize with empty values
$email = $username = $password = $confirm_password = "";
$email_err = $username_err = $password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email address.";
    } else{
        // Prepare a select statement
        if($stmt = $link->prepare("SELECT email FROM mentors WHERE email = ?")){
        
            $param_email = trim($_POST["email"]);
            $stmt->bind_param("s", $param_email);

            // Attempt to execute the prepared statement
            if($stmt->execute()){

                $stmt->store_result();
                if($stmt->num_rows() == 1){
                    $email_err = "This email is already taken.";
                }
                else{
                    $email = fCleanString($link, $_POST["email"], 50);
                }
            }
            else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        $stmt->close();
    }
	
	// Validate username
    if(empty(trim($_POST['username']))){
        $username_err = "Please enter a username.";
    } elseif(strlen(trim($_POST['username'])) < 2){
        $password_err = "Display name must have atleast 2 characters.";
    } else{
        $username = fCleanString($link, $_POST['username'], 200);
    }

    // Validate password
    if(empty(trim($_POST['password']))){
        $password_err = "Please enter a password.";
    } elseif(strlen(trim($_POST['password'])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = fCleanString($link, $_POST['password'], 200);
    }

    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = 'Please confirm password.';
    } else{
        $confirm_password = fCleanString($link, $_POST["confirm_password"], 50);
        if($password != $confirm_password){
            $confirm_password_err = 'Password did not match.';
        }
    }

    // Check input errors before inserting in database
    if(empty($email_err) && empty($password_err) && empty($confirm_password_err)){
		
        if($stmt = $link->prepare("INSERT INTO mentors (email, name, password, isBanned, isMentor, isAdmin, date_created) VALUES (?, ?, ?, 0, 0, 0, ?)")){
            
            // Set parameters
            $param_email = $email;
			$param_name = $username;
            $param_password = encrypt($password, $myPassword);
            $dt = date('Y-m-d H:i:s');

            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssss", $param_email, $param_name, $param_password, $dt);

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $stmt->close();
                // Redirect to login page
                echo '<script>window.location.href = "index.php";</script>';

            } else{
                $stmt->close();
                echo "Something went wrong. Please try again later.";
            }
        }
        $stmt->close();
    }
}
?>

<h2>Sign Up</h2>
<p>Please fill this form to create an account.</p>
<form action="index.php?page=register" method="post">
    <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
        <label>Email</label>
        <input type="text" name="email"class="form-control" value="<?php echo $email; ?>">
        <span class="help-block"><?php echo $email_err; ?></span>
    </div>
	<div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
        <label>Display Name</label>
        <input type="text" name="username"class="form-control" value="<?php echo $username; ?>">
        <span class="help-block"><?php echo $username_err; ?></span>
    </div>
    <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
        <label>Password</label>
        <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
        <span class="help-block"><?php echo $password_err; ?></span>
    </div>
    <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
        <span class="help-block"><?php echo $confirm_password_err; ?></span>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Submit">
        <input type="reset" class="btn btn-default" value="Reset">
    </div>
    <p>Already have an account? <a href="index.php">Login here</a>.</p>
</form>
</div>