<!doctype html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Delete Device</title>
		<link href="assets/css/bootstrap.css" rel="stylesheet" />
		<link href="assets/css/main.css" rel="stylesheet" />
		<link href="assets/css/jquery.dataTables.min.css" rel="stylesheet" />
		<link href="assets/css/responsive.dataTables.min.css" rel="stylesheet" />
		<script src="assets/js/jquery-3.5.1.js"></script>
		<script src="assets/js/jquery.dataTables.min.js"></script>
		<script src="assets/js/dataTables.responsive.min.js"></script>
		<script type="text/javascript">
 			$.extend( $.fn.dataTable.defaults, {
    			responsive: true,
			});
 
			$(document).ready(function() {
    			$('#invDetails').DataTable(); 
			});
		</script>
</head>
	<body>
		<div id="page-inner">
	<?php
			$dblink = new mysqli("localhost","webuser","osuYNHPb2CU75scj","equipment");
			if ($dblink -> connect_errno) {
  				echo "Failed to connect to MySQL: " . $dblink -> connect_error;
  				exit();
			}
			$snum=$_REQUEST['did'];
			$dev=$_REQUEST['device'];
			
			$regex = '/^[0-9]+$/'; 
			if(!preg_match($regex, $snum) && !preg_match($regex, $dev)){
				header("Location: https://ec2-18-116-73-94.us-east-2.compute.amazonaws.com/index.php", true, 301);
				exit();
			}
			
			$sql = "SELECT * FROM device_type WHERE device_id = $dev";
			$result = $dblink -> query($sql) or die("Something went wrong $sql");
			$data = $result -> fetch_array(MYSQLI_ASSOC);			
			$devices=str_replace(" ","_",$data['device_type']);
									
			$sql="SELECT *
			FROM ((device_$devices as m
			INNER JOIN device_type as d 
			ON m.device_id = d.device_id)
			INNER JOIN manufacture as t
			ON m.manufacture_id = t.manufacture_id)
			WHERE m.auto_id=$snum";
			$result = $dblink -> query($sql) or die("Something went wrong $sql");
			
			$data = $result -> fetch_array(MYSQLI_ASSOC);
		
		if(!isset($_POST['submit'])){
			echo '<div id="page-inner">';
			echo '<div class="panel panel-primary">';
			echo '<div class="panel-heading">Device Info</div>';
			echo '<div class="panel-body">';
			echo '<form method="post" action="">';
			echo '<p>Maufacturer: '.$data['manufacture'].'</p>';
			echo '<p>Device Type: '.$data['device_type'].'</p>';
			echo '<p>Serial Number: '.$data['serial_num'].'</p>';
			echo '<hr>';
			echo '<button class="btn btn-primary" type="submit" name="submit" value="deleteRow">Submit</button>';
			//echo '<hr>';
			//echo '<a class="btn btn-primary" href="deleteD.php">Delete device</a>';
			echo '<hr>';
			echo '<a class="btn btn-primary" href="index.php">Return Home</a>';
			echo '</form>';
			echo '</div>';
			echo '</div>';
		}
			
		if(isset($_POST['submit']) && $_POST['submit']=="deleteRow"){
			$serialNum=str_replace(" ","_",$data['serial_num']);
			$deviceid=str_replace(" ","_",$data['device_id']);
			$manufactureid=str_replace(" ","_",$data['manufacture_id']);
			
			$sql = "INSERT INTO `deleted_rec`(`device_id`, `manufacture_id`, `serial_num`) VALUES ('$deviceid','$manufactureid','$serialNum')";
			$buff = $dblink -> query($sql) or die("Something went wrong $sql");
			$sql = "DELETE FROM `device_$devices` WHERE auto_id = $snum";
			$buff = $dblink -> query($sql) or die("Something went wrong $sql");
			echo '<div class="panel panel-primary">';
			echo '<div class="panel-heading"></div>';
  			echo '<div class="panel-body">';
			echo '<p>Maufacturer: '.$data['manufacture'].'</p>';
			echo '<p>Device Type: '.$data['device_type'].'</p>';
			echo '<p>Serial Number: '.$data['serial_num'].'</p>';
			echo '<p>Has been removed!</p>';
			//echo '<button class="btn btn-primary" type="submit" name="submit" value="deleteDevice">Submit</button>';
			echo '<hr>';
			echo '<a class="btn btn-primary" href="index.php">Return Home</a>';
			echo '</div>';
			echo '</div>';
		}
		
	?>
			</div>
</body>
</html>