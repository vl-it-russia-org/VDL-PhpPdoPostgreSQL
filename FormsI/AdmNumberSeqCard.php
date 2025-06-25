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
<title>AdmNumberSeq Card</title></head>
<body>
<?php

CheckLogin1 ();


CheckRight1 ($pdo, 'Admin');

$FldNames=array('Id','IsYearly','Pattern','LastNumber');

$enFields= array();

$Id=addslashes($_REQUEST['Id']);
echo("<H3>".GetStr($pdo, 'AdmNumberSeq')."</H3>");
$dp=array();
  
$FullLink="Id=$Id";

$PdoArr = array();

try {
  if ($Id != '') {

    $PdoArr['Id']= $Id;

    $query = "select * FROM \"AdmNumberSeq\" ".
             "WHERE (\"Id\"=:Id)";
    
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);

    if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    }
  }
  $New=$_REQUEST['New'];

$Editable=1;
if ($Editable) {
  echo ('<form method=get action="AdmNumberSeqSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");
  $LN=0;
  $Fld='Id';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldId' value='$OutVal'>");
  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$Id}' size=10>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='IsYearly';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  $Ch=''; if ($dp[$Fld]==1) $Ch='Checked';
  echo ("<input type=checkbox Name='$Fld'  ID='$Fld' Value=1 $Ch>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Pattern';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='LastNumber';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=number Name='$Fld'  ID='$Fld'  Value='{$dp[$Fld]}'>");
  echo("</td>");
  echo ("</tr><tr>");

  echo ("<td colspan=2 align=right><input type=submit value='".
         GetStr($pdo, 'Save')."'></td></tr></table></form>");
} //Editable
else {
  echo ("<table>");

  $Fld='Id';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr("$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='IsYearly';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr("$Fld").": </td><td>");
  $Checked="";
  if ($OutVal) { 
    $Checked=" checked ";
  }
  echo ("<input type=checkbox $Checked value=1 disabled");
  
  echo("</td></tr>");
  
  $Fld='Pattern';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr("$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='LastNumber';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr("$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  }
echo ("</table>");
echo ("  <hr><br><a href='AdmNumberSeqList.php'>".GetStr($pdo, 'List')."</a>");
if ($Editable)
  echo (" | <a href='AdmNumberSeqDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($pdo, 'Delete')."</a>");

if ($Id !='') {  
  $query = "select * FROM \"AdmNumberSeqYear\" where \"Id\"=:Id order by \"Id\",\"Year\" ";
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
  
  echo('<table><tr class=header>');
  echo('<th>'.GetStr($pdo, 'Year').'</th>');
  echo('<th>'.GetStr($pdo, 'LastNo').'</th>');
  $i=0;
  while ($dpL = $STH->fetch(PDO::FETCH_ASSOC)) {
    $i=NewLine($i);

    echo ("<td align=center>");

    echo("<a href='AdmNumberSeqYearCard.php?Id={$dpL['Id']}&Year={$dpL['Year']}'>");
    echo ("{$dpL['Year']}</a></td>");
    echo ("<td align=center>");
    echo ("{$dpL['LastNo']}</td>");
    
  }
  echo("</tr></table>");
  echo("<a href='AdmNumberSeqYearCard.php?New=1&Id=$Id'>".GetStr($pdo, "Add")."</a>");
}
}
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }
?>
</body></html>
