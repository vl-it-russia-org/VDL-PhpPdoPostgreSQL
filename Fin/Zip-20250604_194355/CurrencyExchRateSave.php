<?php
session_start();

include ("../setup/common.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($mysqli, 'ExtProj.Admin');

$FldNames=array('CurrencyCode','StartDate','Multy','Rate'
      ,'FullRate');
$New=addslashes($_REQUEST['New']);
$CurrencyCode=addslashes($_REQUEST['CurrencyCode']);
if ($CurrencyCode==''){ die ("<br> Error:  Empty CurrencyCode");}
$StartDate=addslashes($_REQUEST['StartDate']);
if ($StartDate==''){ 
    if ($New==1) {  
      $query = "select MAX(StartDate) MX FROM CurrencyExchRate ".
               "WHERE (CurrencyCode='$CurrencyCode')";
      $sql2 = $mysqli->query ($query)
                     or die("Invalid query:<br>$query<br>" . $mysqli->error);
      $MX=0;
      if ($dp = $sql2->fetch_assoc()) {
        $MX=$dp['MX'];
      }
      $MX++;
      $_REQUEST['StartDate']=$MX;
      $StartDate=$MX;
    }
    else { die ("<br> Error:  Empty StartDate");}
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
         "FROM CurrencyExchRate ".
         " WHERE (CurrencyCode='$CurrencyCode') AND (StartDate='$StartDate')";
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
    $q='insert into CurrencyExchRate(';
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
    $q='update CurrencyExchRate set ';
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

$V=addslashes ($_REQUEST['OldCurrencyCode']);
      $S1.="(CurrencyCode='$V')";
  
$V=addslashes ($_REQUEST['OldStartDate']);
      $S1.=" and (StartDate='$V')";
  
      $q.= $S1;
      $sql2 = $mysqli->query ($q)
                 or die("Invalid query:<br>$q<br>" . $mysqli->error);
  
    }
  }
$LNK='';

  $V=$_REQUEST['CurrencyCode'];
  $LNK.="CurrencyCode=$V";
  
  $V=$_REQUEST['StartDate'];
  $LNK.="&StartDate=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=CurrencyExchRateCard.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');
?>
</body>
</html>