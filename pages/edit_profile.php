<?php
	if(!isset($_SESSION["user"]) || empty($_SESSION["user"])){
		header("Location: /index.php");
		die();
		}else{
		$username=$_SESSION['user'];
		$message=$_SESSION['message'];
		$name=fCleanString($link, $username, 25);
	}
	
	
	// Define variables and initialize with empty values
	$email = $username = $password = $confirm_password = "";
	$email_err = $username_err = $password_err = $confirm_password_err = "";
	
	
	// Check if Authorized
	if($_SERVER["REQUEST_METHOD"] == "GET"){
		if(isset($_GET['page']) && ($_GET['page']=='edit_profile')){
			if(isset($_GET['user'])){
				$user_id=fCleanString($link, $_GET['user'], 11);
				
				if($stmt = $link->prepare("SELECT email, name FROM mentors WHERE id=? LIMIT 1")){
					
					// Set parameters
					$param_id = $user_id;
					
					// Bind variables to the prepared statement as parameters
					$stmt->bind_param("s", $param_id);
					
					
					// Attempt to execute the prepared statement
					if(mysqli_stmt_execute($stmt)){
						// Success
						
						$stmt->bind_result($returned_email, $returned_name);
						$stmt->fetch();
						
						if($returned_email!=$name && $_SESSION["isAdmin"]!="yes"){
							//Unauthorized transaction: email does not match
							include_once 'includes/pages/error_401.php';
							return;
							}
						else{
							$username=$returned_name;
							$email=$returned_email;
						}
						
						$stmt->close();
						
						
						} else{
						$stmt->close();
						echo "Something went wrong. Please try again later.";
					}
				}
				$stmt->close();
				
			}
			// If a user id is not given, default to the logged-in user
			else{
				
				if($stmt = $link->prepare("SELECT name FROM mentors WHERE email=? LIMIT 1")){
					
					// Set parameters
					$param_id = $name;
					
					// Bind variables to the prepared statement as parameters
					$stmt->bind_param("s", $param_id);
					
					
					// Attempt to execute the prepared statement
					if(mysqli_stmt_execute($stmt)){
						// Success
						$stmt->bind_result($returned_name);
						$stmt->fetch();

						$username=$returned_name;
						$email=$name;

						$stmt->close();
						
						} else{
						$stmt->close();
						echo "Something went wrong. Please try again later.";
					}
				}
				$stmt->close();
				
			}
		}
	}
	// Processing form data when form is submitted
	if($_SERVER["REQUEST_METHOD"] == "POST"){
	
		if(empty($email)){
			$email = $name;
		}
		
		// Display Name
		if(empty(trim($_POST['username']))){
			$username_err = "Please enter a display name.";
			} elseif(strlen(trim($_POST['username'])) < 2){
			$username_err = "Name must have at least 2 characters.";
			} else{
			$username = fCleanString($link, $_POST['username'], 200);
		}
		
		// Validate password
		if(empty(trim($_POST['password']))){
			$password_err = "Please enter a password.";
			} elseif(strlen(trim($_POST['password'])) < 6){
			$password_err = "Password must have at least 6 characters.";
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
		
			if($stmt = $link->prepare("UPDATE mentors SET name=?, password=? WHERE email=?")){
				
				// Set parameters
				$param_name = $username;
				$param_password = encrypt($password, $myPassword);
				$param_email = $email;
				
				// Bind variables to the prepared statement as parameters
				$stmt->bind_param("sss", $param_name, $param_password, $param_email);
					
				
				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)){
					$stmt->close();
					// Redirect to login page
					$_SESSION['message'] = "Profile Updated";
					echo '<script>window.location.href = "index.php?page=edit_profile";</script>';
					
					} else{
					$stmt->close();
					echo "Something went wrong. Please try again later.";
				}
			}
			$stmt->close();
		}
	}
	
	
?>

<div class="section-vcardbody">
	<center>
		<h1 class="profile-title">WWU <span style="color: #5d86bb;">Profile</span></h1>
		<h2 class="profile-subtitle"><?php echo $message; ?></h2>
	</center>
	
	<form action="index.php?page=edit_profile" method="post">
		<div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
			<label>Email</label>
			<input type="text" name="email"class="form-control" value="<?php echo $email; ?>" disabled>
			<span class="help-block"><?php echo $email_err; ?></span>
		</div>
		<div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
			<label>Display Name</label>
			<input type="text" name="username"class="form-control" value="<?php echo $username; ?>">
			<span class="help-block"><?php echo $username_err; ?></span>
		</div>
		<div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
			<label>New Password</label>
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
			<input type="reset" class="btn btn-default" value="Cancel">
		</div>
	</form>
</div>

<?php $_SESSION['message']=""; ?>