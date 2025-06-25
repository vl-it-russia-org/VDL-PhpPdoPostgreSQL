<?php
session_start();

include ("../setup/common_pg.php");

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="icon" href="DbList.ico" type="image/x-icon">
<title>Table list</title></head>
<body>
<?php
include ("../js_SelAll.js");

CheckLogin1();
CheckRight1 ($pdo, 'Admin');

$PdoArr = array();



//SELECT 'TabName', 'TabDescription', 'TabCode' FROM  WHERE 1

$TabName='AdmTabNames';
$CurrFile='TabList.php';
$Frm='Tab';

echo ("<br><a href='AdmTabRefresh.php'>Refresh table list</a>"); 
echo (" | <a href='FrmSql.php' target='SQLFrm'>SQL Run</a>"); 
echo (" | <a href='FrmFunction.php' target='FuncFrm'>Function</a>"); 
echo (" | <a href='AdmSelectList.php' target='AdmSelFrm'>Select tables</a>"); 


//AdmTabNames
//'TabName', 'TabDescription', 'TabCode', 'TabEditable', 'AutoCalc', 'CalcTableName', 'ChangeDt', 'Ver'
$Fields=array(  'TabCode', 'TabName', 'TabDescription', 'ChangeDt', 'Ver');
 

  $BegPos = '';
  if (!empty ($_REQUEST['BegPos'])) {
    $BegPos = addslashes ($_REQUEST['BegPos']);
  }
  if ($BegPos==''){
    $BegPos=0;
  }


  $ORDS = ' order by "TabName" '; 

  $ORD='';
  if (!empty ($_REQUEST['TORD'])) {
    $ORD= $_REQUEST['TORD'];
  }

  if ( $ORD == 1 ) {
    $ORDS = ' order by "TabCode" '; 
  }
  
  
  $WHS = '';
  $FullRef='?TORD='.$ORD;
  
  foreach ( $Fields as $Fld) {
    if (!empty ($_REQUEST['Fltr_'.$Fld])) {		
      $Fltr=addslashes($_REQUEST['Fltr_'.$Fld]);
      if ($Fltr!='') {
        if ($WHS !='') {
          $WHS.= ' and ';
        }
        $WHS.= SetFilter2Fld ( $Fld, $Fltr, $PdoArr );
        //$WHS.='('.$Fld." Like '%$Fltr%')"; 
        $FullRef.='&Fltr_'.$Fld.'='.$Fltr ;
      }
    }
  }
  
  $LN ='';
  if (!empty($_SESSION['LPP'])) {
    $LN=$_SESSION['LPP'];
  };

  if ($LN=='') {
    $LN=20;  
  };

  if ($WHS != '') {
    $WHS = ' where '.$WHS;
  };   

  $query = "select * ".
           "FROM \"$TabName\" ".
           "$WHS $ORDS LIMIT $LN OFFSET $BegPos";

  echo ("<br>$query<br>");

  $queryCNT = "select COUNT(*) \"CNT\" ".
         "FROM \"$TabName\" ".
         " $WHS ";
  
  $CntLines=0;
  try {
    $STH=$pdo->query($queryCNT);
    $STH->execute($PdoArr); 

    if ($dp =  $STH->fetch(PDO::FETCH_ASSOC)) {
      $CntLines=$dp['CNT'];  
    };
  }
  catch (PDOException $e) {
    die ("<br> Error: ".$e->getMessage());
  }

  
  $CurrPage= round($BegPos/$LN)+1;
  $LastPage= floor($CntLines/$LN)+1;

  echo ('<br><b>'.GetStr($pdo, 'Tables').' '.GetStr($pdo, 'List'). '</b> '.
                  $CntLines.' total lines Page <b>'.$CurrPage.'</b> from '. $LastPage) ;
  
  echo ('<form method=post action="'.$CurrFile.'"><table><tr>');
  $i=0;
  foreach ( $Fields as $Fld) {
    if ($i==3){
      echo('</tr><tr>');
      $i=0;
    }     
    $i++;
    $Val="";
    if (!empty ($_REQUEST['Fltr_'.$Fld])) {
      $Val = $_REQUEST['Fltr_'.$Fld];
    }


    echo("<td align=right>$Fld:</td>".
      "<td><input type=text length=30 size=20 name='Fltr_$Fld' value='$Val'></td>");
  }
  echo ('<td><button type="submit">Filter</button></td></tr></table></form>');

