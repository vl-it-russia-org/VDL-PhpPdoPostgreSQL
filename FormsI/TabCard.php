<?php
session_start();

include ("../setup/common_pg.php");

CheckLogin1();
CheckRight1 ($pdo, 'Admin');


include "../js_module.php";
$TabNo= '';
if (!empty ($_REQUEST['TabCode'])) {
  $TabNo=$_REQUEST['TabCode'];
};

//------- For Ext Tables --------- 
ScriptSelectionTabs('AdmTabNames78', 'SelectAdmTabNames.php', 'AdmTabNames', 'SelId=78&');

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="icon" href="TabCard.ico" type="image/x-icon">
<?php

include ("TableFunc.php");

print_r($_REQUEST);


//SELECT 'TabName', 'TabDescription', 'TabCode' FROM  WHERE 1

$TabName='AdmTabNames';
$Frm='Tab';

$FldNames=array('TabName','TabDescription','TabCode','TabEditable','AutoCalc');



$SqlHist='';
$IsAnalitic=0;

if ($TabNo == '') {

echo ("
<title>Table create</title></head>
<body>
");
  
try {

  
  if ($_REQUEST['New']!=1) { 
    die ("<br> Error table code ");
  }
}
catch (PDOException $e) {
  echo ("<hr> Line ".__LINE__."<br>");
  echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
  print_r($PdoArr);	
  die ("<br> Error: ".$e->getMessage());
}



}
else {

  $SqlHist=" | <b>$db_base</b> | <a href='SqlLogList.php?Fltr_TabNo=$TabNo' target=TabHist$TabNo>History</a> ";

}



$Kind="Table";

$IdArr=array (
  10=> array ('BegIndx'=> 100, 'EndIndx'=>5000, 'Descr'=>'CoreTables'),
  15=> array ('BegIndx'=> 5001, 'EndIndx'=>10000, 'Descr'=>'SysemTables'),
  20=> array ('BegIndx'=> 10001, 'EndIndx'=>50000, 'Descr'=>'LocalizationTables'),
  30=> array ('BegIndx'=> 50001, 'EndIndx'=>70000, 'Descr'=>'UsersTables') );



echo ("<br><table><tr><td><a href='TabList.php'>".GetStr($pdo, 'List')."</a>$SqlHist| </td><td>".
       " <form method=get action='TabList.php'><input type=text Name=Fltr_TabName> ".
       "<input type=submit value='".GetStr($pdo,'Filter')."'></form></td>".
       "<td>Rules for TabCode:".
       "<table><tr class=header><th>Begin</th><th>End</th><th>Description</th><th>Last code</th>");

$i=0;

try {

  $query = "select \"TabCode\" from \"AdmTabNames\" ". 
           "where (\"TabCode\" between :BegIndx and :EndIndx ) ".
           "order by \"TabCode\" desc limit 1 offset 0"; 

  $STH = $pdo->prepare($query);
  $PdoArr = array();

foreach ( $IdArr as $Indx=> $Arr) {


  // AdmTabNames
  // TabName, TabDescription, TabCode, TabEditable, 
  // AutoCalc, CalcTableName

  
  $PdoArr['BegIndx']= $Arr['BegIndx'];
  $PdoArr['EndIndx']= $Arr['EndIndx'];

  $STH->execute($PdoArr);
  
  $LastCode=0;
  if ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
    $LastCode=$dp2['TabCode'];
  }

  $i++;
  $class='';
  if ($i==2) {
    $class=' class=even ';
    $i=0;
  }

  echo ("<tr$class><td align=right>{$Arr['BegIndx']}</td><td align=right>{$Arr['EndIndx']}</td>".
        "<td>{$Arr['Descr']}</td><td>{$LastCode}</td></tr>");
}
echo ("</table></td>".
       "</tr></table>");

}
catch (PDOException $e) {
  echo ("<hr> Line ".__LINE__."<br>");
  echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
  print_r($PdoArr);	
  die ("<br> Error: ".$e->getMessage());
}



