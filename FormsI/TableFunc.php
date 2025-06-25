<?php

function MakeTable (&$db, $TabNo) {

  $TabName='AdmTabNames';
  $Frm='Tab';

  $Fields=array('TabName', 'TabDescription', 'TabEditable');

  if ($TabNo == '') {
    die ("<br> Error table code ");
  }

  echo ("<br><a href='TabCard.php?TabCode=$TabNo'>Table card</a>");

  //======================================================================

$Fld1= array ( 'IndxType', 'IndxName', 'Fields');

$query = "select * FROM \"AdmTabIndx\" ".
         "where \"TabCode\"=:TabNo and  (\"IndxType\"=10) order by \"TabCode\", \"IndxName\"";

$PdoArr = array();
$PdoArr['TabNo']= $TabNo;

try {
  $STH = $db->prepare($query);
  $STH->execute($PdoArr);



$enFld   = array ('IndxName'=>'IndxName');
$FldType = array ('NeedSeria'=>'bool', 'NeedBrand'=>'bool');

$Des='';
while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  
  $Fld='IndxName';
  $IndxName= $dp[$Fld];

  $query = "select * FROM \"AdmTabIndxFlds\" ".
           "where \"TabCode\"=:TabNo and \"IndxName\"=:IndxName order by \"TabCode\", \"IndxName\", \"LineNo\"";

  $PdoArr['IndxName']= $IndxName;

  $STH4 = $db->prepare($query);
  $STH4->execute($PdoArr);
  

  $Div='';
  while ($dp4 = $STH4->fetch(PDO::FETCH_ASSOC)) {
    $Des.=$Div.'"'.GetFldName($db, $TabNo, $dp4['FldNo']).'"';
    $Div=', ';    
  }
}

$PKIndx = $Des;

//=====================================================================================
  $query = "select * ".
           "FROM \"$TabName\" where \"TabCode\"=:TabNo";

  //echo ($query);

  echo ('<br><b>Table</b>');

  $PdoArr = array();
  $PdoArr['TabNo']= $TabNo;

  $STH = $db->prepare($query);
  $STH->execute($PdoArr);

  echo ("<br> Line ".__LINE__.": $query<br>");
              print_r($PdoArr);
              echo ("<br>");
  
  $dp=array();
  $VDL_TabName='';
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $VDL_TabName=$dp['TabName'];
  }
  else {
    die ("<br> Error bad Table $TabNo "); 
  }

        //"<input type=hidden Name=TabCode Value='$TabNo'>".
  echo('<table><tr>');
  
  $Fld= 'TabName';
  echo ( "<td align=right>".GetStr($db, $Fld).': </td>'.
         "<td>{$dp[$Fld]}</td></tr><tr>");

  $Fld= 'TabDescription';
  echo ( "<td align=right>".GetStr($db, $Fld).': </td>'.
         "<td>".DivTxt ($dp[$Fld])."</td></tr>");
  
      
  echo('</table>');

  $TabName1='AdmTabFields';
  echo ("<br><hr>");

  $Fld1= array ('ParamNo', 'ParamName', 'NeedSeria', 'NeedBrand','DocParamType', 
                'DocParamsUOM',   
                'Ord', 'AddParam',  'CalcFormula');

  $enFld   = array ('DocParamsUOM'=>'DocParamsUOM','DocParamType'=>'DocParamType');
  $FldType = array ('NeedSeria'=>'bool', 'NeedBrand'=>'bool');

  $VDL_TabFld='';
  $query = "select * ".
           "FROM \"$TabName1\" where \"TypeId\"=:TabNo order by \"Ord\", \"ParamNo\"";

  $STH2 = $db->prepare($query);
  $STH2->execute($PdoArr);
  
  echo ("<br> Line ".__LINE__.": $query<br>");
              print_r($PdoArr);
              echo ("<br>");

  $dp=array();

  $TabTxt="CREATE TABLE IF NOT EXISTS \"$VDL_TabName\" (";
  $Div='';
  while ($dp = $STH2->fetch(PDO::FETCH_ASSOC)) {
    $FldName=$dp['ParamName'];
    $FldType= $dp['DocParamType'];
    $AI = $dp['AutoInc'];
    $AddParam=$dp['AddParam'];
    $DbType='';
  
    /*
    
    $res1 = $db->query("SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH,".
                        "NUMERIC_PRECISION,NUMERIC_SCALE, COLUMN_TYPE ".   
                        "FROM INFORMATION_SCHEMA.COLUMNS ".
                        "WHERE TABLE_SCHEMA =  '$db_base' ".
                        "AND (TABLE_NAME='$VDL_TabName') and (COLUMN_NAME='FldName')") OR
              die( "<br>Error in line:".__LINE__.' file:'.__FILE___.'<br>'.mysql_error() );
    
    $HaveCol=0;
    if( $dp1 = $res1->fetch_assoc() ) {
      echo ("<br> Already have in DB $VDL_TabName : ") ;
      print_r ($dp1);
      $HaveCol=1;
  
      die ("<br>");
  
    }
    */
  
    // ALTER TABLE  `L1` ADD  `Int1` INT NOT NULL ;
    // ALTER TABLE  `L1` CHANGE  `Int1`  `Int1` DECIMAL( 10, 2 ) NOT NULL ;
  
  
    if ( $FldType == 10) {  // Text50
      if ( $AddParam=='') {
        $AddParam='50';
      }
      $DbType= "varchar($AddParam)"; 
    }
    else 
    if ( $FldType == 15) {  // Text50
      $DbType= "Text"; 
    }
    else 


    if ( $FldType == 20) {  // Число
      if ( $AddParam=='') {
        $DbType= "integer";
        if ($AI==1) {
          $DbType= "serial";
        }
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
      else 
      if ( $AddParam=='0.000000001') {
        $DbType= "decimal(30,9)"; 
      }
    }
    else 
    if ( $FldType == 30) {  // 
      $DbType= "BOOLEAN";
    }
    else 
    if ( $FldType == 50) {  //Перечисление 
      $DbType= "smallint"; 
    
      if ($dp['EnumLong']==1) {
        $DbType= "integer";
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
      $DbType= "time"; 
    }
    else 
    if ( $FldType == 70) {  // datetime 
      $DbType= "timestamp"; 
    }
    
    if ( $DbType == '') {
      die ("<br> Error: For field $FldName not found type $FldType $AddParam ");
    }
    
    $TabTxt.="$Div\r\n      \"$FldName\" $DbType NOT NULL ";
    //if ($AI) {
    //  $TabTxt.=" AUTO_INCREMENT ";
    //}
    $Div=',';
  }

  if ($PKIndx != '' ) {
    $TabTxt.= ", PRIMARY KEY ($PKIndx) "; 
  }

  $TabTxt.= ")";

  echo ("<br>$TabTxt<br>");
  //die ("<br>");

  $query = $TabTxt;

  //-----------------------------------------------------------------------------------
  $STH3 = $db->prepare($query);
  $STH3->execute();



  //==============================================================================

    // SqlLog
    // Id, User, OpDate, TabNo, 
    // Description, SqlText
    $PdoArr = array();
    $PdoArr['Usr']= $_SESSION['login'];
    $PdoArr['TabNo']= $TabNo;
    $PdoArr['Descr']= "Create table $VDL_TabName";
    $PdoArr['Q']= $query;


    $q=addslashes ($query);
    $query = "insert into \"SqlLog\" (\"User\", \"OpDate\", \"TabNo\", \"Description\", \"SqlText\") ". 
             "values (:Usr, now(), :TabNo, :Descr, :Q)"; 

    $STH = $db->prepare($query);
    $STH->execute($PdoArr);

  //==============================================================================
  
  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }
  
  
  echo ("<br> Done ".
        "<br> <a href='CreateOtherIndexes.php?TabCode=$TabNo'>Create other indexes</a><br>");



  echo ("<a href='TabCard.php?TabCode=$TabNo'>Table</a>");
}
//==============================================================================
//==============================================================================
//==============================================================================
function MakeTableHist ( $TabNo) {

  $TabName='AdmTabNames';
  $Frm='Tab';

  $Fields=array('TabName', 'TabDescription', 'TabEditable');

  if ($TabNo == '') {
    die ("<br> Error table code ");
  }

  echo ("<br><a href='TabCard.php?TabCode=$TabNo'>Table card</a>");

  //======================================================================

$Fld1= array ( 'IndxType', 'IndxName', 'Fields');

$query = "select * ".
         "FROM AdmTabIndx where TabCode='$TabNo' and  (IndxType=10) order by TabCode, IndxName";

$sql2 = mysql_query ($query)
                 or die("Invalid query:<br>$query<br>" . mysql_error());

$enFld   = array ('IndxName'=>'IndxName');
$FldType = array ('NeedSeria'=>'bool', 'NeedBrand'=>'bool');

$Des='';
while ($dp = mysql_fetch_array($sql2)) {
  
  $Fld='IndxName';
  $IndxName= $dp[$Fld];

  $query = "select * ".
           "FROM AdmTabIndxFlds where TabCode='$TabNo' and IndxName='$IndxName' order by TabCode, IndxName, LineNo";

  $sql4 = mysql_query ($query)
                 or die("Invalid query:<br>$query<br>" . mysql_error());

  $Div='';
  while ($dp4 = mysql_fetch_array($sql4)) {
    $Des.=$Div.GetFldName( $TabNo, $dp4['FldNo']);
    $Div=',';    
  }
}

$PKIndx = $Des;

  
  $query = "select * ".
           "FROM $TabName where TabCode='$TabNo'";

  //echo ($query);

  $sql2 = mysql_query ($query)
                 or die("Invalid query:<br>$query<br>" . mysql_error());

  $dp=array();
  $VDL_TabName='';
  if ($dp = mysql_fetch_array($sql2)) {
    $VDL_TabName=$dp['TabName'];
  }
  else {
    die ("<br> Error bad Table $TabNo "); 
  }

   echo ('<br>Table <b>'.$VDL_TabName. '</b>');

        //"<input type=hidden Name=TabCode Value='$TabNo'>".
  echo('<table><tr>');
  
  $Fld= 'TabName';
  echo ( "<td align=right>".GetStr($Fld).': </td>'.
         "<td>{$dp[$Fld]}</td></tr><tr>");

  $Fld= 'TabDescription';
  echo ( "<td align=right>".GetStr($Fld).': </td>'.
         "<td>".DivTxt ($dp[$Fld])."</td></tr>");
  
      
  echo('</table>');

  $TabName1='AdmTabFields';
  echo ("<br><hr>");

  $Fld1= array ('ParamNo', 'ParamName', 'NeedSeria', 'NeedBrand','DocParamType', 
                'DocParamsUOM',   
                'Ord', 'AddParam',  'CalcFormula');

  $enFld   = array ('DocParamsUOM'=>'DocParamsUOM','DocParamType'=>'DocParamType');
  $FldType = array ('NeedSeria'=>'bool', 'NeedBrand'=>'bool');

  $VDL_TabFld='';
  $query = "select * ".
           "FROM $TabName1 where TypeId='$TabNo' order by Ord, ParamNo";

  $sql2 = mysql_query ($query)
                   or die("Invalid query:<br>$query<br>" . mysql_error());

  $dp=array();

  $TabTxt="CREATE TABLE IF NOT EXISTS {$VDL_TabName}_hist (hist_id int";
  $Div=',';
  while ($dp = mysql_fetch_array($sql2)) {
    $FldName=$dp['ParamName'];
    $FldType= $dp['DocParamType'];
    $AI = $dp['AutoInc'];
    $AddParam=$dp['AddParam'];
    $DbType='';
  
    $res1 = mysql_query("SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH,".
                        "NUMERIC_PRECISION,NUMERIC_SCALE, COLUMN_TYPE ".   
                        "FROM INFORMATION_SCHEMA.COLUMNS ".
                        "WHERE TABLE_SCHEMA =  '$db_base' ".
                        "AND (TABLE_NAME='$VDL_TabName') and (COLUMN_NAME='FldName')") OR
              die( "<br>Error in line:".__LINE__.' file:'.__FILE___.'<br>'.mysql_error() );
    
    $HaveCol=0;
    if( $dp1 = mysql_fetch_array($res1) ) {
      echo ("<br> Already have in DB $VDL_TabName : ") ;
      print_r ($dp1);
      $HaveCol=1;
  
      die ("<br>");
  
    }
  
    // ALTER TABLE  `L1` ADD  `Int1` INT NOT NULL ;
    // ALTER TABLE  `L1` CHANGE  `Int1`  `Int1` DECIMAL( 10, 2 ) NOT NULL ;
  
  
    if ( $FldType == 10) {  // Text50
      if ( $AddParam=='') {
        $AddParam='50';
      }
      $DbType= "varchar($AddParam)"; 
    }
    else 
    if ( $FldType == 20) {  // Число
      if ( $AddParam=='') {
        $DbType= "INT"; 
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
        $DbType= "decimal(15,5)"; 
      }
    }
    else 
    if ( $FldType == 30) {  // 
      $DbType= "BOOLEAN"; 
    }
    else 
    if ( $FldType == 50) {  //Перечисление 
      $DbType= "TINYINT"; 
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
      $DbType= "DateTime"; 
    }
    
    if ( $DbType == '') {
      die ("<br> Error: For field $FldName not found type $FldType $AddParam ");
    }
    
    $TabTxt.="$Div\r\n      $FldName $DbType NOT NULL ";
    if ($AI) {
      $TabTxt.=" AUTO_INCREMENT ";
    }
    $Div=',';
  }

  if ($PKIndx != '' ) {
    $TabTxt.= ", PRIMARY KEY (hist_id, $PKIndx) "; 
  }

  $TabTxt.= ") DEFAULT CHARSET=utf8";

  echo ("<br>$TabTxt<br>");
  //die ("<br>");

  $query = $TabTxt;

  $sql3 = mysql_query ($query)
                   or die("Invalid query:<br>$query<br>" . mysql_error());
  
  echo ("<br> Done ");
  echo ("<a href='TabCard.php?TabCode=$TabNo'>Table</a>");
}
//==============================================================================
//==============================================================================
function GetFldTieStr(&$db, $VDL_TabName, $VDL_TabFld) {
  $Res='';  
  $query = "select * FROM \"AdmTab2Tab\" ".
           "where \"TabName\"=:TabName and \"FldName\"=:FldName ".
           "order by \"TabName\",\"FldName\",\"LineNo\" ";
  
  $PdoArr = array();
  $PdoArr['TabName']= $VDL_TabName;
  $PdoArr['FldName']= $VDL_TabFld;

  //echo ("<br>$query<br>");
  try {
    $STH = $db->prepare($query);
    $STH->execute($PdoArr);

  

  while ($dpL = $STH->fetch(PDO::FETCH_ASSOC)) {
    $Res.="[ {$dpL['LineNo']}:";
    if ($dpL['TabCond'] != '') {
      $Res.='{'.$dpL['TabCond'].'} ';
    }
    $Res.="{$dpL['Tab2']}/{$dpL['Field2']} ]";
  }
  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }


  return $Res;
}
//==============================================================================
//==============================================================================

  
  
  
?>
  
  
  