<?php
session_start();

include ("../setup/common.php");
BeginProc();
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>usrs Change user</title></head>
<body>
<?php
// Checklogin1();
CheckRight1 ($mysqli, 'ExtProj.Admin');

$UsrId= addslashes($_REQUEST['UsrId']);
if (empty ($UsrId)) {
  die ("<br> Error: UserId is empty");
}


// usrs
// usr_id, usr_pwd, description, admin, 
// email, phone, passwd_duedate, new_passwd, passwd_last_change, 
// SFUser, Blocked, WebCookie, Position, Department, 
// Company, FirstName, LastName, PatronymicName__c, PwdCoded
$query = "select * from usrs ". 
         "where (usr_id = '$UsrId')"; 

$sql2 = $mysqli->query ($query) 
          or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
if ($dp2 = $sql2->fetch_assoc()) {
  echo ("<br> Have User: {$dp2['usr_id']} {$dp2['description']} ");

  echo ("<form method='post' action='usrs-ChangeUser.php'>
   <input type=hidden Name=UsrId value='{$UsrId}'>
   <table border='0'>
   <tr>
   </tr>
   <tr>
   </tr>");


  echo ("<tr>
      <td align=right><input type='submit' value='Run as user $UsrId'></td>
    </tr> 
    </table>
 </form>");
}
  //---------------------------------------------------------------------------------  
?>
</body></html>