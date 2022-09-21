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
		$dblink = new mysqli("localhost","webuser","password","equipment");
		if ($dblink -> connect_errno) {
  			echo "Failed to connect to MySQL: " . $dblink -> connect_error;
  			exit();
		}
		$sql = "SELECT * FROM device_type";
		$result = $dblink -> query($sql) or die("Something went wrong $sql");
		$devices=array();
		while ($data = $result -> fetch_array(MYSQLI_ASSOC)){		
			
			$devices[]=str_replace(" ","_",$data['device_type']);
		}
		
		if(!isset($_POST['submit'])){
			echo '<div class="panel panel-primary">';
			echo '<div class="panel-heading"></div>';
  			echo '<div class="panel-body">';
			echo '<form method="post" action="">';
			echo '<p>Select a device type:</p>';
			echo '<div><select name = "device">';
			echo '<option value="select">select</option>';
			foreach($devices as $key=>$value){
				echo '<option value="'.$key.'">'.$value.'</option>';
			}
			echo '</select></div>';
			echo '<hr>';
			echo '<button class="btn btn-primary" type="submit" name="submit" value="deleteDevice">Submit</button>';
			echo '<hr>';
			echo '<a class="btn btn-primary" href="deleteD.php">Delete device</a>';
			echo '<hr>';
			echo '<a class="btn btn-primary" href="index.php">Return Home</a>';
			echo '</form>';
			echo '</div>';
			echo '</div>';
		}
			
		if(isset($_POST['submit']) && $_POST['submit']=="deleteDevice"){
			$device = $_POST['device'];
			$sql = "DELETE FROM `device_type` WHERE device_type = '$devices[$device]'";
			$buff = $dblink -> query($sql) or die("Something went wrong $sql");
			echo '<div class="panel panel-primary">';
			echo '<div class="panel-heading"></div>';
  			echo '<div class="panel-body">';
			echo '<p>'.$devices[$device].' has been removed!</p>';
			//echo '<button class="btn btn-primary" type="submit" name="submit" value="deleteDevice">Submit</button>';
			echo '<hr>';
			echo '<a class="btn btn-primary" href="deleteD.php">Delete another device</a>';
			echo '<hr>';
			echo '<a class="btn btn-primary" href="index.php">Return Home</a>';
			echo '</div>';
			echo '</div>';
		}
		
	?>
			</div>
</body>
</html>
