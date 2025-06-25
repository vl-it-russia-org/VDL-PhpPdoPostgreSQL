<?php
session_start();

include ("../setup/common_pg.php");
BeginProc(1);
CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

$FldNames=array('MenuName','MenuType','Description','Link','NewWindow','ColumnNo', 
                'ParentId','Ord', 'MenuCode');

$Id=$_REQUEST['Id'];
if ($Id==''){ 
    die ("<br> Error:  Empty Id");
}

$PdoArr = array();
$PdoArr['Id']= $Id;


$dp=array();

try {

$query = "select * FROM \"Menu\" ".
         "WHERE (\"Id\"=:Id)";


$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {

}
else {
  die ("<br> Error: Menu Id=$Id is not exists");
}

  $q='insert into "Menu"(';
  $S1='';
  $S2='';
  $Div='';
  $PdoArr = array();

  foreach ($FldNames as $F) {
    $PdoArr[$F]= $dp[$F];

    $S1.=$Div.'"'.$F.'"';
    $S2.="$Div:$F";
    $Div=', ';
  }
  
  $query=$q.$S1.') values ('.$S2.')';
  
  //echo ("<br>$query<br>");
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);


  $NewId=$pdo->lastInsertId();


$LNK='';

  $V=$_REQUEST['Id'];
  $LNK.="Id=$NewId";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=MenuCard.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
  echo("<H2>Copy $Id --> $NewId</H2>");

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