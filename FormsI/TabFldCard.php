<?php
session_start();

include ("../setup/common_pg.php");

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<?php

//print_r ($_REQUEST);

//SELECT 'TabName', 'TabDescription', 'TabCode' FROM  WHERE 1
CheckLogin1();
CheckRight1 ($pdo, 'Admin');


$TabName='AdmTabNames';
$Frm='Tab';

$Fields=array('TabName', 'TabDescription', 'TabEditable');

$TabNo= $_REQUEST['TypeId'];
$FldNo= $_REQUEST['FldNo'];

echo ("<title>Tab $TabNo Field $FldNo card</title></head>
<body>");


if ($TabNo == '') {
  die ("<br> Error table code ");
}




echo ("<br><a href='TabCard.php?TabCode=$TabNo'>Table card</a>");


$query = "select * ".
         "FROM \"$TabName\" where \"TabCode\"=:TabNo";

  //echo ($query);

  $PdoArr = array();
  $PdoArr['TabNo']= $TabNo;


  echo ('<br><b>Table field</b>');

try {
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);

  $dp=array();
  $VDL_TabName='';
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $VDL_TabName=$dp['TabName'];
  };

        //"<input type=hidden Name=TabCode Value='$TabNo'>".
  echo('<table><tr>');
  
  $Fld= 'TabName';
  echo ( "<td align=right>".GetStr($pdo,$Fld).': </td>'.
         "<td>{$dp[$Fld]}</td></tr><tr>");

  $Fld= 'TabDescription';
  echo ( "<td align=right>".GetStr($pdo, $Fld).': </td>'.
         "<td>".DivTxt ($dp[$Fld])."</td></tr>");
  
      
  echo('</table>');

$TabName1='AdmTabFields';
echo ("<br><hr>");

//SELECT 

$Fld1= array ('ParamNo', 'ParamName','DocParamType', 
                 
               'Ord', 'AddParam', 'AutoInc', 'Description', 
                'ShortInfo', 'EnumLong'
);

$enFld   = array ('DocParamsUOM'=>'DocParamsUOM','DocParamType'=>'DocParamType');
$FldType = array ('NeedSeria'=>'bool', 'AutoInc'=>'bool', 'NeedBrand'=>'bool',
                  'BinCollation'=>'bool', 'ShortInfo'=>'bool', 'EnumLong'=>'bool' );

// FROM 'AdmTabFields' WHERE 1

$VDL_TabFld='';
$query = "select * ".
         "FROM \"$TabName1\" where \"TypeId\"=:TabNo and \"ParamNo\"=:FldNo order by \"Ord\", \"ParamNo\"";

$PdoArr = array();
$PdoArr['TabNo']= $TabNo;
$PdoArr['FldNo']= $FldNo;

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);


$dp=array();

echo ("<table><tr><td>");


echo ('<form method=post action="'.$Frm.'FldSave.php">'.
       "<input type=hidden Name=TabCode Value='$TabNo'>".
       "<input type=hidden Name=FldNo Value='$FldNo'>".
       "<table><tr>");

$i=0;


if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  $VDL_TabFld=$dp['ParamName'];
}

if (!empty($_REQUEST['LastFld'])){
  if ($_REQUEST['New']==1) {
    $NextFld = ceil($_REQUEST['LastFld']/10) *10 + 10; 
    $dp['ParamNo'] = $NextFld;
    $dp['Ord'] = $NextFld;
  }

}


  foreach ( $Fld1 as $Fld) {
    $i=NewLine($i);
    
    if ($enFld[$Fld]!='') {
      echo ("<td align=right>".GetStr($pdo,$Fld).": </td><td>".
            EnumSelection ($pdo, $enFld[$Fld], $Fld, $dp[$Fld])."</td>");
    }
    else    
    if ($FldType[$Fld]=='bool') {
      $Ch='';
      if ( $dp[$Fld] == 1) {
        $Ch=' checked ';
      }
      echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>".
            "<input type=checkbox Name='$Fld' Value='1' $Ch></td>");
    }
    else
      echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>".
            "<input type=text Name='$Fld' Value='{$dp[$Fld]}'></td>");
  }  


echo ( "</tr><tr><td align=right colspan=2>".
         "<input type=submit></td>");

