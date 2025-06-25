<?php
session_start();

include ("../setup/common.php");
BeginProc();
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>CurrencyExchRate Card</title></head>
<body>
<?php

CheckLogin1 ();


CheckRight1 ($mysqli, 'ExtProj.Admin');

$FldNames=array('CurrencyCode','StartDate','Multy','Rate'
          ,'FullRate');
$enFields= array('CurrencyCode'=>'Currency', 'CurrencyCode');
$CurrencyCode=addslashes($_REQUEST['CurrencyCode']);
$StartDate=addslashes($_REQUEST['StartDate']);
echo("<H3>".GetStr($mysqli, 'CurrencyExchRate')."</H3>");
  $dp=array();
  $FullLink="CurrencyCode=$CurrencyCode&StartDate=$StartDate";

  $query = "select * ".
         "FROM CurrencyExchRate ".
         " WHERE (CurrencyCode='$CurrencyCode') AND (StartDate='$StartDate')";
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
  
  if ($dp = $sql2->fetch_assoc()) {
  }
  $New=$_REQUEST['New'];

$Editable=1;
if ($Editable) {
  echo ('<form method=get action="CurrencyExchRateSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");
  if ($New==1) {
      $query = "select max(StartDate) MX ".
      "FROM CurrencyExchRate ".
      " WHERE (1=1)  AND (CurrencyCode='$CurrencyCode')";
      $sql4 = $mysqli->query ($query) 
                  or die("Invalid query:<br>$query<br>" . $mysqli->error);
      $LN=0;
      if ($dp4 = $sql4->fetch_assoc()) {
        $LN=$dp4['MX'];
      }
      $LN+=1;
  }

  $Fld='CurrencyCode';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldCurrencyCode' value='$OutVal'>");
  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$CurrencyCode}' size=10 readonly>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='StartDate';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldStartDate' value='$OutVal'>");
  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$StartDate}' size=10 readonly>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Multy';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=number Name='$Fld'  ID='$Fld'  Value='{$dp[$Fld]}'>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Rate';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=number Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' step=0.0001>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='FullRate';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=number Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' step=0.0001>");
  echo("</td>");
  echo ("</tr><tr>");

  echo ("<td colspan=2 align=right><input type=submit value='".
         GetStr($mysqli, 'Save')."'></td></tr></table></form>");
} //Editable
else {
  echo ("<table>");

  $Fld='CurrencyCode';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ("<b>".GetEnum($mysqli, "Currency", $OutVal)."</b>");
  
  echo("</td></tr>");
  
  $Fld='StartDate';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Multy';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Rate';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  $OW=number_format($OutVal, 4, ".", "'");
  echo ("<b>$OW</b>");
  
  echo("</td></tr>");
  
  $Fld='FullRate';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  $OW=number_format($OutVal, 4, ".", "'");
  echo ("<b>$OW</b>");
  
  echo("</td></tr>");
    echo ("</table>");
}
echo ("  <hr><br><a href='CurrencyExchRateList.php'>".GetStr($mysqli, 'List')."</a>");
if ($Editable)
  echo (" | <a href='CurrencyExchRateDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($mysqli, 'Delete')."</a>");
?>
</body>
</html>
