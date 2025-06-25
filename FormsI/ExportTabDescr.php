<?php
session_start();
include ("../setup/common_pg.php");
BeginProc();
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>Table description</title></head>
<body>
<?php


//$FldNo= addslashes ($_REQUEST['FldNo']);

if (empty ($_REQUEST['TabNo'])) {
  die ("<br> Error: Empty table code ");
}

$TabNo=$_REQUEST['TabNo'] ;
$PdoArr = array();
$PdoArr['TabNo']= $TabNo;
try {

$TabName='AdmTabNames';
$Frm='Tab';

$Fields=array('TabName', 'TabDescription','TabEditable');

$query = "select * FROM \"$TabName\" where \"TabCode\"=:TabNo";

//print_r($pdo);
//echo ("<br>$query<br>");

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

  $dp=array();
  
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    echo ("<hr>");
    print_r($dp);
    echo ("<hr>");  
  }
  else {
    die ("<br> Error do not found table with id $TabNo ");
  }

  $Fld= 'TabName';
  $TabName5=$dp[$Fld];
  //===========================================================
  // Check view
  $PdoArr = array();
  $PdoArr['TabName5']= $TabName5;

  $View=0;
  // AdmTabsAddFunc
  // Id, TabName, AddFunc, Param
  $query = "select * from \"AdmTabsAddFunc\" ". 
           "where (\"TabName\" = :TabName5)and (\"AddFunc\"=5) ";   

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);  
  
  if ($dp21 = $STH->fetch(PDO::FETCH_ASSOC)) {
    $View=1;
  }


  //===========================================================
  if ($View==1) {  
    $query = "SELECT VIEW_DEFINITION FROM INFORMATION_SCHEMA.VIEWS ".
             "WHERE TABLE_NAME = '$TabName5'"; 

    $sql24 = $pdo->query ($query) 
              or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);
    if ($dp24 = $sql24->fetch_assoc()) {
      echo ("<br>\r\nCREATE VIEW $TabName5 as ".$dp24['VIEW_DEFINITION']);
    }  
  }
  else {
    $PdoArr = array();
    $PdoArr['TabName5']= $TabName5;
    
    //SELECT column_name, column_default, data_type 
    //FROM INFORMATION_SCHEMA.COLUMNS 
    //WHERE table_name = 'super_table';
    $Flds = array ("column_name", "ordinal_position", "column_default", "is_nullable", 
                   "data_type", "AddInfo"); 

    // character_maximum_length
    // numeric_precision, numeric_precision_radix

    $query="SELECT * FROM \"information_schema\".\"columns\" where 
    \"table_catalog\"= '$db_base' and 
    \"table_name\" = :TabName5";
    
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
    //echo ("<br> Line ".__LINE__.": $query<br>");
    //          print_r($PdoArr);
    //          echo ("<br>");
    echo ("<table><tr class=header>");
    foreach ($Flds as $F) {
      echo ("<th>$F</th>");
    }
    $i=0;
    while ($row1=$STH->fetch(PDO::FETCH_ASSOC)) {
      $i= NewLine($i);
      foreach ($Flds as $F) {
        if ($F=="AddInfo") {
          //echo ("<td>");
          $AI = '';
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

        }
        else {
          echo ("<td>{$row1[$F]}</td>");
        }
      }

      //echo ("<hr>");
      //PRINT_R ($row1);  
    }
    //PRINT_R ($row1);
    echo ("</tr></table>");

    
    $query="SELECT * FROM \"information_schema\".\"key_column_usage\"  where 
           \"table_catalog\"= '$db_base' and 
           \"table_name\" = :TabName5";
    
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);  // 
    //echo ("<br> Line ".__LINE__.": $query<br>");
    
    $Keys=array();
    $KeyFlds=array();
    $LasKey='';
    
    $i=0;
    while ($row1=$STH->fetch(PDO::FETCH_ASSOC)) {
      //echo ("<hr>");
      //PRINT_R ($row1);  
    
      if ( $LastKey!= $row1['constraint_name']) {
        $LastKey= $row1['constraint_name'];
        if ( $LastKey== $TabName5.'_pkey') {
          $Keys[$LastKey]='PK';
        }
        else {
          $Keys[$LastKey]='-';
        }
        $i=0;
      }

      $i++;
      $KeyFlds[$LastKey][$i]=$row1['column_name'];
    }
    //$query="\nDROP TABLE IF EXISTS `".$TabName5."`;\n".$row1['Create Table'].";\n";
    //echo ("<br>$query<br>");
    echo ("<br> Keys: <br>");

    foreach ($Keys as $KN=>$KV) {
      if ($KV=='PK') {
        echo ("<B>$KN</B>: ");
      }
      else {
        echo ("$KN: ");
      }
      $Div='';
      $FL='';
      foreach ($KeyFlds[$KN] as $I=>$Fld) {
        $FL.="$Div$Fld";
        $Div=', ';  
      }
      echo ("$FL<br>");
    }
  
  }

}
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }
?>
</body></html>