<?php
session_start();
if(!isset($_SESSION["user"]) || empty($_SESSION["user"])){
	header("Location: /wwu/index.php");
	die();
}else if(!isset($_SESSION["isAdmin"]) || empty($_SESSION["isAdmin"])){
	header("Location: /wwu/index.php");
	die();
}else if($_SESSION["isAdmin"]!="yes"){
	include_once '/wwu/includes/pages/error_401.php';
	return;
}


if(isset($_GET['user']) && isset($_GET['state'])){
	
	require_once('../restricted/dbConnect.php');
	$link = fConnectToDatabase();
	include_once '../restricted/encryption.php';
	
	$user=fCleanString($link, $_GET['user'], 25);
	$state=fCleanString($link, $_GET['state'], 25);
	
	
	$value = "";
	if($state=="add_admin"){
		$value = "isAdmin=1";
	}
	else if($state=="remove_admin"){
		$value = "isAdmin=0";
	}
	else if($state=="add_mentor"){
		$value = "isMentor=1";
	}
	else if($state=="remove_mentor"){
		$value = "isMentor=0";
	}
	
	if(!empty($value)){
		$stmt = $link->prepare("UPDATE mentors
								SET $value
								WHERE id=?");
		$stmt->bind_param("s", $user);
		$stmt->execute();
		$stmt->close();
	}
	
	header("Location: /wwu/index.php?page=edit_mentors");
}


?>

<div class="section-vcardbody" style="max-width:600px;">
	<center>
	  <h1 class="profile-title">WWU <span style="color: #5d86bb;">Mentors</span></h1>
	  <h2 class="profile-subtitle"><?php echo $message; ?></h2>
	</center>

	<table class="table table-striped">
		<tr>
			<th>Email</th>
			<th>User</th>
			<th>Mentor</th>
			<th>Admin</th>
			<th>Last Login</th>
		</tr>
	
		<?php
			$stmt = $link->prepare("
							SELECT * FROM (SELECT id, email, name, isMentor, isAdmin, DATE_FORMAT(date_lastLogin, '%b %d, %Y') AS niceDate FROM mentors WHERE isAdmin=1 ORDER BY date_lastLogin DESC) AS A
							UNION
							SELECT * FROM (SELECT id, email, name, isMentor, isAdmin, DATE_FORMAT(date_lastLogin, '%b %d, %Y') AS niceDate FROM mentors WHERE isAdmin=0 AND isMentor=1 ORDER BY date_lastLogin DESC) AS B
							UNION
							SELECT * FROM (SELECT id, email, name, isMentor, isAdmin, DATE_FORMAT(date_lastLogin, '%b %d, %Y') AS niceDate FROM mentors WHERE isAdmin=0 AND isMentor=0 ORDER BY date_lastLogin DESC) AS C
			
			");
			$stmt->execute();
			$stmt->bind_result($id, $email, $name, $isMentor, $isAdmin, $date_lastLogin);
			while ($stmt->fetch()) {
				echo "
				<tr><td><a href=\"index.php?page=edit_profile&user=$id\">$email</a></td>
				    <td><a href=\"index.php?page=edit_profile&user=$id\">$name</a></td>";
				if($isMentor==1){
					echo"<td><a href=\"/wwu/includes/pages/edit_mentors.php?user=$id&state=remove_mentor\" class=\"btn btn-success\">Yes</a></td>";
				}
				else{
					echo"<td><a href=\"/wwu/includes/pages/edit_mentors.php?user=$id&state=add_mentor\" class=\"btn btn-primary\">No</a></td>";
				}
				if($isAdmin==1){
					echo"<td><a href=\"/wwu/includes/pages/edit_mentors.php?user=$id&state=remove_admin\" class=\"btn btn-success\">Yes</a></td>";
				}
				else{
					echo"<td><a href=\"/wwu/includes/pages/edit_mentors.php?user=$id&state=add_admin\" class=\"btn btn-primary\">No</a></td>";
				}
				echo"
					<td>$date_lastLogin</td>
				</tr>
				";
			}
			$stmt->close();
		?>
		</table>
</div>