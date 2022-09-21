<!doctype html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Modify Row</title>
		<link href="assets/css/bootstrap.css" rel="stylesheet" />
		<link href="assets/css/main.css" rel="stylesheet" />
		<link href="assets/css/jquery.dataTables.min.css" rel="stylesheet" />
		<link href="assets/css/responsive.dataTables.min.css" rel="stylesheet" />
		<script src="assets/js/jquery-3.5.1.js"></script>
		<script src="assets/js/jquery.dataTables.min.js"></script>
		<script src="assets/js/dataTables.responsive.min.js"><script>
				
				function myFunction() {
  				
					var checkBox = document.getElementById("myCheck");
  					var text = document.getElementById("text");
  					if (checkBox.checked == true){
    					checkBox.value = 1;
  					} else {
     					checkBox.value = 0;
  					}
				}
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
			$dev=$_REQUEST['deviceOld'];
			
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
			
			$dataOld = $result -> fetch_array(MYSQLI_ASSOC);
			
			$sql = "SELECT * FROM device_type";
			$result = $dblink -> query($sql) or die("Something went wrong $sql");
			$devices=array();
			$deviceId=array();
			while ($data = $result -> fetch_array(MYSQLI_ASSOC)){		
			
				$devices[]=str_replace(" ","_",$data['device_type']);
				$deviceId[]=str_replace(" ","_",$data['device_id']);
			}
		
			$sql = "SELECT * FROM manufacture";
			$result = $dblink -> query($sql);
			$manufacture=array();
			$manufactureid=array();
			while ($data = $result -> fetch_array(MYSQLI_ASSOC)){		
			
				$manufacture[]=$data['manufacture'];	
				$manufactureid[]=$data['manufacture_id'];
			}
			
			if(!isset($_POST['submit'])){
				
				echo '<div id="page-inner">';
				echo '<div class="panel panel-primary">';
				echo '<div class="panel-heading">Device Original Info</div>';
				echo '<div class="panel-body">';
				echo '<p>Device Type: '.$dataOld['device_type'].'</p>';
				echo '<p>Maufacturer: '.$dataOld['manufacture'].'</p>';
				echo '<p>Serial Number: '.$dataOld['serial_num'].'</p>';
				echo '</div>';
				echo '</div>';
				echo '<div id="page-inner">';
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
				echo '<p>Select a Manufacture:</p>';
				echo '<div><select name = "manufacture">';
				echo '<option value="select">select</option>';
				foreach($manufacture as $key=>$value) {
					echo '<option value="'.$key.'">'.$value.'</option>';
				}
				echo '</select></div>';
				echo '<hr>';
				echo '<p>Type Serial Number:</p><br>';
				echo '<p>SN-<input type="text" name="serial_num"></p>';
				echo '<hr>';
				echo '<label for="myCheck">Activate:</label>';
				echo '<input type="checkbox" id="myCheck" name="myCheck" onclick="myFunction()">';
				//echo '<div class="form-check">';
  				//echo '<input class="form-check-input" type="checkbox" id="check1" name="option1" value=1>';
  				//echo '<label class="form-check-label">Active/Inactive</label></div>';
				echo '<hr>';
				echo '<button class="btn btn-primary" type="submit" name="submit" value="ModRow">Submit</button>';
				echo '<hr>';
				echo '<a class="btn btn-primary" href="index.php">Return Home</a>';
				echo '</form>';
				echo '</div>';
				echo '</div>';
				echo '</div>';
			}
			
			$regex = '/^[A-Za-z0-9]{32}$/';
		
			if(isset($_POST['submit']) && $_POST['submit']=="ModRow"){
				$oldDT=str_replace(" ","_",$dataOld['device_type']);
				$oldAId=str_replace(" ","_",$dataOld['auto_id']);
				$oldMId=str_replace(" ","_",$dataOld['manufacture_id']);
				$oldSn=str_replace(" ","_",$dataOld['serial_num']);
				
				echo '<div class="panel panel-primary">';
				echo '<div class="panel-heading">New Device Info!</div>';
				echo '<div class="panel-body">';
				$device = $_POST['device'];
				$manuf = $_POST['manufacture'];
				$snum = $_POST['serial_num'];
				$activ = $_POST['myCheck'];
				if(!strcmp("select",$device)){
					echo '<p>Device Type: '.$dataOld['device_type'].'</p>';
				} else {
					echo '<p>Device chosen is: '.$devices[$device].'.</p>';
				}
				
				if(!strcmp("select",$manuf)){
					echo '<p>Maufacturer: '.$dataOld['manufacture'].'</p>';
				} else {
					echo '<p>Manufacture chosen is: '.$manufacture[$manuf].'.</p>';
				}
				
				if (!preg_match($regex, $snum)){
					echo '<p>Serial Number: '.$dataOld['serial_num'].'</p>';
				} else {
					echo '<p>Serial Number chosen is: SN-'.$snum.'.</p>';
				}
				if($activ == true){
					echo '<p>Device is active: Active.</p>';
					$activ = 1;
				} else {
					echo '<p>Device is active: Inactive.</p>';
					$activ = 0;
				}
				echo '<p>Update Successful!</p>';
				echo '<form method="post" action="">';
				echo '<a class="btn btn-primary" href="index.php">Return Home</a>';
				echo '</form>';
				echo '</div>';
				echo '</div>';
							
				//device updated
				if(strcmp("select",$device) && !strcmp("select",$manuf) && !preg_match($regex, $snum)){
						
					$sql ="INSERT INTO `device_$devices[$device]`
					(`device_id`, `manufacture_id`, `serial_num`, `active`) 
					VALUES ('$deviceId[$device]','$oldMId','$oldSn',$activ)";
					
					$dblink -> query($sql) or die("Something went wrong $sql");
					$sql = "DELETE FROM `device_$devices[$device]` WHERE auto_id = $oldAId";
					$dblink -> query($sql) or die("Something went wrong $sql");

				}
			
				//device and manufacturer updated
				if(strcmp("select",$device) && strcmp("select",$manuf) && !preg_match($regex, $snum)){
	
					$sql ="INSERT INTO `device_$devices[$device]`
					(`device_id`, `manufacture_id`, `serial_num`, `active`) 
					VALUES ('$deviceId[$device]','$manufactureid[$manuf]','$oldSn',$activ)";
					
					$dblink -> query($sql) or die("Something went wrong $sql");
					$sql = "DELETE FROM `device_$devices[$device]` WHERE auto_id = $oldAId";
					$dblink -> query($sql) or die("Something went wrong $sql");
				}
			
				//manufacture updated
				if(!strcmp("select",$device) && strcmp("select",$manuf) && !preg_match($regex, $snum)){
					
					$sql = "UPDATE `device_$oldDT` SET `manufacture_id`='$manufactureid[$manuf]' `active`=$activ WHERE $oldAId";
					$dblink -> query($sql) or die("Something went wrong $sql");
				}
			
				//device and Serial Number updated
				if(strcmp("select",$device) && !strcmp("select",$manuf) && preg_match($regex, $snum)){
					
					$sql ="INSERT INTO `device_$devices[$device]`
					(`device_id`, `manufacture_id`, `serial_num`, `active`) 
					VALUES ('$deviceId[$device]','$oldMId','$snum',$activ)";
					
					$dblink -> query($sql) or die("Something went wrong $sql");
					$sql = "DELETE FROM `device_$devices[$device]` WHERE auto_id = $oldAId";
					$dblink -> query($sql) or die("Something went wrong $sql");
				}
			
				//Serial Number updated
				if(!strcmp("select",$device) && !strcmp("select",$manuf) && preg_match($regex, $snum)){
					$sql = "UPDATE `device_$oldDT` SET `serial_num`='$snum',`active`=$activ WHERE auto_id = $oldAId";
					$dblink -> query($sql) or die("Something went wrong $sql");
					
				}
			
				//Serial Number and manufacture updated
				if(!strcmp("select",$device) && strcmp("select",$manuf) && preg_match($regex, $snum)){
					
					$sql = "UPDATE `device_$oldDT` SET `manufacture_id`='$manufactureid[$manuf]', `serial_num`='$snum',`active`=$activ WHERE auto_id = $oldAId";
					$dblink -> query($sql) or die("Something went wrong $sql");
				}
			
				//Device Manufacture and Serial Number updated
				if(strcmp("select",$device) && strcmp("select",$manuf) && preg_match($regex, $snum)){
					
					$sql ="INSERT INTO `device_$devices[$device]`
					(`device_id`, `manufacture_id`, `serial_num`, `active`) 
					VALUES ('$deviceId[$device]','$manufactureid[$manuf]','$snum',$activ)";
					
					$dblink -> query($sql) or die("Something went wrong $sql");
					$sql = "DELETE FROM `device_$devices[$device]` WHERE auto_id = $oldAId";
					$dblink -> query($sql) or die("Something went wrong $sql");
				}
				
				if(!strcmp("select",$device) && !strcmp("select",$manuf) && !preg_match($regex, $snum) && $activ == true){
				
					$sql = "UPDATE `device_$oldDT` SET `active`=$activ WHERE auto_id = $oldAId";
					$dblink -> query($sql) or die("Something went wrong $sql");
				} else if (!strcmp("select",$device) && !strcmp("select",$manuf) && !preg_match($regex, $snum) && $activ == false){
					$sql = "UPDATE `device_$oldDT` SET `active`=$activ WHERE auto_id = $oldAId";
					$dblink -> query($sql) or die("Something went wrong $sql");
				}
			}
			
			//$result -> free_result();
			//$dblink -> close;
		?>
			</div>
	</body>
</html>