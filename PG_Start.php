<?php
session_start();

include ("setup/common_pg.php");
//BeginProc();

/*
$data = array();
$data['Id']=60;
$data['TextCol']="Рязань";
$data['En1']=30;

try {
  $STH = $pdo->prepare('INSERT INTO "Table1" ("Id", "TextCol", "En1") values (:Id, :TextCol,:En1)'); 
  $STH->execute($data);
}
catch (PDOException $e) {
  die ("<br> Error: ".$e->getMessage());
}
*/

$STH = $pdo->query('SELECT * from "Table1" order by "TextCol"');   
# устанавливаем режим выборки 
$STH->setFetchMode(PDO::FETCH_ASSOC);   
$i=0;
while($row = $STH->fetch()) {   
  $i++;
  echo ("<hr> $i: ");
  print_r($row);
}



echo ("<br> Done");

?>