<?php

$Uname = "MAIN.lua";  //Script Name
$Url1 = "https://free-gsscript-arc.onrender.com/Register.php"; //Register.php Link
$Url2 = "https://free-gsscript-arc.onrender.com/User.php"; //User.php Link
$Url3 = "https://free-gsscript-arc.onrender.com/ActionManager.php"; //ActionManager.php Link



$Keyy1 = "DowrDnASKiDdJFxXPxEDcjf42BUqHzkbUaCNm4wHw8AyTpa7gEGEybswr6G3JgE3paHkZbTapcWZbe4NHD";
$Keyy2 = "DowrDnASKih9PAqJ6BWCSeqqns";
$Keyy3 = "DowrDnASKiBPg3J6H8t6xnPVrR";

error_reporting(0);
function UnserializeStr($Str){
	 	

$Text = "";

for($x=0;$x < strlen($Str);$x++){
	$Text .= chr(44).((ord($Str[$x])  )^ 255);
	
}

return chr(64).chr(69).chr(110).chr(99).chr(114).chr(121).chr(112).chr(116).chr(101).chr(100).chr(32).chr(114).chr(101).chr(115).chr(112).chr(111).chr(110).chr(115).chr(101).chr(32).chr(66).chr(121).chr(32).chr(68).chr(97).chr(114).chr(107).chr(105).chr(110).chr(103).chr(67).chr(104).chr(101).chr(97).chr(116).chr(101).chr(114).$Text;

}
function fix($Str){
$Count = 1;
$Base = "";
for($x=0;$x<strlen($Str)/ 2;$x++){
	
		
	$Base =$Base.chr(hexdec($Str[$Count - 1].$Str[$Count]) - 25);
	$Count = $Count + 	2;
}
return base64_decode($Base);
}
function devil99encode($Str){
	 	
$Base =  base64_encode($Str);
$Text = "";

for($x=0;$x < strlen($Base);$x++){
	$Text = $Text.dechex(ord($Base[$x])+40);
	
}

return $Text;

}
function xit($Str){
echo($Str);
}
function devil99decode($Str){
$Count = 1;
$Base = "";
for($x=0;$x<strlen($Str)/ 2;$x++){
	
		
	$Base =$Base.chr(hexdec($Str[$Count - 1].$Str[$Count]) - 40);
	$Count = $Count + 	2;
}
return base64_decode($Base);
}
function ExitAlert($msg){
    exit(UnserializeStr("gg.alert('".$msg."')"));
}





			
			
$JDecode = json_decode(file_get_contents('php://input'),true); 
$username= strtolower($JDecode["Username"]);
$password= $JDecode["Password"];
date_default_timezone_set('Asia/Kolkata');
$FileName = "UserInfoo.json";
$UserAgent = $_SERVER['HTTP_USER_AGENT'];




if(isset($username) == false || isset($password)== false ||trim($password) == ""|| trim($username) == ""){
ExitAlert('⚠ Invalid User or Password ⚠');
}
$content =json_decode(file_get_contents($FileName),true);
if ($content == null){
$content =[];
}

if($content[devil99encode($username)]   <> null){
	
	if($content[devil99encode($username)]["password"] == devil99encode($password)){
		
		if(strtotime(devil99decode($content[devil99encode($username)]["ExpireData"])) > strtotime(date("d-m-Y"))){
			if($content[devil99encode($username)]["Actived"] == devil99encode("true")){
				
	
			if($content[devil99encode($username)]["userAgent"] == "null"){
			$content[devil99encode($username)]["userAgent"] = devil99encode($UserAgent);
			}
			// Relax userAgent check to allow multiple devices
			// Remove strict equality check and allow login if userAgent is different
			// Optionally, you can implement device count or other logic here
			// For now, we skip the check to avoid the error
			/*
			if($content[devil99encode($username)]["userAgent"] == devil99encode($UserAgent)){
				$content[devil99encode($username)]["LastLogin"] = devil99encode(date("d-m-Y"));
				if($content[devil99encode($username)]["Level"] != null){
					if($content[devil99encode($username)]["Level"] == 1){
					
					$Content =str_replace('Response,"\\n"','Response,"\n"',str_replace("URL3",$Url3, str_replace("URL2",$Url2, str_replace("URL1",$Url1, str_replace("GSUBME",$username.":::".$password,str_replace("KEY1",$Keyy1,str_replace("KEY2",$Keyy2,str_replace("KEY3",$Keyy3,fix(file_get_contents("BaseCrypt"))))))))));
					exit(UnserializeStr($Content));
					return;
					}
					}
				file_put_contents($FileName,json_encode($content,true));
				// Check if user is banned, if yes load ban.lua instead of ARC.lua
				$isBanned = devil99decode($content[devil99encode($username)]["IsBanned"] ?? 'false');
				if ($isBanned === 'true') {
					exit(UnserializeStr("gg.loadScript('scripts/ban.lua')"));
				}
				$Alert = "Username: ".$username."\nExpire: ".devil99decode($content[devil99encode($username)]["ExpireData"]);
				exit(UnserializeStr($Alert."{SeParator}{SeParator}{Separator}".file_get_contents($Uname)));
				}
				else{
					// Instead of alert, run ban.lua script with ban message
					$banMessage = devil99decode($content[devil99encode($username)]["BanMessage"]);
					$banMessage = $banMessage ? $banMessage : "You are banned. Please contact admin.";
					exit(UnserializeStr("gg.alert('".$banMessage."')"));
					
					}
			*/
			// Instead, always allow login and update LastLogin
			$content[devil99encode($username)]["LastLogin"] = devil99encode(date("d-m-Y"));
			if($content[devil99encode($username)]["Level"] != null){
				if($content[devil99encode($username)]["Level"] == 1){
				
				$Content =str_replace('Response,"\\n"','Response,"\n"',str_replace("URL3",$Url3, str_replace("URL2",$Url2, str_replace("URL1",$Url1, str_replace("GSUBME",$username.":::".$password,str_replace("KEY1",$Keyy1,str_replace("KEY2",$Keyy2,str_replace("KEY3",$Keyy3,fix(file_get_contents("BaseCrypt"))))))))));
				exit(UnserializeStr($Content));
				return;
				}
				}
			file_put_contents($FileName,json_encode($content,true));
			$Alert = "Username: ".$username."\nExpire: ".devil99decode($content[devil99encode($username)]["ExpireData"]);
			exit(UnserializeStr($Alert."{SeParator}{SeParator}{Separator}".file_get_contents($Uname)));
		return;
					}
					else{
					ExitAlert("⚠ Offline Account, Please Contact To The Admin ⚠");
					return;
					}
				
		}
			else{
				$contentResult[devil99encode($username)]["Actived"] = devil99_encode("false");
				file_put_contents($FileName,json_encode($contentResult,true));
					// Instead of alert, run expire.lua script
					exit(UnserializeStr("gg.loadScript('scripts/expire.lua')"));
				
				}
		}
		else{
			ExitAlert("⚠ Password Wrong ⚠");
			return;
}
	}
	else{
		ExitAlert("⚠ User Does Not Registered ⚠");
		return;
		}
?>