<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();

CheckRight1 ($pdo, 'Task');

$FldNames=array('Id','ParentId','ToDoCode','Description'
      ,'DateBeg','DateEnd','Status');

if (empty ($_REQUEST["ParentId"])) {
  $_REQUEST["ParentId"]=0;
}

if (empty ($_REQUEST["DateBeg"])) {
  $_REQUEST["DateBeg"]="1900-01-01";
}

if (empty ($_REQUEST["DateEnd"])) {
  $_REQUEST["DateEnd"]="1900-01-01";
}
$PkArr=array('Id');
$New=$_REQUEST['New'];
$PdoArr = array();
$Id=$_REQUEST['Id'];
if ($Id==''){ 
    if ($New==1) {  
      //$query = "select MAX(Id) MX FROM ToDo_Line ".
      //         "WHERE ";
      //$sql2 = $pdo->query ($query)
      //               or die("Invalid query:<br>$query<br>" . $pdo->error);
      //$MX=0;
      //if ($dp = $sql2->fetch_assoc()) {
      //  $MX=$dp['MX'];
      //}
      //$MX++;
      //$_REQUEST['Id']=$MX;
      //$Id=$MX;
    }
    else { die ("<br> Error:  Empty Id");}
}


  //---------------------------- Для автонумерации ---------------
  //include ("NumSeq.php");
  //if($_REQUEST['DocNo']=='') {
  //  $D=$_REQUEST['OpDate'];
  //  if ($D=='') {
  //    $_REQUEST['OpDate']=date('Y-m-d');
  //    $D=$_REQUEST['OpDate'];
  //  }
  //  $_REQUEST['DocNo'] = GetNextNo ( $pdo, 'BankOp', $D);
  //}


  $dp=array();
  $Res = UpdateTable ($pdo, "ToDo_Line", $FldNames, $_REQUEST, $PkArr, 1, "Id");
$LNK='';

  $V=$_REQUEST['Id'];
  $LNK.="Id=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=ToDo_LineCard.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');
?>
</body>
</html>