<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($pdo, 'Fin');

$FldNames=array('ExpId','ExpenseName','HaveRegions','FinDiv');

if (empty ($_REQUEST["HaveRegions"])) {
  $_REQUEST["HaveRegions"]=0;
}
$PkArr=array('ExpId');
$New=$_REQUEST['New'];
$PdoArr = array();
$ExpId=$_REQUEST['ExpId'];
if ($ExpId==''){ 
    if ($New==1) {  
      //$query = "select MAX(ExpId) MX FROM Expenses ".
      //         "WHERE ";
      //$sql2 = $pdo->query ($query)
      //               or die("Invalid query:<br>$query<br>" . $pdo->error);
      //$MX=0;
      //if ($dp = $sql2->fetch_assoc()) {
      //  $MX=$dp['MX'];
      //}
      //$MX++;
      //$_REQUEST['ExpId']=$MX;
      //$ExpId=$MX;
    }
    else { die ("<br> Error:  Empty ExpId");}
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
  $Res = UpdateTable ($pdo, "Expenses", $FldNames, $_REQUEST, $PkArr, 1, "");
$LNK='';

  $V=$_REQUEST['ExpId'];
  $LNK.="ExpId=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=ExpensesCard.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');
?>
</body>
</html>