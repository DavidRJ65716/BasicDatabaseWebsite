<?php
include("functions.php");
$uri=parse_url($_SERVER['REQUEST_URI'],PHP_URL_QUERY);
$uri=explode('&',$uri);
$endPoint=$uri[0];
//die("End Point: $endPoint");
switch ($endPoint){
	case "ViewDevice":
		include("ViewDevice.php");
		break;
	case "ListDevices":
		include("ListDevices.php");
		break;
	case "UploadFile":
		include("UploadFile.php");
		break;
	case "UpdateDevice":
		include("UpdateDevice.php");
		break;
	case "ViewFile":
		include("ViewFile.php");
		break;
	case "deleteDevice":
		include("deleteDevice.php");
		break;
	case "ListManufacturer":
		include("ListManufacturer.php");
		break;		
	default:
		header('Content-Type: application/json');
		header("HTTP/1.1 404 Not Found");
		$message[]="Status: Error";
		$message[]="MSG: Endpoint not found";
		$message[]="";
		echo json_encode($message);
		die();
}
?>