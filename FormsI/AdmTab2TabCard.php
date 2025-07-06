<?php
session_start();

include ("../setup/common_pg.php");

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>AdmTab2Tab Card</title></head>
<body>
<?php

include ("js_module.php");

CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');
$TabNo=0;
$TabNo2=0;

$FldNames=array('TabName','FldName','LineNo','TabCond',
                'Tab2','Field2','CondTab2', 'SelectViewName');
$enFields= array();

if (empty ($_REQUEST['TabName'])) {
  die ("<br> Error: TabName is empty");
}

$TabName=$_REQUEST['TabName'];

$FldName=$_REQUEST['FldName'];
$LineNo=$_REQUEST['LineNo'];


echo("<H3>".GetStr($pdo, 'AdmTab2Tab')."</H3>");

ScriptSelectionTabs ('Tab', 'SelectTab.php', 'Table');

$dp=array();
  $FullLink="TabName=$TabName&FldName=$FldName&LineNo=$LineNo";

$PdoArr = array();
$PdoArr['TabName']= $TabName;
$dp=array();
try {
  $query = "select * FROM \"AdmTabNames\" ".
           "WHERE (\"TabName\"=:TabName)";

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
      
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    //print_r($dp);
    $TabNo=$dp['TabCode'];
    ScriptSelectionTabs ('Fld1', 'SelectField.php', 'Flds', "Par2=$TabNo&");
  }
  else {
    die("<br>Bad table name: $TabName");
  }


echo ("<br><a href='TabCard.php?TabCode=$TabNo'>Table card</a>");

  $PdoArr['FldName']= $FldName;

  
  $dp=array();
  if (!empty ($LineNo)) {
    $query = "select * FROM \"AdmTab2Tab\" ".
             "WHERE (\"TabName\"=:TabName) AND (\"FldName\"=:FldName) AND (\"LineNo\"=:LineNo)";
    
    $PdoArr['LineNo'] = $LineNo;
    
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
        
    if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    
    }
  }
  $New=$_REQUEST['New'];

  echo ('<form method=get action="AdmTab2TabSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");
  
  if ($New==1) {
      $query = "select max(\"LineNo\") \"MX\" FROM \"AdmTab2Tab\" ".
               "WHERE (\"TabName\"=:TabName) AND (\"FldName\"=:FldName)";
      
      $STH = $pdo->prepare($query);
      $STH->execute($PdoArr);
      
      $LN=0;
      if ($dp4 = $STH->fetch(PDO::FETCH_ASSOC)) {
        $LN=$dp4['MX'];
      }
      $LN+=1;
  }

    
  $Fld='Id';
  $OutVal=$dp[$Fld];

  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<b>$OutVal</b>");
  echo("</td>");
  echo ("</tr><tr>");
    
  $Fld='TabName';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldTabName' value='$OutVal'>");
  echo ("<td align=right>".GetStr($pdo,$Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$TabName}' size=30 readonly>");
  echo("</td>");
  echo ("</tr><tr>");
  
  $Fld='FldName';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldFldName' value='$OutVal'>");
  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$FldName}' size=30 readonly>");
  echo("</td>");
  echo ("</tr><tr>");
  
  $Fld='LineNo';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldLineNo' value='$OutVal'>");
  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$LineNo}' size=30 readonly>");
  echo("</td>");
  echo ("</tr><tr>");
  

