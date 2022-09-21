<!doctype html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Home</title>
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
		$dblink = new mysqli("localhost","webuser","#replace with password","equipment");
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
		
		$sql = "SELECT * FROM manufacture";
		$result = $dblink -> query($sql);
		$manufacture=array();
		while ($data = $result -> fetch_array(MYSQLI_ASSOC)){		
			
			$manufacture[]=$data['manufacture'];			
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
			echo '<p>Select a Manufacture:</p>';
			echo '<div><select name = "manufacture">';
			echo '<option value="select">select</option>';
			foreach($manufacture as $key=>$value) {
				echo '<option value="'.$key.'">'.$value.'</option>';
			}
			echo '</select></div>';
			echo '<hr>';
			echo '<p>Type Serial Number:</p><br>';
			//echo '<p>SN-</p>';
			//echo '<input type="text" name="serial_num">';
			echo '<p>SN-<input type="text" name="serial_num"></p>';
			echo '<hr>';
			echo '<button class="btn btn-primary" type="submit" name="submit" value="lookup">Submit</button>';
			echo '<hr>';
			echo '<a class="btn btn-primary" href="deleteD.php">Delete device</a>';
			echo '<hr>';
			echo '<a class="btn btn-primary" href="add.php">Add device</a>';
			echo '</form>';
			echo '</div>';
			echo '</div>';
		}
		
		$regex = '/^[A-Za-z0-9]{32}$/';
		
		if(isset($_POST['submit']) && $_POST['submit']=="lookup"){
			echo '<div class="panel panel-primary">';
			echo '<div class="panel-heading"></div>';
  			echo '<div class="panel-body">';
			$device = $_POST['device'];
			$manuf = $_POST['manufacture'];
			$snum = $_POST['serial_num'];
			echo '<p>Device chosen is: '.$devices[$device].'.</p>';
			echo '<p>Manufacture chosen is: '.$manufacture[$manuf].'.</p>';
			echo '<p>Serial Number chosen is: SN-'.$snum.'.</p>';
			echo '<form method="post" action="">';
			//echo '<div><button class="btn btn-primary" type="submit" name="return" href="index.php">Select Another</button></div>';
			echo '<a class="btn btn-primary" href="index.php">Select Another</a>';
			echo '</form>';
			echo '</div>';
			echo '</div>';
			
			//device search
			if(strcmp("select",$device) && !strcmp("select",$manuf) && !preg_match($regex, $snum)){
				$sql="SELECT t.manufacture, m.serial_num, m.auto_id, m.device_id
					FROM ((device_$devices[$device] as m
					INNER JOIN device_type as d 
					ON m.device_id = d.device_id)
					INNER JOIN manufacture as t
					ON m.manufacture_id = t.manufacture_id) limit 1000";
				$result = $dblink -> query($sql) or die("Something went wrong $sql");
			}
			
			//device and manufacturer search
			if(strcmp("select",$device) && strcmp("select",$manuf) && !preg_match($regex, $snum)){
				$sql="SELECT t.manufacture, m.serial_num, m.auto_id, m.device_id 
					FROM ((device_$devices[$device] as m
					INNER JOIN device_type as d 
					ON m.device_id = d.device_id)
					INNER JOIN manufacture as t
					ON m.manufacture_id = t.manufacture_id)
					where t.manufacture = '$manufacture[$manuf]' limit 1000";
				$result = $dblink -> query($sql) or die("Something went wrong $sql");
			}
			
			//manufacture search
			if(!strcmp("select",$device) && strcmp("select",$manuf) && !preg_match($regex, $snum)){

				foreach($devices as $row) {
					
					$sql="SELECT t.manufacture, m.serial_num, m.auto_id, m.device_id
					FROM ((device_$row as m
					INNER JOIN device_type as d 
					ON m.device_id = d.device_id)
					INNER JOIN manufacture as t
					ON m.manufacture_id = t.manufacture_id)
					where t.manufacture = '$manufacture[$manuf] 'limit 1000";
					$buff = $dblink -> query($sql) or die("Something went wrong $sql");
					if($buff->num_rows != 0){
						$result = $buff;
						break;
					}
				}
			}
			
			//device and Serial Number
			if(strcmp("select",$device) && !strcmp("select",$manuf) && preg_match($regex, $snum)){
				$sql="SELECT t.manufacture, m.serial_num, m.auto_id, m.device_id
					FROM ((device_$devices[$device] as m
					INNER JOIN device_type as d 
					ON m.device_id = d.device_id)
					INNER JOIN manufacture as t
					ON m.manufacture_id = t.manufacture_id)
					where m.serial_num like '%$snum%'";
				$result = $dblink -> query($sql) or die("Something went wrong $sql");
			}
			
			//Serial Number search
			if(!strcmp("select",$device) && !strcmp("select",$manuf) && preg_match($regex, $snum)){
				foreach($devices as $row) {
					
					$sql="SELECT t.manufacture, m.serial_num, m.auto_id, m.device_id
					FROM ((device_$row as m
					INNER JOIN device_type as d 
					ON m.device_id = d.device_id)
					INNER JOIN manufacture as t
					ON m.manufacture_id = t.manufacture_id)
					where m.serial_num like '%$snum%'";
					$buff = $dblink -> query($sql) or die("Something went wrong $sql");
					if($buff->num_rows != 0){
						$result = $buff;
						break;
					}
				}
			}
			
			//Serial Number and manufacture search
			if(!strcmp("select",$device) && strcmp("select",$manuf) && preg_match($regex, $snum)){
				foreach($devices as $row) {
					
					$sql="SELECT t.manufacture, m.serial_num, m.auto_id, m.device_id
					FROM ((device_$row as m
					INNER JOIN device_type as d 
					ON m.device_id = d.device_id)
					INNER JOIN manufacture as t
					ON m.manufacture_id = t.manufacture_id)
					where t.manufacture = '$manufacture[$manuf]'
					AND m.serial_num like '%$snum%' limit 1";
					$buff = $dblink -> query($sql) or die("Something went wrong $sql");
					if($buff->num_rows != 0){
						$result = $buff;
						break;
					}
				}
			}
			
			//Device Manufacture and Serial Number search
			if(strcmp("select",$device) && strcmp("select",$manuf) && preg_match($regex, $snum)){
				$sql="SELECT t.manufacture, m.serial_num, m.auto_id, m.device_id
					FROM ((device_tablet as m
					INNER JOIN device_$devices[$device] as d 
					ON m.device_id = d.device_id)
					INNER JOIN manufacture as t
					ON m.manufacture_id = t.manufacture_id)
					where t.manufacture = '$manufacture[$manuf]'
					AND m.serial_num like '%$snum%' limit 1";
				$result = $dblink -> query($sql) or die("Something went wrong $sql");
			}
			
			echo '<div class="panel panel-primary">';
			echo '<div class="panel-body">';
			echo '<div id="invDetails_wrapper" class="dataTables_wrapper no-footer">';
			echo '<div class="dataTables_length" id="invDetails_length">';
			echo '<table id="invDetails" class="display dataTable no-footer dtr-inline" cellspacing="0" width="100%" role="grid" aria-	describedby="invDetails_info" style="width: 100%;">';
			echo '<thead>';
			echo '<tr role="row">';
			echo '<th class="sorting_asc" tabindex="0" aria-controls="invDetails" rowspan="1" colspan="1" style="width: 443px;" aria-sort="ascending" aria-label="Manufacturer: activate to sort column descending">';
			echo 'Manufacturer';
			echo '</th>';
			echo '<th class="sorting" tabindex="0" aria-controls="invDetails" rowspan="1" colspan="1" style="width: 1053px;" aria-label="Serial Number: activate to sort column ascending">';
			echo 'Serial Number</th>';
			echo '<th class="sorting" tabindex="0" aria-controls="invDetails" rowspan="1" colspan="1" style="width: 287px;" aria-label="Action: activate to sort column ascending">';
			echo 'Action</th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
			$count = $result->num_rows;
			while($data = $result -> fetch_array(MYSQLI_ASSOC)){
				
				echo '<tr role="row" class="odd">';
				echo '<td tabindex="0" class="sorting_1">'.$data['manufacture'].'</td>';
				echo '<td>'.$data['serial_num'].'</td>';
				echo '<td><a class="btn btn-sm btn-primary" 
				href="fileload.php?did='.$data['auto_id'].'&device='.$data['device_id'].'">Upload File</a><a class="btn btn-sm btn-primary" 
				href="mod.php?did='.$data['auto_id'].'&deviceOld='.$data['device_id'].'">Modify</a><a class="btn btn-sm btn-primary" 
				href="deleteR.php?did='.$data['auto_id'].'&device='.$data['device_id'].'">delete</a></td>';
				echo '</tr>';
			}
			echo '</tbody>';
			echo '</table>';
			echo '</div>';
			echo '</div>';
		}
		
		$result -> free_result();
		$dblink -> close;
		?>
			</div>
	</body>
</html>