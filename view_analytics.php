<?php
if(!isset($_SESSION["user"]) || empty($_SESSION["user"])){
	header("Location: /index.php");
	die();
}else{
	$username=$_SESSION['user'];
	$name=fCleanString($link, $username, 25);
}

if(!isset($_SESSION["isMentor"]) || empty($_SESSION["isMentor"]) || !isset($_SESSION["isAdmin"]) || empty($_SESSION["isAdmin"])){
	header("Location: /index.php");
	die();
}else if($_SESSION["isMentor"]!="yes" && $_SESSION["isAdmin"]!="yes"){
	include_once 'includes/pages/error_401.php';
	return;
}
?>

<div class="section-vcardbody section-home">
    <center>
        <h1 class="profile-title">WWU <span style="color: #5d86bb;">Analytics</span></h1>
        <h2 class="profile-subtitle">Analytics for the last 100 days</h2>
    </center>

	<div id="canvas-holder"><canvas id="area0"></canvas></div>
	<div id="canvas-holder"><canvas id="area1"></canvas></div>
	<div id="canvas-holder"><canvas id="area2"></canvas></div>
	<div id="canvas-holder"><canvas id="area3"></canvas></div>
	
	<?php
        $labels="[";
        $data="[";

        $stmt = $link->prepare("SELECT COALESCE(count, 0) AS count, DATE_FORMAT(a.Date, '%M %d %Y') AS day
                                FROM(SELECT curdate() - INTERVAL (a.a + (10 * b.a)) DAY AS Date
                                FROM
                                    (SELECT 0 AS a
                                    UNION ALL SELECT 1
                                    UNION ALL SELECT 2
                                    UNION ALL SELECT 3
                                    UNION ALL SELECT 4
                                    UNION ALL SELECT 5
                                    UNION ALL SELECT 6
                                    UNION ALL SELECT 7
                                    UNION ALL SELECT 8
                                    UNION ALL SELECT 9) AS a CROSS
                                JOIN
                                    (SELECT 0 AS a
                                    UNION ALL SELECT 1
                                    UNION ALL SELECT 2
                                    UNION ALL SELECT 3
                                    UNION ALL SELECT 4
                                    UNION ALL SELECT 5
                                    UNION ALL SELECT 6
                                    UNION ALL SELECT 7
                                    UNION ALL SELECT 8
                                    UNION ALL SELECT 9) AS b) a
                                LEFT JOIN(
                                    SELECT count(1) AS count, cast(created_time as date) AS date
                                    FROM `questions` 
                                    WHERE created_time >= Date_add(Now(),interval - 12 month)
                                    GROUP BY date
                                ) AS info ON a.Date=info.date
                                ORDER BY a.Date DESC");
        $stmt->execute();
        $stmt->bind_result($count, $day);
        while ($stmt->fetch()) {
            $labels.="'$day', ";
            $data.="$count, ";
        }
        $stmt->close();

        $labels=substr($labels, 0, -2);
        $labels.="]";
        $data=substr($data, 0, -2);
        $data.="]";
	?>
	<script>
		var ctx0 = document.getElementById('area0').getContext('2d');
		var area0 = new Chart(ctx0, {
			type: 'line',
			data: {
				labels: <?php echo $labels; ?>,
				datasets: [{
					label: 'questions asked',
					data: <?php echo $data; ?>,
					backgroundColor: "rgba(153,255,51,0.6)"
				}]
			},
			options: {
				scales: {
					xAxes: [{
                        type: 'time',
                        time: {
                            displayFormats: {
                                'millisecond': 'MMM DD',
                                'second': 'MMM DD',
                                'minute': 'MMM DD',
                                'hour': 'MMM DD',
                                'day': 'MMM DD',
                                'week': 'MMM DD',
                                'month': 'MMM DD',
                                'quarter': 'MMM DD',
                                'year': 'MMM DD',
                            }
                        },
                        distribution: 'series',
						ticks: {
							source: 'labels',
							autoSkip: true,
						}
                    }],
					yAxes: [{
						scaleLabel: {
							display: true,
							labelString: 'questions (#)'
						}
					}]
				},
				legend: {
						position: 'bottom',
						},
						responsive: true,
						title: {
								display: true,
								text: 'Questions asked'
						}
			}
		});
	</script>

	<?php
        $labels="[";
        $data="[";

        $stmt = $link->prepare("SELECT COALESCE(avgTime, 0) AS avgTime, DATE_FORMAT(a.Date, '%M %d %Y') AS day
                                FROM(SELECT curdate() - INTERVAL (a.a + (10 * b.a)) DAY AS Date
                                FROM
                                    (SELECT 0 AS a
                                    UNION ALL SELECT 1
                                    UNION ALL SELECT 2
                                    UNION ALL SELECT 3
                                    UNION ALL SELECT 4
                                    UNION ALL SELECT 5
                                    UNION ALL SELECT 6
                                    UNION ALL SELECT 7
                                    UNION ALL SELECT 8
                                    UNION ALL SELECT 9) AS a CROSS
                                JOIN
                                    (SELECT 0 AS a
                                    UNION ALL SELECT 1
                                    UNION ALL SELECT 2
                                    UNION ALL SELECT 3
                                    UNION ALL SELECT 4
                                    UNION ALL SELECT 5
                                    UNION ALL SELECT 6
                                    UNION ALL SELECT 7
                                    UNION ALL SELECT 8
                                    UNION ALL SELECT 9) AS b) a
                                LEFT JOIN(
                                    SELECT AVG(TIMESTAMPDIFF(MINUTE, created_time, completed_time)) AS avgTime, cast(created_time as date) AS date
                                    FROM `questions` 
                                    WHERE created_time >= Date_add(Now(),interval - 12 month)
                                    GROUP BY date
                                ) AS info ON a.Date=info.date
                                ORDER BY a.Date DESC");
        $stmt->execute();
        $stmt->bind_result($avgTime, $day);
        while ($stmt->fetch()) {
            $labels.="'$day', ";
            $data.="$avgTime, ";
        }
        $stmt->close();

        $labels=substr($labels, 0, -2);
        $labels.="]";
        $data=substr($data, 0, -2);
        $data.="]";
	?>
	<script>
		var ctx1 = document.getElementById('area1').getContext('2d');
		var area1 = new Chart(ctx1, {
			type: 'line',
			data: {
				labels: <?php echo $labels; ?>,
				datasets: [{
					label: 'minutes',
					data: <?php echo $data; ?>,
					backgroundColor: "rgba(255,153,51,0.6)"
				}]
			},
			options: {
				scales: {
					xAxes: [{
                        type: 'time',
                        time: {
                            displayFormats: {
                                'millisecond': 'MMM DD',
                                'second': 'MMM DD',
                                'minute': 'MMM DD',
                                'hour': 'MMM DD',
                                'day': 'MMM DD',
                                'week': 'MMM DD',
                                'month': 'MMM DD',
                                'quarter': 'MMM DD',
                                'year': 'MMM DD',
                            }
                        },
                        distribution: 'series',
						ticks: {
							source: 'labels',
							autoSkip: true,
						}
                    }],
					yAxes: [{
						scaleLabel: {
							display: true,
							labelString: 'time (min)'
						}
					}]
				},
				legend: {
					position: 'bottom',
					},
					responsive: true,
					title: {
						display: true,
						text: 'Avg time to complete questions (min)'
					}
			}
		});
	</script>
	
	
	<?php
        $labels="[";
        $data="[";

        $stmt = $link->prepare("SELECT COALESCE(count, 0) AS count, DATE_FORMAT(a.Date, '%M %d %Y') AS day
                                FROM(SELECT curdate() - INTERVAL (a.a + (10 * b.a)) DAY AS Date
                                FROM
                                    (SELECT 0 AS a
                                    UNION ALL SELECT 1
                                    UNION ALL SELECT 2
                                    UNION ALL SELECT 3
                                    UNION ALL SELECT 4
                                    UNION ALL SELECT 5
                                    UNION ALL SELECT 6
                                    UNION ALL SELECT 7
                                    UNION ALL SELECT 8
                                    UNION ALL SELECT 9) AS a CROSS
                                JOIN
                                    (SELECT 0 AS a
                                    UNION ALL SELECT 1
                                    UNION ALL SELECT 2
                                    UNION ALL SELECT 3
                                    UNION ALL SELECT 4
                                    UNION ALL SELECT 5
                                    UNION ALL SELECT 6
                                    UNION ALL SELECT 7
                                    UNION ALL SELECT 8
                                    UNION ALL SELECT 9) AS b) a
                                LEFT JOIN(
                                    SELECT count(1) AS count, cast(created_time as date) AS date
                                    FROM `questions` 
                                    WHERE created_time >= Date_add(Now(),interval - 12 month)
                                    AND mentor_id=(SELECT mentor_id FROM mentors WHERE name=?)
                                    GROUP BY date
                                ) AS info ON a.Date=info.date
                                ORDER BY a.Date DESC");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->bind_result($count, $day);
        while ($stmt->fetch()) {
            $labels.="'$day', ";
            $data.="$count, ";
        }
        $stmt->close();

		$labels=substr($labels, 0, -2);
		$labels.="]";
		$data=substr($data, 0, -2);
		$data.="]";
	?>

	<script>
		var ctx2 = document.getElementById('area2').getContext('2d');
		var area2 = new Chart(ctx2, {
			type: 'line',
			data: {
				labels: <?php echo $labels; ?>,
				datasets: [{
					label: 'questions answered',
					data: <?php echo $data; ?>,
					backgroundColor: "rgba(51,153,255,0.6)"
				}]
			},
			options: {
				scales: {
					xAxes: [{
                        type: 'time',
                        time: {
                            displayFormats: {
                                'millisecond': 'MMM DD',
                                'second': 'MMM DD',
                                'minute': 'MMM DD',
                                'hour': 'MMM DD',
                                'day': 'MMM DD',
                                'week': 'MMM DD',
                                'month': 'MMM DD',
                                'quarter': 'MMM DD',
                                'year': 'MMM DD',
                            }
                        },
                        distribution: 'series',
						ticks: {
							source: 'labels',
							autoSkip: true,
						}
                    }],
					yAxes: [{
						scaleLabel: {
							display: true,
							labelString: 'questions (#)'
						},
					}]
				},
				legend: {
					position: 'bottom',
					},
					responsive: true,
					title: {
						display: true,
						text: 'Questions answered by <?php echo $name; ?>'
					}
			}
		});
	</script>
	
    <?php
        $labels="[";
        $data="[";

        $stmt = $link->prepare("SELECT count(1) AS count, class 
                                FROM `questions` 
                                GROUP BY class
                                ORDER BY count DESC
                                LIMIT 7");
        $stmt->execute();
        $stmt->bind_result($count, $class);
        while ($stmt->fetch()) {
            $labels.="'$class', ";
            $data.="$count, ";
        }
        $stmt->close();
            
		$labels=substr($labels, 0, -2);
		$labels.="]";
		$data=substr($data, 0, -2);
		$data.="]";
	?>
	<script>
		var ctx3 = document.getElementById('area3').getContext('2d');
		var area3 = new Chart(ctx3, {
			type: 'pie',
			data: {
				labels: <?php echo $labels; ?>,
				datasets: [{
					data: <?php echo $data; ?>,
					backgroundColor: [
						"#2ecc71",
						"#3498db",
						"#95a5a6",
						"#9b59b6",
						"#f1c40f",
						"#e74c3c",
						"#34495e"
					  ]
				}]
			},
			options: {
				legend: {
					position: 'bottom',
					},
					responsive: true,
					title: {
						display: true,
						text: 'Questions asked by class'
					}
			}
		});
	</script>
</div>
