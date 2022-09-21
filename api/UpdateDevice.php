<?php
$dblink=db_iconnect("equipment");
$did=$_REQUEST['did'];
$aid=$_REQUEST['aid'];

$ndid=$_REQUEST['ndid'];
$nmid=$_REQUEST['nmid'];
$nsnum=$_REQUEST['nsnum'];

//proccesses device id
if (!is_numeric($did) && $did!=NULL){
	
	$output[]="Status: Invalid Data";
	$output[]="MSG: Device ID must be numbers only.";
	$output[]="";

	$flagdid=1;
} else if ($did==NULL){
	
	$output[]="Status: Invalid Data";
	$output[]="MSG: Device ID must not be blank.";
	$output[]="";

	$flagdid=1;
}

//processes auto id
if (!is_numeric($aid) && $aid!=NULL){

	$output[]="Status: Invalid Data";
	$output[]="MSG: Auto ID must be numbers only.";
	$output[]="";
	
	$flagaid=1;
} else if ($aid==NULL){
	
	
	$output[]="Status: Invalid Data";
	$output[]="MSG: Auto ID must not be blank.";
	$output[]="";
	
	$flagaid=1;
}

if ($flagaid!=NULL || $flagdid!=NULL){
	
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$responseData=json_encode($output);
	echo $responseData;
	die();
}

//proccess new device id
if (!is_numeric($ndid) && $ndid!=NULL){
	
	$output[]="Status: Invalid Data";
	$output[]="MSG: New Device ID must be numbers only.";
	$output[]="";

	$flagndid=1;
}

//processes new maunfacture id
if (!is_numeric($nmid) && $nmid!=NULL){

	$output[]="Status: Invalid Data";
	$output[]="MSG: New Manufacture ID must be numbers only.";
	$output[]="";
	
	$flagaid=1;
}

if (!sNumCheck($nsnum) && $nsnum!=NULL){
	
	$output[]="Status: Invalid Data";
	$output[]="MSG: New Serial Number must be numbers and letters only.";
	$output[]="MSG: Must be 32 chracters";
	$output[]="MSG: Do not include SN-";
	$output[]="";

	$flagnsnum=1;
}

if ($flagnmid!=NULL || $flagndid!=NULL || $flagnsnum!=NULL){
	
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$responseData=json_encode($output);
	echo $responseData;
	die();
} 
	
