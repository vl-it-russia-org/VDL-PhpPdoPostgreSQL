<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

$FldNames=array('Code2','Code3','DigCode','CountryName');
$PkArr=array('Code2');
$New=$_REQUEST['New'];
$PdoArr = array();
$Code2=addslashes($_REQUEST['Code2']);
if ($Code2==''){ 
    if ($New==1) {  
      //$query = "select MAX(Code2) MX FROM Countries ".
      //         "WHERE ";
      //$sql2 = $pdo->query ($query)
      //               or die("Invalid query:<br>$query<br>" . $pdo->error);
      //$MX=0;
      //if ($dp = $sql2->fetch_assoc()) {
      //  $MX=$dp['MX'];
      //}
      //$MX++;
      //$_REQUEST['Code2']=$MX;
      //$Code2=$MX;
    }
    else { die ("<br> Error:  Empty Code2");}
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
    $Res = UpdateTable ($pdo, "Countries", $FldNames, $Arr, $PKArr, 1);

  if ($New==1){
    $q='insert into Countries(';
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
    
    $sql2 = $pdo->query ($q)
                 or die("Invalid query:<br>$q<br>" . $pdo->error);
}
  else {
    $q='update Countries set ';
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

$V=addslashes ($_REQUEST['OldCode2']);
      $S1.="(Code2='$V')";
  
      $q.= $S1;
      $sql2 = $pdo->query ($q)
                 or die("Invalid query:<br>$q<br>" . $pdo->error);
  
    }
  }
$LNK='';

  $V=$_REQUEST['Code2'];
  $LNK.="Code2=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=CountriesCard.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');
?>
</body>
</html>