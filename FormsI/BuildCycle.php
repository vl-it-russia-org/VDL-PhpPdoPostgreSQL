<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();

CheckRight1 ($pdo, 'Admin');

if (empty ($_REQUEST['TabNo']) ) {
  die ("<br>Bad TabNo ");
}

$TabNo=$_REQUEST['TabNo']; 

$TabName1='AdmTabIndx';


$query = "select \"TabName\" from \"AdmTabNames\" ".
         "where (\"TabCode\"=:TabNo)";

$PdoArr = array();
$PdoArr['TabNo']= $TabNo;
         
try {
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);


$TabName='';
if ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $TabName=$dp2['TabName'];    
}
else {
  die ("<br> Error: Bad Table Name ");
}

//-----------------------------------------------------------------------
// \"AdmTabIndx\"
// \"TabCode\", \"IndxType\", \"IndxName\"

// \"AdmTabIndxFlds\"
// \"TabCode\", \"IndxName\", \"LineNo\", \"FldNo\", 
// \"Ord\"
//-------------------------------
// $ArrAll = array ('TabCode', 'IndxType', 'IndxName');
// $ArrDig = array ('TabCode', 'IndxType');


$PKArrFlds=array();
$query = "select \"FldNo\" from \"AdmTabIndx\" I, \"AdmTabIndxFlds\" F ". 
         "where (I.\"TabCode\" = :TabNo) and (I.\"IndxType\"=10) and ".
         "(I.\"TabCode\" =F.\"TabCode\" ) and (I.\"IndxName\"=F.\"IndxName\") order by F.\"Ord\", F.\"FldNo\""; 

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $PKArrFlds[$dp2['FldNo']]=1;  
}



//-----------------------------------------------------------------------
$query = "select * from \"AdmTabFields\" ".
         "where (\"TypeId\"=:TabNo) order by \"Ord\", \"ParamNo\"";
  
$STH = $pdo->prepare($query);
$STH->execute($PdoArr);


$Fields=array();
$F2=array();

$Str="// \\\"$TabName\\\"\r\n// ";

$Div='';

$SN='';
$SNR='';
$DivSN='';
$SNArr=array();
$Sp='';
$ii=0;

$ArrDig='';
$DivDig='';
$DigI=0;


$ArrAll='';
$DivAll='';
$AllI=0;

$ArrPK='';
$PkPdoArr='';
$DivPK='';
$PKI=0;

$ArrEn = array ();



while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $ii++;
  if ($ii>4) {
    $ii=0;
    $Sp=", \r\n// ";
  }

  $FldNo =$dp2['ParamNo']; 
  
  $Fields[$dp2['ParamName']]=$dp2;    
  $F2[$dp2['ParamNo']]=$dp2['ParamName'];
  
  $Str.=$Sp.'\"'.$dp2['ParamName'].'\"';
  $Sp=', ';

  $Type='';
  if ( ($dp2['DocParamType']==50) OR ($dp2['DocParamType']==55)) {
    $ArrEn[$dp2['ParamName']] = $dp2['AddParam']; 
  }


  
  if ( ($dp2['DocParamType']==20) OR ($dp2['DocParamType']==50)) {
    if (! $dp2['AutoInc']) {
      $ArrDig.="$DivDig'{$dp2['ParamName']}'";
      $DivDig=', ';
      $DigI++;
      if ( $DigI>3 ) {
        $DigI=0;
        $ArrDig.="\r\n//        ";
      }
    }
  }

  if (! empty ($PKArrFlds[$FldNo])) {
    $ArrPK.="$DivPK(\\\"{$dp2['ParamName']}\\\"=:{$dp2['ParamName']})";
    $DivPK='and';
    $PkPdoArr.='// $PdoArr["'.$dp2['ParamName'].'"]= $'.$dp2['ParamName'].";\r\n";
  }

  $ArrAll.="$DivAll'{$dp2['ParamName']}'";
  $DivAll=', ';
  $AllI++;
  if ( $AllI>3 ) {
    $AllI=0;
    $ArrAll.="\r\n//        ";
  }
                  

  if ($dp2['ShortInfo']) {
    $SN.="{$DivSN}{$dp2['ParamName']}";
    $DivSN=', ';
    
    $Arr['Name']=$dp2['ParamName'];
    $Arr['Type']=$dp2['DocParamType'];
    $Arr['AddParam']=$dp2['AddParam'];

    $SNArr[]=$Arr;
  }
}