echo ("</tr></table></form>");

echo ("</td><td>");


//=============================================================================
if ($VDL_TabFld!= '') {
 /*
  echo("<hr><h4>".GetStr($pdo, 'AdmFieldsAddFunc')."</h4>");
  $query = "select * FROM AdmFieldsAddFunc ". 
           "where (TabName='$VDL_TabName') and ".
                 "(FldName='$VDL_TabFld') order by Id ";
  $sql2 = $pdo->query ($query)
            or die("Invalid query:<br>$query<br>" . $pdo->error);

  echo('<table><tr class=header>');
  echo('<th>'.GetStr($pdo, 'Id').'</th>');
  echo('<th>'.GetStr($pdo, 'AddFunc').'</th>');
  echo('<th></th>');

  $i=0;
  while ($dpL = $sql2->fetch_assoc()) {
    $i=NewLine($i);

    echo ("<td><a href='AdmFieldsAddFuncCard.php?Id={$dpL['Id']}'>{$dpL['Id']}</a></td>");
    echo ("<td align=center>");
    echo (GetEnum($pdo, "FldAddFunction", $dpL['AddFunc'])."</td>");
    echo ("<td>{$dpL['Param']}</td>");
    
  }
  echo("</tr></table>");
  echo("<a href='AdmFieldsAddFuncCard.php?New=1&TabName=$VDL_TabName&FldName=$VDL_TabFld'>".GetStr($pdo, "Add")."</a>");
  */
}

echo ("</td></tr></table>");


 
//==============================================================================
echo ("<a href='TabFldDelete.php?TabNo=$TabNo&FldNo=$FldNo' onclick=' return confirm(\"Delete?\");'>Delete</a>");
echo (" | <a href='DropFld.php?TabNo=$TabNo&FldNo=$FldNo' onclick=' return confirm(\"Drop field from DB?\");'>Drop field from DB</a>");

echo (" | <a href='CreateFld.php?TabNo=$TabNo&FldNo=$FldNo' onclick=' return confirm(\"Create field?\");'>Create field</a>");
echo (" | <a href='CreateFld.php?TabNo=$TabNo&FldNo=$FldNo&Hist=1' onclick=' return confirm(\"Create field in HistoryTab?\");'>Create History field</a>");

//=========================================================================================  
if ($VDL_TabName!='') {

echo ("<hr><br><h4>".GetStr($pdo, 'TableLink')."</h4>");

$query = "select * FROM \"AdmTab2Tab\" ".
         "where \"TabName\"=:TabName and \"FldName\"=:FldName order by \"TabName\",\"FldName\",\"LineNo\" ";

$PdoArr = array();
$PdoArr['TabName']= $VDL_TabName;
$PdoArr['FldName']= $VDL_TabFld;

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

echo('<table><tr class=header>');
  echo('<th>'.GetStr($pdo, 'FldName').'</th>');
  echo('<th>'.GetStr($pdo, 'LineNo').'</th>');
  echo('<th>'.GetStr($pdo, 'TabCond').'</th>');
  echo('<th>'.GetStr($pdo, 'Tab2').'</th>');
  echo('<th>'.GetStr($pdo, 'Field2').'</th>');
  echo('<th>'.GetStr($pdo, 'CondTab2').'</th>');
  echo('</tr>');
  while ($dpL = $STH->fetch(PDO::FETCH_ASSOC)) {
    echo ("<tr>");

    echo("<td>{$dpL['FldName']}</td>");
    echo("<td><a href='AdmTab2TabCard.php?TabName=$VDL_TabName&FldName=$VDL_TabFld".
         "&LineNo={$dpL['LineNo']}'>{$dpL['LineNo']}</a></td>");
    echo("<td>{$dpL['TabCond']}</td>");
    echo("<td>{$dpL['Tab2']}</td>");
    echo("<td>{$dpL['Field2']}</td>");
    echo("<td>{$dpL['CondTab2']}</td>");
    echo("</tr>");
  }
  echo("</table>");
  echo("<a href='AdmTab2TabCard.php?New=1&TabName=$VDL_TabName&FldName=$VDL_TabFld'>".
       GetStr($pdo, "Add")."</a>");
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
                       