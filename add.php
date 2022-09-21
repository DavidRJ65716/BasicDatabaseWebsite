<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Add Device</title>
		<link href="assets/css/bootstrap.css" rel="stylesheet" />
		<link href="assets/css/main.css" rel="stylesheet" />
		<link href="assets/css/basic.css" rel="stylesheet" />
		<link href="assets/css/bootstrap-fileupload.min.css" rel="stylesheet" />
		<script src="assets/js/jquery-3.5.1.js"></script>
		<script src="assets/js/bootstrap.js"></script>
		<script src="assets/js/bootstrap-fileupload.js"></script>
</head>
	<body>
		<div id="page-inner">
	<?php
		$dblink = new mysqli("localhost","webuser","password","equipment");
		if ($dblink -> connect_errno) {
  			echo "Failed to connect to MySQL: " . $dblink -> connect_error;
  			exit();
		}
	
		if(!isset($_POST['submit'])){
			echo '<div class="panel panel-primary">';
			echo '<div class="panel-heading"></div>';
  			echo '<div class="panel-body">';
			echo '<form method="post" action="">';
			echo '<p>New Device Name:<input type="text" name="device_name"></p>';
			echo '<p>Only letters and Numbers</p>';
			echo '<hr>';
			echo '<div><button class="btn btn-primary" type="submit" name="submit" value="lookup">Submit</button></div>';
			echo '<hr>';
			echo '<a class="btn btn-primary" href="index.php">Return Home</a>';
			echo '</form>';
			echo '</div>';
			echo '</div>';
		}
		
		$regex = '/^[A-Za-z0-9\s]+$/';
		
		if(isset($_POST['submit']) && $_POST['submit']=="lookup"){
					
			$tmp = $_POST['device_name'];
			
			if(!preg_match($regex, $tmp)){
				header("Location: https://ec2-18-116-73-94.us-east-2.compute.amazonaws.com/add.php", true, 301);
				exit();
			}
			
			$device=str_replace(" ","_",$tmp);
			
			$sql = "DESCRIBE `device_$device`";
			
			if($dblink -> query($sql)){
				
				$sql = "SELECT * FROM `device_type` WHERE device_type = '$device'";
				$buff = $dblink -> query($sql) or die("Something went wrong $sql");
				if ($buff->num_rows != 0){
					
					echo '<div class="panel panel-primary">';
					echo '<div class="panel-heading"></div>';
					echo '<div class="panel-body">';
					echo '<form method="post" action="">';
					echo '<p>'.$device.' already exist!</p>';
					echo '<hr>';
					echo '<a class="btn btn-primary" href="add.php">Add Another</a>';
					echo '<hr>';
					echo '<a class="btn btn-primary" href="index.php">Return Home</a>';
					echo '</form>';
					echo '</div>';
					echo '</div>';
				} else {
					
					$sql = "SELECT * FROM `device_$device` limit 1";
					$result = $dblink -> query($sql) or die("Something went wrong $sql");
					if ($buff->num_rows != 0){
						$data = $result -> fetch_array(MYSQLI_ASSOC);
						$deviceNum = $data['device_id'];
					
						$sql = "INSERT INTO device_type (`auto_id`,`device_type`) VALUES ('$deviceNum','$device')";
						$dblink -> query($sql) or die("Something went wrong $sql");
					} else {
						
						$sql = "INSERT INTO device_type (`device_type`) VALUES ('$device')";
						$dblink -> query($sql) or die("Something went wrong $sql");
					}
					
					echo '<div class="panel panel-primary">';
					echo '<div class="panel-heading"></div>';
					echo '<div class="panel-body">';
					echo '<form method="post" action="">';
					echo '<p>'.$device.' has been added to the database</p>';
					echo '<hr>';
					echo '<a class="btn btn-primary" href="add.php">Add Another</a>';
					echo '<hr>';
					echo '<a class="btn btn-primary" href="index.php">Return Home</a>';
					echo '</form>';
					echo '</div>';
					echo '</div>';
				}
			}else {
				$sql = "INSERT INTO device_type (`device_type`) VALUES ('$device')";
				$dblink -> query($sql) or die("Something went wrong $sql");
						
				$sql = "CREATE TABLE `device_$device` (auto_id INT(6) AUTO_INCREMENT PRIMARY KEY, device_id INT NOT NULL, manufacture_id INT NOT NULL, serial_num VARCHAR(64) NOT NULL, `active` BOOLEAN NOT NULL, INDEX (device_id, manufacture_id));";
				$dblink -> query($sql) or die("Something went wrong $sql");
			
				$sql = "ALTER TABLE `device_$device` ADD FOREIGN KEY (`device_id`) REFERENCES `device_type`(`device_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;";
				$dblink -> query($sql) or die("Something went wrong $sql");
				$sql = "ALTER TABLE `device_$device` ADD FOREIGN KEY (`manufacture_id`) REFERENCES `manufacture`(`manufacture_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;";
				$dblink -> query($sql) or die("Something went wrong $sql");
			
				echo '<div class="panel panel-primary">';
				echo '<div class="panel-heading"></div>';
				echo '<div class="panel-body">';
				echo '<form method="post" action="">';
				echo '<p>'.$device.' has been added to the database</p>';
				echo '<hr>';
				echo '<a class="btn btn-primary" href="add.php">Add Another</a>';
				echo '<hr>';
				echo '<a class="btn btn-primary" href="index.php">Return Home</a>';
				echo '</form>';
				echo '</div>';
				echo '</div>';
			}
		}
		
	?>
			</div>
	</body>
</html>
