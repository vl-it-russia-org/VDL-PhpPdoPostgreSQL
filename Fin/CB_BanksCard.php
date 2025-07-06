<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();

$TabName='CB_Banks';
OutHtmlHeader ($TabName." card");


// Checklogin1();


CheckRight1 ($pdo, 'Fin');

$FldNames=array('BIK','BankName','BankTransitAcc','City'
          );
$enFields= array();
$PdoArr = array();
$BIK=$_REQUEST['BIK'];
$PdoArr["BIK"]=$BIK;
echo("<H3>".GetStr($pdo, 'CB_Banks')."</H3>");
  $dp=array();
  $FullLink="BIK=$BIK";

  $query = "select * FROM \"CB_Banks\" ".
           "WHERE (\"BIK\"=:BIK)";
  
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
  echo ('<form method=post action="CB_BanksSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");

  $LN=0;
  $Fld='BIK';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldBIK' value='$OutVal'>");
  echo ("<td align=right><label for='BIK'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$BIK}' size=10 readonly>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='BankName';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='BankName'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<textarea Name='$Fld'  ID='$Fld'  cols=50 rows=3>{$dp[$Fld]}</textarea>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='BankTransitAcc';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='BankTransitAcc'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='City';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='City'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=50>");
  echo("</td>");
  echo ("</tr><tr>");

  echo ("<td colspan=2 align=right><input type=submit value='".
         GetStr($pdo, 'Save')."'></td></tr></table></form>");
} //Editable
else {
  echo ("<table>");

  $Fld='BIK';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='BankName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='BankTransitAcc';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='City';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
    echo ("</table>");
}
echo ("  <hr><br><a href='CB_BanksList.php'>".GetStr($pdo, 'List')."</a>");
if ($Editable)
  echo (" | <a href='CB_BanksDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($pdo, 'Delete')."</a>");
?>
</body>
</html>
