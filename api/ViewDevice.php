<?php
$dblink=db_iconnect("equipment");
$did=$_REQUEST['did'];
$aid=$_REQUEST['aid'];

//gets device list
$sql = "SELECT * FROM device_type";		
$result = $dblink -> query($sql) or die("Something went wrong $sql");
$devices=array();
while ($data = $result -> fetch_array(MYSQLI_ASSOC)){			

	$devices[]=str_replace(" ","_",$data['device_type']);
}

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
	//$responseData=json_encode($output);
	//echo $responseData;
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

//Runs sql if inputs are good
if ($flagaid==NULL && $flagdid==NULL){
	$did -= 1;
	$sql="SELECT t.manufacture, m.serial_num, m.auto_id, m.device_id 
	FROM ((device_$devices[$did] as m
	INNER JOIN device_type as d 
	ON m.device_id = d.device_id)
	INNER JOIN manufacture as t
	ON m.manufacture_id = t.manufacture_id)
	where m.auto_id = $aid";
	$result = $dblink -> query($sql) or die("Something went wrong $sql");
	$buff = $result -> fetch_array(MYSQLI_ASSOC);
	if ($result->num_rows>0){
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]="Status: OK";
		$output[]="MSG: ";
		$data[]='Maufacturer: '.$buff['manufacture'];
		$data[]='Device Type: '.$devices[$did];
		$data[]='Serial Number: '.$buff['serial_num'];
		$output[]=$data;
		$responseData=json_encode($output);
		echo $responseData;
	} else{
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]="Status: Not Found";
		$output[]="MSG: Device: $devices[$did] at $aid not in database";
		$data[]="";
		$output[]=$data;
		$responseData=json_encode($output);
		echo $responseData;
	}
} else {
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
?>