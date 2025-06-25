<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>Countries Card</title></head>
<body>
<?php
// Checklogin1();


CheckRight1 ($pdo, 'Admin');

$FldNames=array('Code2','Code3','DigCode','CountryName'
          );
$enFields= array();
$PdoArr = array();
$Code2=$_REQUEST['Code2'];
$PdoArr["Code2"]=$Code2;
echo("<H3>".GetStr($pdo, 'Countries')."</H3>");
  $dp=array();
  $FullLink="Code2=$Code2";

  $query = "select * FROM \"Countries\" ".
           "WHERE (\"Code2\"=:Code2)";
  
  try {
  
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
  
  $New=$_REQUEST['New'];

$Editable=1;
if ($Editable) {
  echo ('<form method=post action="CountriesSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");

  $LN=0;
  $Fld='Code2';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldCode2' value='$OutVal'>");
  echo ("<td align=right><label for='Code2'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$Code2}' size=10 readonly>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Code3';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Code3'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=3>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='DigCode';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='DigCode'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=number Name='$Fld'  ID='$Fld'  Value='{$dp[$Fld]}'>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='CountryName';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='CountryName'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<textarea Name='$Fld'  ID='$Fld'  cols=50 rows=3>{$dp[$Fld]}</textarea>");
  echo("</td>");
  echo ("</tr><tr>");

  echo ("<td colspan=2 align=right><input type=submit value='".
         GetStr($pdo, 'Save')."'></td></tr></table></form>");
} //Editable
else {
  echo ("<table>");

  $Fld='Code2';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Code3';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='DigCode';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='CountryName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
    echo ("</table>");
}
echo ("  <hr><br><a href='CountriesList.php'>".GetStr($pdo, 'List')."</a>");
if ($Editable)
  echo (" | <a href='CountriesDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($pdo, 'Delete')."</a>");
?>
</body>
</html>