$dp=array();
try {


if ($TabNo!= '') {

$query = "select * ".
         "FROM \"$TabName\" where (\"TabCode\"=:TabNo)";

$PdoArr = array();
$PdoArr['TabNo']= $TabNo;


  //echo ($query);
  $STH2 = $pdo->prepare($query);
  $STH2->execute($PdoArr);


  if ($dp = $STH2->fetch(PDO::FETCH_ASSOC)) {

    $TabName = $dp['TabName'];

    // AdmTabsAddFunc
    // Id, TabName, AddFunc, Param
    $query = "select * from \"AdmTabsAddFunc\" ". 
             "where (\"TabName\" =:TabName) and (\"AddFunc\"=:AddFunc)"; 

    $PdoArr = array();
    $PdoArr['TabName']= $TabName;
    $PdoArr['AddFunc']= 5;

    $STH22 = $pdo->prepare($query);
    $STH22->execute($PdoArr);

    
    
    
    if ($dp22 = $STH22->fetch(PDO::FETCH_ASSOC)) {
      $Kind='View';
    }
  };
}          
echo ("<br><H2>$Kind</H2>");
    
  
echo ("
<title>{$dp['TabName']} $Kind $TabNo</title></head>
<body>
");
  
  

  echo ('<form method=post action="'.$Frm.'Save.php">'.
        "<input type=hidden Name=OldTabCode Value='$TabNo'>");
        
  if ($_REQUEST['New']==1) {
    echo ("<input type=hidden Name=New Value='1'>");    
  }


     
  
  
  echo ("<table><tr><td>".
        '<table><tr>');


  $Fld= 'TabCode';
  echo ( "<td align=right>".GetStr($pdo, $Fld).': </td>'.
         "<td><input type=text Name='$Fld' value='{$dp[$Fld]}' size=10></td></tr><tr valign=top>");

  
  $Fld= 'TabName';
  echo ( "<td align=right>".GetStr($pdo, $Fld).': </td>'.
         "<td><input type=text Name='$Fld' value='{$dp[$Fld]}' size=40></td></tr><tr valign=top>");
  $TabName5=$dp[$Fld];
  $VDL_TabName=$dp[$Fld];
  
  
  $Fld= 'TabDescription';
  echo ( "<td align=right>".GetStr($pdo, $Fld).': </td>'.
         "<td><textarea Name='$Fld' cols=40 rows=5>{$dp[$Fld]}</textarea></td></tr>");
  
  $Fld= 'TabEditable';
  echo ( "<td align=right>".GetStr($pdo, $Fld).': </td>'.
         "<td><textarea Name='$Fld' cols=40 rows=5>{$dp[$Fld]}</textarea></td></tr>");


  $Fld= 'ChangeDt';
  echo ( "<tr><td align=right>".GetStr($pdo, $Fld).': </td>'.
         "<td>{$dp[$Fld]}</td></tr>");

  $Fld= 'Ver';
  echo ( "<tr><td align=right>".GetStr($pdo, $Fld).': </td>'.
         "<td><input type=text Name='$Fld' value='{$dp[$Fld]}' size=40></td></tr>");



  echo ( "<tr><td align=right colspan=2>".
         "<input type=submit></td></tr>");
      
  echo('</table></form>'.
       "</td><td>");

if ($VDL_TabName!='') {
  //==============================================================
  echo("<hr><h4>".GetStr($pdo, 'AdmTabsAddFunc')."</h4>");
  
  $query = "select * FROM \"AdmTabsAddFunc\" ". 
           "where (\"TabName\"=:TabName)  order by \"Id\" ";
  
  $PdoArr = array();
  $PdoArr['TabName']= $VDL_TabName;
  
  $STH22 = $pdo->prepare($query);
  $STH22->execute($PdoArr);
  

  echo('<table><tr class=header>');
  echo('<th>'.GetStr($pdo, 'Id').'</th>');
  echo('<th>Code</th>');
  echo('<th>'.GetStr($pdo, 'AddFunc').'</th>');
  echo('<th></th>');

  $i=0;
  while ($dpL = $STH22->fetch(PDO::FETCH_ASSOC)) {
    $i=NewLine($i);

    echo ("<td><a href='AdmTabsAddFuncCard.php?Id={$dpL['Id']}'>{$dpL['Id']}</a></td>");
    echo ("<td align=center>{$dpL['AddFunc']}</td>");
    echo ("<td align=center>");
    echo (GetEnum($pdo, "TabAddFunction", $dpL['AddFunc'])."</td>");
    echo ("<td>{$dpL['Param']}</td>");

    if ( $dpL['AddFunc']==40) {
      $IsAnalitic=1;
    }

    
  }
  echo("</tr></table>");
  echo("<a href='AdmTabsAddFuncCard.php?New=1&TabName=$VDL_TabName'>".GetStr($pdo, "Add")."</a>");
}

echo ("</td></tr></table>");





$TabName1='AdmTabFields';
echo ("<br><hr>");

  echo ('<form method=post action="TabFldCard.php">'.
        "<input type=hidden Name=TypeId Value='$TabNo'>".
        '<input type=hidden Name=New VALUE=1>'.
        "<input type=submit Value='".GetStr($pdo, 'New')."'>".
        " | <a href='CopyFlds.php?TabNo=$TabNo'>Copy fileds</a>".
        "</form>" );


//SELECT 

$Fld1= array ( 'ParamName', //'NeedSeria', 
               'DocParamType', 'AddParam', 
              //'DocParamsUOM', 
                
              //'NeedBrand', 
              'Ord',   
              //'CalcFormula',
              'Description', 'AutoInc'
);
// FROM 'AdmTabFields' WHERE 1

$query = "select * ".
         "FROM \"$TabName1\" where \"TypeId\"=:TabNo order by \"Ord\", \"ParamNo\"";

$PdoArr = array();
$PdoArr['TabNo']= $TabNo;

$STH2 = $pdo->prepare($query);
$STH2->execute($PdoArr);



$dp=array();

$FldsList= '';
$FldsList1= '';
$FDiv='';

echo ("<table><tr class=header>");

$Fld='ParamNo';
echo ("<th>".GetStr($pdo, $Fld). "</th>");

foreach ( $Fld1 as $Fld) {
  echo ("<th>".GetStr($pdo, $Fld). "</th>");
}

echo ("<th>Ties</th>");

$i=0;

$enFld   = array ('DocParamsUOM'=>'DocParamsUOM','DocParamType'=>'DocParamType');
$FldType = array ('NeedSeria'=>'bool', 'AutoInc'=>'bool');
$txtFld =array ('Description'=>1, 'ParamName'=>1);

$LastFld='';
while ($dp = $STH2->fetch(PDO::FETCH_ASSOC)) {
  $i=NewLine($i);
  $Fld='ParamNo';
  
  $LastFld=$dp[$Fld];
  
  $FldName=$dp['ParamName'];
  $FldsList.="$FDiv$FldName";
  $FldsList1.="$FDiv'$FldName'";
  $FDiv=', ';
  
  echo ("<td align=center><a href='TabFldCard.php?TypeId=$TabNo&FldNo={$dp[$Fld]}' ".
        "Name='Fld{$dp[$Fld]}'>{$dp[$Fld]}</td>");

  $Add2='';
  if ($dp['DocParamType']==50) {
    $Add2="<a href=''";
  }

  foreach ( $Fld1 as $Fld) {
    
    if (  ($Fld == 'AddParam') and ($dp['DocParamType']==50)) {
      if ( $dp[$Fld] != '') {
        echo ("<td><a href='EnumFrm.php?Enum={$dp[$Fld]}' ".
              "target=TranslEnumFrm>{$dp[$Fld]}</a></td>");
      }
      else {
        echo ( "<td></td>");
      }
    }
    else    
    if ( $txtFld[$Fld]!='') {
      echo ("<td><a href='TranslateFrm.php?Enum={$dp[$Fld]}' target=TranslFrm>{$dp[$Fld]}</a></td>");
    }
    else
    if ( $enFld[$Fld]!='') {
      if ($Fld=='DocParamType') {
        $BOL='';
        $BOLE='';
        if ($dp['EnumLong']==1) {
          $BOL="<b title='Long enum'>";
          $BOLE='</b>';
        }
        echo ("<td>$BOL".GetEnum($pdo, $enFld[$Fld], $dp[$Fld])."$BOLE</td>");
      }
      else
        echo ("<td>".GetEnum($pdo, $enFld[$Fld], $dp[$Fld])."</td>");
    }
    else
    if ( $FldType[$Fld]=='bool') {
      $Ch='';
      if ( $dp[$Fld] == 1) {
        $Ch=' checked ';
      }
      echo ("<td align=center><input type=checkbox $Ch></td>");
    }
    else
      echo ("<td>{$dp[$Fld]}</td>");
  }
  
  $Tie=GetFldTieStr($pdo, $TabName5, $FldName);
  echo ("<td>$Tie</td>");
}
echo ("</tr></table><hr>");

echo ("<a href='TabFldCard.php?TypeId=$TabNo&New=1&LastFld=$LastFld'>".GetStr($pdo, 'NewField')."</a>");

echo ("<br><hr>");

echo ($TabName5.'<br>'.$FldsList.'<br><br>'.
     $TabName5.'<br>'.$FldsList1);

//=====================================================================================
//=====================================================================================
$TabName1='AdmTabIndx';

echo ("<br><hr>");



  echo ('<form method=post action="TabIndxCard.php">'.
        "<input type=hidden Name=TypeId Value='$TabNo'>".
        '<input type=hidden Name=New VALUE=1>'.
        "<input type=submit Value='".GetStr($pdo, 'New')."'>".
        "</form>" );


//SELECT 
// SELECT 'TabCode', 'IndxType', 'IndxName' FROM 'AdmTabIndx' WHERE 1
$Fld1= array ( 'IndxType', 'IndxName', 'Fields');
// FROM 'AdmTabFields' WHERE 1

$query = "select * ".
         "FROM \"$TabName1\" where (\"TabCode\"=:TabNo) order by \"TabCode\", \"IndxName\"";

$PdoArr = array();
$PdoArr['TabNo']= $TabNo;



try {
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

$dp=array();

echo ("<table><tr class=header>");

//$Fld='ParamNo';
//echo ("<th>".GetStr($Fld). "</th>");

foreach ( $Fld1 as $Fld) {
  echo ("<th>".GetStr($pdo, $Fld). "</th>");
}

$i=0;

$enFld   = array ('IndxName'=>'IndxName');
$FldType = array ('NeedSeria'=>'bool', 'NeedBrand'=>'bool');

$HavePK=0;
$PK_Indx =$TabName5.'_pkey';

//echo ("<br>$PK_Indx<br>");
$query2 = "select * FROM \"AdmTabIndxFlds\" ".
          "where (\"TabCode\"=:TabNo) and (\"IndxName\"=:IndxName) order by \"TabCode\", \"IndxName\", \"Ord\"";

$STH4 = $pdo->prepare($query2);

//echo ("<br> Line ".__LINE__.": $query2<br>");


while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  $i=NewLine($i);
  $Fld='IndxType';
  echo ("<td>".GetEnum($pdo, 'IndxType', $dp[$Fld])."</td>");
  
  
  $Fld='IndxName';
  echo ("<td><a href='TabIndxCard.php?TabCode=$TabNo&IndxName={$dp[$Fld]}' Name='Indx_{$dp[$Fld]}'>{$dp[$Fld]}</a></td>");
  $IndxName= $dp[$Fld];
  $PdoArr['IndxName']= $IndxName;

  if ($IndxName == $PK_Indx ) {
    //echo ("<br>IN: $Indx_Name<br>");
    $HavePK=1;
  }
  
  $STH4->execute($PdoArr);

  //echo ("<br> Line ".__LINE__.": $query2<br>");
  //print_r($PdoArr);
  //echo ("<br>");


  $Des='';
  $Div='';
  while ($dp4 = $STH4->fetch(PDO::FETCH_ASSOC)) {
    $Des.=$Div.GetFldName($pdo, $TabNo, $dp4['FldNo']);
    $Div=',';    
  }

  echo ("<td>$Des</td>");

}

if ($HavePK==0) {
  echo ("</tr><tr><td colspan=3><a href='CreatePK.php?TabNo=$TabNo'>".
        GetStr($pdo, 'CreatePK').
        "</td>");
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
if ($TabNo!='') {
/*

  echo("<hr><h4 Id=TabMaster>".GetStr($pdo, 'AdmTabMaster')."</h4>");
  $query = "select * FROM AdmTabMaster where TabNo='$TabNo' order by TabNo,LineNo ";
  $sql2 = $pdo->query ($query)
            or die("Invalid query:<br>$query<br>" . $pdo->error);

  echo('<table><tr class=header>');
  echo('<th>'.GetStr($pdo, 'LineNo').'</th>');
  echo('<th>'.GetStr($pdo, 'FldName').'</th>');
  echo('<th>'.GetStr($pdo, 'Val').'</th>');
  echo('<th>'.GetStr($pdo, 'MasterTabName').'</th>');
  echo('<th>'.GetStr($pdo, 'FldsCorresp').'</th>');
  $i=0;
  while ($dpL = $sql2->fetch_assoc()) {
    $i=NewLine($i);

    echo ("<td align=center>");

    echo("<a href='AdmTabMasterCard.php?TabNo={$dpL['TabNo']}&LineNo={$dpL['LineNo']}'>");
    echo ("{$dpL['LineNo']}</a></td>");
    echo ("<td>");
    echo ("{$dpL['FldName']}</td>");
    echo ("<td>");
    echo ("{$dpL['Val']}</td>");
    echo ("<td>");
    echo ("{$dpL['MasterTabName']}</td>");
    echo ("<td>");
    echo ("{$dpL['FldsCorresp']}</td>");
    
  }
  echo("</tr></table>");
  echo("<a href='AdmTabMasterCard.php?New=1&TabNo=$TabNo'>".GetStr($pdo, "Add")."</a>");
*/
//----------------------------------------------------------------------------------------------------------

echo ("<hr><br><h4>".GetStr($pdo, 'View')."</h4>");

$query = "select * FROM \"AdmView\" where \"TabNo\"=:TabNo order by \"TabNo\",\"ViewNo\" ";

$PdoArr = array();

$PdoArr['TabNo']= $TabNo;

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);
    
echo('<table><tr class=header>');
  echo('<th>'.GetStr($pdo, 'ViewNo').'</th>');
  echo('<th>'.GetStr($pdo,'ViewName').'</th>');
  $i=0;
  while ($dpL = $STH->fetch(PDO::FETCH_ASSOC)) {
    $i=NewLine($i);

    echo("<td><a href='AdmViewCard.php?TabNo=$TabNo&ViewNo={$dpL['ViewNo']}'>{$dpL['ViewNo']}</a></td>");
    echo("<td>{$dpL['ViewName']}</td>");

    if ($IsAnalitic==1) {
      echo("<td><a href='AnaliticViewSetup.php?AnId=$TabNo&View={$dpL['ViewName']}' ".
                "target=An$TabNo title='Analitic setup'>...</a></td>");
    
    }


    
  }
  echo("</tr></table>");
  echo("<a href='AdmViewCard.php?New=1&TabNo=$TabNo'>".GetStr($pdo, "Add")."</a>");
}
//=====================================================================================
if ($VDL_TabName != '') {
/*
  echo("<hr><h4>".GetStr($pdo, 'Analitics')."</h4>");
  $query = "select * FROM Analitics where BasedOnTable='$VDL_TabName' order by Id ";
  $sql2 = $pdo->query ($query)
            or die("Invalid query:<br>$query<br>" . $pdo->error);

  echo('<table><tr class=header>');
  echo('<th>'.GetStr($pdo, 'Id').'</th>');
  echo('<th>'.GetStr($pdo, 'AnaliticName').'</th>');
  echo('<th>'.GetStr($pdo, 'Description').'</th>');
  echo('<th>'.GetStr($pdo, 'BasedOnTable').'</th>');
  $i=0;
  while ($dpL = $sql2->fetch_assoc()) {
    $i=NewLine($i);

    echo ("<td><a href='AnaliticsCard.php?Id={$dpL['Id']}'>");
    echo ("{$dpL['Id']}</a></td>");
    echo ("<td>");
    echo ("{$dpL['AnaliticName']}</td>");
    echo ("<td>");
    echo ("{$dpL['Description']}</td>");
    echo ("<td>");
    echo ("{$dpL['BasedOnTable']}</td>");
    
  }
  echo("</tr></table>");
  echo("<a href='AnaliticsCard.php?New=1&BasedOnTable='$VDL_TabName'>".GetStr($pdo, "Add")."</a>");

*/
}

}
catch (PDOException $e) {
  echo ("<hr> Line ".__LINE__."<br>");
  echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
  print_r($PdoArr);	
  die ("<br> Error: ".$e->getMessage());
}

//=====================================================================================
echo ("<hr><br>");
echo ("<a href='BuildFrmList.php?TabNo=$TabNo'>Build list</a> | ");
echo ("<a href='BuildTable.php?TabNo=$TabNo'>Create table</a> | ");
echo ("<a href='CreateTable.php?TabNo=$TabNo'>Make table in DB</a> | ");
echo ("<a href='CreateTableHist.php?TabNo=$TabNo'>Make History table in DB</a> | ");
echo ("<a href='ExportTabDescr.php?TabNo=$TabNo' target=TabDescr>Table descr</a> | ");
echo ("<a href='ExportTabValues.php?TabNo=$TabNo'>Table values</a> | ");
echo ("<a href='ExportTabValuesAll.php?TabNo=$TabNo&Rows=1000000' tiltle='For very long tables'>All values</a> | ");
echo ("<a href='BuildGetShortName.php?TabNo=$TabNo'>Short Name</a> | ");
echo ("<a href='BuildCycle.php?TabNo=$TabNo'>Cycle</a> | ");
echo ("<a href='XmlTable.php?TabNo=$TabNo'>XML</a> | ");
echo ("<a href='DropTable.php?TabNo=$TabNo' onclick='return confirm(\"Delete?\");'>".
      "Drop table</a> | ");

echo ("<a href='../Forms/{$TabName5}List.php'>Form</a> "); 

echo ("<hr><a href='FrmXmlTablesUpload.php' target=FrmUloadXml>Upload XML</a> | ");
  
echo ("<a href='TabSqlValuesGet.php?TabNo=$TabNo'>SQL export values</a> | ");
echo ("<a href='FrmSqlValuesUpload.php' target=SqlValuesUpload>Upload SQL values</a> | ");

?>
</body>
</html>
                       