//=============================================================
// Out Enum


//$SEn=sort($ArrEn);
$PrevVal = '';

$EnFldList='';
$EnumVals='';
$EnumValsDiv='';
$EnDiv='';


foreach ($ArrEn as $FldName=>$EnName) {
  if ($PrevVal != $EnName) {
    if ($PrevVal != '') {
      $Str.="\r\n//-------------------------------\r\n".
      $Str.="\r\n// Enum: $EnFldList [ $PrevVal ] : $EnumVals\r\n//-------------------------------\r\n".
      
      $EnFldList='';
      $EnumVals='';
      $EnumValsDiv='';
      $EnDiv='';


    }
    $PrevVal = $EnName;

    // \"EnumValues\"
    // \"EnumName\", \"EnumVal\", \"Lang\", \"EnumDescription\"
    //-------------------------------
    // (\"EnumName\"=:EnumName)and(\"EnumVal\"=:EnumVal)and(\"Lang\"=:Lang)
    // $PdoArr["EnumName"]= $EnumName;
    // $PdoArr["EnumVal"]= $EnumVal;
    // $PdoArr["Lang"]= $Lang;

    // $ArrAll = array ('EnumName', 'EnumVal', 'Lang', 'EnumDescription'
    //        );

    // $ArrDig = array ('EnumVal');

    $PdoArr = array();
    $PdoArr["EnumName"]= $EnName;
    $PdoArr["Lang"]= "EN";

    $query = "select * from \"EnumValues\" ". 
             "where (\"EnumName\"=:EnumName)and(\"Lang\"=:Lang) order by \"EnumVal\""; 

    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);


    $ii=0;
    while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
      $ii++;
      if ($ii>3) {
        $EnumVals.="\r\n//          ";
        $ii=0;
      }

      $EnumVals.="$EnumValsDiv". '['.$dp2['EnumVal'].'] '.$dp2['EnumDescription'];
      $EnumValsDiv=', ';
    }
  }

  $EnFldList.="$EnDiv$FldName";
  $EnDiv=', ';
} 

if ($PrevVal != '') {
  $Str.="\r\n//-------------------------------\r\n".
  $Str.="\r\n// Enum: $EnFldList [ $PrevVal ] : $EnumVals\r\n//-------------------------------\r\n";
}      


//-------------------------------------------------------------


$Str.="\r\n//-------------------------------\r\n".
      '// '.$ArrPK."\r\n$PkPdoArr\r\n".
      '// $ArrAll = array ('.$ArrAll.');'."\r\n"; 

if ($ArrDig!='') {
  $Str.="\r\n".'// $ArrDig = array ('.$ArrDig.');'."\r\n"; 
}

$Str.="\r\n".
  '$PdoArr = array();'."\r\n".
  '$PdoArr[\'\']= $;'. "\r\n\r\n".
  'try {'."\r\n".
  '  $query = "select * from \"'.$TabName.'\" ". '."\r\n".
      '           "where ( = ) order by "; '."\r\n"."\r\n".
  '  $STH = $pdo->prepare($query);'."\r\n".
  '  $STH->execute($PdoArr);'."\r\n\r\n".
  '  while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {'."\r\n".
  '  }'."\r\n";

$Str.="\r\n".
  '}'."\r\n".
  'catch (PDOException $e) {'."\r\n".
  '  echo ("<hr> Line ".__LINE__."<br>");'."\r\n".
  '  echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");'."\r\n".
  '  print_r($PdoArr);'."\r\n".	
  '  die ("<br> Error: ".$e->getMessage());'."\r\n".
  '}'."\r\n";


$Dat=date("Ymd-His");
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: text/html');
header('Content-Disposition: attachment;filename=Cycle_'.$TabName.'-'.$Dat.'.txt');

echo ($Str);


//================================================================
}
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

?>