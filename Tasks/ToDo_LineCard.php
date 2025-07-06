<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();

$TabName='ToDo_Line';
OutHtmlHeader ($TabName." card");


// Checklogin1();
include "../js_module.php";

//------- For Ext Tables --------- 
  ScriptSelectionTabs('ToDo_Line3', 'SelectToDo_Line.php', '<a href=\'https://kolya.it-russia.org/PG2025/FormsI/TranslateFrm.php?Enum=ToDo_Line\' target=Translate>_</a>ToDo_Line', 'SelId=3&');

CheckRight1 ($pdo, 'Task');

$FldNames=array('Id','ParentId','ToDoCode','Description'
          ,'DateBeg','DateEnd','Status');
$enFields= array('Status'=>'ToDoStatus');
$PdoArr = array();
$Id=$_REQUEST['Id'];
$PdoArr["Id"]=$Id;

$ParentTxt='';

echo("<H3>".GetStr($pdo, 'ToDo_Line')."</H3>");
  $dp=array();
  $FullLink="Id=$Id";

  $query = "select * FROM \"ToDo_Line\" ".
           "WHERE (\"Id\"=:Id)";
  
  try {
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

  
  
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $PdoArr = array();
    if ($dp['ParentId']!=0) {
      $PdoArr["Id"]= $dp['ParentId'];
      $query = "select * FROM \"ToDo_Line\" ".
               "WHERE (\"Id\"=:Id)";
        
      $STH = $pdo->prepare($query);
      $STH->execute($PdoArr);

      if ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
        $ParentTxt="<b>{$dp2['ToDoCode']}</b> {$dp2['Description']}"; 
      }    
    }
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
  echo ('<form method=post action="ToDo_LineSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");

  $LN=0;
  $Fld='Id';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldId' value='$OutVal'>");
  echo ("<td align=right><label for='Id'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$Id}' size=10 readonly>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='ParentId';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='ParentId'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=number Name='$Fld'  ID='$Fld'  Value='{$dp[$Fld]}'>");
  echo(" <input type=button value='...' onclick='return SelectToDo_Line3Fld(\"ParentId\");'> $ParentTxt");

  echo("</td>");
  echo ("</tr><tr>");

  $Fld='ToDoCode';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='ToDoCode'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=20>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Description';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Description'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=100>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='DateBeg';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='DateBeg'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=date Name='$Fld'  ID='$Fld'  Value='{$dp[$Fld]}'>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='DateEnd';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='DateEnd'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=date Name='$Fld'  ID='$Fld'  Value='{$dp[$Fld]}'>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Status';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Status'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ( EnumSelection($pdo, "ToDoStatus", "Status ID='$Fld' ", $OutVal));
  echo("</td>");
  echo ("</tr><tr>");

  echo ("<td colspan=2 align=right><input type=submit value='".
         GetStr($pdo, 'Save')."'></td></tr></table></form>");
} //Editable
else {
  echo ("<table>");

  $Fld='Id';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='ParentId';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo(" $ParentTxt</td></tr>");
  
  $Fld='ToDoCode';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Description';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='DateBeg';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='DateEnd';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Status';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ("<b>".GetEnum($pdo, "ToDoStatus", $OutVal)."</b>");
  
  echo("</td></tr>");
    echo ("</table>");
}
echo ("  <hr><br><a href='ToDo_LineList.php'>".GetStr($pdo, 'List')."</a>");
echo (" | <a href='ToDo_LineCopy.php?$FullLink' onclick='return confirm(\"Copy?\");'>".
        GetStr($pdo, 'Copy')."</a>");

if ($Editable)
  echo (" | <a href='ToDo_LineDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($pdo, 'Delete')."</a>");
?>
</body>
</html>
