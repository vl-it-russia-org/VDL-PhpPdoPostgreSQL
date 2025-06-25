<?php
session_start();

include ("../setup/common_pg.php");

BeginProc();
CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

//print_r($_REQUEST)
$Tab= $_REQUEST['Tab'];
$PdoArr = array();
$PdoArr['Tab']= $Tab;



$Res=array();

$TabCode='';
// AdmTabNames
// TabName, TabDescription, TabCode, TabEditable, 
// AutoCalc, CalcTableName, ChangeDt, Ver
$query = "select \"TabCode\", \"TabName\", \"TabDescription\" from \"AdmTabNames\" ". 
         "where (\"TabName\" =:Tab ) order by \"TabName\" "; 
try {
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
if ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $Res['TabCode']=$dp2['TabCode'];
  $TabCode=$Res['TabCode'];
}


$LANG = $_SESSION['LANG'];
if ($LANG=='') {
  $LANG='RU';
}

// AdmTabFields
// TypeId, ParamNo, ParamName, NeedSeria, 
// DocParamType, NeedBrand, Ord, AddParam, DocParamsUOM, 
// CalcFormula, AutoInc, Description, BinCollation, ShortInfo, 
// EnumLong
$PdoArr = array();
$PdoArr['TabCode']= $TabCode;
$query = "select \"ParamNo\", \"ParamName\" \"FldName\", \"DocParamType\", \"AddParam\" from \"AdmTabFields\" ". 
         "where (\"TypeId\" = :TabCode) order by \"Ord\""; 

$j=0;
$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $j++;

  $FldType = GetEnum($pdo, 'DocParamType', $dp2['DocParamType']);
  if ( ($dp2['DocParamType']==10) ) {
    $FldType.=' ['.$dp2['AddParam'].']';  
  }
  else
  if ( ($dp2['DocParamType']==20) ) {
    if ($dp2['AddParam']!= '') {  
      $FldType.=' ('.$dp2['AddParam'].')';  
    }
  }
  else
  if ( ($dp2['DocParamType']==50) or ($dp2['DocParamType']==55)) {
    $FldType.=' ( Enum: '.$dp2['AddParam'].")\r\n".
              " ===============================\r\n";
    
    $Enum=addslashes ($dp2['AddParam']);
    if ($Enum=='') {
      $Enum=$dp2['FldName'];
    }
    // EnumValues
    // EnumName, EnumVal, Lang, EnumDescription
    $PdoArr = array();
    $PdoArr['Enum']= $Enum;
    $PdoArr['LANG']= $LANG;

    $query = "select * from \"EnumValues\" ". 
             "where (\"EnumName\" = :Enum) and (\"Lang\"=:LANG) order by \"EnumVal\" "; 

    $STH22 = $pdo->prepare($query);
    $STH22->execute($PdoArr);

    
    while ($dp22 = $STH22->fetch(PDO::FETCH_ASSOC)) {
      $FldType.="{$dp22['EnumVal']} \t {$dp22['EnumDescription']}\r\n";   
    }
  }

  $dp2['FldType']=$FldType;
  $Res[$j]=$dp2;  


}
}
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

$Res['Count']=$j;

echo ( json_encode($Res));

?>