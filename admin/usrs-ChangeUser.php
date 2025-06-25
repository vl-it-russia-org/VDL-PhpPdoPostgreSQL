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
<title>Upload xlsx to usrs</title></head>
<body>
<?php
echo '<br>User: ' . $_SESSION['login'].'<br>';

//print_r($_FILES);
//echo ("<br>");

//print_r($_REQUEST);
//echo ("<br>");

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include "common_func.php";


CheckRight1 ($mysqli, 'ExtProj.Admin');

$FileName='usrs';


//=============================================================================================

// Запоминаем параметры (в какой колонке хранятся)
echo("<hr><h4>Run as user</h4>");


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
  
    
  MakeAdminRec ($mysqli, $_SESSION['login'], 'USR', $UsrId, 
                      'RunAs', 'Run session as user '.$UsrId);
  

  session_unset();
  session_destroy();

  echo ("<hr> New session  as $UsrId");
  session_start();
  $_SESSION['login']=$UsrId;
  $_SESSION['DEBUG']=1;
  echo (" <a href='../adv/'>ADV</a> ");


}


?>
</body></html>