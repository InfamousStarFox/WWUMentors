<?php
session_start();
if(!isset($_SESSION["user"]) || empty($_SESSION["user"])){
	header("Location: /wwu/index.php");
	die();
}

$message = "Queing Software for CS Students";
$isDisabled=false;

if(isset($_SESSION['user'])){
	
	if(!isset($link)){
		require_once('../restricted/dbConnect.php');
		$link = fConnectToDatabase();
		include_once '../restricted/encryption.php';
	}

	$username=$_SESSION['user'];
	$email= fCleanString($link, $username, 25);

	$stmt = $link->prepare("SELECT id, class, location, question, difficulty, a.created_time, 
								(SELECT count(1)
									FROM `questions` as b 
									WHERE completed_time IS NULL 
									AND a.created_time < b.created_time) 
								as waitlist
							FROM `questions` as a
							WHERE name=? 
							AND a.completed_time IS NULL");
	$stmt->bind_param("s", $email);
	$stmt->execute();
	$stmt->bind_result($id, $class, $location, $question, $difficulty, $created_time, $waitlist);
	if($stmt->fetch()){
		$isDisabled=true;
		$message = "Question Submitted $created_time";
	}
	$stmt->close();
	
	if($isDisabled && isset($_POST["answer"])){
		$answer= fCleanString($link, $_POST["answer"], 300);
		$date = date('Y-m-d H:i:s');
		
		$stmt = $link->prepare("UPDATE questions
								SET answer=?, completed_time=?
								WHERE id=?");
		$stmt->bind_param("sss", $answer, $date, $id);
		$stmt->execute();
		$stmt->close();
		$message="Question Closed";
		$isDisabled=false;
	}
	else if (!empty($_POST["question"])) {
		$question= fCleanString($link, $_POST["question"], 300);
		if(!empty($_POST["class"])) {
			$class= fCleanString($link, $_POST["class"], 100);
		}
		if(!empty($_POST["location"])){
			$location= fCleanString($link, $_POST["location"], 100);
		}
		if(!empty($_POST["difficulty"])){
			$difficulty= fCleanString($link, $_POST["difficulty"], 1);
		}
		$date = date('Y-m-d H:i:s');

		$stmt = $link->prepare("INSERT INTO questions (name, class, location, question, difficulty, created_time)
								VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("ssssss", $email, $class, $location, $question, $difficulty, $date);
		$stmt->execute();
		$stmt->close();
		$message="Question Submitted";
		$isDisabled=true;
		
		
		$stmt = $link->prepare("SELECT count(1) AS waitlist FROM `questions` WHERE completed_time IS NULL AND created_time < ?");
		$stmt->bind_param("s", $date);
		$stmt->execute();
		$stmt->bind_result($waitlist);
		$stmt->fetch();
		$stmt->close();
		header("Location: /wwu/index.php");
	}
}
?>
<div class="section-vcardbody section-home">
    <center>
        <h1 class="profile-title">WWU <span style="color: #5d86bb;">Mentors</span></h1>
        <h2 class="profile-subtitle"><?php echo $message; ?></h2>
    </center>

    <?php if($isDisabled){
		$stmt = $link->prepare("SELECT name FROM mentors WHERE email=?");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$stmt->bind_result($studentName);
		$stmt->fetch();
		$stmt->close();
		
		echo "
				<h3 class=\"section-item-title-1\">Waitlist Position #$waitlist</h3>
				<p>
					<b>Student:</b> $studentName<br>
					<b>Class:</b> $class<br>
					<b>Location:</b> $location<br>
					<b>Question:</b> $question<br>
					<b>Difficulty:</b> $difficulty</br>
				</p>
				<form action=\"index.php\" method=\"POST\">
				<div class=\"form-group\">
					<label for=\"answer\">Answer</label>
					<input type=\"text\" class=\"form-control\" name=\"answer\">
				</div>

				<input type=\"submit\" value=\"Mark as Complete\" class=\"btn btn-primary\">
				<a href=\"index.php?page=logout\" class=\"btn btn-danger\">Sign Out</a>
				</form>";
		}
		else{
			include_once 'includes/pages/tickets_create_form.php';
		}
    ?>
</div>