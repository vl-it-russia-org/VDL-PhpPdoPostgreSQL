<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>ComConstValues Card</title></head>
<body>
<?php

CheckLogin1 ();


CheckRight1 ($pdo, 'Admin');

$FldNames=array('ConstName','OpDate','ValidTill','Value'
          ,'ValueDate','ValueTxt');
$enFields= array();
$ConstName=$_REQUEST['ConstName'];

if ($ConstName=='') {
  die ("<br> Error: ConstName is empty ");
}

$OpDate=$_REQUEST['OpDate'];
echo("<H3>".GetStr($pdo, 'ComConstValues')."</H3>");
$dp=array();

if ($OpDate=='') {
  $OpDate=date('Y-m-d');
}

$PdoArr = array();
try {
  $PdoArr['ConstName']= $ConstName;



$ConstType=0;
  // Enum ConstType
  //    0     -- Выберите --
  //    5     Число
  //    10    Дата
  //    15    Текст

// ComConst
// ConstName, Description, ConstType
$query = "select * from \"ComConst\" ". 
         "where (\"ConstName\" = :ConstName)"; 

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  echo ("<table>");

  $Fld='ConstName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  echo ($OutVal);
  echo("</td></tr>");
  
  $Fld='Description';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  echo ($OutVal);
  echo("</td></tr>");
  
  $Fld='ConstType';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  echo ("<b>".GetEnum($pdo, $Fld, $OutVal)."</b>");
  echo("</td></tr>");
  echo ("</table>");

  $ConstType=$OutVal;
}
else {
  die ("<br> Error: ComConst not found const = $ConstName ");
}
  
  
  
  
  $FullLink="ConstName=$ConstName&OpDate=$OpDate";

  $query = "select * FROM \"ComConstValues\" ".
           "WHERE (\"ConstName\"=:ConstName) AND (\"OpDate\"=:OpDate)";

  $PdoArr['OpDate']= $OpDate;
  
  //echo ("<br> Line ".__LINE__.": $query<br>");
  //            print_r($PdoArr);
  //            echo ("<br>");

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

  
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    //echo ("<hr>");
    //print_r($dp);
    //echo ("<hr>");
  
  }
  $New=$_REQUEST['New'];

$Editable=1;
if ($Editable) {
  echo ('<form method=post action="ComConstValuesSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");

  $Fld='ConstName';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldConstName' value='$OutVal'>");
  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$ConstName}' size=30 readonly>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='OpDate';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldOpDate' value='$OutVal'>");
  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=date Name='$Fld'  ID='$Fld' Value='{$OpDate}'>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='ValidTill';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=date Name='$Fld'  ID='$Fld'  Value='{$dp[$Fld]}'>");
  echo("</td>");
  echo ("</tr><tr>");

  if ( $ConstType==5) {
    $Fld='Value';
    $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
    echo ("<input type=number Name='$Fld'  ID='$Fld'  Value='{$dp[$Fld]}' step=0.0001>");
    echo("</td>");
    echo ("</tr><tr>");
  }
  else
  if ( $ConstType==10) {
    $Fld='ValueDate';
    $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
    echo ("<input type=date Name='$Fld'  ID='$Fld'  Value='{$dp[$Fld]}'>");
    echo("</td>");
    echo ("</tr><tr>");
  }
  else
  if ( $ConstType==15) {
    $Fld='ValueTxt';
    $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
    echo ("<textarea Name='$Fld'  ID='$Fld'  cols=50 rows=3>{$dp[$Fld]}</textarea>");
    echo("</td>");
    echo ("</tr><tr>");
  }

  echo ("<td colspan=2 align=right><input type=submit value='".
         GetStr($pdo, 'Save')."'></td></tr></table></form>");
} //Editable
else {
  echo ("<table>");

  $Fld='ConstName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr("$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='OpDate';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr("$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='ValidTill';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr("$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Value';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr("$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='ValueDate';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr("$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='ValueTxt';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr("$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  }
echo ("</table>");
echo ("  <hr><br><a href='ComConstValuesList.php'>".GetStr($pdo, 'List')."</a>");
if ($Editable)
  echo (" | <a href='ComConstValuesDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($pdo, 'Delete')."</a>");

 }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

?>
</body></html>