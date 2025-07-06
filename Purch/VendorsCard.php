<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();

$TabName='Vendors';
OutHtmlHeader ($TabName." card");


// Checklogin1();
include "../js_module.php";

//------- For Ext Tables --------- 
  ScriptSelectionTabs('Location21', 'SelectLocation.php', '<a href=\'https://kolya.it-russia.org/PG2025/FormsI/TranslateFrm.php?Enum=Location\' target=Translate>_</a>Location', 'SelId=21&');
  ScriptSelectionTabs('Countries20', 'SelectCountries.php', 'Страны', 'SelId=20&');

CheckRight1 ($pdo, 'Admin');

$FldNames=array('Id','VendorType','VendorName','ShortName'
          ,'INN','KPP','Country','PostIndx'
          ,'City','Address','Phone','WebLink'
          ,'DefaultDeliveryPoint','Description','Status','Holding'
          ,'Position','Director','Accountant','GeneralBusinessGroup'
          ,'TaxBusinessGroup','Blocked');
$enFields= array('VendorType'=>'VendorType', 'Status'=>'StatusNUZ', 'GeneralBusinessGroup'=>'GeneralBusinessGroup', 'TaxBusinessGroup'=>'TaxBusinessGroup');
$PdoArr = array();
$Id=$_REQUEST['Id'];
$PdoArr["Id"]=$Id;
echo("<H3>".GetStr($pdo, 'Vendors')."</H3>");
  $dp=array();
  $FullLink="Id=$Id";

  $query = "select * FROM \"Vendors\" ".
           "WHERE (\"Id\"=:Id)";
  
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

$Editable=($Status==0);
if ($Editable) {
  echo ('<form method=post action="VendorsSave.php">'.
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

  $Fld='VendorType';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='VendorType'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ( EnumSelection($pdo, "VendorType", "VendorType ID='$Fld' ", $OutVal));
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='VendorName';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='VendorName'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=80>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='ShortName';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='ShortName'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=50>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='INN';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='INN'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=20>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='KPP';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='KPP'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=20>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Country';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Country'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=2>");
  echo(" <input type=button value='...' onclick='return SelectCountries20Fld(\"Country\");'>");

  echo("</td>");
  echo ("</tr><tr>");

  $Fld='PostIndx';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='PostIndx'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=10>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='City';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='City'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=50>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Address';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Address'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=100>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Phone';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Phone'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=80>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='WebLink';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='WebLink'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=100>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='DefaultDeliveryPoint';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='DefaultDeliveryPoint'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
  echo(" <input type=button value='...' onclick='return SelectLocation21Fld(\"DefaultDeliveryPoint\");'>");

  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Description';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Description'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<textarea Name='$Fld'  ID='$Fld'  cols=50 rows=3>{$dp[$Fld]}</textarea>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Status';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Status'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ( EnumSelection($pdo, "StatusNUZ", "Status ID='$Fld' ", $OutVal));
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Holding';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Holding'>".GetStr($pdo, $Fld).":</label></td><td>");
  $Ch=''; if ($dp[$Fld]==1) $Ch='Checked';
  echo ("<input type=checkbox Name='$Fld'  ID='$Fld' Value=1 $Ch>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Position';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Position'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=50>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Director';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Director'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=80>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Accountant';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Accountant'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=80>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='GeneralBusinessGroup';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='GeneralBusinessGroup'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ( EnumSelection($pdo, "GeneralBusinessGroup", "GeneralBusinessGroup ID='$Fld' ", $OutVal));
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='TaxBusinessGroup';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='TaxBusinessGroup'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ( EnumSelection($pdo, "TaxBusinessGroup", "TaxBusinessGroup ID='$Fld' ", $OutVal));
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Blocked';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Blocked'>".GetStr($pdo, $Fld).":</label></td><td>");
  $Ch=''; if ($dp[$Fld]==1) $Ch='Checked';
  echo ("<input type=checkbox Name='$Fld'  ID='$Fld' Value=1 $Ch>");
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
  
  $Fld='VendorType';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ("<b>".GetEnum($pdo, "VendorType", $OutVal)."</b>");
  
  echo("</td></tr>");
  
  $Fld='VendorName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='ShortName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='INN';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='KPP';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Country';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='PostIndx';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='City';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Address';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Phone';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='WebLink';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='DefaultDeliveryPoint';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Description';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Status';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ("<b>".GetEnum($pdo, "StatusNUZ", $OutVal)."</b>");
  
  echo("</td></tr>");
  
  $Fld='Holding';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  $Checked="";
  if ($OutVal) { 
    $Checked=" checked ";
  }
  echo ("<input type=checkbox $Checked value=1 disabled");
  
  echo("</td></tr>");
  
  $Fld='Position';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Director';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Accountant';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='GeneralBusinessGroup';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ("<b>".GetEnum($pdo, "GeneralBusinessGroup", $OutVal)."</b>");
  
  echo("</td></tr>");
  
  $Fld='TaxBusinessGroup';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ("<b>".GetEnum($pdo, "TaxBusinessGroup", $OutVal)."</b>");
  
  echo("</td></tr>");
  
  $Fld='Blocked';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  $Checked="";
  if ($OutVal) { 
    $Checked=" checked ";
  }
  echo ("<input type=checkbox $Checked value=1 disabled");
  
  echo("</td></tr>");
    echo ("</table>");
}
echo ("  <hr><br><a href='VendorsList.php'>".GetStr($pdo, 'List')."</a>");
if ($Editable)
  echo (" | <a href='VendorsDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($pdo, 'Delete')."</a>");
?>
</body>
</html>
