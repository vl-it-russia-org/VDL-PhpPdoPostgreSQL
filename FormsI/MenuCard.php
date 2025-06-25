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
<title>Menu Card</title></head>
<body>
<?php

CheckLogin1 ();


CheckRight1 ($pdo, 'Admin');

$FldNames=array('Id','MenuName','Description','Link'
          ,'NewWindow','ParentId','Ord');
$enFields= array();
$Id=$_REQUEST['Id'];

echo ("<form method=get action='MenuCard.php'>".
      GetStr($pdo, 'ChooseOther').": <input type=text Name=Id size=6> <input type=submit></form>");

echo ("<hr>");


echo("<H3>".GetStr($pdo, 'Menu')."</H3>");
$dp=array();
$FullLink="Id=$Id";


if (!empty($Id)) {
  $PdoArr = array();
  $PdoArr['Id']= $Id;

  try {
  
  $query = "select * FROM \"Menu\" ".
           "WHERE (\"Id\"=:Id)";

      $STH = $pdo->prepare($query);
      $STH->execute($PdoArr);
  
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  }
  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

}
  $New=$_REQUEST['New'];

$Editable=1;
if ($Editable) {
  echo ('<form method=post action="MenuSave.php">'.
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

  $Fld='MenuType';
  $OutVal= $dp[$Fld];  
  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo (EnumSelection($pdo, $Fld, "$Fld ID=$Fld", $dp[$Fld]));
  echo("</td>");
  echo ("</tr><tr>");


  $Fld='MenuCode';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
  echo("</td>");
  echo ("</tr><tr>");


  $Fld='MenuName';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
  echo("</td><td>".GetStr($pdo, $OutVal)."</td>");
  echo ("</tr><tr>");

  $Fld='Description';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
  echo("</td><td>".GetStr($pdo, $OutVal)."</td>");
  echo ("</tr><tr>");

  $Fld='Link';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<textarea Name='$Fld'  ID='$Fld'  cols=50 rows=3>{$dp[$Fld]}</textarea>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='NewWindow';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  $Ch=''; if ($dp[$Fld]==1) $Ch='Checked';
  echo ("<input type=checkbox Name='$Fld'  ID='$Fld' Value=1 $Ch>");
  echo("</td>");
  echo ("</tr><tr>");

  // ColumnNo

  $Fld='ColumnNo';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=number Name='$Fld'  ID='$Fld'  Value='{$dp[$Fld]}'>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='ParentId';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=number Name='$Fld'  ID='$Fld'  Value='{$dp[$Fld]}'>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Ord';
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
  
  $Fld='MenuName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr("$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Description';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr("$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Link';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr("$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='NewWindow';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr("$Fld").": </td><td>");
  $Checked="";
  if ($OutVal) { 
    $Checked=" checked ";
  }
  echo ("<input type=checkbox $Checked value=1 disabled");
  
  echo("</td></tr>");
  
  $Fld='ParentId';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr("$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Ord';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr("$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  }
echo ("</table>");
echo ("  <hr><br><a href='MenuList.php'>".GetStr($pdo, 'List')."</a>");
if ($Id!= '') {
  echo (" | <a href='MenuCopy.php?Id=$Id' onclick='return confirm(\"Copy?\");'>".GetStr($pdo, 'Copy')."</a>");
  
  echo (" | <a href='Rights2SetupVals.php?RightSel=Menu&Sub=$Id' target=MenR>".GetStr($pdo, 'Rights')."</a>");

}
if ($Editable)
  echo (" | <a href='MenuDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($pdo, 'Delete')."</a>");

?>
</body>
</html>