<?php
session_start();


include ("../setup/common_pg.php");

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>usrs Card</title></head>
<body>
<?php
include ("FIO_fill.js");

CheckLogin1 ();


CheckRight1 ($pdo, 'Admin');

$FldNames=array('usr_id','usr_pwd','description','admin'
          ,'email','phone','passwd_duedate','new_passwd'
          ,'passwd_last_change', 'Blocked','WebCookie'
          ,'Position','Department','Company','FirstName'
          ,'LastName','PatronymicName__c');
$enFields= array();
$usr_id=$_REQUEST['usr_id'];
echo("<H3>".GetStr($pdo, 'usrs')."</H3>");
  
  $dp=array();
  $FullLink="usr_id=$usr_id";

  $query = "select * FROM \"usrs\" ".
           "WHERE (\"usr_id\"=:usr_id)";

  $PdoArr = array();
  $PdoArr['usr_id']= $usr_id;
  
try {
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  }
  $New=$_REQUEST['New'];

$Editable=1;
$Mail='';

if ($Editable) {
  echo ('<form method=get action="usrsSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");
  $LN=0;
  $Fld='usr_id';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='Oldusr_id' value='$OutVal'>");
  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$usr_id}' size=30>");
  echo (" <a href='Frm-usrs-ChangeUser.php?UsrId=$OutVal' title='Run as user $OutVal'>...</a> ");

  echo("</td>");
  echo ("</tr><tr>");

  $Fld='LastName';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=40>");
  echo (" <button Name=IOF onclick='return MakeFIO (\"$Fld\", 2);' value='IOF'>IOF (IF)</button> ");
  echo (" <button Name=FIO onclick='return MakeFIO (\"$Fld\", 1);' value='FIO'>FIO (FI)</button> ");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='FirstName';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=50>");
  echo("</td>");

  $Fld='PatronymicName__c';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=50>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='description';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<textarea Name='$Fld'  ID='$Fld'  cols=50 rows=3>{$dp[$Fld]}</textarea>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='admin';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  $Ch=''; if ($dp[$Fld]==1) $Ch='Checked';
  echo ("<input type=checkbox Name='$Fld'  ID='$Fld' Value=1 $Ch>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='email';
  $OutVal= $dp[$Fld];  
  $Mail = $dp[$Fld];  

  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=45> ".
        "<a href='Frm-usrs-OneMailChange.php?UsrId=$usr_id' target=ChMail ".
        "title='Set new mail'>...</a> ");
  echo("</td>");

  $Fld='phone';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=50>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Position';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=50>");
  echo("</td>");

  $Fld='Department';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=50>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Company';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=50>");
  echo("</td>");

  //$Fld='SFUser';
  //$OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  //echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=50>");
  //echo("</td>");
  echo ("</tr><tr>");

  echo("<td colspan=2></td>");

  $Fld='passwd_last_change';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ($dp[$Fld]);
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Blocked';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  $Ch=''; if ($dp[$Fld]==1) $Ch='Checked';
  echo ("<input type=checkbox Name='$Fld'  ID='$Fld' Value=1 $Ch>");
  echo("</td>");

  $Fld='WebCookie';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  $Ch=''; if ($dp[$Fld]==1) $Ch='Checked';
  echo ("<input type=checkbox Name='$Fld'  ID='$Fld' Value=1 $Ch>");
  echo("</td>");
  echo ("</tr><tr>");



  echo ("<td colspan=2 align=right><input type=submit value='".
         GetStr($pdo, 'Save')."'></td></tr></table></form>");
} //Editable
else {
  echo ("<table>");

  $Fld='usr_id';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");  
  echo ($OutVal);  
  echo("</td></tr>");
  
  $Fld='description';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  echo ($OutVal);
  echo("</td></tr>");
  
  $Fld='admin';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  $Checked="";
  if ($OutVal) { 
    $Checked=" checked ";
  }
  echo ("<input type=checkbox $Checked value=1 disabled");
  
  echo("</td></tr>");
  
  $Fld='email';
  $OutVal= $dp[$Fld];
  $Mail = $dp[$Fld];  
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='phone';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='SFUser';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
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
  
  $Fld='WebCookie';
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
  
  $Fld='Department';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Company';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='FirstName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='LastName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='PatronymicName__c';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  }
echo ("</table>");
echo ("  <hr><br><a href='usrsList.php'>".GetStr($pdo, 'List')."</a>");


if ($Editable)
  echo (" | <a href='usrsDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($pdo, 'Delete')."</a>");

if ($usr_id != '') {

  echo (" | <a href='../FormsI/RigtsSetup.php?RightSel=&LocSel=-&WH=$usr_id&WHF=usr_id' Target=UsrRight>".
        GetStr($pdo, 'UserRight')."</a>");

  echo (" | <a href='user_setup.php?UserId=$usr_id' Target=UsrSetup>".
        GetStr($pdo, 'AdditonalSetup')."</a>");

  // RespPersonsList.php


  echo (" | <a href='RespPersonsList.php?Mail=$Mail' Target=RespUsres>".
        GetStr($pdo, 'RespPersons')."</a>");


  
  echo ("<hr><br><form method=post action='CleanUpUserBlock.php'>".
        "<input type=hidden Name=UserId value='$usr_id'>".
        "<input type=submit value='Cleanup bad logins'></form>");

  echo ("<br><form method=post action='upd_sf_pass.php'>".
        "<input type=hidden Name=usr value='$usr_id'>".
        "<input type=submit value='Set SF (Calc) pass'></form>");

}

}
catch (PDOException $e) {
  echo ("<hr> Line ".__LINE__."<br>");
  echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
  print_r($PdoArr);	
  die ("<br> Error: ".$e->getMessage());
}
?>
</body>
</html>

