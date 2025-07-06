<?php
session_start();
include ("../setup/common_pg.php");
BeginProc();

$TabName='Vendors';
OutHtmlHeader ($TabName." list");

include ("../js_SelAll.js");

$CurrFile='VendorsList.php';
$Frm='Vendors';
$Fields=array('Id','VendorType','VendorName'
      ,'ShortName','INN','KPP','Country'
      ,'PostIndx','City','Address','Phone'
      ,'WebLink','DefaultDeliveryPoint','Description','Status'
      ,'Holding','Position','Director','Accountant'
      ,'GeneralBusinessGroup','TaxBusinessGroup','Blocked');
$enFields= array('VendorType'=>'VendorType', 'Status'=>'StatusNUZ', 'GeneralBusinessGroup'=>'GeneralBusinessGroup', 'TaxBusinessGroup'=>'TaxBusinessGroup');
CheckRight1 ($pdo, 'Admin');

 $BegPos = 0;
if (!empty($_REQUEST['BegPos'])) {
  $BegPos = $_REQUEST['BegPos'] +0;
};

$ORD = '"Id"';
if ($ORD =='1') {
$ORD = '"Id"';
  }
  else {
    $ORD = '"Id"';
  }

  $ORDS = ' order by  '; 
  if ($ORD !='') {
    $ORDS = ' order by '.$ORD;
  }
  
  $WHS = '';
  $FullRef='?ORD=1';
  
  $PdoArr = array();
  foreach ( $Fields as $Fld) {
    $Fltr=$_REQUEST['Fltr_'.$Fld];
    
    if ($Fltr!='') {
      if ($WHS !='') {
        $WHS.= ' and ';
      }
      
      if ($enFields[$Fld]!='') {
        $PdoArr[$Fld]= $Fltr;
        
        $WHS.='("'.$Fld."\" = :$Fld)"; 
      }
      else {
        $WHS.= SetFilter2Fld ( $Fld, $Fltr, $PdoArr );
      }
      $FullRef.='&Fltr_'.$Fld.'='.$Fltr ;
    }
  }


try {

  $LN = $_SESSION['LPP'];
  if ($LN=='') {
    $LN=20;  
  };

  if ($WHS != '') {
    $WHS = ' where '.$WHS;
  };   

  $query = "select * FROM \"Vendors\" ".
           "$WHS $ORDS LIMIT $LN offset $BegPos";

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
  
  $queryCNT = "select COUNT(*) \"CNT\" FROM \"Vendors\" ".
              "$WHS ";
  
  $STHCnt = $pdo->prepare($queryCNT);
  $STHCnt->execute($PdoArr);
  
  $CntLines=0;
  if ($dp = $STHCnt->fetch(PDO::FETCH_ASSOC)) {
    $CntLines=$dp['CNT'];  
  };
  $CurrPage= round($BegPos/$LN)+1;
  $LastPage= floor($CntLines/$LN)+1;

  echo ('<br><b>'.GetStr($pdo, 'Vendors').' '.
        GetStr($pdo, 'List').
        '</b> '.$CntLines.' total lines Page <b>'.
        $CurrPage.'</b> from '. $LastPage) ;
  
  echo ('<form method=get action="'.$CurrFile.'"><table><tr>');
  $i=0;
  foreach ( $Fields as $Fld) {
    if ($i==4){
      echo('</tr><tr>');
      $i=0;
    }     
    $i++;
    $CN= "Fltr_$Fld";
    echo("<td align=right><label for=\"$CN\">".GetStr($pdo, $Fld).":</label></td>");

    if ($enFields[$Fld]!=''){
      echo("<td>".EnumSelection($pdo, $enFields[$Fld],"$CN ID=$CN", $_REQUEST['Fltr_'.$Fld], 1)."</td>");
    }
    else {
      echo("<td><input type=text size=12 name='$CN' id='$CN' value='".
        $_REQUEST[$CN]."'></td>");
    }
  }
  echo ('<td><button type="submit">Filter</button></td></tr></table></form>');
  echo ('<hr><table><tr><td><form method=post action="VendorsCard.php">'.
        '<input type=hidden Name=New VALUE=1>'.
        "<input type=submit Value='".GetStr($pdo, 'New')."'></form></td><td>" );
//--------------------------------------------------------------------------------
echo ('<form method=post action="VendorsGroupOp.php">'.
        "<input type=submit  Name=OpType Value='".GetStr($pdo, 'Delete')."' 
          onclick='return confirm(\"Delete selected?\");'></td></tr></table>" );
echo ('<table><tr class="header">');

echo("<th><input type=checkbox onclick='return SelAll();'></th>");


foreach ( $Fields as $Fld) {
  echo("<th>".GetStr($pdo, $Fld)."</th>");
}
echo("</tr>");

$n=0;
$Cnt=0;
while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  $Cnt++;
  $classtype="";
  $n++;
  if ($n==2) {
    $n=0;
    $classtype=' class="even"';
  }
  
  echo ("<tr".$classtype.">");

  $PKValArr=array();
    $PKValArr['Id']= $dp['Id'];
  $PKRes=base64_encode( json_encode($PKValArr));
  
  echo ("<td><input type=checkbox ID='Chk_$Cnt' Name=Chk[$Cnt] value='$PKRes'></td>");
  

  $Fld='Id';
  echo("<td><a href='VendorsCard.php?Id={$dp['Id']}'>{$dp[$Fld]}</a></td>");
  

  $Fld='VendorType';
  echo("<td>".GetEnum($pdo, 'VendorType', $dp[$Fld])."</td>");
  

  $Fld='VendorName';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='ShortName';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='INN';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='KPP';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='Country';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='PostIndx';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='City';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='Address';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='Phone';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='WebLink';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='DefaultDeliveryPoint';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='Description';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='Status';
  echo("<td>".GetEnum($pdo, 'StatusNUZ', $dp[$Fld])."</td>");
  

  $Fld='Holding';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='Position';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='Director';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='Accountant';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='GeneralBusinessGroup';
  echo("<td>".GetEnum($pdo, 'GeneralBusinessGroup', $dp[$Fld])."</td>");
  

  $Fld='TaxBusinessGroup';
  echo("<td>".GetEnum($pdo, 'TaxBusinessGroup', $dp[$Fld])."</td>");
  

  $Fld='Blocked';
  echo('<td>'.$dp[$Fld]."</td>");
  echo("</tr>");
}
echo("</table>".
     "<input type=hidden ID=AllCnt value='$Cnt'>".
     "<input type=submit Name=OpType Value='".GetStr($pdo, 'Delete')."' 
          onclick='return confirm(\"Delete selected?\");'></form>");

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
      '<td><a href="VendorsPrintXLS.php'.$FullRef.'">Print XLS</a></td>'.

      '<td><a href="Frm-Vendors-XlsUpload.php'.$FullRef.'">Upload from XLS</a></td>'.

       '</tr></table>');

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
