<?php

header("content-type: application/json");
function devil99_decode($Str){
$Count = 1;
$Base = "";
for($x=0;$x<strlen($Str)/ 2;$x++){
	$Base =$Base.chr(hexdec($Str[$Count - 1].$Str[$Count]) - 40);
	$Count = $Count + 	2;
}
return base64_decode($Base);
}

function Dec($str){
$R = "";
if($str == "null"){
return "null";

}
else{
return devil99_decode($str);
}
}
$FileName = "UserInfoo.json";
$JDecode = json_decode(file_get_contents('php://input'),true); 
$Authorization = $JDecode["KEY"] == "DowrDnASKiBPg3J6H8t6xnPVrR"? true : false;
$content = json_decode(file_get_contents($FileName),true);
$Data = "";
$isAdmin = $JDecode["Secret"] =="DowrDnASKiMAgiCYHsMiwHHwbnV27Jvs4" ? true : false;
if($Authorization == false){
	return;
	
	}
for($x=0;$x<sizeof($content);$x++){
$key = (string)key($content);
next($content);
if($isAdmin){
	if($content[$key]["Level"] != null){
		if($content[$key]["Level"] == 1){
			
			$IsDev = "true";
			}
			else{
				$IsDev = "false";
		}
		
		}
		else{
			$IsDev = "false";
			
			}
	$Data = $Data.Dec($key)."||".Dec($content[$key]["password"])."||".Dec($content[$key]["ExpireData"])."||".Dec($content[$key]["Actived"])."||".Dec($content[$key]["LastLogin"])."||".Dec($content[$key]["Owner"])."||".Dec($content[$key]["Created"])."||".$IsDev.'\n';
	}
	else{
$Data = $Data.Dec($key)."||".Dec($content[$key]["password"])."||".Dec($content[$key]["ExpireData"])."||".Dec($content[$key]["Actived"])."||".Dec($content[$key]["LastLogin"])."\n";
 }
 }



echo($Data);






?>