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

$load_count = 5;
if(isset($_GET['c'])){
	$load_count=fCleanNumber($_GET['c']);
	if(empty($load_count)){
		$load_count=5;
	}
	else if($load_count>100){
		$load_count=100;
	}
}

$page_tab=0;
if(isset($_GET['p'])){
	$page_tab=fCleanNumber($_GET['p'])-1;
	$offset=$page_tab*$load_count;
}

?>

<div class="section-vcardbody">
	<center>
        <h1 class="profile-title">WWU <span style="color: #5d86bb;">Database</span></h1>
        <h2 class="profile-subtitle">Previously asked questions</h2>
    </center>

	<form action="/wwu/index.php" method="get">
		<input name="page" value="tickets_closed" hidden>
		<div class="form-group">
			<label for="c">Results per Page:</label>
			<select class="form-control" name="c">
				<option value="<?php echo $load_count; ?>" selected><?php echo $load_count; ?>
				<option value="5">5</option>
				<option value="10">10</option>
				<option value="25">25</option>
				<option value="50">50</option>
			</select>
		</div>
		<div class="form-group">
			<label for="class">Filter by Class:</label>
			<select class="form-control" name="class">
				<option value="" selected>*</option>
				<?php
					$stmt = $link->prepare("SELECT DISTINCT class
											FROM questions
											ORDER BY class");
					$stmt->execute();
					$stmt->bind_result($class);
					while ($stmt->fetch()) {
						echo "<option value=\"$class\">$class</option>";
					}
					$stmt->close();
				?>
			</select>
		</div>
		<input type="submit" value="Submit">
	</form>
	<hr>
	<?php
		
		
		
		
        $stmt = $link->prepare("SELECT COUNT(1) 
								FROM questions 
								WHERE completed_time IS NOT NULL");
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
	
        $stmt = $link->prepare("SELECT DATE_FORMAT(created_time, '%b %d, %Y %I:%i %p') AS niceDate, class, question, answer 
                                FROM questions 
                                WHERE completed_time IS NOT NULL
								ORDER BY created_time DESC
								LIMIT ?,?");
		$stmt->bind_param("ii", $offset, $load_count);
        $stmt->execute();
        $stmt->bind_result($asked_date, $class, $question, $answer);
        while ($stmt->fetch()) {
            echo "
					<h3 class=\"section-item-title-1\">$question</h3>
					<p><b>CSCI $class</b> $asked_date</p>
					<p>$answer</p>
					<hr>";
        }
        $stmt->close();
		
        if($count<1){
            echo "<p>There are no completed issues</p>";
        }
		else{
			$pages = 1;
			
			if($page_tab>1){
				echo "<a href=\"/wwu/index.php?page=tickets_closed&p=1&c=$load_count\" class=\"btn btn-primary\" style=\"margin:5px;\"><<<</a>";
			}
			if($page_tab>0){
				echo "<a href=\"/wwu/index.php?page=tickets_closed&p=$page_tab&c=$load_count\" class=\"btn btn-primary\" style=\"margin:5px;\">$page_tab</a>";
			}
			
			$page_tab++;
			echo "<a href=\"/wwu/index.php?page=tickets_closed&p=$page_tab&c=$load_count\" class=\"btn btn-primary active\" style=\"margin:5px;\">$page_tab</a>";
			
			$page_tab++;
			if(($offset+$load_count) < $count){
				echo "<a href=\"/wwu/index.php?page=tickets_closed&p=$page_tab&c=$load_count\" class=\"btn btn-primary\" style=\"margin:5px;\">$page_tab</a>";
			}
			
			if((2*$load_count)+$offset < $count){
				echo "<a href=\"/wwu/index.php?page=tickets_closed&p=".(ceil($count/$load_count))."&c=$load_count\" class=\"btn btn-primary\" style=\"margin:5px;\">>>></a>";
			}
		}
	?>

</div>
