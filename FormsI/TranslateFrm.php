<?php
session_start();
include ("../setup/common_pg.php");
BeginProc();

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>Translate</title></head>
<body>
<?php
include '../Translate.js';

// Типы документов -- в EnumValues


if (!HaveRight1 ($pdo, 'Translation')) {
  CheckRight1 ($pdo, 'Admin');
}


$AllLang=array ("EN", "RU", "FR");

echo("<a href='TranslateList.php'>List</a>"); 
echo(" | <a href='MakeMultiTranslation.php' target=MultiTransl>Multi line translation</a>"); 
echo(" | <a href='EnumFrm.php' target=EnumFrm>Enum translation</a>"); 


$Tab='TranslationText';
$Frm='Translate';

$EnName= $_REQUEST['Enum'];
if ($EnName=='') {
  $EnName= $_REQUEST['SubStr'];
}

$FullRef1='';

echo ('<form method=get action="'.$Frm.'Frm.php">'.
      "String code:<input type=text size=30 Name=Enum value='$EnName'> ".
      "<input type=submit value='Set'></form><br><hr>");

$Lang= $_SESSION['Lang'];
if ($Lang=='') {
  $_SESSION['Lang']='RU';
  $Lang='RU';
}

$CurrType= $_REQUEST['CurrType'];


// SELECT `EnumName`, `EnumVal`, `Lang`, `EnumDescription` FROM `EnumValues` WHERE 1

echo ('<table><tr><td><b>'.GetStr($pdo, $Frm).
      '</b></td>');

$query = "select * FROM \"$Tab\" ".
         "where \"ID\"=:EnName and \"Lang\"=:Lang";

$PdoArr = array();
$PdoArr['EnName']= $EnName;
try {


 
echo ('<table><tr><td><form method=get action="'.$Frm.'Frm.php">'.
      "<input type=hidden Name=Enum value='$EnName'>");

echo ("</select><input type=submit value='".GetStr($pdo,"Select")."'></form>".
       " <a href='{$Frm}Frm.php?Ins=New&Enum=$EnName'>".GetStr($pdo,'New').'</a></td></tr></table>');

$CurrVals=array ();

$ShowVals=false;

$CurrName=$EnName;
$CurrType=$EnName;
if ( $CurrName != '' ) {
  echo ( "<br>$CurrName <a href='{$Frm}Frm.php?CurrType=$CurrType'>".GetStr($pdo, "Change").'</a> | '.
           "<a href='{$Frm}Del.php?CurrType=$CurrType' onClick='return confirm(\"Delete?\");'>".
            GetStr($pdo,"Delete").'</a>');

  $query = "select * FROM \"$Tab\" ".
           "where \"ID\"=:EnName";
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

  while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $CurrVals[$dp['Lang']]= $dp['TextVal'];
  }
  $ShowVals=true;
}
else {
  if ($_REQUEST['Ins']=='New') {
    $ShowVals=true;
    $CurrVals['EN']=$EnName;  
  }
}
 
if ($ShowVals) {
  echo ('<td><form method=get action="'.$Frm.'Save.php">');
        
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
    echo ("<tr><td align=center>$Lang</td>".
          "<td><input type=text size=50 length=100 required Name=Descr_$Lang ID=Descr_$Lang value='{$CurrVals[$Lang]}'>".
          "</td></tr>");

    if ($FL==0) {
      $FL=1;
      echo ("<tr><td></td><td align=center>".
            "<button type=button onclick='return TranslateTxtEn2RuFr (\"Descr_EN\", \"Descr_RU\", \"Descr_FR\");'>".
            "Translate EN</button>".
            "</td></tr>");

    }
    else
    if ($FL==1) {
      $FL=2;         //TranslateTxtRu2EnFr (RuId, EnId,  FrId)
      echo ("<tr><td></td><td align=center>".
            "<button type=button onclick='return TranslateTxtRu2EnFr ( \"Descr_RU\", \"Descr_EN\", \"Descr_FR\");'>".
            "Translate RU</button>".
            "</td></tr>");

    }
  
  
  }
  echo ("<tr colspan=2><td align=right><input type=submit value='".GetStr($pdo,'Save')."'>".
         "</td></tr></table>".
         "</form>");    
}
  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }


echo ("<br><hr>".
      "<a href='ExportTranslate.php?ID=$EnName'>XML export</a> | ");  
echo ("<a href='FrmXmlTranslateUpload.php'>XML upload</a> | ");

if ($EnName != '') {
  echo ("<a href='https://project.kontaktor.ru/legrand/FormsI/TranslateOut.php?Str=$EnName'>Get translation from top</a> | ");

} 

?>
</body>
</html>
                       