<?php
session_start();

include ("../setup/common_pg.php");

CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>Create index</title></head>
<?php

//print_r ($_REQUEST);
//die();

$TabName='AdmTabIndx';
$Frm='Tab';


$TabNo= $_REQUEST['TabNo'];
$IndxName= $_REQUEST['IndxName'];
$IndxType='';

$PdoArr = array();
$PdoArr['TabNo']= $TabNo;
$PdoArr['IndxName']= $IndxName;

try {


$query="select \"IndxType\" from \"AdmTabIndx\" ".
       "where (\"TabCode\"=:TabNo) AND (\"IndxName\"=:IndxName)";

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  $IndxType=$dp['IndxType'];                                     
  // Enum IndxType
  //    10	  Первичный ключ
  //    20	  Индекс
  //    30	  Уникальный ключ
  $query = "select * ".
           "FROM \"AdmTabNames\" where \"TabCode\"=:TabNo";

  //echo ($query);
  $PdoArr = array();
  $PdoArr['TabNo']= $TabNo;

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);


  $VDL_TabName='';
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $VDL_TabName=$dp['TabName'];
  }
  else {
    die ("<br> Error: Do not found tablename for $TabNo table");
  }

  //(TabCode, IndxName, LineNo, FldNo)
  $query = "select \"ParamName\" ".
           "FROM \"AdmTabIndxFlds\" \"I\", \"AdmTabFields\" \"F\" ".
           " where (\"I\".\"TabCode\"=:TabNo) and (\"I\".\"IndxName\"=:IndxName) and  ".
                 " (\"F\".\"TypeId\"=:TabNo) and (\"I\".\"FldNo\"=\"F\".\"ParamNo\") ".     
                 "order by \"I\".\"Ord\"";

  $PdoArr['IndxName']= $IndxName;
  //echo ($query);
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

  $FldsList='';
  $Div='';
  while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $FldsList.="$Div \"{$dp['ParamName']}\"";
    $Div=',';
  }
  
  if ($FldsList=='') {
    die ("<br> Error: Do not fields for index $IndxName for $TabNo table");
  }

  $query ='';
  if ( $IndxType== 10) {     //    10	  Первичный ключ
    $query = "ALTER TABLE \"$VDL_TabName\" DROP PRIMARY KEY,  ADD PRIMARY KEY($FldsList)";
  }
  else {
    //    30	  Уникальный ключ
    $UNIQ = '';
    if ( $IndxType== 30 ) {
      $UNIQ = ' UNIQUE';
    }
    
    $query = "CREATE$UNIQ INDEX \"$IndxName\" ON \"$VDL_TabName\"($FldsList)";

  }
  echo ("<br>$query<br>");

  //$sql2 = mysql_query ($query)
  //               or die("Invalid query:<br>$query<br>" . mysql_error());



  //ALTER TABLE TABLE_NAME ADD INDEX (COLUMN_NAME);
}
else {
  die ("<Br> Error: Not found Index with name $IndxName in table $TabNo ");
}


//echo ($query);
$STH = $pdo->prepare($query);
$STH->execute();

$ErrArr = $STH->errorInfo();
echo ("<br> Line ".__LINE__.":ErrArr: ");
print_r($ErrArr);
echo ("<br>");


  //========================================================

    // SqlLog
    // Id, User, OpDate, TabNo, 
    // Description, SqlText
    $Usr=$_SESSION['login'];

    $PdoArr = array();
    $PdoArr['Usr']= $Usr;
    $PdoArr['q']= $query;
    $PdoArr['AddTxt']= "Add index $IndxName for table $VDL_TabName";
    $PdoArr['TabNo']= $TabNo;

    $q=addslashes ($query);
    $query = "insert into \"SqlLog\" (\"User\", \"OpDate\", \"TabNo\", \"Description\", \"SqlText\") ". 
             "values (:Usr, now(), :TabNo, :AddTxt, :q)"; 

    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);


  //========================================================

  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }


echo('
<META HTTP-EQUIV="REFRESH" CONTENT="2;URL='.$Frm.'Card.php?TabCode='.$TabNo.'">');

echo ('<body><br><b>'.GetStr($pdo, 'Edit'). '</b> ') ;
echo ($Proc);

?>
</body>
</html>
				       