<?php
$file = fopen("../Forms/{$TabName}Save.php","w");

fwrite($file,"<?php\r\n");
fwrite($file,"session_start();\r\n");

$S= '
include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();'.
"\r\n";
fwrite($file,$S);

$S= "CheckRight1 (\$pdo, 'Admin');\r\n\r\n".
'$FldNames=array(';
$Div='';


$R=0;


$DigArr='';
$DivDigArr='';

$DateArr='';
$DivDateArr='';

$AutoIncFld='';

foreach ($Fields as $Fld=>$Arr) {
  $R++;
  if ($R>4) {
    $R=0;
    $S.="\r\n      ";
  }

  $S.="$Div'$Fld'";
  $Div=',';
  
  //echo (" Fld:$Fld ");
  echo ("<hr>$Fld");
  print_r($Arr);
  echo ("<hr>");

}
$S.=");\r\n";

foreach ($Fields as $Fld=>$Arr) {
  if ($Arr['AutoInc']==0) {
    if ( ($Arr['DocParamType']==20) OR ($Arr['DocParamType']==30)) {       //    20	  Число    30	  Да/Нет
      $S.="\r\n".'if (empty ($_REQUEST["'.$Fld.'"])) {'.
          "\r\n".'  $_REQUEST["'.$Fld.'"]=0;'.
          "\r\n}\r\n";
    }
  }
  else {
    $AutoIncFld=$Fld;
  }

  if ( $Arr['DocParamType']==60){     //    60	  Дата
    $S.="\r\n".'if (empty ($_REQUEST["'.$Fld.'"])) {'.
        "\r\n".'  $_REQUEST["'.$Fld.'"]="1900-01-01";'.
        "\r\n}\r\n";
  }


}


$PKFldsList='';
$PkDiv='';
foreach ($PKFields as $PK) {
  $PKFldsList.="$PkDiv'$PK'";
  $PkDiv=', ';
}

$S.='$PkArr=array('.$PKFldsList.");\r\n";


$WH='';
$DW='';

$S.='$New=$_REQUEST[\'New\'];'."\r\n";

//====================================================================================
// Field can be filled by NumberSequence

// AdmFieldsAddFunc
// Id, TabName, FldName, AddFunc
$FldNumSeq = array();
$query = "select \"FldName\", \"Param\" from \"AdmFieldsAddFunc\" ". 
         "where (\"TabName\"=:TabName) and (\"AddFunc\" = 30) order by \"FldName\" "; 

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $FldNumSeq[$dp2['FldName']] = $dp2['Param'];
}

//====================================================================================
 

$IdAuto=0;
$NSInclude=0;

$S.='$PdoArr = array();'."\r\n";  


