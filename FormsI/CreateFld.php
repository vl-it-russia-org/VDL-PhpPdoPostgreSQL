<?php
session_start();

include ("../setup/common_pg.php");

//echo ("<br> 2 <br>");

CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>Create field list</title></head>
<body>
<?php

//include ("common_lab.php");


$TabName='AdmTabNames';
$Frm='Tab';

$Fields=array('TabName', 'TabDescription', 'TabEditable');

$TabNo= $_REQUEST['TabNo'];
$FldNo= $_REQUEST['FldNo'];
$Hist = $_REQUEST['Hist'];

  $Tab1='';
  if ($Hist==1) {
    $Tab1='_hist';
  }

if ($TabNo == '') {
  die ("<br> Error table code ");
}

echo ("<br><a href='TabCard.php?TabCode=$TabNo'>Table card</a>");

$PdoArr = array();
$PdoArr['TabNo']= $TabNo;
try {

$query = "select * FROM \"$TabName\" where \"TabCode\"=:TabNo";

  //echo ("<br>$query<br>");

  //print_r($pdo);

  //echo ('<br><b>'.GetStr($pdo,'TableField'). ' create</b>');
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

  $dp=array();
  $VDL_TabName='';
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $VDL_TabName=$dp['TabName'];
  }
  else {
    die ("<br> Error bad Table $TabNo "); 
  }

  if ($Hist==1) {
    $VDL_TabName.='_hist';
  }

        //"<input type=hidden Name=TabCode Value='$TabNo'>".
  echo('<table><tr>');
  
  $Fld= 'TabName';
  echo ( "<td align=right>".GetStr($pdo, $Fld).': </td>'.
         "<td>{$dp[$Fld]}</td></tr><tr>");

  $Fld= 'TabDescription';
  echo ( "<td align=right>".GetStr($pdo, $Fld).': </td>'.
         "<td>".DivTxt ($dp[$Fld])."</td></tr>");
  
      
  echo('</table>');

$TabName1='AdmTabFields';
echo ("<br><hr>");

//SELECT 

$Fld1= array ('ParamNo', 'ParamName', 'NeedSeria', 'NeedBrand','DocParamType', 
              'DocParamsUOM',   
               'Ord', 'AddParam',  'CalcFormula');

$enFld   = array ('DocParamsUOM'=>'DocParamsUOM','DocParamType'=>'DocParamType');
$FldType = array ('NeedSeria'=>'bool', 'NeedBrand'=>'bool');

// FROM 'AdmTabFields' WHERE 1

// AdmTabFields
// TypeId, ParamNo, ParamName, NeedSeria, 
// DocParamType, NeedBrand, Ord, AddParam, DocParamsUOM, 
// CalcFormula, AutoInc, Description, BinCollation, ShortInfo

$VDL_TabFld='';
$query = "select * FROM \"$TabName1\" ".
         "where \"TypeId\"=:TabNo and \"ParamNo\"=:FldNo order by \"Ord\", \"ParamNo\"";

$PdoArr['FldNo']= $FldNo;

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);


