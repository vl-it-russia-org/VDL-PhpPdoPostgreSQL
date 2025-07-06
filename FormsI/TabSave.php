<?php
session_start();


include ("../setup/common_pg.php");

CheckLogin1();

CheckRight1 ($pdo, 'Admin');


?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>Tab save</title></head>
<?php

//print_r ($_REQUEST);

$TabName='AdmTabNames';
$Frm='Tab';

$Fields=array('TabName', 'TabDescription','TabEditable', 'Ver');

$TabNo= $_REQUEST['TabCode'];
$New= $_REQUEST['New'];
$PdoArr = array();

try {

if ($New !='1' ) {
  $Proc='Updated';
  
  $OldTabNo= $_REQUEST['OldTabCode'];
  if ( empty($TabNo) or empty($OldTabNo)) {
    die ("<br> Error: Empty TabNo or OldTabNo");
  }

  if ( $OldTabNo != $TabNo) {
    // Изменение кода таблицы
    
    $PdoArr['TabNo']= $TabNo;
    
    $query = "select * from \"AdmTabNames\" ". 
             "where (\"TabCode\" = :TabNo)"; 

    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);

    if ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
      die ("<br> Error: $TabNo is exists");    
    }
    
    // AdmTabNames
    // TabName, TabDescription, TabCode, TabEditable, 
    // AutoCalc, CalcTableName
    $PdoArr['OldTabNo']= $OldTabNo;
    $query = "update \"AdmTabNames\" set \"TabCode\"=:TabNo ". 
             "where (\"TabCode\" = :OldTabNo)"; 
    
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
  
    // AdmTabFields
    // TypeId, ParamNo, ParamName, NeedSeria, 
    // DocParamType, NeedBrand, Ord, AddParam, DocParamsUOM, 
    // CalcFormula, AutoInc, Description, BinCollation, ShortInfo
    $query = "update \"AdmTabFields\" set \"TypeId\"=:TabNo ". 
             "where (\"TypeId\"=:OldTabNo')"; 

    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);

    // AdmTabIndx
    // TabCode, IndxType, IndxName
    $query = "update \"AdmTabIndx\" set \"TabCode\"=:TabNo ". 
             "where (\"TabCode\"=:OldTabNo)"; 

    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
  
  
    // AdmTabIndxFlds
    // TabCode, IndxName, LineNo, FldNo, 
    // Ord
    $query = "update \"AdmTabIndxFlds\" set \"TabCode\"=:TabNo ". 
             "where (\"TabCode\"=:OldTabNo)"; 

    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
  
  } 
  $PdoArr = array();
  $PdoArr['TabNo']= $TabNo;
  
  $query="update \"$TabName\" set ";
  $Div='';
  foreach ( $Fields as $Fld) {
    $query.=$Div." \"$Fld\"=:$Fld'";
    $Div=',';
    $PdoArr[$Fld]= $_REQUEST[$Fld];
  }
  $query.=$Div." \"ChangeDt\"=now()";

  $query.=" where \"TabCode\"=:TabNo";
}
else {
  $Str1='"TabCode"';
  $PdoArr['TabNo']= $TabNo;
  $Str2=":TabNo";
  
  if (empty ($_REQUEST['AutoCalc'])) {
    $_REQUEST['AutoCalc']='0';
  }
  
  foreach ( $Fields as $Fld) {
    $Str1.=", \"$Fld\"";
    $Str2.=", :$Fld";
    $PdoArr[$Fld]= $_REQUEST[$Fld];
  }
  
  $Str1.=", \"ChangeDt\"";
  $Str2.=", now() ";

  $query="insert into \"$TabName\" ($Str1) values ($Str2)";
}


//echo ($query);
$STH = $pdo->prepare($query);
$STH->execute($PdoArr);


  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }


echo('                                  
<META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$Frm.'Card.php?TabCode='.$TabNo.'">');

echo ('<body><br><b>'.GetStr($pdo, 'Edit'). '</b> ') ;
echo ($Proc);

?>
</body>
</html>
                       