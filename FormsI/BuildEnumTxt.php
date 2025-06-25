<?php
session_start();
include ("../setup/common_pg.php");
BeginProc();


if (!HaveRight1 ($pdo, 'Translation')) {
  CheckRight1 ($pdo, 'Admin');
}

$EnumName = addslashes ($_REQUEST['EnumName']);
if ($EnumName=='') {
  die ("<br>Error: EnumName is empty");
}

$Dat=date("Ymd-His");

//print_r($_REQUEST);
//die("");
$PdoArr = array();
$PdoArr['EnumName']= $EnumName;
try {

$Str= "  // Enum $EnumName\r\n";

// EnumValues
// EnumName, EnumVal, Lang, EnumDescription
$query = "select * from \"EnumValues\" ". 
         "where (\"EnumName\"=:EnumName) and (\"Lang\"='RU') order by \"EnumName\", \"EnumVal\", \"Lang\""; 

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $Str.= "  //    {$dp2['EnumVal']}\t  {$dp2['EnumDescription']}\r\n";
}

}
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
}

$Dat=date("Ymd-His");
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: text/html');
header('Content-Disposition: attachment;filename=EnumTxt_'.$EnumName.'-'.$Dat.'.txt');

echo ($Str);

//================================================================

?>