$dp=array();
$BeforeFld='';
$AI=0;
if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  $FldName=$dp['ParamName'];
  $FldType= $dp['DocParamType'];
  $EnumLong = $dp['EnumLong'];
  
  $AddParam=$dp['AddParam'];
  $DbType='';
  $AI=$dp['AutoInc'];
  
  $FldOrd= $dp['Ord'];

  // AdmTabFields
  // TypeId, ParamNo, ParamName, NeedSeria, 
  // DocParamType, NeedBrand, Ord, AddParam, DocParamsUOM, 
  // CalcFormula, AutoInc, Description, BinCollation, ShortInfo
  $query = "select \"ParamName\" from \"AdmTabFields\" ". 
           "where (\"TypeId\"=:TabNo) and (\"Ord\"<:FldOrd) order by \"Ord\" desc, \"ParamNo\" desc "; 

  $PdoArr = array();
  $PdoArr['TabNo']= $TabNo;
  $PdoArr['FldOrd']= $FldOrd;

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
  
  if ($dp21 = $STH->fetch(PDO::FETCH_ASSOC)) {
    $BeforeFld=$dp21['ParamName'];
    echo (" before $BeforeFld ");   
  }
  
  
  //$res1 = $pdo->query("SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH,".
  //                    "NUMERIC_PRECISION,NUMERIC_SCALE, COLUMN_TYPE ".   
  //                    "FROM INFORMATION_SCHEMA.COLUMNS ".
  //                    "WHERE TABLE_SCHEMA =  '$db_base' ".
  //                    "AND (TABLE_NAME='$VDL_TabName') and (COLUMN_NAME='$FldName')") OR
  //          die( "<br>Error in line:".__LINE__.' file:'.__FILE___.'<br>'.$pdo->error);
  //=============================================================================================

  $HaveCol=0;
  $PdoArr = array();
  $PdoArr['TabName5']= $VDL_TabName;
  $PdoArr['FldName']= $FldName;
    
    //SELECT column_name, column_default, data_type 
    //FROM INFORMATION_SCHEMA.COLUMNS 
    //WHERE table_name = 'super_table';
    $Flds = array ("column_name", "ordinal_position", "column_default", "is_nullable", 
                   "data_type", "AddInfo"); 

    // character_maximum_length
    // numeric_precision, numeric_precision_radix

    $query="SELECT * FROM \"information_schema\".\"columns\" where 
    \"table_catalog\"= '$db_base' and 
    (\"table_name\" = :TabName5) and (\"column_name\"= :FldName)";
    
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
    //echo ("<br> Line ".__LINE__.": $query<br>");
    //          print_r($PdoArr);
    //          echo ("<br>");
    if ($row1=$STH->fetch(PDO::FETCH_ASSOC)) {

      echo ("<br> Already have in DB $VDL_TabName : ") ;
      print_r ($dp1);
      $HaveCol=1;

      echo ("<br>");

      /*
      if ($row1['data_type']=='character varying') {
            $AI = '['.$row1['character_maximum_length'].']';

          }
          else
          if ($row1['data_type']=='numeric') {
            if ( !empty($row1['numeric_scale'])) { 
            
              $AI = '['.$row1['numeric_precision'].', '.
                        $row1['numeric_scale'].']';
            }
            else {
              $AI = '['.$row1['numeric_precision'].']';

            }

          }
          //print_r($row1);
          echo ("<td>$AI</td>");
      */
      }
      //echo ("<hr>");
      //PRINT_R ($row1);  
  //=============================================================================================
  
  // ALTER TABLE  `L1` ADD  `Int1` INT NOT NULL ;
  // ALTER TABLE  `L1` CHANGE  `Int1`  `Int1` DECIMAL( 10, 2 ) NOT NULL ;

  echo ("<br>FldType: $FldType ");
  
  if ( $FldType == 10) {  // Text50
    if ( $AddParam=='') {
      $AddParam='50';
    }
    $DbType= "varchar($AddParam)"; 
  }
  else 
  if ( $FldType == 15) {  // TextLong
    $DbType= "text"; 
    //if ($dp['BinCollation']) {
    //  $DbType.= " CHARACTER SET utf8 COLLATE utf8_bin ";
    //}
    //else {
    //  $DbType.= " CHARACTER SET utf8 COLLATE utf8_general_ci " ;
    //}
  }
  else 
  if ( $FldType == 20) {  // Число
    if ( $AddParam=='') {
      $DbType= "integer"; 
    }
    else 
    if ( $AddParam=='0.1') {
      $DbType= "decimal(15,1)"; 
    }
    else 
    if ( $AddParam=='0.01') {
      $DbType= "decimal(15,2)"; 
    }
    else 
    if ( $AddParam=='0.001') {
      $DbType= "decimal(15,3)"; 
    }
    else 
    if ( $AddParam=='0.0001') {
      $DbType= "decimal(15,4)"; 
    }
    else 
    if ( $AddParam=='0.00001') {
      $DbType= "decimal(20,5)"; 
    }
    else 
    if ( $AddParam=='0.000001') {
      $DbType= "decimal(20,6)"; 
    }

  }
  else 
  if ( $FldType == 30) {  // 
    $DbType= "BOOLEAN"; 
  }
  else 
  if ( $FldType == 50) {  //Перечисление 
    $DbType= "smallint"; 
    if ($EnumLong==1) {
      $DbType= "INTEGER"; 
    }
  }
  else 
  if ( $FldType == 55) {  // Множ выбор перечисление 
    $DbType= "varchar(250)"; 
  }
  else 
  if ( $FldType == 60) {  // date 
    $DbType= "date"; 
  }
  else 
  if ( $FldType == 65) {  // time 
    $DbType= "Time"; 
  }
  else 
  if ( $FldType == 70) {  // datetime 
    $DbType= "timestamp"; 
  }
  
  if ( $DbType == '') {
    die ("<br> Error: For field $FldName not found type $FldType $AddParam ");
  }
  

  if ($HaveCol==0) { 
    //ADD [COLUMN] column_name column_definition [FIRST|AFTER existing_column];
    $query = "ALTER TABLE \"$VDL_TabName\" ADD \"$FldName\" $DbType NOT NULL";
    if ($BeforeFld != '') {
      //$query.= " after $BeforeFld";
    }
  }
  else {
    $query = "ALTER TABLE \"$VDL_TabName\" ALTER COLUMN \"$FldName\" TYPE $DbType";
  }
  
  echo ("<br>$query<br>");
  // die();

  $STH = $pdo->prepare($query);
  $STH->execute();


  //========================================================

    // SqlLog
    // Id, User, OpDate, TabNo, 
    // Description, SqlText
    $PdoArr = array();

    $Usr=$_SESSION['login'];
    
    $PdoArr['Usr']= $Usr;
    $PdoArr['q']= $query;
    $PdoArr['TabNo']= $TabNo;
    $PdoArr['Msg']= "Field $FldName for table $VDL_TabName";
    
    $query = "insert into \"SqlLog\" (\"User\", \"OpDate\", \"TabNo\", \"Description\", \"SqlText\") ". 
             "values (:Usr, now(), :TabNo, :Msg, :q)"; 

    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);


  //========================================================

}
else {
  die ("<br> Error bad Table $TabNo field $FldNo "); 
}

echo ("<br> Done ");
echo ("<a href='TabCard.php?TabCode=$TabNo'>Table</a>");

}
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

?>