<?php
session_start();

include ("../setup/common.php");
include ("ParamVal.php");

BeginProc();
CheckLogin1 ();
CheckRight1 ($mysqli, 'ExtProj.Admin');

$FldNames=array('RepId','EMail');
$UserId=addslashes($_REQUEST['UserId']);
if ($UserId==''){ 
  die ("<br> Error:  Empty UserId");
}



// usrs
// usr_id, usr_pwd, description, admin, 
// email, phone, passwd_duedate, new_passwd, passwd_last_change, 
// SFUser, Blocked, WebCookie, Position, Department, 
// Company, FirstName, LastName, PatronymicName__c, PwdCoded
$Usr=array();
$query = "select * from usrs ". 
         "where (usr_id = '$UserId')"; 

$sql2 = $mysqli->query ($query) 
          or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
if ($Usr = $sql2->fetch_assoc()) {
  $Val = addslashes ($Usr['LastName'].' '.substr($Usr['FirstName'], 0, 1).'.');
  SetParam ($mysqli, '0001', 'ReportUsrName', $UserId, $Val);
   

  $Val = addslashes ($Usr['LastName']);
  SetParam ($mysqli, '0002', 'LastName', $UserId, $Val);

  $Val = addslashes ($Usr['FirstName']);
  SetParam ($mysqli, '0002', 'FirstName', $UserId, $Val);

  $Val = addslashes ($Usr['Position']);
  SetParam ($mysqli, '0002', 'Position', $UserId, $Val);

  $Val = addslashes ($Usr['PatronymicName__c']);
  SetParam ($mysqli, '0002', 'PATRONIMIC', $UserId, $Val);

  $Val = addslashes ($Usr['LastName'].' '.substr($Usr['FirstName'], 0, 1).'. '.substr($Usr['FirstName'],0, 1).'.');
  SetParam ($mysqli, '0002', 'FamilyIO', $UserId, $Val);

}
else {
  die ("<br> Error: Usr is not found $UserId ");
}

$LNK='';

  $V=$_REQUEST['UserId'];
  $LNK.="UserId=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=user_setup.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');
?>
</body>
</html>