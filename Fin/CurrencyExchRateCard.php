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
<title>CurrencyExchRate Card</title></head>
<body>
<?php
// Checklogin1();


CheckRight1 ($pdo, 'Admin');

$FldNames=array('CurrencyCode','StartDate','Multy','Rate'
          ,'FullRate');
$enFields= array('CurrencyCode'=>'Currency');
$PdoArr = array();
$CurrencyCode=$_REQUEST['CurrencyCode'];
$PdoArr["CurrencyCode"]=$CurrencyCode;
$StartDate=$_REQUEST['StartDate'];
$PdoArr["StartDate"]=$StartDate;
echo("<H3>".GetStr($pdo, 'CurrencyExchRate')."</H3>");
  $dp=array();
  $FullLink="CurrencyCode=$CurrencyCode&StartDate=$StartDate";

  $query = "select * FROM \"CurrencyExchRate\" ".
           "WHERE (\"CurrencyCode\"=:CurrencyCode) AND (\"StartDate\"=:StartDate)";
  
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
  echo ('<form method=post action="CurrencyExchRateSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");
  $PdoArr = array();
  $PdoArr["CurrencyCode"]= $CurrencyCode;

  if ($New==1) {
      $query = "select max(\"StartDate\") \"MX\" ".
      "FROM \"CurrencyExchRate\" ".
      " WHERE (1=1)  AND (\"CurrencyCode\"=:CurrencyCode)";
      $STH4 = $pdo->prepare($query);
      $STH4->execute($PdoArr);
      $LN=0;
      if ($dp4 = $STH4->fetch(PDO::FETCH_ASSOC)) {
        $LN=$dp4['MX'];
      }
      $LN+=1;
  }

  $Fld='CurrencyCode';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldCurrencyCode' value='$OutVal'>");
  echo ("<td align=right><label for='CurrencyCode'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ( EnumSelection($pdo, "Currency", "CurrencyCode ID='$Fld' ", $OutVal));
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='StartDate';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldStartDate' value='$OutVal'>");
  echo ("<td align=right><label for='StartDate'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=date Name='$Fld'  ID='$Fld' Value='{$StartDate}'>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Multy';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Multy'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=number Name='$Fld'  ID='$Fld'  Value='{$dp[$Fld]}'>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Rate';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Rate'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=number Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' step=0.000001>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='FullRate';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='FullRate'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=number Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' step=0.000001>");
  echo("</td>");
  echo ("</tr><tr>");

  echo ("<td colspan=2 align=right><input type=submit value='".
         GetStr($pdo, 'Save')."'></td></tr></table></form>");
} //Editable
else {
  echo ("<table>");

  $Fld='CurrencyCode';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ("<b>".GetEnum($pdo, "Currency", $OutVal)."</b>");
  
  echo("</td></tr>");
  
  $Fld='StartDate';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Multy';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Rate';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  $OW=number_format($OutVal, 6, ".", "'");
  echo ("<b>$OW</b>");
  
  echo("</td></tr>");
  
  $Fld='FullRate';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  $OW=number_format($OutVal, 6, ".", "'");
  echo ("<b>$OW</b>");
  
  echo("</td></tr>");
    echo ("</table>");
}
echo ("  <hr><br><a href='CurrencyExchRateList.php'>".GetStr($pdo, 'List')."</a>");
if ($Editable)
  echo (" | <a href='CurrencyExchRateDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($pdo, 'Delete')."</a>");
?>
</body>
</html>
