<?php
session_start();

include ("../setup/common_pg.php");

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>Tab Index Edit</title></head>
<body>
<?php

CheckLogin1();
CheckRight1 ($pdo, 'Admin');

//print_r ($_REQUEST);

//SELECT  FROM  WHERE 1

$TabName='AdmTabIndx';
$Frm='Tab';

$Fields=array('TabCode', 'IndxType', 'IndxName');


$TabNo= addslashes ($_REQUEST['TypeId']);
$IndxName= addslashes ($_REQUEST['IndxName']);

if ($TabNo == '') {
  $TabNo= addslashes ($_REQUEST['TabCode']);
}

$PdoArr = array();
try {

if ($TabNo == '') {
  if ($_REQUEST['New']!=1) { 
    die ("<br> Error table code ");
  }
  else {

    $query = "select max(\"TabCode\") \"TC\" ".
             "FROM \"$TabName\" ";

    //echo ($query);
    $STH = $pdo->prepare($query);
    $STH->execute();

    if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
      $TabNo= $dp['TC']+1;
      echo ("<br> TabNo: $TabNo ");
    }
  }
}

// AdmTabNames
// TabName, TabDescription, TabCode, TabEditable
$query = "select * from \"AdmTabNames\" ". 
         "where (\"TabCode\" = :TabNo)"; 

$PdoArr['TabNo']= $TabNo;
$STH = $pdo->prepare($query);
$STH->execute($PdoArr);


if ($dp5 = $STH->fetch(PDO::FETCH_ASSOC)) {
  echo ("<br>{$dp5['TabName']}<br>");
}



echo ("<br><a href='TabList.php'>".GetStr($pdo,'List')."</a>");

$query = "select * FROM \"AdmTabIndx\" where \"TabCode\"=:TabNo and \"IndxName\"= :IndxName";

  //echo ($query);
$PdoArr['IndxName']= $IndxName;

  echo ('<br><b>'.GetStr($pdo, 'Index'). '</b>');
  
$STH = $pdo->prepare($query);
$STH->execute($PdoArr);  
  
  $dp=array();
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  };

  echo ('<form method=post action="TabIndxSave.php">'.
        "<input type=hidden Name=TabCode Value='$TabNo'>".
        "<input type=hidden Name=IndxOldName Value='{$dp['IndxName']}'>");
        
  if ($_REQUEST['New']==1) {
    echo ("<input type=hidden Name=New Value='1'>");    
  }        
  echo ('<table><tr>');
  
  $Fld= 'IndxType';
  echo ( "<td align=right>".GetStr($pdo,$Fld).': </td>'.
         "<td>".
         EnumSelection($pdo,$Fld,$Fld,$dp[$Fld])."</td></tr><tr>");

  $Fld= 'IndxName';
  echo ( "<td align=right>".GetStr($pdo,$Fld).': </td>'.
         "<td><input type=text Name='$Fld' size=50 Value='{$dp[$Fld]}'></td></tr>");
  
  echo ( "<tr><td align=right colspan=2>".
         "<input type=submit></td></tr>");
      
  echo('</table></form>');

//===============================================================================
$query = "select * ".
         "FROM \"AdmTabIndxFlds\" where \"TabCode\"=:TabNo and \"IndxName\"=:IndxName";

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

$Param1= array ();
$OrdArr= array ();
while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  $Param1[$dp['FldNo']] = $dp['FldNo'];  
  $OrdArr[$dp['FldNo']] = $dp['Ord'];  
}
//===============================================================================


$i=0;

$enFld   = array ('DocParamsUOM'=>'DocParamsUOM','DocParamType'=>'DocParamType');
$FldType = array ('Index'=>'bool1');

//================================================================================
$TabName1='AdmTabFields';
echo ("<br><hr>");

  echo ('<form method=post action="TabIndxFldsSave.php">'.
        "<input type=hidden Name=TypeId Value='$TabNo'>".
        "<input type=hidden Name=IndxName Value='$IndxName'>");

//SELECT 

$Fld1= array ( 'ParamName', 'DocParamType', 
              'DocParamsUOM', 'AddParam',  'Index');
// FROM 'AdmTabFields' WHERE 1

$query = "select * ".
         "FROM \"$TabName1\" where \"TypeId\"=:TabNo order by \"Ord\", \"ParamNo\"";

$PdoArr = array();
$PdoArr['TabNo']= $TabNo;

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

$dp=array();

echo ("<table><tr class=header>");

$Fld='ParamNo';
echo ("<th>".GetStr($pdo, $Fld). "</th>");

