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
<title>ComConst Card</title></head>
<body>
<?php

CheckLogin1 ();


CheckRight1 ($pdo, 'Admin');

$FldNames=array('ConstName','Description','ConstType');
$enFields= array('ConstType'=>'ConstType', 'ConstType');
$ConstName=$_REQUEST['ConstName'];

$PdoArr = array();

try {

echo("<H3>".GetStr($pdo, 'ComConst')."</H3>");
  $dp=array();
  $FullLink="ConstName=$ConstName";


  if ($ConstName!='') {
  $query = "select * FROM \"ComConst\" ".
           "WHERE (\"ConstName\"=:ConstName)";
  
  $PdoArr['ConstName']= $ConstName;
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
  
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  
  }
  }

  $New=$_REQUEST['New'];

$Editable=1;
if ($Editable) {
  echo ('<form method=get action="ComConstSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");
  $LN=0;
  $Fld='ConstName';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldConstName' value='$OutVal'>");
  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$ConstName}' size=30 maxlength=30>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Description';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<textarea Name='$Fld'  ID='$Fld'  cols=50 rows=3>{$dp[$Fld]}</textarea>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='ConstType';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ( EnumSelection($pdo, "ConstType", "ConstType ID='$Fld' ", $OutVal));
  echo("</td>");
  echo ("</tr><tr>");

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
  
  $Fld='Description';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr("$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='ConstType';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr("$Fld").": </td><td>");
  
  echo ("<b>".GetEnum($pdo, "", $OutVal)."</b>");
  
  echo("</td></tr>");
  }
echo ("</table>");
echo ("  <hr><br><a href='ComConstList.php'>".GetStr($pdo, 'List')."</a>");
if ($Editable)
  echo (" | <a href='ComConstDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($pdo, 'Delete')."</a>");

if ($ConstName!='') {
  
  $PdoArr['ConstName']= $ConstName;

  $query = "select * FROM \"ComConstValues\" where \"ConstName\"=:ConstName order by \"ConstName\",\"OpDate\" ";
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

  echo('<table><tr class=header>');
  echo('<th>'.GetStr($pdo, 'OpDate').'</th>');
  echo('<th>'.GetStr($pdo, 'ValidTill').'</th>');
  echo('<th>'.GetStr($pdo, 'Value').'</th>');
  echo('<th>'.GetStr($pdo, 'ValueDate').'</th>');
  echo('<th>'.GetStr($pdo, 'ValueTxt').'</th>');
  $i=0;
  while ($dpL = $STH->fetch(PDO::FETCH_ASSOC)) {
    $i=NewLine($i);

    echo ("<td>");

    echo("<a href='ComConstValuesCard.php?ConstName={$dpL['ConstName']}&OpDate={$dpL['OpDate']}'>");
    echo ("{$dpL['OpDate']}</a></td>");
    echo ("<td>");
    echo ("{$dpL['ValidTill']}</td>");
    echo ("<td align=center>");
    echo ("{$dpL['Value']}</td>");
    echo ("<td>");
    echo ("{$dpL['ValueDate']}</td>");
    echo ("<td>");
    echo ("{$dpL['ValueTxt']}</td>");
    
  }
  echo("</tr></table>");
  echo("<a href='ComConstValuesCard.php?New=1&ConstName=$ConstName'>".GetStr($pdo, "Add")."</a>");
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
