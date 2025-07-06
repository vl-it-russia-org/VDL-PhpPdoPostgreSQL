<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();

CheckRight1 ($pdo, 'Task');


$FldNames=array('ParentId','ToDoCode',
                'Description','DateBeg','DateEnd', 'Status');

if (empty ($_REQUEST["Id"])) {
  die ("<br> Error: Id is empty ");

}

$Id=$_REQUEST["Id"];

$PdoArr = array();
$PdoArr['Id']= $Id;

$query = "select * from \"ToDo_Line\" ". 
         "where (\"Id\" =:Id )"; 

$NewId='';
try {
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $S1='';
    $S2='';
    $Div='';

    $PdoArr = array();
    foreach ($FldNames as $F) {
      $S1.="$Div\"$F\"";
      $S2.="$Div:$F";
      $Div =', ';
      $PdoArr[$F]= $dp[$F];
    }

    $PdoArr["DateBeg"]=date('Y-m-d');
    $PdoArr["DateEnd"]="1900-01-01";
    $PdoArr["Status"]=0;
    
    
    $query = "insert into \"ToDo_Line\" ($S1) values ($S2)"; 
  
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
    $NewId = $pdo->lastInsertId();
  }
  else {
    die ("<br> Error: ToDo Id = $Id is not found");
  }
}
catch (PDOException $e) {
  echo ("<hr> Line ".__LINE__."<br>");
  echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
  print_r($PdoArr);	
  die ("<br> Error: ".$e->getMessage());
}


$LNK='';

  $LNK.="Id=$NewId";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=ToDo_LineCard.php?'.$LNK.'">'.
'<title>Copy</title></head>
<body>');
  
  echo('<H2>Copied '.$Id.' --> '.$NewId.'</H2>');
?>
</body>
</html>