<?php

error_reporting(0);
function devil99_decode($Str){
	if($Str == null ||  $Str == ""){
		
		return $Str;
		}
$Count = 1;
$Base = "";
for($x=0;$x<strlen($Str)/ 2;$x++){
	
		
	$Base =$Base.chr(hexdec($Str[$Count - 1].$Str[$Count]) - 40);
	$Count = $Count + 	2;
}
return base64_decode($Base);
}
function devil99_encode($Str){
	 	if($Str == null){
		return "";
		}
$Base =  base64_encode($Str);
$Text = "";

for($x=0;$x < strlen($Base);$x++){
	$Text = $Text.dechex(ord($Base[$x])+40);
	
}

return $Text;

}

function ExitAlert($msg){
    exit("gg.alert('".$msg."')");
}
$LogCast = devil99_decode(file_get_contents("LogCat.txt"));
if($LogCast == null){
	$LogCast  = "";
	
	}
$JDecode = json_decode(file_get_contents('php://input'),true); 
$FileName = "UserInfoo.json";
$username= strtolower($JDecode["Username"]);
$password= $JDecode["Password"];
$confirm_password= $JDecode["ConfirmPassword"];
$Expiration = $JDecode["Expiration"];
date_default_timezone_set('Asia/Kolkata');
$data = date("d-m-Y");
$ExpireData = date('d-m-Y', strtotime("+".$Expiration." days",strtotime($data)));
$Token = $JDecode["Token"] != null ? explode(":::",$JDecode["Token"]) : [];
$isAdmin = $JDecode["Secret"] =="DowrDnASKiMqJtMaxtNGV0RWp1RWtaRWJ4RWp4RWVdNHZcNWFtNGV2RWN1RWF2RWFaRWF1RWF2RWB0" ? true : false;
$Level = (int)$JDecode["Level"] == 1 && $isAdmin == true ? 1 : 0;
$Key  = $JDecode["Key"]  == "DowrDnASKih9PAqJ6BWCSeqqns" ? true : false;
if($Key == false){
	exit("⚠️ UNATHORIZED ⚠️");
	return;
	}
	
		$content =json_decode(file_get_contents($FileName),true);
if ($content == null){
$content =[];
}

		if($isAdmin == false){
		if($content[devil99_encode(strtolower($Token[0]))] != null){
			if($content[devil99_encode(strtolower($Token[0]))]["password"] == devil99_encode($Token[1])){
				if($content[devil99_encode(strtolower($Token[0]))]["Level"] == 1 ){
					
					
					
					}
					else{
				exit("UNATHORIZED");
				}
				}
				else{
					exit("UNATHORIZED");
					}
			
			}
		else{
			exit("UNATHORIZED");
			}
		
		}
		

if(isset($username) == false || isset($password)== false ||trim($password) == ""|| trim($username) == ""){
ExitAlert('⚠ User Or Password Invalid ⚠');
}
if($confirm_password <>$password){
ExitAlert('⚠️ Password & Confirm Password Can Not Be Defferent ⚠️');
}

if($content[devil99_encode($username)] == null){
	if((int)date("Y",  strtotime($ExpireData)) > 2018){
	    $Owner = $Token[0] != null ? devil99_encode($Token[0]) : devil99_encode("Admin");
	$content[devil99_encode($username)] =  array(
    "password" => devil99_encode($password),
"userAgent" => "null",
"LastLogin" => "null",
"ExpireData" =>devil99_encode($ExpireData),
"Actived" => devil99_encode("true"),
"Level" => $Level,
"Created"  => devil99_encode($data),
"Owner" => $Owner
);
file_put_contents("LogCat.txt",devil99_encode($LogCast."\n"."[".date('d-m-Y H:i')."]\n"."Action: Register\nUsername: ".$username."\nPassword: ".$password."\nDias: ".$Expiration."\nAdmin: ".devil99_decode($Owner)));
    file_put_contents($FileName,json_encode($content,true));
    ExitAlert(" Sucess ✅");
    }
    else{
    	ExitAlert("⚠️ Day Invalid ⚠️");
    }
	}
	
	else{
		ExitAlert("⚠️ This User Already Exist ⚠️");
		}
?>