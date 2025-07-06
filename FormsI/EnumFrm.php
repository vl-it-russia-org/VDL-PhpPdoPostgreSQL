<?php
session_start();

include ("../setup/common_pg.php");


?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="icon" href="../Img/TranslateEnum.ico" type="image/x-icon">
<title>Enum values</title></head>
<body>
<?php
include "../Translate.js";

// Типы документов -- в EnumValues

//CheckLogin1();

$EnName= addslashes($_REQUEST['Enum']);

$HaveRight=0;
if ( substr ($EnName, 0, 4)== 'Clf-') {
  $Pos = strrpos ( $EnName, '-');
  $ClassCode= substr($EnName, 0, $Pos);
  echo (  "<br>$ClassCode<br>");
  if (  HaveRight1($pdo, $ClassCode) ) {
    $HaveRight=1;
    echo (" have ");
  }
}


if ($HaveRight==0) {

  if (!HaveRight1 ($pdo, 'Translation')) {
    CheckRight1 ($pdo, 'Admin');
  } 

}

$AllLang=array ("EN", "RU", "FR");

echo ("<br><a href='EnumList.php'>List</a>");

echo(" | <a href='MakeMultiTranslation.php' target=MultiTransl>Multi line translation</a>"); 
echo(" | <a href='TranslateFrm.php' target=OneWordFrm>Translation One word</a>"); 


$Tab='EnumValues';
$Frm='Enum';

$EnName= addslashes ($_REQUEST['Enum']);
if ($EnName=='') {
  $EnName= addslashes ($_REQUEST['SubStr']);
}

echo(" | <a href='MakeEnumMultiTranslation.php?Enum=$EnName' target=MultiEnumTransl>Multi enum translation</a>"); 


$FullRef1='';

echo ('<form method=get action="'.$Frm.'Frm.php">'.
      "Enum:<input type=text size=30 Name=Enum value='$EnName'> ".
      "<input type=submit value='Set'></form><br><hr>");

$Lang= $_SESSION['Lang'];
if ($Lang=='') {
  $_SESSION['Lang']='RU';
  $Lang='RU';
}

$CurrType= $_REQUEST['CurrType'];


// SELECT `EnumName`, `EnumVal`, `Lang`, `EnumDescription` FROM `EnumValues` WHERE 1

echo ('<table><tr><td><b>'.GetStr($pdo,$Frm).
      '</b></td>');

$query = "select * FROM \"$Tab\" ".
         "where \"EnumName\"=:EnName and \"Lang\"=:Lang ".
         "order by \"EnumName\", \"EnumVal\" ";

$PdoArr = array();
$PdoArr['EnName'] = $EnName;
$PdoArr['Lang']=$Lang;

try {
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);


echo ('<table><tr><td><form method=get action="'.$Frm.'Frm.php">'.
      "<input type=hidden Name=Enum value='$EnName'>");

echo (GetStr($pdo, 'Value').": <select Name=CurrType>");

$CurrName='';
while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  $Sel='';
  if ( $dp['EnumVal']==$CurrType) {
    $Sel=' selected';
    $CurrName= $dp['EnumDescription'];
  }
  echo ("<option value='{$dp['EnumVal']}' $Sel>{$dp['EnumDescription']} [{$dp['EnumVal']}]");
}

}
catch (PDOException $e) {
  echo ("<hr> Line ".__LINE__."<br>");
  echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
  print_r($PdoArr);	
  die ("<br> Error: ".$e->getMessage());
}



echo ("</select><input type=submit value='".GetStr($pdo, "Select")."'></form>".
       " <a href='{$Frm}Frm.php?Ins=New&Enum=$EnName'>".GetStr($pdo, 'New').
       '</a></td></tr></table>');

$CurrVals=array ();

$ShowVals=false;
if ( $CurrName != '' ) {
  echo ( "<br>$CurrName <href a='{$Frm}Frm?CurrType=$CurrType'>".GetStr($pdo,"Change").
            '</a> | '.
           "<a href='{$Frm}Del.php?Enum=$EnName&CurrType=$CurrType' ".
           "onClick='return Confirm(\"Delete?\");'>".
            GetStr($pdo, "Delete").'</a>');


  $query = "select * FROM \"$Tab\" ".
           "where \"EnumName\"=:EnumName and \"EnumVal\"=:EnumVal ";

  $PdoArr = array();
  $PdoArr['EnumName']=$EnName;
  $PdoArr['EnumVal']=$CurrType;
  try {
    $STH2 = $pdo->prepare($query);
    $STH2->execute($PdoArr);


    while ($dp = $STH2->fetch(PDO::FETCH_ASSOC)) {
      $CurrVals[$dp['Lang']]= $dp['EnumDescription'];
    }
    $ShowVals=true;

  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }



}
else {
  if ($_REQUEST['Ins']=='New') {
    $ShowVals=true;  
  }
}
 
if ($ShowVals) {
  echo ('<td><form method=post action="'.$Frm.'Save.php">');
        
  if ( $CurrType=='') {
    echo ("Enum:<input type=text size=30 Name=Enum value='$EnName'>");
    echo ("Code:<input type=number Name=CurrType>");
  }
  else {
     
   echo ("<input type=hidden Name=Enum value='$EnName'>".
         "<input type=hidden Name=CurrType value='$CurrType'>");
  }
  
  echo ('<table><tr>'.
       '<th>'.GetStr($pdo,'Lang').'</th>'.
       '<th>'.GetStr($pdo,'Description').'</th></tr>');


  $FL=0;
  foreach ( $AllLang as $Lang) {
    $FL++;
    echo ("<tr><td align=center>$Lang</td>".
          "<td><input type=text size=50 length=100 required Name=Descr_$Lang Id=Descr_$Lang  value='{$CurrVals[$Lang]}'>".
          "</td></tr>");
    
    if ($FL==2) {
      echo ("<tr><td></td><td align=center>".
            "<button type=button onclick='return TranslateTxtRu2EnFr(\"Descr_RU\", \"Descr_EN\",  \"Descr_FR\");'>".
            "Translate</button>".
            "</td></tr>");

    }
    
  }
  echo ("<tr colspan=2><td align=right><input type=submit value='".GetStr($pdo,'Save').
         "'>".
         "</td></tr></table>".
         "</form>");    
}


if ($EnName != '') {
  echo ("<br><hr><H3>Add Multiline</H3>");
  echo ("Create in Excel three colums file: Code, Lang=EN|RU|FR, Value and copy values");

  echo ('<form method=post action="'.$Frm.'SaveML.php">');
  echo ("Enum: <input type=text size=30 Name=Enum value='$EnName'>");
  echo (" 3 cols from XLS:<textarea Name=EnumML cols=80 rows=5></textarea>");
  echo (" <input type=submit value='".GetStr($pdo,'Save')."'>".
         "</form>");    

}
echo ("<br><hr><a href='XmlReadEnum_Frm.php'>Upload from XML</a> | ". 
      "<a href='ExportEnum.php?EnumName=$EnName'>Export to XML</a> | ".
      "<a href='EnumValuesPrintXLS.php?EnumName=$EnName'>Export to XLS</a> | ".
      "<a href='BuildEnumTxt.php?EnumName=$EnName'>Export to Txt</a> | "
      );

?>
</body>
</html>
				       