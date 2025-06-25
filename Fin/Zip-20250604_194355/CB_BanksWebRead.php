<?php
session_start();

include ("../setup/common.php");
BeginProc();
//CheckLogin1 ();
//CheckRight1 ($mysqli, 'ExtProj.Admin');

$FldNames=array('BIK','BankName','BankTransitAcc','City');
$BIK=addslashes($_REQUEST['BIK']);

if ($BIK==''){ 
  die ("<br> Error:  Empty BIK");
}


$Buf= file_get_contents ( "https://bik-info.ru/api.html?type=json&bik=$BIK");
if ($Buf=='') {
  die ("<br> Bad request to bik-info.ru/api.html?type=json&bik=$BIK");
}


$ArrVal=json_decode ($Buf,1); 
$Res='Not Read';

if (is_array($ArrVal) ) {

  $ArrVal['namemini'].= ', г. '.$ArrVal['city'];

$dp=array();
$query = "select * FROM CB_Banks ".
         "WHERE (BIK='$BIK')";
$sql2 = $mysqli->query ($query)
               or die("Invalid query:<br>$query<br>" . $mysqli->error);

$FldArr = array ('BankName'=>'namemini', 'BankTransitAcc'=>'ks', 'City'=>'city');


$Res='No change ';

if ($dp = $sql2->fetch_assoc()) {
  
  $S="";
  $Div="";

  foreach ($FldArr as $F1=> $F2) {
    if ( $dp[$F1]!= $ArrVal[$F2]) {
      $V=addslashes ($ArrVal[$F2]);
      $S.= "$Div$F1='$V'";
      $Res.=$Div.GetStr($mysqli, $F1);

      $Div=', ';
      
    }

    if ($S!= ''){
    
      $query = "update CB_Banks set $S ".
               "WHERE (BIK='$BIK')";
      $sql2 = $mysqli->query ($query)
                   or die("Invalid query:<br>$query<br>" . $mysqli->error);
  
      $Res.= ' changed';
    }
  }
}
else {
  $S1="BIK";
  $S2="'$BIK'";

  foreach ($FldArr as $F1=> $F2) {
    $S1.= ", $F1";
    $V=addslashes ($ArrVal[$F2]);
    $S2.= ", '$V'";
  }


  $query = "insert into CB_Banks ($S1) ".
           "values ($S2)";
  $sql5 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
  $Res= 'Inserted ';
}
}

$LNK='';

  $LNK.="BIK=$BIK";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=CB_BanksCard.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
  echo("<H2>$Res</H2>");
?>
</body>
</html>