foreach ( $Fld1 as $Fld) {
  echo ("<th>".GetStr($pdo,$Fld). "</th>");
}


$i=0;

$enFld   = array ('DocParamsUOM'=>'DocParamsUOM','DocParamType'=>'DocParamType');
$FldType = array ('Index'=>'bool1');


while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  $i=NewLine($i);
  $Fld='ParamNo';
  
  echo ("<td><a href='TabFldCard.php?TypeId=$TabNo&FldNo={$dp[$Fld]}'>{$dp[$Fld]}</td>");

  foreach ( $Fld1 as $Fld) {
    if ( $enFld[$Fld]!='') {
      echo ("<td>".GetEnum($pdo, $enFld[$Fld], $dp[$Fld])."</td>");
    }
    else
    if ( $FldType[$Fld]=='bool1') {
      $Ch='';

      //--------------------------------------------

      //---------------------------------------------
      if ( $Param1[$dp['ParamNo']] != '') {
        $Ch=' checked ';
      }
      echo ("<td align=center><input type=checkbox Name=Par[{$dp['ParamNo']}] value=1 $Ch></td>");
    }
    else
      echo ("<td>{$dp[$Fld]}</td>");
  }

  echo ("<td><input type=text Name=IndxOrd[{$dp['ParamNo']}] value='{$OrdArr[$dp['ParamNo']]}' size=4></td>");
  
}

  echo ( "</tr><tr><td align=right colspan=2>".
         "<input type=submit></td></tr>");
      
  echo('</table></form>');

//=====================================================================================
echo ("<br><hr> <a href='CreateIndx.php?TabNo=$TabNo&IndxName=$IndxName' onclick=' return confirm(\"Create index $IndxName?\");'>Create index</a>");
echo (" | <a href='DropIndx.php?TabNo=$TabNo&IndxName=$IndxName' onclick=' return confirm(\"Drop index $IndxName?\");'>Drop index</a>");
echo (" | <a href='TabIndxDelete.php?TabNo=$TabNo&IndxName=$IndxName' onclick=' return confirm(\"Delete index $IndxName?\");'>Delete index from table</a>");


//=====================================================================================
$TabName1='AdmTabIndx';

echo ("<br><hr>");



  echo ('<form method=post action="TabIndxCard.php">'.
        "<input type=hidden Name=TypeId Value='$TabNo'>".
        '<input type=hidden Name=New VALUE=1>'.
        "<input type=submit Value='".GetStr($pdo, 'New')."'></form>" );


//SELECT 
// SELECT 'TabCode', 'IndxType', 'IndxName' FROM 'AdmTabIndx' WHERE 1
$Fld1= array ( 'IndxType', 'IndxName', 'Fields');
// FROM 'AdmTabFields' WHERE 1

$query = "select * ".
         "FROM \"$TabName1\" where \"TabCode\"=:TabNo order by \"TabCode\", \"IndxName\"";

$PdoArr = array();
$PdoArr['TabNo']= $TabNo;

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);
$dp=array();

echo ("<table><tr class=header>");

//$Fld='ParamNo';
//echo ("<th>".GetStr($Fld). "</th>");

foreach ( $Fld1 as $Fld) {
  echo ("<th>".GetStr($pdo,$Fld). "</th>");
}

$i=0;

$enFld   = array ('IndxName'=>'IndxName');
$FldType = array ('NeedSeria'=>'bool', 'NeedBrand'=>'bool');


$query = "select * FROM \"AdmTabIndxFlds\" ".
         "where \"TabCode\"=:TabNo and \"IndxName\"=:IndxName order by \"TabCode\", \"IndxName\", \"Ord\"";

$STH4 = $pdo->prepare($query); 


while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  $i=NewLine($i);
  $Fld='IndxType';
  echo ("<td>".GetEnum($pdo,'IndxType', $dp[$Fld])."</td>");
  
  
  $Fld='IndxName';
  echo ("<td><a href='TabIndxCard.php?TabCode=$TabNo&IndxName={$dp[$Fld]}'>{$dp[$Fld]}</td>");
  $IndxName= $dp[$Fld];
  $PdoArr['IndxName']= $IndxName;
  $STH4->execute($PdoArr);


  $Des='';
  $Div='';
  while ($dp4 = $STH4->fetch(PDO::FETCH_ASSOC)) {
    $Des.=$Div.GetFldName($pdo, $TabNo, $dp4['FldNo']);
    $Div=', ';    
  }

  echo ("<td>$Des</td>");

}
echo ("</tr></table>");

}
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

//=====================================================================================
  
?>
</body>
</html>				       