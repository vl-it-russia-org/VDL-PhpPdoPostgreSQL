<?php
session_start();

include ("../setup/common.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($mysqli, 'ExtProj.Admin');

 $FldNames=array('Id','MenuName','Description'
      ,'Link','NewWindow','ParentId','Ord');
$New=addslashes($_REQUEST['New']);
$Id=addslashes($_REQUEST['Id']);
if ($Id==''){ die ("<br> Error:  Empty Id");}

$dp=array();
  
  $query = "select * ".
         "FROM Menu ".
         " WHERE (Id='$Id')";
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
  
  if ($dp = $sql2->fetch_assoc()) {
  }
  else {
    die ("<br> Error: not found record (Id='$Id')"); 
  }
  $Editable=1;
  if (!Editable) {
    die ("<br> Error: Not Editable record ");
  }
  
  $query = "delete ".
         "FROM Menu ".
         " WHERE (Id='$Id')";
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);  
$LNK='';

  $V=$_REQUEST['Id'];
  $LNK.="Id=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=MenuList.php">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Deleted</H2>');
?>
</body>
</html>