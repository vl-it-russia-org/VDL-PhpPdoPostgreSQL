<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();

$TabName='Expenses';
OutHtmlHeader ($TabName." card");


// Checklogin1();


CheckRight1 ($pdo, 'Fin');

$FldNames=array('ExpId','ExpenseName','HaveRegions','FinDiv'
          );
$enFields= array('FinDiv'=>'ExpensesDiv');
$PdoArr = array();
$ExpId=$_REQUEST['ExpId'];
$PdoArr["ExpId"]=$ExpId;
echo("<H3>".GetStr($pdo, 'Expenses')."</H3>");
  $dp=array();
  $FullLink="ExpId=$ExpId";

  $query = "select * FROM \"Expenses\" ".
           "WHERE (\"ExpId\"=:ExpId)";
  
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
  echo ('<form method=post action="ExpensesSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");

  $LN=0;
  $Fld='ExpId';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldExpId' value='$OutVal'>");
  echo ("<td align=right><label for='ExpId'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$ExpId}' size=10 readonly>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='ExpenseName';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='ExpenseName'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<textarea Name='$Fld'  ID='$Fld'  cols=50 rows=3>{$dp[$Fld]}</textarea>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='HaveRegions';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='HaveRegions'>".GetStr($pdo, $Fld).":</label></td><td>");
  $Ch=''; if ($dp[$Fld]==1) $Ch='Checked';
  echo ("<input type=checkbox Name='$Fld'  ID='$Fld' Value=1 $Ch>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='FinDiv';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='FinDiv'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ( EnumSelection($pdo, "ExpensesDiv", "FinDiv ID='$Fld' ", $OutVal));
  echo("</td>");
  echo ("</tr><tr>");

  echo ("<td colspan=2 align=right><input type=submit value='".
         GetStr($pdo, 'Save')."'></td></tr></table></form>");
} //Editable
else {
  echo ("<table>");

  $Fld='ExpId';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='ExpenseName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='HaveRegions';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  $Checked="";
  if ($OutVal) { 
    $Checked=" checked ";
  }
  echo ("<input type=checkbox $Checked value=1 disabled");
  
  echo("</td></tr>");
  
  $Fld='FinDiv';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ("<b>".GetEnum($pdo, "ExpensesDiv", $OutVal)."</b>");
  
  echo("</td></tr>");
    echo ("</table>");
}
echo ("  <hr><br><a href='ExpensesList.php'>".GetStr($pdo, 'List')."</a>");
if ($Editable)
  echo (" | <a href='ExpensesDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($pdo, 'Delete')."</a>");
?>
</body>
</html>
