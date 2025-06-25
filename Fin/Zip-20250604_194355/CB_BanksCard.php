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
<title>CB_Banks Card</title></head>
<body>
<?php

CheckLogin1 ();


CheckRight1 ($mysqli, 'ExtProj.Admin');

$FldNames=array('BIK','BankName','BankTransitAcc','City'
          );
$enFields= array();
$BIK=addslashes($_REQUEST['BIK']);
echo("<H3>".GetStr($mysqli, 'CB_Banks')."</H3>");
  $dp=array();
  $FullLink="BIK=$BIK";

  $query = "select * ".
         "FROM CB_Banks ".
         " WHERE (BIK='$BIK')";
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
  
  if ($dp = $sql2->fetch_assoc()) {
  }
  $New=$_REQUEST['New'];

$Editable=1;
if ($Editable) {
  echo ('<form method=get action="CB_BanksSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");
  $LN=0;
  $Fld='BIK';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldBIK' value='$OutVal'>");
  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$BIK}' size=10 readonly>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='BankName';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<textarea Name='$Fld'  ID='$Fld'  cols=50 rows=3>{$dp[$Fld]}</textarea>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='BankTransitAcc';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='City';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=50>");
  echo("</td>");
  echo ("</tr><tr>");

  echo ("<td colspan=2 align=right><input type=submit value='".
         GetStr($mysqli, 'Save')."'></td></tr></table></form>");
} //Editable
else {
  echo ("<table>");

  $Fld='BIK';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='BankName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='BankTransitAcc';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='City';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
    echo ("</table>");
}
echo ("  <hr><br><a href='CB_BanksList.php'>".GetStr($mysqli, 'List')."</a>");

if ($Editable)
  echo (" | <a href='CB_BanksDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($mysqli, 'Delete')."</a>");

if ($BIK!= '') {
  echo (" | <a href='CB_BanksWebRead.php?$FullLink'>".
        GetStr($mysqli, 'WebRefersh')."</a>");
}

?>
</body>
</html>