//--------------- Выбираем название поля / для построения выражения ------------
 $Fld='TabCond';
 $OutVal= $dp[$Fld];echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
 echo ("<textarea Name='$Fld'  ID='$Fld'  cols=50 rows=3>{$dp[$Fld]}</textarea>");
 echo("</td>");

 echo ("<td><button onclick=\"return SelectFld1Fld('$Fld');\">...</button></td>");
 echo ("</tr><tr>");

 //--------------- Выбираем название таблицы 2 ---------------------------------
 $Fld='Tab2';
 $OutVal= $dp[$Fld];
 
 //--------------- Выбираем поля таблицы 2 ---------------------------------
 $Tab2=$OutVal;
 $i=strpos($Tab2, '[T:');
 if ($i!== false) {
   $end=strpos($Tab2, ']', $i);
   if ($end!==false) {
     $Tab2= substr($Tab2, $i+3, $end-$i-3);
     //echo ("<br> Tab2: $Tab2 ");
     if ($Tab2==$TabName) {
       ScriptSelectionTabs ('Fld2', 'SelectField.php', 'Flds', "Par2=$TabNo&");     
     }
     else {
       $PdoArr = array();
       $PdoArr['TabName']= $Tab2;
       $query = "select * FROM \"AdmTabNames\" ".
                "WHERE (\"TabName\"=:TabName) ";
       
       $STH = $pdo->prepare($query);
       $STH->execute($PdoArr);
       
       if ($dp3 = $STH->fetch(PDO::FETCH_ASSOC)) {
         //print_r($dp);
         $TabNo2=$dp3['TabCode'];
         ScriptSelectionTabs ('Fld2', 'SelectField.php', 'Flds', "Par2=$TabNo2&");
       }
       else {
         echo ("<br>Bad table name: $Tab2 line:".__LINE__.' file:'.__FILE__);
       }
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
 
 
 echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
 echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
 echo (" <button onclick=\"return SelectTabFld('$Fld');\">...</button></td>");
 if ($TabNo2>0) {
   echo ("<td><a href='TabCard.php?TabCode=$TabNo2' target=Tab$TabNo2>$TabNo2</td>");
 }
 echo ("</tr><tr>");


 
 $Fld='Field2';
 $OutVal= $dp[$Fld];echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
 echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
 echo (" <button onclick=\"return SelectFld2Fld('$Fld');\">...</button></td>");
 echo ("</tr><tr>"); 
 
 $Fld='CondTab2';
 $OutVal= $dp[$Fld];echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
 echo ("<textarea Name='$Fld'  ID='$Fld'  cols=50 rows=3>{$dp[$Fld]}</textarea>");
 echo ("</td><td>");
 echo ("<button onclick=\"return SelectFld2Fld('$Fld');\">...</button></td>");
 echo ("</tr><tr>");

 //$Fld='AddFldsListTo';
 //$OutVal= $dp[$Fld];echo ("<td align=right>".GetStr($Fld).": </td><td>");
 //echo ("<textarea Name='$Fld'  ID='$Fld'  cols=50 rows=3>{$dp[$Fld]}</textarea>");
 //echo ("</td><td>");
 //echo ("<button onclick=\"return SelectFld1Fld('$Fld');\">...</button></td>");
 //echo("</td>");
 //echo ("</tr><tr>");
 
 echo ("<td colspan=4><hr></td></tr><tr>");
 
 $Fld='AddConnFldFrom';
 $OutVal= $dp[$Fld];echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
 echo ("<textarea Name='$Fld'  ID='$Fld'  cols=50 rows=2>{$dp[$Fld]}</textarea>");
 echo ("</td><td>");
 echo ("<button onclick=\"return SelectFld2Fld('$Fld');\">...</button></td>");
 echo("</td>");
 echo ("</tr><tr>");

 echo ("</tr><tr>");

 $Fld='AddConnFldTo';
 $OutVal= $dp[$Fld];echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
 echo ("<textarea Name='$Fld'  ID='$Fld'  cols=50 rows=2>{$dp[$Fld]}</textarea>");
 echo ("</td><td>");
 echo ("<button onclick=\"return SelectFld1Fld('$Fld');\">...</button></td>");
 echo("</td>");
 echo ("</tr><tr>");
 
 
 //========================= 2016-06-05 ===========
 echo ("<td colspan=4><hr></td></tr><tr>");


 $Fld='AddFldsListFrom';
 $OutVal= $dp[$Fld];echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
 echo ("<textarea Name='$Fld'  ID='$Fld'  cols=50 rows=3>{$dp[$Fld]}</textarea>");
 echo ("</td><td>");
 echo ("<button onclick=\"return SelectFld2Fld('$Fld');\">...</button></td>");
 echo("</td>");
 echo ("</tr><tr>");

 $Fld='AddFldsListTo';
 $OutVal= $dp[$Fld];echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
 echo ("<textarea Name='$Fld'  ID='$Fld'  cols=50 rows=3>{$dp[$Fld]}</textarea>");
 echo ("</td><td>");
 echo ("<button onclick=\"return SelectFld1Fld('$Fld');\">...</button></td>");
 echo("</td>");
 echo ("</tr><tr>");

 $Fld='SelectViewName';
 $OutVal= $dp[$Fld];echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
 echo ("<input type=text Name='$Fld'  ID='$Fld'  size=30 Value='{$dp[$Fld]}'>");
 echo ("</td><td>");
 echo ("<button onclick=\"return SelectFld1Fld('$Fld');\">...</button></td>");
 echo("</td>");
 echo ("</tr><tr>");


 echo ("<td colspan=2 align=right><input type=submit value='".GetStr($pdo, 'Save').
       "'></td></tr></table></form>");


echo ("<hr><br><a href='AdmTab2TabList.php'>".GetStr($pdo, 'List')."</a>");
echo (" | <a href='AdmTab2TabDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
      GetStr($pdo, 'Delete')."</a>");

