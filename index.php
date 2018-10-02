<?php session_start();

if(isset($_GET['page']) && ($_GET['page']=='logout')){
	session_destroy(); // Destroy the session.
	session_start(); // Initialize a new session
}
require_once('includes/restricted/dbConnect.php');
$link = fConnectToDatabase();
include_once 'includes/restricted/encryption.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>WWU - Mentors</title>

	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
	<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
	<link href="/wwu/styles.css" rel="stylesheet">
	
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.js"></script>
	
</head>
<body>

    <?php
		if(isset($_SESSION['user'])){
            $username=$_SESSION['user'];

            /* create a prepared statement */
            if ($stmt = $link->prepare("SELECT isMentor, isAdmin FROM mentors WHERE name=?")) {

                /* bind parameters for markers */
                $stmt->bind_param("s", $username);

                /* execute query */
                $stmt->execute();

                /* bind result variables */
                $stmt->bind_result($isMentor, $isAdmin);

                /* fetch value */
                $stmt->fetch();
                
                /* close statement */
                $stmt->close();
                
				
				$_SESSION['isMentor'] = "no";
				$_SESSION['isAdmin'] = "no";
				if($isMentor==1){
					$_SESSION['isMentor'] = "yes";
				}
				if($isAdmin==1){
					$_SESSION['isAdmin'] = "yes";
				}
				
                include_once 'includes/navbar.php';
				
				if(isset($_GET['page']) && ($_GET['page']=='tickets_create')){
					include_once 'includes/pages/tickets_create.php';
				}
				else if(isset($_GET['page']) && ($_GET['page']=='tickets_opened')){
					include_once 'includes/pages/tickets_opened.php';
				}
				else if(isset($_GET['page']) && ($_GET['page']=='tickets_closed')){
					include_once 'includes/pages/tickets_closed.php';
				}
				else if(isset($_GET['page']) && ($_GET['page']=='view_analytics')){
					include_once 'includes/pages/view_analytics.php';
				}
				else if(isset($_GET['page']) && ($_GET['page']=='edit_users')){
					include_once 'includes/pages/edit_users.php';
				}
				else if(isset($_GET['page']) && ($_GET['page']=='edit_mentors')){
					include_once 'includes/pages/edit_mentors.php';
				}
				else if(isset($_GET['page']) && ($_GET['page']=='edit_admins')){
					include_once 'includes/pages/edit_admins.php';
				}
				else if(isset($_GET['page']) && ($_GET['page']=='edit_profile')){
					include_once 'includes/pages/edit_profile.php';
				}
				
				/* If a page is not selected, default to the mentor or user page */
                else if($isMentor==1) include_once 'includes/pages/tickets_opened.php';
                else include_once 'includes/pages/tickets_create.php';
				
				
            }
            
        }
        else if(isset($_GET['page']) && ($_GET['page']=='register')){
            include_once 'includes/login/register.php';
        }
        else { 
            include_once 'includes/login/login.php';
        }
     ?>
	
<div class="background-image"></div>
</body>
</html>