//--------------------------------------------------------------------------------
  
  echo ('<hr><table><tr><td><form method=post action="TabCard.php">'.
        '<input type=hidden Name=New VALUE=1>'.
        "<input type=submit Value='".GetStr($pdo, 'New')."'></form></td>".
        "<form  method=post action=TabXlsOut.php>".
        "<input type=submit Name=OpType Value='".GetStr($pdo, 'Xls')."'></td>".
        "<td><input type=submit Name=OpType Value='XML'></td>".
        "</tr></table>" );


//--------------------------------------------------------------------------------

echo ('<table><tr class="header">');
echo("<th><input type=checkbox onclick='return SelAll();'></th>");


$I=0;
foreach ( $Fields as $Fld) {
  $I++;
  echo("<td><b><a href='TabList.php?TORD=$I'>$Fld</a></b></td>");
}

echo("<td><b>Size</b></td>");

echo("</tr>");

$n=0;
$Cnt=0;

$MB=1048576;   // 1024*1024

try {
  
  $STH=$pdo->query($query);
  $STH->execute($PdoArr);

  while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    
    $classtype="";
    $n++;
    if ($n==2) {
      $n=0;
      $classtype=' class="even"';
    }
    
    $Cnt++;

    echo ("<tr".$classtype.">");

    $PKValArr=array();
    $PKValArr['TabCode']= $dp['TabCode'];
    $PKRes=base64_encode( json_encode($PKValArr));

    $TC = $dp['TabCode'];


    echo ("<td><input type=checkbox ID='Chk_$Cnt' Name=Chk[$Cnt] value='$PKRes'></td>");
    
    
    
    foreach ( $Fields as $Fld) {
      if ($Fld=='TabCode') {
        echo('<td align=left><a href="'.$Frm.'Card.php?'.$Fld.'='.$dp[$Fld].'">'.
             $dp[$Fld]."</a></td>");
      }
      else {
        echo('<td>'.$dp[$Fld]."</td>");
      
      }
    }
    //=========================================================
    $SZ=0;
    $TN=$dp['TabName'];
    /*
    $query = "SELECT data_length + index_length as SZ ". 
             "FROM information_schema.TABLES ". 
             "where (table_schema='$db_base') and (table_name='$TN')"; 

    $sql21 = $pdo->query ($query) 
              or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);
    if ($dp21 = $sql21->fetch_assoc()) {
      $SZ1= $dp21['SZ'];

      if ($SZ1>$MB) {
        $SZ= number_format($SZ1/$MB, 1, ".", "'").' Mb';
      }
      else 
      if ($SZ1>1024) {
        $SZ= number_format($SZ1/1024, 1, ".", "'").' Kb';
      }
      else 
        $SZ= number_format($SZ1, 0, ".", "'");

    }

    echo ("<td align=right>$SZ</td>");
    echo ("<td><a href='UpdDefFields.php?TabNo=$TC' target=T$TC>Set default</a></td>");
    */
    //=========================================================
    
    echo("</tr>");
  };
}
catch (PDOException $e) {
  die ("<br> Error: ".$e->getMessage());
}



echo ("</table>".
      "<input type=hidden ID=AllCnt value='$Cnt'>");

$PredPage= $BegPos-$LN;
if ($PredPage<0)
  $PredPage = 0; 

$LastPage1= floor($CntLines/$LN) * $LN;

echo('<table><tr class="header">');

if ($CurrPage>1) {
  echo('<td><a href="'.$CurrFile.$FullRef.'&BegPos=0"> << First page </a></td>' .
       '<td><a href="'.$CurrFile.$FullRef.'&BegPos='.$PredPage.'"> < Pred Page </a></td>');
};

echo ('<td>Page '.$CurrPage.'</td>');

if ($CurrPage< $LastPage) {
  echo ('<td><a href="'.$CurrFile.$FullRef.'&BegPos='.($BegPos+$LN).'"> Next Page > > </a></td>');
};



echo ('<td><a href="'.$CurrFile.$FullRef.'&BegPos='.$LastPage1.'"> Last Page '.$LastPage.'>> </a></td>'.
      '<td><a href="AdmTabNamesPrintXLS.php'.$FullRef.'">Print Xls</a></td>'.
      "<td><a href='FrmXmlTablesUpload.php' target=FrmUloadXml>Upload XML</a></td>".
       '</tr></table></form>');

?>
</body>
</html>
				       