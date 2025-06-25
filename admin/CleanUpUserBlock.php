<?php
session_start();

include ("../setup/common.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($mysqli, 'ExtProj.Admin');

$FldNames=array('transf_id','op_date','code','param1'
                ,'param2','description','user_id');

$UserId=addslashes($_REQUEST['UserId']);
if ($UserId==''){ 
  die ("<br> Error:  Empty UserId");
}

$Dt = date('Y-m-d');

$query = "update admin_protocol set code='BadPass-cln' ".
         " WHERE (code='BadPass') and (Param1='$UserId') and ".
                "(op_date>DATE_SUB(NOW(), INTERVAL 20 MINUTE))";

$sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);

$row_cnt = $sql2->num_rows;

echo ("<br> updated  $row_cnt rows bad logins");                 
  
$LNK='';

  $V=$UserId;
  $LNK.="usr_id=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=usrsCard.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');
?>
</body>
</html>