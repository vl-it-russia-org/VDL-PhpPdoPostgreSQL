<?php
session_start();
include ("../setup/common_pg.php");
include ("../setup/HtmlTxt.php");

BeginProc();

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>SystemDescription list</title></head>
<body>

<?php
include ("../js_SelAll.js");
$TabName='SystemDescription';
$CurrFile='SystemDescriptionList.php';
$Frm='SystemDescription';
$Fields=array('Id','ParagraphNo','ElType'
      ,'Description','Ord1','ParentId');
$enFields= array('ElType'=>'PGElType');
CheckRight1 ($pdo, 'Admin');

 $BegPos = 0;
if (!empty($_REQUEST['BegPos'])) {
  $BegPos = $_REQUEST['BegPos'] +0;
};

$ORD = '"Ord1"';
if ($ORD =='1') {
$ORD = '"ParagraphNo", "Ord1"';
  }
  else {
    $ORD = '"ParagraphNo", "Ord1"';
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

  $query = "select * FROM \"SystemDescription\" ".
           "$WHS $ORDS LIMIT $LN offset $BegPos";

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
  
  $queryCNT = "select COUNT(*) \"CNT\" FROM \"SystemDescription\" ".
              "$WHS ";
  
  $STHCnt = $pdo->prepare($queryCNT);
  $STHCnt->execute($PdoArr);
  
  $CntLines=0;
  if ($dp = $STHCnt->fetch(PDO::FETCH_ASSOC)) {
    $CntLines=$dp['CNT'];  
  };
  $CurrPage= round($BegPos/$LN)+1;
  $LastPage= floor($CntLines/$LN)+1;

  echo ('<br><b>'.GetStr($pdo, 'SystemDescription').' '.
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
  echo ('<hr><table><tr><td><form method=post action="SystemDescriptionCard.php">'.
        '<input type=hidden Name=New VALUE=1>'.
        "<input type=submit Value='".GetStr($pdo, 'New')."'></form></td><td>" );
//--------------------------------------------------------------------------------
echo ('<form method=post action="SystemDescriptionGroupOp.php">'.
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
  echo("<td><a href='SystemDescriptionCard.php?Id={$dp['Id']}'>{$dp[$Fld]}</a></td>");
  

  $Fld='ParagraphNo';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='ElType';
  echo("<td>".GetEnum($pdo, 'PGElType', $dp[$Fld])."</td>");
  

  $Fld='Description';
  echo('<td>'.HtmlTxt($dp[$Fld])."</td>");
  

  $Fld='Ord1';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='ParentId';
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
      '<td><a href="SystemDescriptionPrintXLS.php'.$FullRef.'">Print XLS</a></td>'.

      '<td><a href="Frm-SystemDescription-XlsUpload.php'.$FullRef.'">Upload from XLS</a></td>'.

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
