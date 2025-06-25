<?php
session_start();

include ("../setup/common.php");
BeginProc();
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>FavPlaces Card</title></head>
<body>
<?php

CheckLogin1 ();


CheckRight1 ($mysqli, 'ExtProj.Admin');

$FldNames=array('UserId','DirNo','ShortName','Tip'
          ,'DirName','Ord');
$enFields= array();
$UserId=addslashes($_REQUEST['UserId']);
$DirNo=addslashes($_REQUEST['DirNo']);
echo("<H3>".GetStr($mysqli, 'FavPlaces')."</H3>");
  $dp=array();
  $FullLink="UserId=$UserId&DirNo=$DirNo";

  $query = "select * ".
         "FROM FavPlaces ".
         " WHERE (UserId='$UserId') AND (DirNo='$DirNo')";
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
  
  if ($dp = $sql2->fetch_assoc()) {
  }
  $New=$_REQUEST['New'];

$Editable=1;
if ($Editable) {
  echo ('<form method=get action="FavPlacesSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");
  if ($New==1) {
      $query = "select max(DirNo) MX ".
      "FROM FavPlaces ".
      " WHERE (1=1)  AND (UserId='$UserId')";
      $sql4 = $mysqli->query ($query) 
                  or die("Invalid query:<br>$query<br>" . $mysqli->error);
      $LN=0;
      if ($dp4 = $sql4->fetch_assoc()) {
        $LN=$dp4['MX'];
      }
      $LN+=1;
  }

  $Fld='UserId';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldUserId' value='$OutVal'>");
  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$UserId}' size=10 readonly>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='DirNo';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldDirNo' value='$OutVal'>");
  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$DirNo}' size=10 readonly>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='ShortName';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Tip';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=60>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='DirName';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<textarea Name='$Fld'  ID='$Fld'  cols=50 rows=3>{$dp[$Fld]}</textarea>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Ord';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=number Name='$Fld'  ID='$Fld'  Value='{$dp[$Fld]}'>");
  echo("</td>");
  echo ("</tr><tr>");

  echo ("<td colspan=2 align=right><input type=submit value='".
         GetStr($mysqli, 'Save')."'></td></tr></table></form>");
} //Editable
else {
  echo ("<table>");

  $Fld='UserId';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='DirNo';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='ShortName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Tip';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='DirName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Ord';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
    echo ("</table>");
}
echo ("  <hr><br><a href='FavPlacesList.php'>".GetStr($mysqli, 'List')."</a>");
if ($Editable)
  echo (" | <a href='FavPlacesDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($mysqli, 'Delete')."</a>");
?>
</body>
</html>