foreach ($PKFields as $PK) {  
  $S.='$'.$PK.'=$_REQUEST[\''.$PK."'];\r\n";  
  
  if ( ! empty($FldNumSeq[$PK])) {
   $NumSeq=GetNumberSeq ($FldNumSeq[$PK]);
   if ($NSInclude==0) {
     $S.= 'include ("NumSeq.php");'."\r\n";
     $NSInclude=1;
   }
   
   $S.='if ($'.$PK.'==\'\'){ 
  if ($New==1) {
    $'.$PK.'= GetNextNo ( $pdo, \''.$NumSeq.'\');  
    $_REQUEST[\''.$PK.'\']=$'.$PK.';
  }
  else {
    die ("<br> Error:  Empty '.$PK.'");
  }
}'."\r\n";
       
  
  }
  else{
  
  if ($PK != $LastPK) {
    $S.='if ($'.$PK.'==\'\'){ die ("<br> Error:  Empty '.$PK.'");}'."\r\n";
    $S.='$PdoArr[\''.$PK.'\']= $'.$PK.';'."\r\n";
  }
  else {
    $S.='if ($'.$PK.'==\'\'){ 
    if ($New==1) {';

    if ($WH !='') {  
      $S.='  
      $query = "select MAX(\"'.$PK.'\") \"MX\" FROM \"'.$TabName.'\" ".
               "WHERE '.$WH.'";
      
      $sql2 = $pdo->query ($query)
                     or die("Invalid query:<br>$query<br>" . $pdo->error);
      $MX=0;
      if ($dp = $sql2->fetch_assoc()) {
        $MX=$dp[\'MX\'];
      }
      $MX++;
      $_REQUEST[\''.$PK.'\']=$MX;
      $'.$PK.'=$MX;
    }';
    }
    else {
      $S.='  
      //$query = "select MAX('.$PK.') MX FROM '.$TabName.' ".
      //         "WHERE '.$WH.'";
      //$sql2 = $pdo->query ($query)
      //               or die("Invalid query:<br>$query<br>" . $pdo->error);
      //$MX=0;
      //if ($dp = $sql2->fetch_assoc()) {
      //  $MX=$dp[\'MX\'];
      //}
      //$MX++;
      //$_REQUEST[\''.$PK.'\']=$MX;
      //$'.$PK.'=$MX;
    }';
    
    }
    $S.='
    else { die ("<br> Error:  Empty '.$PK.'");}
}
'."\r\n";  
  }
  }
  $WH.= $DW. '(\"'.$PK."\\\"=:".$PK.')';
  $DW=' AND ';

};

$S.='
  //---------------------------- Для автонумерации ---------------
  //include ("NumSeq.php");
  //if($_REQUEST[\'DocNo\']==\'\') {
  //  $D=$_REQUEST[\'OpDate\'];
  //  if ($D==\'\') {
  //    $_REQUEST[\'OpDate\']=date(\'Y-m-d\');
  //    $D=$_REQUEST[\'OpDate\'];
  //  }
  //  $_REQUEST[\'DocNo\'] = GetNextNo ( $pdo, \'BankOp\', $D);
  //}

';


$S.="\r\n".
 '  $dp=array();
  $Res = UpdateTable ($pdo, "'.$TabName.'", $FldNames, $_REQUEST, $PkArr, 1, "'.$AutoIncFld.'");
';

//===============================================================================
// Have master table

$MasterTab = GetMasterTabName($pdo, $TabName);
$MasterFldsCorr = array();                      
if ( $MasterTab != '') {
  $Param = GetParam_TabAddFunc($pdo, $TabName, 20);
  if ( $Param != '') {
    echo ("<br> MasterTab: $MasterTab $Param <br>");
    $MasterFldsCorr1 = GetMasterCorrFields ($pdo, $Param);
    
    echo ("<br> Flds Corr: ");
    print_r($MasterFldsCorr1);
    echo ("<br>");

    foreach ( $MasterFldsCorr1 as $TypeFld=> $Arr3) {
      foreach ($Arr3 as $Fld1=>$Indx) {
        $MasterFldsCorr[$Indx][$TypeFld]=$Fld1;        
      }
    }

    print_r($MasterFldsCorr);

    $S.="//------------- Master Tab --------------------------\r\n".
'  
$MTab=array();

$query = "select * FROM '.$MasterTab.' ".
         "WHERE ';

$MDiv='';

foreach ( $MasterFldsCorr as $Indx=>$ArrT) {
  $S.= $MDiv.'('.$ArrT['FldM'].'=\'$'.$ArrT['FldT'].'\')'; 
  $MDiv=' and ';
}

$S.='"; 
  $sql22 = $pdo->query ($query)
                 or die("Invalid query:<br>$query<br>" . $pdo->error);
  
  if ($MTab = $sql22->fetch_assoc()) {
  }
';

    $Param30=GetParam_TabAddFunc($pdo, $TabName, 30);  
    
    $MCopyFlds1 = GetMasterCorrFields ($pdo, $Param30);
    $MCopyFlds = array();
    foreach ( $MCopyFlds1  as $TypeFld=> $Arr3) {
      foreach ($Arr3 as $Fld1=>$Indx) {
        $MCopyFlds[$Indx][$TypeFld]=$Fld1;        
      }
    }

    echo ("<br> MCopyFlds: ");
    print_r($MCopyFlds);
    
    foreach ( $MCopyFlds as $Indx=>$Arr5) {
      $S='$_REQUEST[\''.$$Arr5['FldT'].'\']= $MTab[\''.$$Arr5['FldM'].'\']; 
';
        
    }
  }

}

//===============================================================================  
/*
$S.='
  if ($New==1){
    $q=\'insert into '.$TabName.'(\';
    $S1=\'\';
    $S2=\'\';
    $Div=\'\';

    foreach ($FldNames as $F) {
      $V=addslashes ($_REQUEST[$F]);
      $S1.=$Div.$F;
      $S2.="$Div\'$V\'";
      $Div=\', \';
    }
    $q.=$S1.\') values (\'.$S2.\')\';
    
    $sql2 = $pdo->query ($q)
                 or die("Invalid query:<br>$q<br>" . $pdo->error);'."\r\n"  ;

  foreach ($AutoIncArr as $AIFld) {
    $S.='    $'.$AIFld.'= $pdo->insert_id ;
    $_REQUEST[\''.$AIFld.'\']= $'.$AIFld.';'."\r\n";
  }

  $S.='}
  else {
    $q=\'update '.$TabName.' set \';
    $S1=\'\';
    $Div=\'\';

    foreach ($FldNames as $F) {
      $V1=$_REQUEST[$F];
      $V=addslashes ($_REQUEST[$F]);
      if ( $V1 != $dp[$F]) {
        $S1.=$Div.$F."=\'$V\'";
        $Div=\', \';
      }
    }
    if ( $S1 != \'\' ) {
      $q.=$S1.\' WHERE \';
      
      $S1=\'\';
';
$Div='';
$L='';
$DL='';
foreach ($PKFields as $PK) {
  $S.="\r\n".'$V=addslashes ($_REQUEST[\'Old'.$PK.'\']);
      $S1.="'.$Div.'('.$PK.'=\'$V\')";
  ';
  $Div=' and ';
}

$S.='
      $q.= $S1;
      $sql2 = $pdo->query ($q)
                 or die("Invalid query:<br>$q<br>" . $pdo->error);
  
    }
  }
';
*/
  
fwrite($file,$S);

$S='$LNK=\'\';
';


if ($MasterTab != '') {
//================================== IF HAVE MASTER TAB -- after save open Master Card

//print_r($MasterFldsCorr);

$DL='';
$N=0;
foreach ($MasterFldsCorr as $Indx=>$ArrM) {
  $N++;
  $S.= '
  $V=$_REQUEST[\''.$ArrM['FldT'].'\'];
  $LNK.="'.$DL.$ArrM['FldM'].'=$V";
  ';
  $DL='&';
}

$S.="\r\n".

'echo (\'<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">\'.
\'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$MasterTab.'Card.php?\'.$LNK.\'">\'.
\'<title>Save</title></head>
<body>\');
  
  echo(\'<H2>Saved</H2>\');
?>
</body>
</html>';


}
else {

$DL='';
$N=0;
foreach ($PKFields as $PK) {
  $N++;
  $S.= '
  $V=$_REQUEST[\''.$PK.'\'];
  $LNK.="'.$DL.$PK.'=$V";
  ';
  $DL='&';
}

$S.="\r\n".

'echo (\'<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">\'.
\'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$TabName.'Card.php?\'.$LNK.\'">\'.
\'<title>Save</title></head>
<body>\');
  
  echo(\'<H2>Saved</H2>\');
?>
</body>
</html>';

}

fwrite($file,$S);

fclose($file);
?>