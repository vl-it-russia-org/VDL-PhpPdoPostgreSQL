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
<title>AdmNumberSeqYear Card</title></head>
<body>
<?php

CheckLogin1 ();


CheckRight1 ($mysqli, 'ExtProj.Admin');

$FldNames=array('Id','Year','LastNo');
$enFields= array();
$Id=addslashes($_REQUEST['Id']);
$Year=addslashes($_REQUEST['Year']);
echo("<H3>".GetStr($mysqli, 'AdmNumberSeqYear')."</H3>");
  $dp=array();
  $FullLink="Id=$Id&Year=$Year";

  $query = "select * ".
         "FROM AdmNumberSeqYear ".
         " WHERE (Id='$Id') AND (Year='$Year')";
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
  
  if ($dp = $sql2->fetch_assoc()) {
  }
  $New=$_REQUEST['New'];

$Editable=1;
if ($Editable) {
  echo ('<form method=get action="AdmNumberSeqYearSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");
  if ($New==1) {
      $query = "select max(Year) MX ".
      "FROM AdmNumberSeqYear ".
      " WHERE (1=1)  AND (Id='$Id')";
      $sql4 = $mysqli->query ($query) 
                  or die("Invalid query:<br>$query<br>" . $mysqli->error);
      $LN=0;
      if ($dp4 = $sql4->fetch_assoc()) {
        $LN=$dp4['MX'];
      }
      $LN+=1;
  }

  $Fld='Id';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldId' value='$OutVal'>");
  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$Id}' size=10 readonly>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Year';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldYear' value='$OutVal'>");
  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$Year}' size=10 readonly>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='LastNo';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=number Name='$Fld'  ID='$Fld'  Value='{$dp[$Fld]}'>");
  echo("</td>");
  echo ("</tr><tr>");

  echo ("<td colspan=2 align=right><input type=submit value='".
         GetStr($mysqli, 'Save')."'></td></tr></table></form>");
} //Editable
else {
  echo ("<table>");

  $Fld='Id';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr("$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Year';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr("$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='LastNo';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr("$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  }
echo ("</table>");
echo ("  <hr><br><a href='AdmNumberSeqYearList.php'>".GetStr($mysqli, 'List')."</a>");
if ($Editable)
  echo (" | <a href='AdmNumberSeqYearDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($mysqli, 'Delete')."</a>");

