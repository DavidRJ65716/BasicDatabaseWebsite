<?php
$dblink=db_iconnect("equipment");
$did=$_REQUEST['did'];
$aid=$_REQUEST['aid'];

$sql = "SELECT * FROM device_type";		
$result = $dblink -> query($sql) or die("Something went wrong $sql");
$devices=array();
while ($data = $result -> fetch_array(MYSQLI_ASSOC)){			

	$devices[]=str_replace(" ","_",$data['device_type']);
}

if ($did!=NULL && $aid==NULL){
	
	if (!is_numeric($did) && $did!=NULL){
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]="Status: Invalid Data";
		$output[]="MSG: Device ID must be numbers only.";
		$output[]="";
		$responseData=json_encode($output);
		echo $responseData;
		die();
	} else{
		//$did -= 1;
		$sql = "SELECT * FROM device_type where device_id = $did";		
    	$result = $dblink -> query($sql) or die("Something went wrong $sql");
    	$data = $result -> fetch_array(MYSQLI_ASSOC);
    	$buff = str_replace(" ","_",$data['device_type']);
		
		$sql = "ALTER TABLE $buff DROP INDEX device_$buff;";
		$dblink -> query($sql) or die("Something went wrong $sql");
		
		
		$sql = "DELETE FROM `device_type` WHERE device_id = $did";
		$dblink -> query($sql) or die("Something went wrong $sql");
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]="Status: OK";
		$output[]="MSG: ";
		$output[]= $devices[$did].' was deleted from the database';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}
} else if ($did!=NULL && $aid!=NULL){
	
	if (!is_numeric($did) && $did!=NULL){
	
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]="Status: Invalid Data";
		$output[]="MSG: Device ID must be numbers only.";
		$output[]="";
		$responseData=json_encode($output);
		echo $responseData;
		$flagdid=1;
		//die();
	}
	
	if (!is_numeric($aid) && $aid!=NULL){
	
        header('Content-Type: application/json');
        header('HTTP/1.1 200 OK');
        $output[]="Status: Invalid Data";
        $output[]="MSG: Auto ID must be numbers only.";
        $output[]="";
        //$responseData=json_encode($output);
        //echo $responseData;
        $flagmid=1;
        //die();
	}
		
	if ($flagdid==NULL && $flagaid==NULL){
		
		$sql = "SELECT * FROM device_type where device_id = $did";		
		$result = $dblink -> query($sql) or die("Something went wrong $sql");
		$data = $result -> fetch_array(MYSQLI_ASSOC);
		$buff = str_replace(" ","_",$data['device_type']);
		
		$sql = "SELECT * FROM `device_$buff` WHERE auto_id = $aid";
		$result = $dblink -> query($sql) or die("Something went wrong $sql");
		$data = $result -> fetch_array(MYSQLI_ASSOC);
		$DeviceID = $data['device_id'];
		$ManufID = $data['manufacture_id'];
		$snum = $data['serial_num'];
		
		$sql = "DELETE FROM `device_$buff` WHERE auto_id = $aid";
		$dblink -> query($sql) or die("Something went wrong $sql");
		
		$sql = "INSERT INTO `deleted_rec`(`device_id`, `manufacture_id`, `serial_num`) VALUES ('$DeviceID','$ManufID','$snum')";
		$dblink -> query($sql) or die("Something went wrong $sql");
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]="Status: OK";
		$output[]="MSG: ";
		$output[]= 'Device:'.$buff.' at '.$aid.' was deleted from the database.';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}else {
		$responseData=json_encode($output);
        echo $responseData;
		die();	
	}
} else {
	
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: Device ID must not be blank.";
	$output[]="MSG: Auto ID must not be blank.";
	$output[]="";
	$responseData=json_encode($output);
	die();
}
?>
