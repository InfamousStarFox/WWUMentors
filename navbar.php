<?php
session_start();
if(!isset($_SESSION["user"]) || empty($_SESSION["user"])){
	header("Location: /index.php");
	die();
}

$isMentor=0;
$isAdmin=0;
if($_SESSION['isMentor'] == "yes"){
	$isMentor=1;
}
if($_SESSION['isAdmin'] == "yes"){
	$isAdmin=1;
}


?>

<nav class="navbar navbar-default navbar-fixed-top">
	<button type="button" class="navbar-toggle" onclick="openNav()" aria-expanded="false" aria-controls="navbar">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	</button>
	<div class="container">
		<div id="navbar" class="sidenav">
		  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">x</a>
			<h2>Questions</h2>
			<a href="index.php?page=tickets_create">Create New</a>
			<?php if($isMentor==1){ ?>
			<a href="index.php?page=tickets_opened">View Open</a>
			<a href="index.php?page=tickets_closed">View Closed</a>
			<h2>Analytics</h2>
			<a href="index.php?page=view_analytics">View Analytics</a>
			<?php } ?>
			<?php if($isAdmin==1){ ?>
			<h2>Manage Users</h2>
			<a href="index.php?page=edit_users">Edit Users</a>
			<a href="index.php?page=edit_mentors">Edit Mentors</a>
			<?php } ?>
			<h2>Settings</h2>
			<a href="index.php?page=edit_profile">Edit Profile</a>
			<a href="index.php?page=logout">Logout</a>
		</div>
		<script>
		  function openNav() {
			document.getElementById("navbar").style.width = "250px";
		  }
		  function closeNav() {
			document.getElementById("navbar").style.width = "0";
		  }
		</script>
	</div>
</nav>