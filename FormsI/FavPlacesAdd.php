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
<title>FavPlaces Card</title></head>
<body>
<?php

CheckLogin1 ();


CheckRight1 ($pdo, 'Admin');

$FldNames=array('UserId','DirNo','ShortName','Tip'
          ,'DirName','Ord');
$enFields= array();
$UserId=$_REQUEST['UserId'];
if ($UserId=='') {
  $UserId=$_SESSION['login'];
}

$DirNo=$_REQUEST['DirNo'];





echo("<H3>".GetStr($pdo, 'FavPlaces')."</H3>");

$dp=array();
$FullLink="UserId=$UserId&DirNo=$DirNo";

$PdoArr = array();
$PdoArr['UserId']= $UserId;  
$PdoArr['DirNo']= $DirNo;  
try {
  if (!empty ($DirNo)) {  
    $query = "select * FROM \"FavPlaces\" ".
             "WHERE (\"UserId\"=:UserId) AND (\"DirNo\"=:DirNo)";
    
    
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
    
    if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    }
  }
  else {
    $dp['DirName']=$_REQUEST['Dir'];
  }
  $New=$_REQUEST['New'];

$Editable=1;
if ($Editable) {
  echo ('<form method=get action="FavPlacesSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");
  if ($New==1) {
      /*
      $query = "select max(DirNo) MX FROM FavPlaces ".
               "WHERE (1=1)  AND (UserId='$UserId')";
      $sql4 = $pdo->query ($query) 
                  or die("Invalid query:<br>$query<br>" . $pdo->error);
      $LN=0;
      if ($dp4 = $sql4->fetch_assoc()) {
        $LN=$dp4['MX'];
      }
      $LN+=1;
      */
  }

  $Fld='UserId';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldUserId' value='$OutVal'>");
  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$UserId}' size=20>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='DirNo';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldDirNo' value='$OutVal'>");
  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$DirNo}' size=10>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='ShortName';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Tip';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=60>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='DirName';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<textarea Name='$Fld'  ID='$Fld'  cols=50 rows=3>{$dp[$Fld]}</textarea>");
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

  $Fld='UserId';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='DirNo';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='ShortName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Tip';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='DirName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Ord';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
    echo ("</table>");
}
echo ("  <hr><br><a href='FavPlacesList.php'>".GetStr($pdo, 'List')."</a>");
if ($Editable)
  echo (" | <a href='FavPlacesDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($pdo, 'Delete')."</a>");
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
