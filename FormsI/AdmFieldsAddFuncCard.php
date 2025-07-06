<?php
session_start();

include ("../setup/common_pg.php");
include ("common_func.php");
BeginProc();
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>AdmFieldsAddFunc Card</title></head>
<body>
<?php
// Checklogin1();
include ("../js_SelAll.js");


CheckRight1 ($pdo, 'Admin');

$FldNames=array('Id','TabName','FldName','AddFunc');

$enFields= array('AddFunc'=>'FldAddFunction');

$Id=$_REQUEST['Id'];
echo("<H3>".GetStr($pdo, 'AdmFieldsAddFunc')."</H3>");
  $dp=array();
  $FullLink="Id=$Id";

$PdoArr = array();
$PdoArr['Id']= $Id;

try{  
  
  if (!empty ($Id)) {
  $query = "select * FROM \"AdmFieldsAddFunc\" ".
           "WHERE (\"Id\"=:Id)";
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
      
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  
  }
  }
  $New=$_REQUEST['New'];

  if ($New==1) {
    $dp['TabName']= $_REQUEST['TabName'];
    $dp['FldName']= $_REQUEST['FldName'];
  }




// AdmTabNames
// TabName, TabDescription, TabCode, TabEditable, 
// AutoCalc, CalcTableName, ChangeDt, Ver
$TabCode='';
$TN= $dp['TabName'];

$PdoArr = array();
$PdoArr['TN']= $TN;

$query = "select * from \"AdmTabNames\" ". 
         "where (\"TabName\" = :TN)"; 

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

if ($dp22 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $TabCode=$dp22['TabCode'];
  echo ("<br> Table: <b>$TabCode</b> <b>{$dp22['TabName']}</b> {$dp22['TabDescription']}");
}


// AdmTabFields
// TypeId, ParamNo, ParamName, NeedSeria, 
// DocParamType, NeedBrand, Ord, AddParam, DocParamsUOM, 
// CalcFormula, AutoInc, Description, BinCollation, ShortInfo, 
// EnumLong
$FldCode='';
$FN= $dp['FldName'];

$PdoArr = array();
$PdoArr['TabCode']= $TabCode;
$PdoArr['FN']= $FN;

$query = "select * from \"AdmTabFields\" ". 
         "where (\"TypeId\"=:TabCode)and (\"ParamName\"= :FN) "; 

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

if ($dp22 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $FldCode=$dp22['ParamNo'];
  echo ("<br> Field: <b>$FldCode</b> <b>{$dp22['ParamName']}</b><hr>");

}


$Editable=1;
if ($Editable) {
  echo ('<form method=get action="AdmFieldsAddFuncSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");
  $LN=0;
  $Fld='Id';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldId' value='$OutVal'>");
  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$Id}' size=10 readonly>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='TabName';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='FldName';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='AddFunc';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ( EnumSelection($pdo, "FldAddFunction", "AddFunc ID='$Fld' ", $OutVal));
  echo(" $OutVal </td>");
  echo ("</tr><tr>");

  //============================================================================
  if ($OutVal==10) {
    // SelectionArr ($Name, $StdVal, &$ArrVals)
    $ArrVals=array();

    $Val= $dp['Param'];  // [Editable=;0;10;20;]
    
    echo ("<td></td><td>$Val</td></tr>");
    $Sel='';
    if ($Val!= '') {
      $Sel=' checked ';
    }
    echo ("<tr><td align=right>".GetStr($pdo, "SetEditable").
          "</td><td><input type=checkbox Name=SetEditable value=1$Sel></td>");
    
    $CurrVal=GetEditableFld($Val);
    //echo ("<br>OutVal = $OutVal <br>");

    $Fld='Param10';
    echo ("</tr><tr><td align=right>".GetStr($pdo, 'EditableInCase').": </td><td>");
    echo ( EnumMultiSelect2($pdo, $dp22['AddParam'], $Fld, $CurrVal));
    echo("</td>");
    echo ("</tr><tr>");
  }
  else

  //============================================================================
  if ($OutVal==30) {
    // SelectionArr ($Name, $StdVal, &$ArrVals)
    $ArrVals=array();

    $Val= $dp['Param'];
    $i=strpos($Val, '[SerNo=');
    
    echo ("<td></td><td>$Val</td>");
    $CurrVal=GetNumberSeq($Val);

    // AdmNumberSeq
    // Id, IsYearly, Pattern, LastNumber, 
    // Description
    $query = "select * from \"AdmNumberSeq\" ". 
             "order by \"Id\""; 

    $STH = $pdo->prepare($query);
    $STH->execute();
        
    while ($dp22 = $STH->fetch(PDO::FETCH_ASSOC)) {
      
      $ArrVals[$dp22['Id']]= $dp22['Id'].' '. $dp22['Pattern'].' '. $dp22['Description']; 
    }


    $Fld='Param30';
    $OutVal= $dp['Param'];  
    echo ("</tr><tr><td align=right>".GetStr($pdo, 'NoSerName').": </td><td>");
    echo ( SelectionArr ($Fld, $CurrVal, $ArrVals));
    echo("</td>");
    echo ("</tr><tr>");
  }


  //============================================================================
  echo ("<td colspan=2 align=right><input type=submit value='".
         GetStr($pdo, 'Save')."'></td></tr></table></form>");
} //Editable
else {
  echo ("<table>");

  $Fld='Id';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='TabName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='FldName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='AddFunc';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ("<b>".GetEnum($pdo, "FldAddFunction", $OutVal)."</b>");
  
  echo("</td></tr>");
    echo ("</table>");
}
echo ("  <hr><br><a href='AdmFieldsAddFuncList.php'>".GetStr($pdo, 'List')."</a>");
if ($Editable)
  echo (" | <a href='AdmFieldsAddFuncDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($pdo, 'Delete')."</a>");

}
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }


?>
</body>
</html>
