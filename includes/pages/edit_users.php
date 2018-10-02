<?php
if(!isset($_SESSION["user"]) || empty($_SESSION["user"])){
	header("Location: /index.php");
	die();
}else{
	$username=$_SESSION['user'];
	$name=fCleanString($link, $username, 25);
}

if(!isset($_SESSION["isAdmin"]) || empty($_SESSION["isAdmin"])){
	header("Location: /index.php");
	die();
}else if($_SESSION["isAdmin"]!="yes"){
	include_once 'includes/pages/error_401.php';
	return;
}







if(isset($_GET['user']) && isset($_GET['state'])){
	
	// if the page has been loaded directly
	if(!isset($link)){
		require_once('../restricted/dbConnect.php');
		$link = fConnectToDatabase();
		include_once '../restricted/encryption.php';
	}
	
	$user=fCleanString($link, $_GET['user'], 25);
	$state=fCleanString($link, $_GET['state'], 25);
	
	
	$value = "";
	if($state=="add_ban"){
		$value = "isBanned=1";
	}
	else if($state=="remove_ban"){
		$value = "isBanned=0";
	}
	else if($state=="add_mentor"){
		$value = "isMentor=1";
	}
	else if($state=="delete_user"){
		$stmt = $link->prepare("DELETE FROM mentors
								WHERE id=?");
		$stmt->bind_param("s", $user);
		$stmt->execute();
		$stmt->close();
	}
	
	if(!empty($value)){
		$stmt = $link->prepare("UPDATE mentors
								SET $value
								WHERE id=?");
		$stmt->bind_param("s", $user);
		$stmt->execute();
		$stmt->close();
	}
	
	header("Location: /wwu/index.php?page=edit_users");
}









?>

<div class="section-vcardbody" style="max-width:600px;">
	<center>
	  <h1 class="profile-title">WWU <span style="color: #5d86bb;">Users</span></h1>
	  <h2 class="profile-subtitle"><?php echo $message; ?></h2>
	</center>

	<table class="table table-striped">
		<tr>
			<th>Email</th>
			<th>Name</th>
			<th>Banned</th>
			<th>Delete</th>
			<th>Last Login</th>
		</tr>
	
		<?php
			$stmt = $link->prepare("SELECT id, email, name, isBanned, DATE_FORMAT(date_lastLogin, '%b %d, %Y') AS niceDate FROM mentors WHERE isMentor=0 AND isAdmin=0");
			$stmt->execute();
			$stmt->bind_result($id, $email, $name, $isBanned, $date_lastLogin);
			while ($stmt->fetch()) {
				echo "
				<tr>
					<td><a href=\"index.php?page=edit_profile&user=$id\">$email</a></td>
					<td><a href=\"index.php?page=edit_profile&user=$id\">$name</a></td>";
				if($isBanned){
					echo "<td><a href=\"index.php?page=edit_users&user=$id&state=remove_ban\" class=\"btn btn-warning\">Yes</a></td>";
				}
				else{
					echo "<td><a href=\"index.php?page=edit_users&user=$id&state=add_ban\" class=\"btn btn-primary\">No</a></td>";
				}
				echo
					"<td><a href=\"index.php?page=edit_users&user=$id&state=delete_user\" class=\"btn btn-danger\">X</a></td>
					<td>$date_lastLogin</td>
				</tr>
				";
			}
			$stmt->close();
		?>
		</table>
</div>