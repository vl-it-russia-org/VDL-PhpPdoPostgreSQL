<?php
session_start();

include ("../setup/common.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($mysqli, 'ExtProj.Admin');

 $FldNames=array('Id','UserName','Region'
      ,'FirstName','LastName','Email','IsActive'
      ,'UserRoleId','ManagerId','LastLoginDate','LegrandRegion__c'
      ,'EnglishName__c','Firm__c','SalesTeam__c','PatronymicName__c'
      ,'UnId','Title','CompanyName','Department'
      ,'Phone');
$New=addslashes($_REQUEST['New']);
$UnId=addslashes($_REQUEST['UnId']);
if ($UnId==''){ die ("<br> Error:  Empty UnId");}

$dp=array();
  
  $query = "select * ".
         "FROM SF_User ".
         " WHERE (UnId='$UnId')";
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
  
  if ($dp = $sql2->fetch_assoc()) {
  }
  else {
    die ("<br> Error: not found record (UnId='$UnId')"); 
  }
  $Editable=1;
  if (!$Editable) {
    die ("<br> Error: Not Editable record ");
  }
  
  $query = "delete ".
         "FROM SF_User ".
         " WHERE (UnId='$UnId')";
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);  
$LNK='';

  $V=$_REQUEST['UnId'];
  $LNK.="UnId=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=SF_UserList.php">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Deleted</H2>');
?>
</body>
</html>