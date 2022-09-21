<?php
$dblink=db_iconnect("equipment");
//$did=$_REQUEST['did'];

$sql="Select * from `manufacture`";
$result=$dblink->query($sql) or
		die("Something went wrong with $sql");

if ($result->num_rows>0){
	
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: OK";
	$output[]="MSG: ";
	while($device = $result -> fetch_array(MYSQLI_ASSOC)){
		$data[]='Manufacturer ID: '.$device['manufacture_id'];
		$data[]='Manufacturer: '.$device['manufacture'];
	}
	$output[]=$data;
	$responseData=json_encode($output);
	echo $responseData;
} else{
		
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Not Found";
	$data[]="";
	$output[]=$data;
	$responseData=json_encode($output);
	echo $responseData;
}

?>