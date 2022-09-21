<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Fileload</title>
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
			
			$data = $result -> fetch_array(MYSQLI_ASSOC);;
		
			echo '<div id="page-inner">';
			echo '<div class="panel panel-primary">';
			echo '<div class="panel-heading">Device Info</div>';
			echo '<div class="panel-body">';
			echo '<p>Maufacturer: '.$data['manufacture'].'</p>';
			echo '<p>Device Type: '.$data['device_type'].'</p>';
			echo '<p>Serial Number: '.$data['serial_num'].'</p>';
			
			if(isset($_REQUEST['totaltime'])){
				
				$totalTime=$_REQUEST['totaltime'];
				echo '<div class="alert alert-success alert-dismissible" role="alert">';
				echo '<button type="button" clase="close" data-dismiss="alert" arial-label="close"><span
				aria-hidden="true">&times;</span></button>';
				echo '<p>File Process time. Execution time:'.$totalTime.'</p>';
				echo '</div>';
			}
				
			$sql="SELECT * FROM `files` WHERE `device`=$dev AND `device_num`=$snum";
			$result = $dblink -> query($sql) or die("Something went wrong $sql");
			if($result->num_rows>0){
				
				echo '<p>Record Files Found:</p>';
				
				while($row=$result -> fetch_array(MYSQLI_ASSOC)){
					
					//$name=$data['file_name'];
					//$location=str_replace(" ","_",$data['file_name']);
					echo'<div><a class="btn btn-sm btn-info" href="./files/'.$row['file_name'].'" target="_blank">View '.$name.'</a></div>';					
				}
			}
			echo '</div>';//End Body Info
			echo '</div>';
			/*echo '<div class="panel panel-primary">';
			echo '<div class="panel-heading">Upload File to Database</div>';
			echo '<div class="panel-body">';
			echo '<form role="form" method="post" enctype="multipart/form-data" action="fileload.php?did='.$snum.'&device='.$dev.'">';
			echo '<input type="hidden" name="MAX_FILE_SIZE" value="50000000">';
			echo '<input type="hidden" name="did" value="'.$snum.'">';
			echo '<div class="form-group"><label class="control-label col-lg-4">';
			echo 'File Upload</label>';
			echo '<div class="">';
			echo '<div class="fileupload fileupload-new" data-provides="fileupload"><div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">';
			echo '</div>';
			echo '<div class="row">';
			echo '<div class="col-md-2">';
			echo '<span class="btn btn-file btn-primary">';
			echo '<span class="fileupload-new">Select File</span>';
			echo '<span class="fileupload-exists">Change</span>';
			echo '<input name="userfile" type="file"></span>';
			echo '</div>';
			echo '<div class="col-md-2">';
			echo '<a href="fileload.php?did='.$snum.'&device='.$dev.'" class="btn btn-danger fileupload-exists" data-dismiss="fileupload">';
			echo 'Remove</a>';
			echo '</div>';
			echo '</div>';
			echo '</div>';
			echo '</div>';
			echo '<hr>';
			echo '<div class="col-md-2">';
			echo '<button class="btn btn-success" name="UploadAppDoc" type="submit" value="UploadAppDoc">';
			echo 'Upload</button>';
			echo '</div>';
			echo '<div class="col-md-2">';
			echo '<a class="btn btn-danger" href="fileload.php?did='.$snum.'&device='.$dev.'">';
			echo 'Cancel</a>';
			echo '</div>';
			echo '</div>';
			echo '</form>';
			echo '</div>';
			echo '</div>';
			if(isset($_POST["UploadAppDoc"]) && $_FILES['userfile']['size'] > 0){
				
				//$uploadDir="/var/www/html/files";
				//$uploadedby=$_POST['uploadedby'];
				$fileName=$_FILES['userfile']['name'];
				$tmpName=$_FILES['userfile']['tmp_name'];
				$fileSize=$_FILES['userfile']['size'];
				$fileType=$_FILES['userfile']['type'];
				$fp=fopen($tmpName, 'r');
				$content=fread($fp, filesize($tmpName));
				$content=addslashes($content);
				fclose($fp);
				$sql="INSERT INTO `files`(`file_name`,`file_type`,`file_size`,`content`,`device`,`device_num`) Values";
				$sql.="('$fileName','$fileType','$fileSize','$content','$dev','$snum')";
				$dblink -> query($sql) or die("Something went wrong $sql");
				redirect("https://ec2-18-116-73-94.us-east-2.compute.amazonaws.com/fileload.php?did=$snum&device=$dev");
				
			}*/
			echo '<div class="panel panel-primary">';
			echo '<div class="panel-heading">';
			echo 'Upload File to Filesystem</div>';
			echo '<div class="panel-body">';
			echo '<form role="form" method="post" enctype="multipart/form-data" action="fileload.php?did='.$snum.'&device='.$dev.'">';
			echo '<input type="hidden" name="MAX_FILE_SIZE" value="50000000">';
			echo '<input type="hidden" name="did" value="'.$snum.'">';
			echo '<div class="form-group">';
			echo '<label class="control-label col-lg-4">';
			echo 'File Upload</label>';
			echo '<div class="">';
			echo '<div class="fileupload fileupload-new" data-provides="fileupload">';
			echo '<div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">';
			echo '</div>';
			echo '<div class="row">';
			echo '<div class="col-md-2">';
			echo '<span class="btn btn-file btn-primary">';
			echo '<span class="fileupload-new">Select File</span><span class="fileupload-exists">Change</span>';
			echo '<input name="userfile" type="file">';
			echo '</span>';
			echo '</div>';
			echo '<div class="col-md-2">';
			echo '<a href="fileload.php?did='.$snum.'&device='.$dev.'" class="btn btn-danger fileupload-exists" data-dismiss="fileupload">';
			echo 'Remove</a>';
			echo '</div>';
			echo '</div>';
			echo '</div>';
			echo '</div>';
			echo '</div>';
			echo '<hr>';
			echo '<div class="col-md-2">';
			echo '<button class="btn btn-success" name="UploadFileSys" type="submit" value="UploadFileSys">';
			echo 'Upload</button>';
			echo '</div>';
			echo '<div class="col-md-2">';
			echo '<a class="btn btn-danger" href="fileload.php?did='.$snum.'&device='.$dev.'">';
			echo 'Cancel</a>';
			echo '</div>';
			echo '</form>';
			echo '</div>';
			echo '</div>';
			if (isset($_POST["UploadFileSys"]) && $_FILES['userfile']['size'] > 0){
				
				$start_time=microtime(true);
				$uploadDir="/var/www/html/files";
				$fileName=$_FILES['userfile']['name'];
				$tmpName=$_FILES['userfile']['tmp_name'];
				$fileSize=$_FILES['userfile']['size'];
				$fileType=$_FILES['userfile']['type'];
				$location="$uploadDir/$fileName";
				move_uploaded_file($tmpName, $location);
				$sql="INSERT INTO `files`(`file_name`,`file_type`,`file_size`,`location`,`device`,`device_num`) Values";
				$sql.="('$fileName','$fileType','$fileSize','$location','$dev','$snum')";
				$dblink -> query($sql) or die("Something went wrong $sql");
				$end_time=microtime(true);
				$total_time=($end_time - $start_time);
				header("Location: https://ec2-18-116-73-94.us-east-2.compute.amazonaws.com/fileload.php?did=$snum&device=$dev&totaltime=$total_time", true, 301);
				exit();
			}
			
		?>
		</div>
	</body>
</html>
