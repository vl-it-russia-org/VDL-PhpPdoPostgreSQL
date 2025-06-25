<?php
session_start();

include ("../setup/common.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($mysqli, 'ExtProj.Admin');

 $FldNames=array('usr_id','usr_pwd','description'
      ,'admin','email','phone','passwd_duedate'
      ,'new_passwd','passwd_last_change','SFUser','Blocked'
      ,'WebCookie','Position','Department','Company'
      ,'FirstName','LastName','PatronymicName__c','PwdCoded');
$New=addslashes($_REQUEST['New']);
$usr_id=addslashes($_REQUEST['usr_id']);
if ($usr_id==''){ die ("<br> Error:  Empty usr_id");}

$dp=array();
  
  $query = "select * ".
         "FROM usrs ".
         " WHERE (usr_id='$usr_id')";
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
  
  if ($dp = $sql2->fetch_assoc()) {
  }
  else {
    die ("<br> Error: not found record (usr_id='$usr_id')"); 
  }
  $Editable=1;
  if (!$Editable) {
    die ("<br> Error: Not Editable record ");
  }
  
  $query = "delete ".
         "FROM usrs ".
         " WHERE (usr_id='$usr_id')";
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);  
$LNK='';

  $V=$_REQUEST['usr_id'];
  $LNK.="usr_id=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=usrsList.php">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Deleted</H2>');
?>
</body>
</html>