if($ndid!=NULL && $nmid==NULL && $nsnum==NULL){	
    
	//grabs old device type
    $sql = "SELECT * FROM device_type where device_id = $did";		
    $result = $dblink -> query($sql) or die("Something went wrong $sql");
    $data = $result -> fetch_array(MYSQLI_ASSOC);
    $buff = str_replace(" ","_",$data['device_type']);

    //grabs new device Type
    $sql = "SELECT * FROM device_type where device_id = $ndid";		
    $result = $dblink -> query($sql) or die("Something went wrong $sql");
    $newdata = $result -> fetch_array(MYSQLI_ASSOC);
    $newbuff = str_replace(" ","_",$newdata['device_type']);

    //Grabs old data
    $sql = "SELECT * FROM `device_$buff` WHERE auto_id = $aid";
    $result = $dblink -> query($sql) or die("Something went wrong $sql");
	
	if($result->num_rows>0){
		$data = $result -> fetch_array(MYSQLI_ASSOC);
		//$DeviceID = $data['device_id'];
		$ManufID = $data['manufacture_id'];
    	$snum = $data['serial_num'];
	} else {
		
		header('Content-Type: application/json');
    	header('HTTP/1.1 200 OK');
    	$output[]="Status: Invalid Data";
    	$output[]="MSG: $buff at $aid dosen't exist.";
    	$responseData=json_encode($output);
    	echo $responseData;
    	die();
	}

    $sql = "DELETE FROM `device_$buff` WHERE auto_id = $aid";
    $dblink -> query($sql) or die("Something went wrong $sql");

    $sql = "INSERT INTO `device_$newbuff`(`device_id`, `manufacture_id`, `serial_num`) VALUES ('$ndid','$ManufID','$snum')";
    $dblink -> query($sql) or die("Something went wrong $sql");

    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output[]="Status: OK";
    $output[]="MSG: $buff at $aid updated.";
    $output[]="Device Type: $newbuff";
    $responseData=json_encode($output);
    echo $responseData;
    die();
} else if($ndid!=NULL && $nmid!=NULL && $nsnum==NULL){
	
	//grabs old device type
    $sql = "SELECT * FROM device_type where device_id = $did";		
    $result = $dblink -> query($sql) or die("Something went wrong $sql");
    $data = $result -> fetch_array(MYSQLI_ASSOC);
    $buff = str_replace(" ","_",$data['device_type']);

    //grabs new device Type
    $sql = "SELECT * FROM device_type where device_id = $ndid";		
    $result = $dblink -> query($sql) or die("Something went wrong $sql");
    $newdata = $result -> fetch_array(MYSQLI_ASSOC);
    $newbuff = str_replace(" ","_",$newdata['device_type']);

    //Grabs old data
    $sql = "SELECT * FROM `device_$buff` WHERE auto_id = $aid";
    $result = $dblink -> query($sql) or die("Something went wrong $sql");

    if($result->num_rows>0){
		$data = $result -> fetch_array(MYSQLI_ASSOC);
		//$DeviceID = $data['device_id'];
		//$ManufID = $data['manufacture_id'];
    	$snum = $data['serial_num'];
	} else {
		
		header('Content-Type: application/json');
    	header('HTTP/1.1 200 OK');
    	$output[]="Status: Invalid Data";
    	$output[]="MSG: $buff at $aid dosen't exist.";
    	$responseData=json_encode($output);
    	echo $responseData;
    	die();
	}

    $sql = "DELETE FROM `device_$buff` WHERE auto_id = $aid";
    $dblink -> query($sql) or die("Something went wrong $sql");

    $sql = "INSERT INTO `device_$newbuff`(`device_id`, `manufacture_id`, `serial_num`) VALUES ('$ndid','$nmid','$snum')";
    $dblink -> query($sql) or die("Something went wrong $sql");
	
	header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output[]="Status: OK";
    $output[]="MSG: $buff at $aid updated.";
    $output[]="Device Type: $newbuff";
	$output[]="Manufacture ID: $nmid";
    $responseData=json_encode($output);
    echo $responseData;
    die();
} else if($ndid!=NULL && $nmid!=NULL && $nsnum!=NULL){
	
	//grabs old device type
    $sql = "SELECT * FROM device_type where device_id = $did";		
    $result = $dblink -> query($sql) or die("Something went wrong $sql");
    $data = $result -> fetch_array(MYSQLI_ASSOC);
    $buff = str_replace(" ","_",$data['device_type']);

    //grabs new device Type
    $sql = "SELECT * FROM device_type where device_id = $ndid";		
    $result = $dblink -> query($sql) or die("Something went wrong $sql");
    $newdata = $result -> fetch_array(MYSQLI_ASSOC);
    $newbuff = str_replace(" ","_",$newdata['device_type']);
	
	$sql = "SELECT * FROM `device_$buff` WHERE auto_id = $aid";
    $result = $dblink -> query($sql) or die("Something went wrong $sql");

    if($result->num_rows>0){
		//$data = $result -> fetch_array(MYSQLI_ASSOC);
		//$DeviceID = $data['device_id'];
		//$ManufID = $data['manufacture_id'];
    	//$snum = $data['serial_num'];
	} else {
		
		header('Content-Type: application/json');
    	header('HTTP/1.1 200 OK');
    	$output[]="Status: Invalid Data";
    	$output[]="MSG: $buff at $aid dosen't exist.";
    	$responseData=json_encode($output);
    	echo $responseData;
    	die();
	}
		
    $sql = "DELETE FROM `device_$buff` WHERE auto_id = $aid";
    $dblink -> query($sql) or die("Something went wrong $sql");

    $sql = "INSERT INTO `device_$newbuff`(`device_id`, `manufacture_id`, `serial_num`) VALUES ('$ndid','$nmid','$nsnum')";
    $dblink -> query($sql) or die("Something went wrong $sql");
	
	header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output[]="Status: OK";
    $output[]="MSG: $buff at $aid updated.";
    $output[]="Device Type: $newbuff";
	$output[]="Manufacture ID: $nmid";
	$output[]="Serial Number: $nsnum";
    $responseData=json_encode($output);
    echo $responseData;
    die();
} else if($ndid==NULL && $nmid!=NULL && $nsnum!=NULL){
	
	$sql = "SELECT * FROM device_type where device_id = $did";		
    $result = $dblink -> query($sql) or die("Something went wrong $sql");
    $data = $result -> fetch_array(MYSQLI_ASSOC);
    $buff = str_replace(" ","_",$data['device_type']);
	
	//UPDATE `device_$buff` SET `manufacture_id`=$nmid,`serial_num`=$nsnum WHERE auto_id = $aid
	$sql = "SELECT * FROM `device_$buff` WHERE auto_id = $aid";
    $result = $dblink -> query($sql) or die("Something went wrong $sql");

    if($result->num_rows>0){
		//$data = $result -> fetch_array(MYSQLI_ASSOC);
		//$DeviceID = $data['device_id'];
		//$ManufID = $data['manufacture_id'];
    	//$snum = $data['serial_num'];
	} else {
		
		header('Content-Type: application/json');
    	header('HTTP/1.1 200 OK');
    	$output[]="Status: Invalid Data";
    	$output[]="MSG: $buff at $aid dosen't exist.";
    	$responseData=json_encode($output);
    	echo $responseData;
    	die();
	}
	
	$sql = "UPDATE `device_$buff` SET `manufacture_id`=$nmid,`serial_num`='$nsnum' WHERE auto_id = $aid";
    $dblink -> query($sql) or die("Something went wrong $sql");
	
	header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output[]="Status: OK";
    $output[]="MSG: $buff at $aid updated.";
    //$output[]="Device Type: $newbuff";
	$output[]="Manufacture ID: $nmid";
	$output[]="Serial Number: $nsnum";
    $responseData=json_encode($output);
    echo $responseData;
    die();
	
} else if($ndid==NULL && $nmid!=NULL && $nsnum==NULL){
	
	$sql = "SELECT * FROM device_type where device_id = $did";		
    $result = $dblink -> query($sql) or die("Something went wrong $sql");
    $data = $result -> fetch_array(MYSQLI_ASSOC);
    $buff = str_replace(" ","_",$data['device_type']);
	
	$sql = "SELECT * FROM `device_$buff` WHERE auto_id = $aid";
    $result = $dblink -> query($sql) or die("Something went wrong $sql");

    if($result->num_rows>0){
		//$data = $result -> fetch_array(MYSQLI_ASSOC);
		//$DeviceID = $data['device_id'];
		//$ManufID = $data['manufacture_id'];
    	//$snum = $data['serial_num'];
	} else {
		
		header('Content-Type: application/json');
    	header('HTTP/1.1 200 OK');
    	$output[]="Status: Invalid Data";
    	$output[]="MSG: $buff at $aid dosen't exist.";
    	$responseData=json_encode($output);
    	echo $responseData;
    	die();
	}
	
	$sql = "UPDATE `device_$buff` SET `manufacture_id`=$nmid WHERE auto_id = $aid";
    $dblink -> query($sql) or die("Something went wrong $sql");
	
	header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output[]="Status: OK";
    $output[]="MSG: $buff at $aid updated.";
    //$output[]="Device Type: $newbuff";
	$output[]="Manufacture ID: $nmid";
	//$output[]="Serial Number: $nsnum";
    $responseData=json_encode($output);
    echo $responseData;
    die();
} else if($ndid==NULL && $nmid==NULL && $nsnum!=NULL){
	
	$sql = "SELECT * FROM device_type where device_id = $did";		
    $result = $dblink -> query($sql) or die("Something went wrong $sql");
    $data = $result -> fetch_array(MYSQLI_ASSOC);
    $buff = str_replace(" ","_",$data['device_type']);
	
	$sql = "SELECT * FROM `device_$buff` WHERE auto_id = $aid";
    $result = $dblink -> query($sql) or die("Something went wrong $sql");

    if($result->num_rows>0){
		//$data = $result -> fetch_array(MYSQLI_ASSOC);
		//$DeviceID = $data['device_id'];
		//$ManufID = $data['manufacture_id'];
    	//$snum = $data['serial_num'];
	} else {
		
		header('Content-Type: application/json');
    	header('HTTP/1.1 200 OK');
    	$output[]="Status: Invalid Data";
    	$output[]="MSG: $buff at $aid dosen't exist.";
    	$responseData=json_encode($output);
    	echo $responseData;
    	die();
	}
	
	$sql = "UPDATE `device_$buff` SET `serial_num`='$nsnum' WHERE auto_id = $aid";
    $dblink -> query($sql) or die("Something went wrong $sql");
	
	header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output[]="Status: OK";
    $output[]="MSG: $buff at $aid updated.";
    //$output[]="Device Type: $newbuff";
	//$output[]="Manufacture ID: $nmid";
	$output[]="Serial Number: $nsnum";
    $responseData=json_encode($output);
    echo $responseData;
    die();
} else{
	
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: ";
	$output[]="There is no valid Input.";
	$output[]="No device where updated.";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}

		
	
	
	



?>