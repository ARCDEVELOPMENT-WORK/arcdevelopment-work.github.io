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
$FileName = "UserInfoo.json";
$Hour = date('d-m-Y H:i');
$LogCast = devil99_decode(file_get_contents("LogCat.txt"));
$JDecode = json_decode(file_get_contents('php://input'),true); 
$Authorization = $JDecode["Authorization"] == "DowrDnASKiDdJFxXPxEDcjf42BUqHzkbUaCNm4wHw8AyTpa7gEGEybswr6G3JgE3paHkZbTapcWZbe4NHD"? true : false;
$Action  = $JDecode["Action"];
$Token = $JDecode["Token"] != null ? explode(":::",$JDecode["Token"]) : [];
$Owner = $Token[0] == null ? "Admin" : $Token[0];
$Content =json_decode(file_get_contents($FileName),true);
$isAdmin = $JDecode["Secret"] =="DowrDnASKiMqJtMaxtNGV0RWp1RWtaRWJ4RWp4RWVdNHZcNWFtNGV2RWN1RWF2RWFaRWF1RWF2RWB0" ? true : false;
if($LogCast == null){
	$LogCast = "";
	}
if($Content == null){
$Content = [];
}

if($isAdmin == false){
		if($Content[devil99_encode(strtolower($Token[0]))] != null){
			if($Content[devil99_encode(strtolower($Token[0]))]["password"] == devil99_encode($Token[1])){
				if($Content[devil99_encode(strtolower($Token[0]))]["Level"] == 1 ){
					
					
					
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
			exit("unathorized");
			}
		
		}

if($Authorization == true){
	if($isAdmin == false){
	$Username = strtolower($JDecode["Username"]);
	if($Content[devil99_encode($Username)] <> null){
		if($Content[devil99_encode($Username)]["Level"] <> null){
			if($Content[devil99_encode($Username)]["Level"]  == $Content[devil99_encode(strtolower($Token[0]))]["Level"]){
			
				ExitAlert("UNATHORIZED");
				
				}
			}
		
		
		
		}
		else{
			
			ExitAlert("⚠ User Does Not Exist ⚠");
			}
			}
	if($Action == "Reset"){
		$Username = strtolower($JDecode["Username"]);
		if($Content[devil99_encode($Username)] <> null){
			$Content[devil99_encode($Username)]["userAgent"] = "null";
			file_put_contents($FileName,json_encode($Content,true));
			file_put_contents("LogCat.txt",devil99_encode($LogCast."\n"."[".$Hour."]\n"."Action: RESET\nUsername: ".$Username."\nAdmin: ".$Owner));
			ExitAlert(" ✅ Sucess ✅");
		}
		}
		
if($Action == "Delete"){
	$Username = strtolower($JDecode["Username"]);
	
	if($Content[devil99_encode($Username)] <> null){
	unset($Content[devil99_encode($Username)]);
file_put_contents($FileName,json_encode($Content,true));
file_put_contents("LogCat.txt",devil99_encode($LogCast."\n"."[".$Hour."]\n"."Action: DELETE\nUsername: ".$Username."\nAdmin: ".$Owner));
ExitAlert("✅ Sucess ✅");

}
else{
	ExitAlert("⚠ User Does Not Exist ⚠");
}

}
	if($Action == "Edit"){
		$Username = strtolower($JDecode["Username"]);
		$Password = $JDecode["password"];
		$Date = $JDecode["date"];
		$Actived = $JDecode["actived"];
		if($isAdmin){
		$Level = $JDecode["level"] == "true" ? 1 : 0;
		}
		else{
			$Level = 0;
			}
		if($Content[devil99_encode($Username)]["password"] <> null){
			$Content[devil99_encode($Username)]["password"] = devil99_encode($Password);
			$Content[devil99_encode($Username)]["ExpireData"] = devil99_encode($Date);
			$Content[devil99_encode($Username)]["Actived"] = devil99_encode($Actived);
			$Content[devil99_encode($Username)]["Level"] = $Level;
			file_put_contents($FileName,json_encode($Content,true));
			$Data = "\nActive: ".$Actived."\nData: ".$Date."\nPassword: ".$Password."\nAdmin: ".(string)$Level;
			file_put_contents("LogCat.txt",devil99_encode($LogCast."\n"."[".$Hour."]\n"."Action: EDIT\nUsername: ".$Username.$Data."\nAdmin: ".$Owner));
ExitAlert("✅ Sucess ✅");
			}
			
			else{
				ExitAlert("⚠ User Does Not Exist ⚠");
				}
	}
if($Action == "Renovate"){
	date_default_timezone_set('Asia/Kolkata');
	$Username = strtolower($JDecode["Username"]);
	$Expiration = $JDecode["Expire"];
	if($Content[devil99_encode($Username)]["password"] <> null){
	    $date = devil99_decode($Content[devil99_encode($Username)]["ExpireData"]);
		$ExpireData = date('d-m-Y', strtotime("+".$Expiration." days",strtotime($date)));
		$Content[devil99_encode($Username)]["ExpireData"] = devil99_encode($ExpireData);
		file_put_contents($FileName,json_encode($Content,true));
		file_put_contents("LogCat.txt",devil99_encode($LogCast."\n"."[".$Hour."]\n"."Action: RENOVAR\nUsername: ".$Username."\nDias: ".$Expiration."\nAdmin: ".$Owner));
ExitAlert("✅ Sucess ✅");
		}
	}
	}
?>