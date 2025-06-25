<?php
session_start();

include ("../setup/common.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($mysqli, 'ExtProj.Admin');

$FldNames=array('BIK','BankName','BankTransitAcc','City');
$New=addslashes($_REQUEST['New']);
$BIK=addslashes($_REQUEST['BIK']);
if ($BIK==''){ 
    if ($New==1) {  
      //$query = "select MAX(BIK) MX FROM CB_Banks ".
      //         "WHERE ";
      //$sql2 = $mysqli->query ($query)
      //               or die("Invalid query:<br>$query<br>" . $mysqli->error);
      //$MX=0;
      //if ($dp = $sql2->fetch_assoc()) {
      //  $MX=$dp['MX'];
      //}
      //$MX++;
      //$_REQUEST['BIK']=$MX;
      //$BIK=$MX;
    }
    else { die ("<br> Error:  Empty BIK");}
}


  //---------------------------- Для автонумерации ---------------
  //include ("NumSeq.php");
  //if($_REQUEST['DocNo']=='') {
  //  $D=$_REQUEST['OpDate'];
  //  if ($D=='') {
  //    $_REQUEST['OpDate']=date('Y-m-d');
  //    $D=$_REQUEST['OpDate'];
  //  }
  //  $_REQUEST['DocNo'] = GetNextNo ( $mysqli, 'BankOp', $D);
  //}


  $dp=array();
  $query = "select * ".
         "FROM CB_Banks ".
         " WHERE (BIK='$BIK')";
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
  
  if ($dp = $sql2->fetch_assoc()) {
    if ($New==1){
      echo ("<br>");
      print_r($dp);
      die ("<br> Error: Already have record ");
    }

    $Editable=1;
    if (!Editable) {
      die ("<br> Error: Not Editable record ");
    }      
  }
  
  if ($New==1){
    $q='insert into CB_Banks(';
    $S1='';
    $S2='';
    $Div='';

    foreach ($FldNames as $F) {
      $V=addslashes ($_REQUEST[$F]);
      $S1.=$Div.$F;
      $S2.="$Div'$V'";
      $Div=', ';
    }
    $q.=$S1.') values ('.$S2.')';
    
    $sql2 = $mysqli->query ($q)
                 or die("Invalid query:<br>$q<br>" . $mysqli->error);
}
  else {
    $q='update CB_Banks set ';
    $S1='';
    $Div='';

    foreach ($FldNames as $F) {
      $V1=$_REQUEST[$F];
      $V=addslashes ($_REQUEST[$F]);
      if ( $V1 != $dp[$F]) {
        $S1.=$Div.$F."='$V'";
        $Div=', ';
      }
    }
    if ( $S1 != '' ) {
      $q.=$S1.' WHERE ';
      
      $S1='';

$V=addslashes ($_REQUEST['OldBIK']);
      $S1.="(BIK='$V')";
  
      $q.= $S1;
      $sql2 = $mysqli->query ($q)
                 or die("Invalid query:<br>$q<br>" . $mysqli->error);
  
    }
  }
$LNK='';

  $V=$_REQUEST['BIK'];
  $LNK.="BIK=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=CB_BanksCard.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');
?>
</body>
</html>