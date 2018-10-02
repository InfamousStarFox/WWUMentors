<div class="section-vcardbody section-home" id="section-home">
<?php

session_start();

// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Check if email is empty
    if(empty(trim($_POST["email"]))){
        $email_err = 'Please enter your email.';
    } else{
        $email = fCleanString($link, $_POST["email"], 50);
    }

    // Check if password is empty
    if(empty(trim($_POST['password']))){
        $password_err = 'Please enter your password.';
    } else{
        $password = fCleanString($link, $_POST['password'], 200);
    }

    if($email!="" && $password!=""){
        $stmt = $link->prepare("SELECT password FROM mentors WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($storedPassword);
        $stmt->fetch();
        $stmt->close();

        $storedPasswordplain = decrypt($storedPassword, $myPassword);

        if($storedPasswordplain==$password){
            $_SESSION['user'] = $email;
            $date = date('Y-m-d H:i:s');
            
            $stmt = $link->prepare("UPDATE mentors SET date_lastLogin=? WHERE email=?");
            $stmt->bind_param("ss", $date, $email);
            $stmt->execute();
            $stmt->close();

            echo '<script>window.location.href = "/wwu/index.php";</script>';

        }
        else{ 
        $password_err= "Email or Password Incorrect";
        }
    }

    // Close connection
    mysqli_close($link);
}
?>


<center>
  <h1 class="profile-title">WWU Mentors <span style="color: #5d86bb;">Login</span></h1>
  <h2 class="profile-subtitle">Please fill in your credentials to login.</h2>
</center>

<form action="index.php" method="post">
    <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
        <label>Email</label>
        <input type="text" name="email"class="form-control" value="<?php echo $email; ?>">
        <span class="help-block"><?php echo $email_err; ?></span>
    </div>
    <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
        <label>Password</label>
        <input type="password" name="password" class="form-control">
        <span class="help-block"><?php echo $password_err; ?></span>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Login">
    </div>
    <p>Don't have an account? <a href="index.php?page=register">Sign up now</a>.</p>
</form>
</div>