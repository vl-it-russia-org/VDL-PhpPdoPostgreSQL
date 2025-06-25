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
<title>SF_User Card</title></head>
<body>
<?php
// Checklogin1();


CheckRight1 ($mysqli, 'ExtProj.Admin');

$FldNames=array('Id','UserName','Region','FirstName'
          ,'LastName','Email','IsActive','UserRoleId'
          ,'ManagerId','LastLoginDate','LegrandRegion__c','EnglishName__c'
          ,'Firm__c','SalesTeam__c','PatronymicName__c','UnId'
          ,'Title','CompanyName','Department','Phone'
          );
$enFields= array('LastLoginDate'=>'LastLoginDate', 'LastLoginDate');
$UnId=addslashes($_REQUEST['UnId']);
echo("<H3>".GetStr($mysqli, 'SF_User')."</H3>");
  $dp=array();
  $FullLink="UnId=$UnId";

  $query = "select * ".
         "FROM SF_User ".
         " WHERE (UnId='$UnId')";
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
  
  if ($dp = $sql2->fetch_assoc()) {
  }
  $New=$_REQUEST['New'];

$Editable=1;
if ($Editable) {
  echo ('<form method=get action="SF_UserSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");
  $LN=0;
  $Fld='Id';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='UserName';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<textarea Name='$Fld'  ID='$Fld'  cols=50 rows=3>{$dp[$Fld]}</textarea>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Region';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=60>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='FirstName';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=50>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='LastName';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=50>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Email';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=60>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='IsActive';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  $Ch=''; if ($dp[$Fld]==1) $Ch='Checked';
  echo ("<input type=checkbox Name='$Fld'  ID='$Fld' Value=1 $Ch>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='UserRoleId';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=50>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='ManagerId';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='LastLoginDate';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ( EnumSelection($mysqli, "", "LastLoginDate ID='$Fld' ", $OutVal));
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='LegrandRegion__c';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=50>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='EnglishName__c';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=60>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Firm__c';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='SalesTeam__c';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=50>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='PatronymicName__c';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=50>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='UnId';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldUnId' value='$OutVal'>");
  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$UnId}' size=10 readonly>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Title';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=40>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='CompanyName';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=40>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Department';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=50>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Phone';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($mysqli, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=40>");
  echo("</td>");
  echo ("</tr><tr>");

  echo ("<td colspan=2 align=right><input type=submit value='".
         GetStr($mysqli, 'Save')."'></td></tr></table></form>");
} //Editable
else {
  echo ("<table>");

  $Fld='Id';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='UserName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Region';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='FirstName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='LastName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Email';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='IsActive';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  $Checked="";
  if ($OutVal) { 
    $Checked=" checked ";
  }
  echo ("<input type=checkbox $Checked value=1 disabled");
  
  echo("</td></tr>");
  
  $Fld='UserRoleId';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='ManagerId';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='LastLoginDate';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ("<b>".GetEnum($mysqli, "", $OutVal)."</b>");
  
  echo("</td></tr>");
  
  $Fld='LegrandRegion__c';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='EnglishName__c';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Firm__c';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='SalesTeam__c';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='PatronymicName__c';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='UnId';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Title';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='CompanyName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Department';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Phone';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($mysqli, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
    echo ("</table>");
}
echo ("  <hr><br><a href='SF_UserList.php'>".GetStr($mysqli, 'List')."</a>");
if ($Editable)
  echo (" | <a href='SF_UserDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($mysqli, 'Delete')."</a>");
?>
</body>
</html>
