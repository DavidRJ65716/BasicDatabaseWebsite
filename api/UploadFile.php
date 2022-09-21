<?php
$dblink=db_iconnect("equipment");
$did=$_REQUEST['did'];
$aid=$_REQUEST['aid'];
//$snum=$_REQUEST['snum'];
//$file=$_REQUEST['userfile'];
echo $file;
//proccesses device id
if (!is_numeric($did) && $did!=NULL){
	
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: Device ID must be numbers only.";
	$output[]="";
	//$responseData=json_encode($output);
	//echo $responseData;
	//die();
	$flagdid=1;
} else if ($did==NULL){
	
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: Device ID must not be blank.";
	$output[]="";
	//$responseData=json_encode($output);
	//echo $responseData;
	//die();
	$flagdid=1;
}

//processes auto id
if (!is_numeric($aid) && $aid!=NULL){
	
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: Auto ID must be numbers only.";
	$output[]="";
	
	$flagaid=1;
	//die();
} else if ($aid==NULL){
	
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: Auto ID must not be blank.";
	$output[]="";
	
	$flagaid=1;
	//die();
}

if ($_FILES['userfile']['size'] == 0){
	
	//header('Content-Type: application/json');
	//header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: File doesn't exist.";
	$output[]="";
}

if ($flagaid==NULL && $flagdid==NULL && $_FILES['userfile']['size'] > 0){
	
	//grabs device type
    $sql = "SELECT * FROM device_type where device_id = $did";		
    $result = $dblink -> query($sql) or die("Something went wrong $sql");
    $data = $result -> fetch_array(MYSQLI_ASSOC);
    $buff = str_replace(" ","_",$data['device_type']);

    //Grabs old data
    $sql = "SELECT * FROM `device_$buff` WHERE auto_id = $aid";
    $result = $dblink -> query($sql) or die("Something went wrong $sql");

    if($result->num_rows==0){
		
		header('Content-Type: application/json');
    	header('HTTP/1.1 200 OK');
    	$output[]="Status: Invalid Data";
    	$output[]="MSG: $buff at $aid dosen't exist.";
    	$responseData=json_encode($output);
    	echo $responseData;
    	die();
	}
	
	$start_time=microtime(true);
	$uploadDir="/var/www/html/files";
	$fileName=$_FILES['userfile']['name'];
	$tmpName=$_FILES['userfile']['tmp_name'];
    $fileSize=$_FILES['userfile']['size'];
    $fileType=$_FILES['userfile']['type'];
    $location="$uploadDir/$fileName";
    move_uploaded_file($tmpName, $location);
    $sql="INSERT INTO `files`(`file_name`,`file_type`,`file_size`,`location`,`device`,`device_num`) Values";
    $sql.="('$fileName','$fileType','$fileSize','$location','$did','$aid')";
    $dblink -> query($sql) or die("Something went wrong $sql");
    $end_time=microtime(true);
    $total_time=($end_time - $start_time);
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: OK";
	$output[]="MSG: ";
	$output[]="$fileName was uploaded to $buff at $aid.";
	$output[]="Total time to upload file: $total_time.";
	$responseData=json_encode($output);
	echo $responseData;
	die();
    //exit();
} else{
	
	$responseData=json_encode($output);
	echo $responseData;
	die();
}



?>