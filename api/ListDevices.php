<?php
$dblink=db_iconnect("equipment");
$did=$_REQUEST['did'];

$sql = "SELECT * FROM device_type";		
$result = $dblink -> query($sql) or die("Something went wrong $sql");
$devices=array();
while ($data = $result -> fetch_array(MYSQLI_ASSOC)){			

	$devices[]=str_replace(" ","_",$data['device_type']);
}

if (!is_numeric($did) && $did!=NULL){
	
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: Device ID must be numbers only.";
	$output[]="";
	$responseData=json_encode($output);
	echo $responseData;
	die();
} else if ($did==NULL){
	
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: Device ID must not be blank.";
	$output[]="";
	$responseData=json_encode($output);
	echo $responseData;
	die();
} else {
  	$did -= 1;
	$sql="SELECT t.manufacture, m.serial_num, m.auto_id, m.device_id
	FROM ((device_$devices[$did] as m
	INNER JOIN device_type as d 
	ON m.device_id = d.device_id)
	INNER JOIN manufacture as t
	ON m.manufacture_id = t.manufacture_id) limit 7000";
	$result = $dblink -> query($sql) or die("Something went wrong $sql");
	
	if ($result->num_rows>0){

        header('Content-Type: application/json');
        header('HTTP/1.1 200 OK');
        $output[]="Status: OK";
        $output[]="MSG: $devices[$did]";
        while($device = $result -> fetch_array(MYSQLI_ASSOC)){
			$data[]='Auto ID: '.$device['auto_id'];
            $data[]='Manufacturer: '.$device['manufacture'];
			$data[]='Serial Number: '.$device['serial_num'];
        }
        $output[]=$data;
        $responseData=json_encode($output);
        echo $responseData;
		die();
    } else{

        header('Content-Type: application/json');
        header('HTTP/1.1 200 OK');
        $output[]="Status: Not Found";
        $data[]="";
        $output[]=$data;
        $responseData=json_encode($output);
        echo $responseData;
		die();
    }
}

?>