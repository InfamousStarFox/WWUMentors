<?php

if(!isset($_SESSION["user"]) || empty($_SESSION["user"])){
	header("Location: /index.php");
	die();
}

if(!isset($_SESSION["isMentor"]) || empty($_SESSION["isMentor"]) || !isset($_SESSION["isAdmin"]) || empty($_SESSION["isAdmin"])){
	header("Location: /index.php");
	die();
}else if($_SESSION["isMentor"]!="yes" && $_SESSION["isAdmin"]!="yes"){
	include_once 'includes/pages/error_401.php';
	return;
}


$username=$_SESSION['user'];
$name= fCleanString($link, $username, 25);

$stmt = $link->prepare("SELECT id FROM `mentors` WHERE name=?");
$stmt->bind_param("s", $name);
$stmt->execute();
$stmt->bind_result($id);
$stmt->fetch();
$stmt->close();


$stmt = $link->prepare("SELECT count(1) AS waitlist FROM `questions` WHERE completed_time IS NULL AND assigned_time IS NULL AND mentor_id IS NULL");
$stmt->execute();
$stmt->bind_result($waitlist);
$stmt->fetch();
$stmt->close();
if($waitlist) $message = "Select an open issue from the database to get started<br><br>There are $waitlist unassigned/open issues.";
else $message = "Congratulations! There are zero open issues.";


$date = date('Y-m-d H:i:s');
if(!empty($_POST["completeID"])){
	$completeID= fCleanNumber($_POST["completeID"]);
	if(!empty($_POST["answer"])){ $answer= fCleanString($link, $_POST["answer"], 300); }
	
	$stmt = $link->prepare("UPDATE questions SET answer=?, completed_time=? WHERE id=?");
	$stmt->bind_param("sss", $answer, $date, $completeID);
	$stmt->execute();
	$stmt->close();
	$message="Question Closed";
}
else if(!empty($_POST["helpID"])){
	$helpID= fCleanNumber($_POST["helpID"]);
	$disabled="disabled";

	$stmt = $link->prepare("UPDATE questions SET assigned_time=?, mentor_id=? WHERE id=?");
	$stmt->bind_param("sss", $date, $id, $helpID);
	$stmt->execute();
	$stmt->close();
}
else if(!empty($_POST["cancelID"])){
	$cancelID= fCleanNumber($_POST["cancelID"]);
	$stmt = $link->prepare("UPDATE questions SET mentor_id=NULL, assigned_time=NULL WHERE id=?");
	$stmt->bind_param("s", $cancelID);
	$stmt->execute();
	$stmt->close();
	$message="Help Canceled";
}

$stmt = $link->prepare("SELECT questions.id, mentors.name, questions.class, questions.location, questions.question, questions.difficulty 
						FROM questions, mentors
						WHERE mentors.email=questions.name
						AND questions.mentor_id=? 
						AND questions.completed_time IS NULL");





$stmt->bind_param("s", $id);
$stmt->execute();
$stmt->bind_result($helpID, $name, $class, $location, $question, $difficulty, $created_time);
if($stmt->fetch()){
	$message="Open Issue #$helpID";
}
$stmt->close();

?>

<div class="section-vcardbody">
    <center>
        <h1 class="profile-title">WWU <span style="color: #5d86bb;">Mentors</span></h1>
        <h2 class="profile-subtitle"><?php echo $message; ?></h2>
    </center>
        <?php if($helpID){
            echo "
					<p>
                        <b>Student:</b> $name<br>
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

                    <button type=\"submit\" name=\"completeID\" value=\"$helpID\" class=\"btn btn-success\">Mark as Complete</button>
                    <button type=\"submit\" name=\"cancelID\" value=\"$helpID\" class=\"btn btn-warning\">Cancel Help</button>
                    </form>";
            }
			
			else{
				$count=0;
				$stmt = $link->prepare("SELECT questions.id, mentors.name, questions.class, questions.location, questions.question, questions.difficulty 
										FROM questions, mentors
										WHERE mentors.email=questions.name
										AND questions.completed_time IS NULL
										AND questions.assigned_time IS NULL");
				$stmt->execute();
				$stmt->bind_result($id, $name, $class, $location, $question, $difficulty);
				while ($stmt->fetch()) {
					$count++;
					echo "
							<h3 class=\"section-item-title-1\">Open Issue #$id</h3>
							<p>
								<b>Student</b> $name<br>
								<b>Class</b> $class<br>
								<b>Location</b> $location<br>
								<b>Question</b> $question<br>
								<b>Difficulty</b> $difficulty</br>
							</p>
							<form action=\"index.php\" method=\"POST\">
								<button type=\"submit\" name=\"helpID\" value=\"$id\" class=\"btn btn-success\" $disabled>Start Help</button>
							</form>";
				}
				$stmt->close();
			}
    ?>
</div>