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
		
$sql = "SELECT * FROM manufacture";
$result = $dblink -> query($sql);
$manufacture=array();
while ($data = $result -> fetch_array(MYSQLI_ASSOC)){		
		
	$manufacture[]=$data['manufacture'];			
}

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

if ($flagaid==NULL && $flagdid==NULL){
	
	$sql="SELECT * FROM `files` WHERE `device`=$did AND `device_num`=$aid";
	$result = $dblink -> query($sql) or die("Something went wrong $sql");
	if($result->num_rows>0){
		
		while($row=$result -> fetch_array(MYSQLI_ASSOC)){
			
			$location=$row['location'];
			$name=$row['file_name'];
			$data[]="File: $name";
			$data[]="https://ec2-18-116-73-94.us-east-2.compute.amazonaws.com/files/$name";
			
		}
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]="Status: OK";
		$output[]="MSG: ";
		$output[]=$data;
		$responseData=json_encode($output);
		echo $responseData;
		die();
	} else {
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]="Status: Invalid Data";
		$output[]="MSG: No Files Found for $did at $aid.";
		$output[]="";
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}				
} else{
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
?>