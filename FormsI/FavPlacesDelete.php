<?php
session_start();

include ("../setup/common.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($mysqli, 'ExtProj.Admin');

 $FldNames=array('UserId','DirNo','ShortName'
      ,'Tip','DirName','Ord');
$New=addslashes($_REQUEST['New']);
$UserId=addslashes($_REQUEST['UserId']);
if ($UserId==''){ die ("<br> Error:  Empty UserId");}
$DirNo=addslashes($_REQUEST['DirNo']);
if ($DirNo==''){ die ("<br> Error:  Empty DirNo");}

$dp=array();
  
  $query = "select * ".
         "FROM FavPlaces ".
         " WHERE (UserId='$UserId') AND (DirNo='$DirNo')";
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
  
  if ($dp = $sql2->fetch_assoc()) {
  }
  else {
    die ("<br> Error: not found record (UserId='$UserId') AND (DirNo='$DirNo')"); 
  }
  $Editable=1;
  if (!Editable) {
    die ("<br> Error: Not Editable record ");
  }
  
  $query = "delete ".
         "FROM FavPlaces ".
         " WHERE (UserId='$UserId') AND (DirNo='$DirNo')";
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);  
$LNK='';

  $V=$_REQUEST['UserId'];
  $LNK.="UserId=$V";
  
  $V=$_REQUEST['DirNo'];
  $LNK.="&DirNo=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=FavPlacesList.php">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Deleted</H2>');
?>
</body>
</html>