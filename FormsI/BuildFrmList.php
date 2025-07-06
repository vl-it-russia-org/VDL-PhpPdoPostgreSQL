<?php
session_start();

include ("../setup/common_pg.php");
include ("BuildChangeStatus.php");
include ("common_func.php");

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>Build list</title></head>
<body>
<?php
CheckRight1 ($pdo, 'RIGHT_EDIT');
mb_internal_encoding("UTF-8");

include "../js_module.php";

$DefDir='../';

$Add_FldsArr=array ();   // Для повторяющихся полей связи (если к одной таблице несколько полей)
$Add_FldsArrT=array ();


$EnumFlds=array();

$FromFldsConn=array();
$ToFldsConn=array();
$ConnCount=0;

if (empty ($_REQUEST['TabNo'])) {
  die ("<br>Error: Bad TabNo ");
}

$TabNo = $_REQUEST['TabNo'];

$PdoArr = array();
$PdoArr['TabNo']= $TabNo;

try {

$query = "select * from \"AdmTabNames\" ".
         "where (\"TabCode\"=:TabNo)";

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);
  
$TabName='';
$TabEditable='$Editable=1;';
if ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $TabName=$dp2['TabName'];
  if ($dp2['TabEditable']!='') {
    $TabEditable=$dp2['TabEditable'];
  }      
}
else {
  die ("<br> Error: Bad Table Name ");
}

echo ("<br> TabName: $TabName ");
//================================================================
// AdmTabFields
// TypeId, ParamNo, ParamName, NeedSeria, DocParamType, NeedBrand, Ord, AddParam, 
// DocParamsUOM, CalcFormula, AutoInc, Description, BinCollation, ShortInfo, EnumLong

$query = "select * from \"AdmTabFields\" ".
         "where (\"TypeId\"=:TabNo) order by \"Ord\", \"ParamNo\"";
  
$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

$Fields=array();
$F2=array();

$EnumTxt='';
$Div='';


$DateTxt='';
$DateDiv='';


$AutoIncArr=array();

while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $Fields[$dp2['ParamName']]=$dp2;
  if ($dp2['AutoInc']) {
    $AutoIncArr[]=$dp2['ParamName'];
  }  
  $F2[$dp2['ParamNo']]=$dp2['ParamName'];


  if ($dp2['DocParamType']==50) {     // ----------------  Enum
    $EnName= $dp2['ParamName'];
    if ( !empty (trim($dp2['AddParam'])) ) {
      $EnName=$dp2['AddParam'];  
    }

    $EnumTxt.= "$Div'{$dp2['ParamName']}'=>'{$EnName}'";
    $Div=",\r\n        ";
  
    $EnumFlds[$dp2['ParamName']]=$EnName;
  }

  if ($dp2['DocParamType']==60) {     // ----------------  Date
    $DateTxt.= "$DateDiv'{$dp2['ParamName']}'=>1";
    $DateDiv=",\r\n        ";
  }

}


// Для ссылок на другие таблицы 
$query = "select * from \"AdmTab2Tab\" ".
         "where (\"TabName\"=:TabName) and (\"Tab2\"!='') and (\"Field2\"!='') ";

$PdoArr = array();
$PdoArr['TabName']= $TabName;
  

//echo ("<br>$query<br>");
$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

$FOtherTab=array();
$ExtTab=array();
$ExtTab1=array();

$ExtTab3=array();


$HaveRef=0;
while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $FOtherTab[$dp2['FldName']][$dp2['LineNo']]=$dp2;
  

  $Tab2= $dp2['Tab2'];
  $i=0;
  $Tab2N=GetTableName ($pdo, $Tab2, $i);

  $ExtTab3 [$dp2['FldName']][$Tab2N]=$dp2['LineNo'];


  $FOtherTab[$dp2['FldName']][$dp2['LineNo']]['TabName2']=$Tab2N;

  if (empty ($Add_FldsArr[$Tab2N])) {
    $Add_FldsArr[$Tab2N]=1;
  }
  else {
    $Add_FldsArrT[$dp2['FldName']]=$Add_FldsArr[$Tab2N];
    $Add_FldsArr[$Tab2N]+=1;
  }      
      
  echo ("<br>ExtTab: $Tab2N / $Tab2 ");

  if ($Tab2N!='') {
    $ExtTab[$Tab2N]=1;
    $ExtTab1[$dp2['Id']]=$Tab2N;
    $HaveRef=1;
  } 


  $i=0;
  $Str=$dp2['AddConnFldFrom'];
  $Fin=0;
  while (!$Fin) {
    $NewFld=GetFieldName($pdo, $Str,$i);
    if ($NewFld=='') {
      $Fin=1;
    }
    else {
      $FromFldsConn[$dp2['Id']][]=$NewFld;
    }
  } 

}

//echo ("<br>");
//print_r ($FromFldsConn);


//echo ("<br>");
//print_r ($Fields);

//echo ("<br>");
//print_r ($F2);

//echo ("<br> --- ");
//print_r ($ExtTab);
//echo ("<br>");

//echo ("<br> --- ");
//print_r ($FOtherTab);
//echo ("<br>");

//================================================================

$query = "select F.* from \"AdmTabIndxFlds\" F ".
           "where (F.\"TabCode\"=:TabNo) and (F.\"IndxName\"='{$TabName}_pkey') ".
           "order by \"LineNo\"";
  
//echo ("<br>$query<br>");
$PdoArr = array();
$PdoArr['TabNo']= $TabNo;

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

$PKFields=array();
$Div='';
$PKList='';
$LastPK='';
while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $FldNo = $dp2['FldNo']; 
  $FldName = $F2[$FldNo];

  $PKFields[]= $FldName;
  $PKList.= $Div.'"'.$FldName.'"';
  $Div=', ';
  $LastPK=$FldName;    
}

//echo ("<br> PkFields: ");
//print_r ($PKFields);

//================================================================
//                              List
//================================================================

$file = fopen("../Forms/{$TabName}List.php","w");

fwrite($file,"<?php\r\n");
fwrite($file,"session_start();\r\n");

$S= 'include ("../setup/common_pg.php");
BeginProc();

$TabName=\''.$TabName.'\';
OutHtmlHeader ($TabName." list");

include ("../js_SelAll.js");'.
"\r\n";
fwrite($file,$S);

$S= '
$CurrFile=\''.$TabName.'List.php\';
$Frm=\''.$TabName.'\';'.
"\r\n";
fwrite($file,$S);

$S= '$Fields=array(';
$Div='';

$enS='$enFields= array(';
$enDiv='';
$enArr=array ();

$DigArr=array();

$kk=0;
foreach ($Fields as $Fld=>$Arr) {
  $kk++;
  if ($kk==4) {
    $S.="\r\n      ";
    $kk=0;
  }

  $S.="$Div'$Fld'";
  $Div=',';

  if ($Fields[$Fld]['DocParamType']==50) { // enum
    $SetName= $Fld;
    if ($Fields[$Fld]['AddParam']!='') {
      $SetName= $Fields[$Fld]['AddParam']; 
    } 
    
    $enS.="$enDiv'$Fld'=>'$SetName'";
    $enArr[$Fld]=$SetName;
    $enDiv=', ';
  }

  if ($Fields[$Fld]['DocParamType']==20) { // numbers
    if ($Fields[$Fld]['AddParam']!='') {
      $p=strpos($Fields[$Fld]['AddParam'], '.');
      if ($p!==false) {
        $len=strlen(trim($Fields[$Fld]['AddParam']));
        if ($len>2){
          $len-=2;
        }
        $DigArr[$Fld]=$len;
      }
    }
  }

}

$S.=");\r\n".
    $enS.");\r\n".

"CheckRight1 (\$pdo, 'Admin');\r\n\r\n ".
  '$BegPos = 0;'."\r\n".
  'if (!empty($_REQUEST[\'BegPos\'])) {'."\r\n".
  '  $BegPos = $_REQUEST[\'BegPos\'] +0;'."\r\n".
  '};'."\r\n\r\n".
  '$ORD = \''.$PKList.'\';'."\r\n".
  'if ($ORD ==\'1\') {'."\r\n".
    '$ORD = \''.$PKList.'\';
  }
  else {
    $ORD = \''.$PKList.'\';
  }

  $ORDS = \' order by  \'; 
  if ($ORD !=\'\') {
    $ORDS = \' order by \'.$ORD;
  }
  
  $WHS = \'\';
  $FullRef=\'?ORD=1\';
  
  $PdoArr = array();
  foreach ( $Fields as $Fld) {
    $Fltr=$_REQUEST[\'Fltr_\'.$Fld];
    
    if ($Fltr!=\'\') {
      if ($WHS !=\'\') {
        $WHS.= \' and \';
      }
      
      if ($enFields[$Fld]!=\'\') {
        $PdoArr[$Fld]= $Fltr;
        
        $WHS.=\'("\'.$Fld."\" = :$Fld)"; 
      }
      else {
        $WHS.= SetFilter2Fld ( $Fld, $Fltr, $PdoArr );
      }
      $FullRef.=\'&Fltr_\'.$Fld.\'=\'.$Fltr ;
    }
  }
'.
"\r\n";
fwrite($file,$S);


$S='
try {

  $LN = $_SESSION[\'LPP\'];
  if ($LN==\'\') {
    $LN=20;  
  };

  if ($WHS != \'\') {
    $WHS = \' where \'.$WHS;
  };   

  $query = "select * FROM \"'.$TabName.'\" ".
           "$WHS $ORDS LIMIT $LN offset $BegPos";

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
  
  $queryCNT = "select COUNT(*) \"CNT\" FROM \"'.$TabName.'\" ".
              "$WHS ";
  
  $STHCnt = $pdo->prepare($queryCNT);
  $STHCnt->execute($PdoArr);
  
  $CntLines=0;
  if ($dp = $STHCnt->fetch(PDO::FETCH_ASSOC)) {
    $CntLines=$dp[\'CNT\'];  
  };
  $CurrPage= round($BegPos/$LN)+1;
  $LastPage= floor($CntLines/$LN)+1;

  echo (\'<br><b>\'.GetStr($pdo, \''.$TabName.'\').\' \'.
        GetStr($pdo, \'List\').
        \'</b> \'.$CntLines.\' total lines Page <b>\'.
        $CurrPage.\'</b> from \'. $LastPage) ;
  
  echo (\'<form method=get action="\'.$CurrFile.\'"><table><tr>\');
  $i=0;
  foreach ( $Fields as $Fld) {
    if ($i==4){
      echo(\'</tr><tr>\');
      $i=0;
    }     
    $i++;
    $CN= "Fltr_$Fld";
    echo("<td align=right><label for=\"$CN\">".GetStr($pdo, $Fld).":</label></td>");

    if ($enFields[$Fld]!=\'\'){
      echo("<td>".EnumSelection($pdo, $enFields[$Fld],"$CN ID=$CN", $_REQUEST[\'Fltr_\'.$Fld], 1)."</td>");
    }
    else {
      echo("<td><input type=text size=12 name=\'$CN\' id=\'$CN\' value=\'".
        $_REQUEST[$CN]."\'></td>");
    }
  }
  echo (\'<td><button type="submit">Filter</button></td></tr></table></form>\');
  ';

fwrite($file,$S);


$S='echo (\'<hr><table><tr><td><form method=post action="'.$TabName.'Card.php">\'.
        \'<input type=hidden Name=New VALUE=1>\'.
        "<input type=submit Value=\'".GetStr($pdo, \'New\')."\'></form></td><td>" );
//--------------------------------------------------------------------------------
echo (\'<form method=post action="'.$TabName.'GroupOp.php">\'.
        "<input type=submit  Name=OpType Value=\'".GetStr($pdo, \'Delete\')."\' 
          onclick=\'return confirm(\"Delete selected?\");\'></td></tr></table>" );
echo (\'<table><tr class="header">\');

echo("<th><input type=checkbox onclick=\'return SelAll();\'></th>");


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
    $classtype=\' class="even"\';
  }
  
  echo ("<tr".$classtype.">");
';

if ($PKCnt==1) {
  $S.='
  echo ("<td><input type=checkbox ID=\'Chk_$Cnt\' Name=Chk[$Cnt] value=\'{$dp[\''.
         $LastPK.'\']}\'></td>");
  ';
}
else {
  $MS='';
  foreach ($PKFields as $Fld) {
    $MS.="\r\n    ".'$PKValArr[\''.$Fld.'\']= $dp[\''.$Fld.'\'];';  
  }
  $S.='
  $PKValArr=array();'.$MS.'
  $PKRes=base64_encode( json_encode($PKValArr));
  
  echo ("<td><input type=checkbox ID=\'Chk_$Cnt\' Name=Chk[$Cnt] value=\'$PKRes\'></td>");
  ';
}


foreach ($Fields as $Fld=>$Arr) {
  $S.="\r\n\r\n".'  $Fld=\''.$Fld.'\';
  ';
  if ($Fld==$LastPK) {
    $S.='echo("<td><a href=\''.$TabName.'Card.php?';

    $Div='';
    foreach ( $PKFields as $Fld) {
      $S.=$Div.$Fld.'={$dp[\''.$Fld.'\']}';
      $Div='&';  
    }

    $S.='\'>';
    if ($enArr[$LastPK]!='') {
      $S.='".GetEnum($pdo, \''.$enArr[$LastPK].'\',$dp[$Fld])."'; 
    }
    else {
      $S.='{$dp[$Fld]}';
    }
    $S.='</a></td>");
  ';
  }
  else 
  if ($enArr[$Fld]!=''){
    $S.='echo("<td>".GetEnum($pdo, \''.$enArr[$Fld].'\', $dp[$Fld])."</td>");
  ';

  }
  else 
  if ($DigArr[$Fld]!=0){
    $S.='$OW=number_format($dp[$Fld], '.$DigArr[$Fld].', ".", "\'");
  echo("<td align=right> $OW </td>");
  ';

  }
  else {
    $S.='echo(\'<td>\'.$dp[$Fld]."</td>");
  ';
  }
};
$S.='echo("</tr>");
}
echo("</table>".
     "<input type=hidden ID=AllCnt value=\'$Cnt\'>".
     "<input type=submit Name=OpType Value=\'".GetStr($pdo, \'Delete\')."\' 
          onclick=\'return confirm(\"Delete selected?\");\'></form>");

$PredPage= $BegPos-$LN;
if ($PredPage<0)
  $PredPage = 0; 

$LastPage1= floor($CntLines/$LN) * $LN;

echo(\'<table><tr class="header">\');

if ($CurrPage>1) {
  echo(\'<td><a href="\'.$CurrFile.$FullRef.\'&BegPos=0"> << First page </a></td>\' .
       \'<td><a href="\'.$CurrFile.$FullRef.\'&BegPos=\'.$PredPage.\'"> < Pred Page </a></td>\');
};

echo (\'<td>Page \'.$CurrPage.\'</td>\');

if ($CurrPage< $LastPage) {
  echo (\'<td><a href="\'.$CurrFile.$FullRef.\'&BegPos=\'.($BegPos+$LN).\'"> Next Page > > </a></td>\');
};

echo (\'<td><a href="\'.$CurrFile.$FullRef.\'&BegPos=\'.$LastPage1.\'"> Last Page \'.$LastPage.\'>> </a></td>\'.
      \'<td><a href="'.$TabName.'PrintXLS.php\'.$FullRef.\'">Print XLS</a></td>\''.".\r\n".'
      \'<td><a href="Frm-'.$TabName.'-XlsUpload.php\'.$FullRef.\'">Upload from XLS</a></td>\''.".\r\n".'
       \'</tr></table>\');

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
';
fwrite($file,$S);

fclose($file);

echo ("<br><a href='../Forms/{$TabName}List.php'>Frm List $TabName</a> ");

//--------------------------------------------------------------------------------
//--------------------------------------------------------------------------------
//                             PrintXLS 
//--------------------------------------------------------------------------------
//--------------------------------------------------------------------------------

$file = fopen("../Forms/{$TabName}PrintXLS.php","w");

fwrite($file,"<?php\r\n");
fwrite($file,"session_start();\r\n");

$S= 
"require '../../composer/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;\r\n".


'include ("../setup/common_pg.php");
BeginProc();'."\r\n";

fwrite($file,$S);

$S= 'define(\'EOL\',(PHP_SAPI == \'cli\') ? PHP_EOL : \'<br />\');'."\r\n"; 

//'require_once \'../PhpExcel/Classes/PHPExcel.php\';'."\r\n";

$S.= '$TabName=\''.$TabName.'\';
$Frm=\''.$TabName.'\';'."\r\n";

fwrite($file,$S);

$S= '$Fields=array(';
$Div='';

$enS='$enFields= array(';
$enDiv='';
$enArr=array ();

$DigArr=array();

$kk=0;
foreach ($Fields as $Fld=>$Arr) {
  $kk++;
  if ($kk==4) {
    $S.="\r\n      ";
    $kk=0;
  }

  $S.="$Div'$Fld'";
  $Div=',';

  if ($Fields[$Fld]['DocParamType']==50) { // enum
    $SetName= $Fld;
    if ($Fields[$Fld]['AddParam']!='') {
      $SetName= $Fields[$Fld]['AddParam']; 
    } 
    
    $enS.="$enDiv'$Fld'=>'$SetName'";
    $enArr[$Fld]=$SetName;
    $enDiv=', ';
  }

  if ($Fields[$Fld]['DocParamType']==20) { // numbers
    if ($Fields[$Fld]['AddParam']!='') {
      $p=strpos($Fields[$Fld]['AddParam'], '.');
      if ($p!==false) {
        $len=strlen(trim($Fields[$Fld]['AddParam']));
        if ($len>2){
          $len-=2;
        }
        $DigArr[$Fld]=$len;
      }
    }
  }

}

$S.=");\r\n".
   $enS.");\r\n".

"CheckRight1 (\$pdo, 'Admin');\r\n\r\n ".

  '$ORD = $_REQUEST[\'ORD\'];'."\r\n".
  'if ($ORD ==\'1\') {'."\r\n".
    '$ORD = \''.$PKList.'\';
  }
  else {
    $ORD = \''.$PKList.'\';
  }

  $ORDS = \' order by  \'; 
  if ($ORD !=\'\') {
    $ORDS = \' order by \'.$ORD;
  }

  $PdoArr = array();
  $WHS = \'\';
  $FullRef=\'?ORD=\'.$ORD;
  foreach ( $Fields as $Fld) {
    $Fltr=$_REQUEST[\'Fltr_\'.$Fld];
    if ($Fltr!=\'\') {
      if ($WHS !=\'\') {
        $WHS.= \' and \';
      }
      if ($enFields[$Fld]!=\'\') {
        $WHS.=\'("\'.$Fld."\" = :$Fld)";
        $PdoArr[$Fld]= $Fltr;
      }
      else {
        $WHS.= SetFilter2Fld ( $Fld, $Fltr, $pdo );
      }
      $FullRef.=\'&Fltr_\'.$Fld.\'=\'.$Fltr ;
    }
  }
'.
"\r\n";
fwrite($file,$S);

$Let = array ( 'A','B','C','D','E', 'F','G','H','I','J',
                   'K','L','M','N','0', 'P','Q','R','S','T','U');

$S="
\$Let = array ( 'A','B','C','D','E', 'F','G','H','I','J',
                   'K','L','M','N','0', 'P','Q','R','S','T','U');
".

'
$objPHPExcel = new Spreadsheet();
$objPHPExcel->getProperties()->setCreator("Vladislav Levitskiy")
             ->setLastModifiedBy($_SESSION[\'login\'])
             ->setTitle("AccPhp '.$TabName.'")
             ->setSubject("'.$TabName.'")
             ->setDescription("VDL PHP+PDO+PostgreSQL")
             ->setKeywords("AccPhp;'.$TabName.'")
             ->setCategory("AccPhp;'.$TabName.'");
  
$objPHPExcel->setActiveSheetIndex(0);
$aSheet = $objPHPExcel->getActiveSheet();

$W= array ( 10, 10, 10, 10, 10, 10, 10, 11, 11, 12, 12);
$LastCol="";
foreach ($W as $i => $Val) {
  $aSheet->getColumnDimension($Let[$i])->setWidth($Val);
  $LastCol=$Let[$i];
};
';

fwrite($file,$S);

$S='
if ($WHS != \'\') {
  $WHS = \' where \'.$WHS;
};

$row=1;
$col=1;   


$aSheet->setCellValue([$col, $row], GetStr($pdo, \''.$TabName.'\').
      \' \'.  GetStr($pdo, \'List\'));
  
$row++;
$aSheet->setCellValue([$col, $row], GetStr($pdo, \'Created\').
      ": {$_SESSION[\'login\']} ". date("Y-m-d H:i:s"));


$query = "select * FROM \"'.$TabName.'\" ".
         "$WHS $ORDS";

try {
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);

';

fwrite($file,$S);


$S='

$row++;
$col=1;

$FL=$row;

foreach ( $Fields as $Fld) {
  $aSheet->setCellValue([$col, $row], GetStr($pdo, $Fld));
  $col++; 
}

$n=0;
$Cnt=0;
$row++;

while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  $col=1;
';

foreach ($Fields as $Fld=>$Arr) {
  
  $S.="\r\n".
      '  $Fld=\''.$Fld.'\';'."\r\n";
  
  if ($enArr[$Fld]!=''){
    $S.='  $aSheet->setCellValue([$col, $row], GetEnum($pdo, \''.$enArr[$Fld].'\', $dp[$Fld]));'."\r\n";
  }
  else 
  if ($DigArr[$Fld]!=0){
    $S.='  $OW=$dp[$Fld]; //$OW=number_format($dp[$Fld], '.$DigArr[$Fld].', ".", "\'");
    $aSheet->setCellValue([$col, $row], $OW);'."\r\n";
  }
  else {
    $S.='  $aSheet->setCellValue([$col, $row], $dp[$Fld]);'."\r\n";
  }
  $S.='  $col++;'."\r\n";
};
$S.='  $row++;
}
';

fwrite($file,$S);
//------------------------------ Page setup

$S='
  $l=$row-1;
  $aSheet->setAutoFilter("A3:{$LastCol}3");
  $aSheet->freezePane("C4");

  $styleArray = array(
      \'borders\' => array(
          \'outline\' => array(
              \'borderStyle\' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
              //\'color\' => array(\'argb\' => \'FFFF0000\'),
          ),
          
          \'inside\' => array(
              \'borderStyle\' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
              //\'color\' => array(\'argb\' => \'FFFF0000\'),
          ),
      ),
  );  

  $aSheet->getStyle("A$FL:$LastCol$l")->applyFromArray($styleArray);



  $aSheet->getPageSetup()
                ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                                                                           // ::ORIENTATION_PORTRAIT );
  $aSheet->getPageSetup()
                ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);


  $margin = 0.5 / 2.54;
  $aSheet->getPageMargins()->setTop($margin*5);
  $aSheet->getPageMargins()->setRight($margin);
  $aSheet->getPageMargins()->setLeft($margin*2);
  $aSheet->getPageMargins()->setBottom($margin);

  //$aSheet->getPageSetup()->setScale(80);

  $aSheet->getPageSetup()->setFitToWidth(1);
  $aSheet->getPageSetup()->setFitToHeight(10);  

  $add_str=date(\'-Ymd_His\');


  //MakeAdminRec ($pdo, $_SESSION[\'login\'], \'EDI_ORD\', $OrdId, 
  //                      \'Out XLS\', "Out file $add_str.XLS: $LineNo lines, amount $TotAmount");

  $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel , \'Xlsx\');

 }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }


';
fwrite($file,$S);


$S="
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=Xls-$TabName'.\$add_str.'.xlsx');
\$writer->save('php://output');
?>";

fwrite($file,$S);

fclose($file);


echo ("<br><a href='../Forms/{$TabName}List.php'>Print XLS $TabName</a> ");

//--------------------------------------------------------------------------------
//--------------------------------------------------------------------------------
//                             GroupOp 
//--------------------------------------------------------------------------------
//--------------------------------------------------------------------------------
$file = fopen("../Forms/{$TabName}GroupOp.php","w");

fwrite($file,"<?php\r\n");
fwrite($file,"session_start();\r\n");

$S= '
include "'.$DefDir.'setup/common_pg.php";
BeginProc();
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<meta http-equiv="Content-Language" content="ru">
<title>'.$TabName.' Card</title></head>
<body>
<?php
//CheckLogin1 ();
CheckRight1 ($pdo, "Admin");

//print_r($_REQUEST);
//die();
'.
"\r\n";
fwrite($file,$S);

$FldAccArr=array ();

$S= '
if (is_array($_REQUEST[\'Chk\'])) { 
  $Res=\'\';
  $Div=\'\';
  foreach ( $_REQUEST[\'Chk\'] as $Indx=> $Val) {
    ';
    if ($PKCnt==1) {
      $S.='
      $V=addslashes ($Val);
      $Res.="$Div\'$V\'";
      $Div=\',\';
    }
    if ($_REQUEST[\'OpType\']== GetStr($pdo, \'Delete\')) {
      $query = "delete from \"'.$TabName.'\" ".
               " WHERE ('.$LastPK.' in $Res) ";
      $sql2 = $pdo->query ($query)
                 or die("Invalid query:<br>$query<br>" . $pdo->error());
      
    }  
    ';
    }
    else {
      $S.='
      $PKValArr=json_decode(base64_decode($Val), true);
      ';
      $Div='';
      
      $PKStr='';
      $VDiv='';
      foreach ($PKFields as $Fld) {
        $S.="\r\n    ".
            '$V'.$VDiv.'="'.$Div.'\'".addslashes($PKValArr[\''.$Fld.'\'])."\'";';
        $PKStr.= "$Div$Fld";
        $Div=',';
        $VDiv='.'; 
      }
      $S.='
      $Res.="$Div($V)";
      $Div=",";
    }
    if ($_REQUEST[\'OpType\']== GetStr($pdo, \'Delete\')) {
      $query = "delete from '.$TabName.' ".
               " WHERE ( ('.$PKStr.') in ($Res) )";
      $sql2 = $pdo->query ($query)
                 or die("Invalid query:<br>$query<br>" . $pdo->error());
      
    }  
    ';

  }
$S.="\r\n}\r\n".

'echo (\'<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="style.css">\'.
\'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$TabName.'List.php?\'.$LNK.\'">\'.
\'<title>Save</title></head>
<body>\');
  
  echo(\'<H2>Saved</H2>\');
?>
</body>
</html>';

fwrite($file,$S);

fclose($file);

//--------------------------------------------------------------------------------




//--------------------------------------------------------------------------------
//--------------------------------------------------------------------------------
//                         Card
//--------------------------------------------------------------------------------
//--------------------------------------------------------------------------------
$file = fopen("../Forms/{$TabName}Card.php","w");

fwrite($file,"<?php\r\n");
fwrite($file,"session_start();\r\n");

$S= '
include ("../setup/common_pg.php");
BeginProc();

$TabName=\''.$TabName.'\';
OutHtmlHeader ($TabName." card");


// Checklogin1();'."\r\n";

fwrite($file,$S);

$FldAccArr=array ();

if ($HaveRef) {
  //echo ("<br> FOtherTab: ");
  //print_r($FOtherTab);
  $S= 'include "../js_module.php";'."\r\n\r\n//------- For Ext Tables --------- ";
  foreach ($ExtTab1 as $II=>$EXT ) {
    //echo ("<br> $IT -> $Ext ");

    $S.="\r\n  ScriptSelectionTabs('$EXT$II', 'Select$EXT.php', '".
        addslashes (GetStr($pdo, $EXT))."', 'SelId=$II&');";
  }
}
else {
  $S='';
}

$S.="\r\n\r\n";
fwrite($file,$S);

$S= "CheckRight1 (\$pdo, 'Admin');\r\n\r\n".
'$FldNames=array(';
$Div='';

$Cnt=0;
foreach ($Fields as $Fld=>$Arr) {
  $S.="$Div'$Fld'";
  $Div=',';

  $Cnt++;
  if ($Cnt==4) {
    $S.="\r\n          ";
    $Cnt=0;
  }
  
  if ($Arr['DocParamType']==50) {
    //$enS.="$enDiv'$Fld'";
    //$enDiv=',';
  }
  //echo (" Fld:$Fld ");
}

$S.=");\r\n".$enS.");\r\n";

$WH='';
$DW='';

$RR=0;
$WW='';
$WW1='';
$LastFld='';

$FullLink='';
$DivFL='';

//-------------------------------
$PDO1='';
$LPK='';

$S.='$PdoArr = array();'."\r\n";  


foreach ($PKFields as $PK) {
  $RR++;
  $S.='$'.$PK.'=$_REQUEST[\''.$PK."'];\r\n";
  $S.='$PdoArr["'.$PK.'"]=$'.$PK.";\r\n";
    
  $WH.= $DW. '(\"'.$PK."\\\"=:".$PK.')';
  if ($WW1!='') {
    $WW.= ' AND '. $WW1;
    $PDO1.='$PdoArr["'.$LPK.'"]= $'.$LPK.";\r\n";
  }
  
  $LastFld=$PK;

  $WW1= '(\"'.$PK."\\\"=:".$PK.')';
  $DW=' AND ';
  
  $LPK=$PK;

  $FullLink.=$DivFL.$PK.'=$'.$PK;
  $DivFL='&';

};

//=================================================

$FillNewFlds=array();


// AdmFieldsAddFunc
// Id, TabName, FldName, AddFunc
$query = "select * from \"AdmFieldsAddFunc\" ". 
         "where (\"TabName\" = :TabName) and (\"AddFunc\"=40) order by \"FldName\" "; 

$PdoArr = array();
$PdoArr['TabName']= $TabName;

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

while ($dp22 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $FillNewFlds[$dp22['FldName']]=1;
}

//=================================================



$S.='echo("<H3>".GetStr($pdo, \''.$TabName.'\')."</H3>");'."\r\n".
  '  $dp=array();
  $FullLink="'.$FullLink.'";

  $query = "select * FROM \"'.$TabName.'\" ".
           "WHERE '.$WH.'";
  
  try {
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);  
  
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  }
  
  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }  
  
  $New=$_REQUEST[\'New\'];

'.$TabEditable.'
if ($Editable) {
';
$FillNewFT=0;  

foreach ($FillNewFlds as $Fld=>$V) {
  if ($FillNewFT==0) {
    $S.='  if ($New==1) {
  ';
    $FillNewFT=1;  
  };

  $S.='  $dp["'.$Fld.'"]= $_REQUEST["'.$Fld.'"];'."\r\n";
}

if ($FillNewFT==1) {
  $S.='  }'."\r\n";
}
  
$S.='  echo (\'<form method=post action="'.$TabName.'Save.php">\'.
        "<input type=hidden Name=\'New\' value=\'$New\'>");
  
  echo ("<table><tr>");';

fwrite($file,$S);

//=================================================================
$S="\r\n".


//=================================================================



$S="\r\n".'  $LN=0;';

//=================================================

$StatusFlds=array();

// AdmFieldsAddFunc
// Id, TabName, FldName, AddFunc
$query = "select * from \"AdmFieldsAddFunc\" ". 
         "where (\"TabName\" = :TabName) and (\"AddFunc\"=10) order by \"FldName\" "; 

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

while ($dp22 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $StatusFlds[$dp22['FldName']]=1;
}

//=================================================


if ($RR>1) {
  $S="\r\n  ".'$PdoArr = array();'."\r\n  $PDO1".
     "\r\n  if (".'$New==1) {'.
     "\r\n      ".'$query = "select max(\"'.$PKFields[$RR-1].'\") \"MX\" ".'.
     "\r\n      ".'"FROM \"'.$TabName.'\" ".'.
     "\r\n      ".'" WHERE (1=1) '.$WW.'";'.
     "\r\n      ".'$STH4 = $pdo->prepare($query);'.
     "\r\n      ".'$STH4->execute($PdoArr);'.
     "\r\n      ".'$LN=0;'.
     "\r\n      ".'if ($dp4 = $STH4->fetch(PDO::FETCH_ASSOC)) {'.
     "\r\n        ".'$LN=$dp4[\'MX\'];'.
     "\r\n      ".'}'.
     "\r\n      ".'$LN+=1;'.
     "\r\n  ".'}'."\r\n";
}

foreach ($Fields as $Fld=>$Arr) {
  $S.= "\r\n".'  $Fld=\''.$Fld.'\';
  $OutVal= $dp[$Fld];';
  if (in_array ($Fld, $PKFields)) {             
    $S.="\r\n  echo (\"<input type=hidden Name='Old$Fld' ".
        'value=\'$OutVal\'>");'.
        "\r\n";
  }
  
  $S.='  echo ("<td align=right><label for=\''.$Fld.'\'>".GetStr($pdo, $Fld).":</label></td><td>");'."\r\n";
  
  $FldType=$Arr['DocParamType'];
  if (in_array ($Fld, $PKFields)) {
    if (!empty($EnumFlds[$Fld])){
      $S.='  echo ( EnumSelection($pdo, "'.$EnumFlds[$Fld].'", "'.$Fld.' ID=\'$Fld\' ", $OutVal));';
    }
    else {
      $S.='  echo ("<input type=text Name=\'$Fld\'  ID=\'$Fld\' Value=\'{$'.$Fld.'}\' size=10 readonly>");';  
    }
  }
  else {
  
  
  
  if ($FldType==10) {
    // 10 EN Text50
    if ($Arr['AddParam']=='') {
      $S.='  echo ("<input type=text Name=\'$Fld\' ID=\'$Fld\' Value=\'{$dp[$Fld]}\' size=50>");';  
    }
    else {
      $N=$Arr['AddParam']+0;
      if ($N>100) {
        $S.='  echo ("<textarea Name=\'$Fld\'  ID=\'$Fld\'  cols=50 rows=3>{$dp[$Fld]}</textarea>");';  
      }
      else 
        $S.='  echo ("<input type=text Name=\'$Fld\'  ID=\'$Fld\' Value=\'{$dp[$Fld]}\' size='.$N.'>");';  
    } 
  }
  else 
  if ($FldType==15) {
      $S.='  echo ("<textarea Name=\'$Fld\'  ID=\'$Fld\'  cols=50 rows=3>{$dp[$Fld]}</textarea>");';  
  }
  else
  if ($FldType==20) {
    // Number
    if ($Arr['AddParam']=='') {
      $S.='  echo ("<input type=number Name=\'$Fld\'  ID=\'$Fld\'  Value=\'{$dp[$Fld]}\'>");';  
    }
    else {
      $S.='  echo ("<input type=number Name=\'$Fld\'  ID=\'$Fld\' Value=\'{$dp[$Fld]}\' step='.$Arr['AddParam'].'>");';  
    } 
  }
  else 
  if ($FldType==60) {
    // Date
    $S.='  echo ("<input type=date Name=\'$Fld\'  ID=\'$Fld\'  Value=\'{$dp[$Fld]}\'>");';  
  }
  
  else 
  if ($FldType==60) {
    // Date
    $S.='  echo ("<input type=\'datetime-local\' Name=\'$Fld\'  ID=\'$Fld\'  Value=\'{$dp[$Fld]}\'>");';  
  }
  
  else 
  if ($FldType==30) {
    
    $S.='  $Ch=\'\'; if ($dp[$Fld]==1) $Ch=\'Checked\';'.
        "\r\n".'  echo ("<input type=checkbox Name=\'$Fld\'  ID=\'$Fld\' Value=1 $Ch>");';  
  }
  else 
  if ($FldType==40) {
  }
  else 
  if ($FldType==45) {
  }
  else 
  if ($FldType==50) {
    if (empty ($StatusFlds[$Fld]) ) {
      $S.='  echo ( EnumSelection($pdo, "'.$Arr['AddParam'].'", "'.$Fld.' ID=\'$Fld\' ", $OutVal));';
    }
    else {
      $S.='  echo ( "<b>".GetEnum($pdo, "'.$Arr['AddParam'].'", $OutVal)."</b>");';
    }
  }
  } //------------------ 
  
  

  //echo ("<br><br> --$Fld-- ");
  //print_r ($ExtTab);
  //echo ("<br><br> --$Fld-- ");
  //print_r ($FOtherTab);


  if (!empty($FOtherTab[$Fld])) {
    $ET= $FOtherTab[$Fld];
    //echo ("<br><br>-- OtherTab: ");
    //print_r($ET);
    
    //echo ("<br><br>-- ExtTab3: ");
    //print_r($ExtTab3);
    //echo ("<br>");
    
    foreach ($ET as $Indx1=> $Arr1) {
      
      $ET1= $Arr1['TabName2'];
      $SMI= $Arr1['Id'];
      
      $S.="\r\n".'  echo(" <input type=button value=\'...\' '.
          'onclick=\'return Select'.$ET1.$SMI.'Fld(\"'.$Fld.'\"';
      if (! empty($FromFldsConn[$SMI])) {
        foreach ($FromFldsConn[$SMI] as $AI1=>$AIF1){
          $S.=', \"'.$AIF1.'\"'; 
        }
      }

      $S.=');\'>");'."\r\n";
    }
  }

  $S.="\r\n".'  echo("</td>");'.
  "\r\n".
  '  echo ("</tr><tr>");'."\r\n";  
} //-------------------------------- Fld foreach --------------------------

fwrite($file,$S);
$S="\r\n".
'  echo ("<td colspan=2 align=right>'.
   '<input type=submit value=\'".
         GetStr($pdo, \'Save\')."\'></td></tr></table></form>");
} //Editable
else {
  echo ("<table>");
';
  
  foreach ($Fields as $Fld=>$Arr) {
  $FldType=$Arr['DocParamType'];
  
  $S.= "\r\n".'  $Fld=\''.$Fld.'\';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  ';
  if ( $FldType==30) {
    
    $S.='$Checked="";
  if ($OutVal) { 
    $Checked=" checked ";
  }
  echo ("<input type=checkbox $Checked value=1 disabled");
  ';
   
  }
  else
  if ( $FldType==50) {
    // Enum
    $S.='
  echo ("<b>".GetEnum($pdo, "'.$Arr['AddParam'].'", $OutVal)."</b>");
  ';
   
  }
  else 
  if ($DigArr[$Fld]!=0){
    $S.='$OW=number_format($OutVal, '.$DigArr[$Fld].', ".", "\'");
  echo ("<b>$OW</b>");
  ';

  }
  else {
    // All
    $S.='
  echo ($OutVal);
  ';
  }
  $S.='
  echo("</td></tr>");
  ';

  }  

$S.='  echo ("</table>");
}
echo ("  <hr><br><a href=\''.$TabName.'List.php\'>".GetStr($pdo, \'List\')."</a>");';

foreach ($StatusFlds as $Fld=>$V) {
  $S.="\r\n".'echo (" | <a href=\''.$TabName.
       'Change$Fld.php?$FullLink&NewStatus=0\'>".GetStr($pdo, \'Change'.$Fld.'\')."</a>");';
}

$S.='
if ($Editable)
  echo (" | <a href=\''.$TabName.'Delete.php?$FullLink\' onclick=\'return confirm(\"Delete?\");\'>".
        GetStr($pdo, \'Delete\')."</a>");
?>
</body>
</html>
';

fwrite($file,$S);
fclose($file);
//-------------------------------------------------------------------------------
foreach ($StatusFlds as $Fld=>$V) {
  BuildChangeStatus($pdo, $TabName, $Fld);
}
//--------------------------------------------------------------------------------
//   Save file
//--------------------------------------------------------------------------------
include "BuildFrmSave.php";

//--------------------------------------------------------------------------------
//                Delete file
//--------------------------------------------------------------------------------

$file = fopen("../Forms/{$TabName}Delete.php","w");

fwrite($file,"<?php\r\n");
fwrite($file,"session_start();\r\n");

$S= '
include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();'.
"\r\n";
fwrite($file,$S);

$S= "CheckRight1 (\$pdo, 'Admin');\r\n\r\n ".
'$FldNames=array(';
$Div='';

$kk=0;
foreach ($Fields as $Fld=>$Arr) {
  $kk++;
  if ($kk==4) {
    $S.="\r\n      ";
    $kk=0;
  }
  $S.="$Div'$Fld'";
  $Div=',';

  if ($Arr['DocParamType']==50) {
    
  }
  //echo (" Fld:$Fld ");
}

$S.=");\r\n";

$WH='';
$DW='';

$S.='$New=$_REQUEST[\'New\'];'."\r\n";  
$S.='$PdoArr = array();'."\r\n";  

$FullLink='';
$DivFL='';
foreach ($PKFields as $PK) {
  $S.='$'.$PK.'=$_REQUEST[\''.$PK."'];\r\n";  
  $S.='if ($'.$PK.'==\'\'){ die ("<br> Error:  Empty '.$PK.'");}'."\r\n";
  
  $WH.= $DW. '(\"'.$PK."\\\"= :".$PK.')';
  $S.='$PdoArr["'.$PK.'"] = $'.$PK.';'."\r\n";
  
  $DW=' AND ';
};

$S.="\r\n".
 '$dp=array();
  
  $query = "select * FROM \"'.$TabName.'\" ".
           "WHERE '.$WH.'";
  try{
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
  
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  }
  else {
    die ("<br> Error: not found record '.$WH.'"); 
  }
  '.$TabEditable.'
  if (!$Editable) {
    die ("<br> Error: Not Editable record ");
  }
  
  $query = "delete FROM \"'.$TabName.'\" ".
           "WHERE '.$WH.'";
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
  
  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

';  
fwrite($file,$S);

$S='$LNK=\'\';
';

$DL='';
$N=0;
foreach ($PKFields as $PK) {
  $N++;
  $S.= '
  $V=$_REQUEST[\''.$PK.'\'];
  $LNK.="'.$DL.$PK.'=$V";
  ';
  $DL='&';
}

$S.="\r\n".

'echo (\'<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">\'.
\'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$TabName.'List.php">\'.
\'<title>Save</title></head>
<body>\');
  
  echo(\'<H2>Deleted</H2>\');
?>
</body>
</html>';

fwrite($file,$S);

fclose($file);
//--------------------------------------------------------------------------------
//                Form Xls Upload file
//--------------------------------------------------------------------------------

$file = fopen("../Forms/Frm-{$TabName}-XlsUpload.php","w");

fwrite($file,"<?php\r\n");
fwrite($file,"session_start();\r\n");

$S= '
include ("../setup/common_pg.php");
BeginProc();
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>'.$TabName.' Card</title></head>
<body>
<?php
// Checklogin1();'."\r\n";

fwrite($file,$S);

$S= "CheckRight1 (\$pdo, 'ExtProj.Admin');\r\n\r\n ";
$Div='';

$kk=0;

$Cnt=0;

$FldsList='';
foreach ($Fields as $Fld=>$Arr) {
  $kk++;
  $FldsList.="$Div";
  $Div=', ';
  $Cnt++;
  if ($kk==4) {
    $FldsList.="<br>\r\n      ";
    $kk=0;
  }
  $FldsList.=$Fld;

}

$S.="\r\n".

'  echo ("<form method=\'post\' action=\''.$TabName.'-UploadXlsx.php\' enctype=\'multipart/form-data\'>
   <table border=\'0\'>
   <tr>
     <td>Upload XLSX file to '.$TabName.' with up to '.$Cnt.' columns:<br>'.$FldsList.'</td>
    </tr>
    <tr>
      <td><input type=\'file\' accept=\'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet\' '.
      'value=\'File name:\' name=\'userfile\'></td>
    </tr> ".
    "<tr>".
     //"<td>Add lines to project: <input type=checkbox name=\'AddToProject\' value=1></td>".
    "</tr>".

    "<tr>
      <td align=right><input type=\'submit\' value=\'Upload\'></td>
    </tr> 
    </table>
 </form>");
  //---------------------------------------------------------------------------------  
?>
</body></html>' ;

fwrite($file,$S);

fclose($file);

//===============================================================
//                        Upload XLS File
//===============================================================

$file = fopen("../Forms/{$TabName}-UploadXlsx.php","w");
fwrite($file,"<?php  
session_start();
include (\"../setup/common.php\");
BeginProc();\r\n");

$S='
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>Upload xlsx to '.$TabName.'</title></head>
<body>
<?php
echo \'<br>User: \' . $_SESSION[\'login\'].\'<br>\';

//print_r($_FILES);
//echo ("<br>");

//print_r($_REQUEST);
//echo ("<br>");

//error_reporting(E_ALL);
//ini_set(\'display_errors\', 1);

include "common_func.php";
require \'../../composer/vendor/autoload.php\';

use PhpOffice\PhpSpreadsheet\IOFactory;


CheckRight1 ($pdo, \'ExtProj.Admin\');

$FileName=\''.$TabName;


$S.='\';

$real_name = "$TmpFilesDir/SIUpl/$FileName.xlsx";

echo ("<br>File $real_name<br>");
ini_set(\'memory_limit\', \'2048M\');

//=============================================================================================
// Copy file to temp dir 
';
$S.='
$size = $_FILES[\'userfile\'][\'size\'];
$name_temp = $_FILES[\'userfile\'][\'tmp_name\'];
$type = $_FILES[\'userfile\'][\'type\'];

$dir = "$TmpFilesDir/SIUpl";
';

$S.='
$sizeStr=\'\';
if ($size> (1024*1024) ) {
  $sizeStr = round($size/1024/1024, 1).\'M\';
  if ($Size > 10000000 ) {
    die ("<br> file size $sizeStr is not Allowed try upload less");
  }
}';

$S.='else{
  if ($size>1024) {
    $sizeStr = round ($size/1024, 1).\'K\';
  }
  else
    $sizeStr = $size.\'b\';
};
echo ("File: $real_name $sizeStr<br>");
';


$S.='
if (file_exists  ( $real_name )) {
  unlink ($real_name);
};

if (move_uploaded_file($name_temp, $real_name)) {
  echo ("Document downloaded $Firm<br>");

   MakeAdminRec ($pdo, $_SESSION[\'login\'], \'UploadXlsx\', $sizeStr, 
                        $FileName, \'Uploaded xlsx file\');
  
}
else {
  die ("<br> Error: Uploading is not ok file:".__'. 'FILE__." line:".__'.'LINE__);
}';

$S.='

//=============================================================================================

// Запоминаем параметры (в какой колонке хранятся)
$ColsArr=array ();

$PkIndx=\'\';

$L=3;

$HeadersArr=array ();

$objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($real_name);

//$sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
//  Get worksheet dimensions


echo("<hr><h4> Upload Xlsx file for ".GetStr($pdo, \''.$TabName.'\')."</h4>");
';

fwrite($file, $S);

$S='
$FldsIndxArr= array (';

$KK=0;
$Div='';
foreach ($Fields as $Fld=>$Arr) {
  $S.= $Div;
  $KK++;
  if ($KK>4) {
    $KK=0;
    $S.="\r\n          ";
  }

  $S.=' \''.$Fld.'\'=>-1';
  $Div=', ';
}
$S.=');

$ColHeader= array (' ;

$KK=0;
$Div='';
foreach ($Fields as $Fld=>$Arr) {
  $S.= $Div;
  $KK++;
  if ($KK>4) {
    $KK=0;
    $S.="\r\n          ";
  }

  $S.=' \''.$Fld.'\'=>\''. addslashes(GetStr($pdo, $Fld)) .'\'';
  $Div=', ';
}
$S.=');

$sheetData = $objPHPExcel->getActiveSheet()->toArray(null, false, false, false);

$Cnt = count($sheetData);
echo ("<br>Rows= $Cnt<br>");

$EnumArr=array('.$EnumTxt.');
$DateArr=array('.$DateTxt.');
';

fwrite($file,$S);

$S = '
$BegLine=7;
$HeadLine=$sheetData[$BegLine];

foreach ( $HeadLine as $Col=> $Val) {
  $FindIdnx=\'\';
  foreach ( $ColHeader as $ColName => $ColDescr ) {
    if ($FindIndx== "") {
      if ($Val == $ColDescr) {
        $FindIndx=$Col;
        $FldsIndxArr[$ColName]=$Col;       
      }
    }
  }
}

$PKArr=array ();
';

$DivPK='';
foreach ($PKFields as $PK) {
  $S.='$PKArr[\''.$PK.'\']= $FldsIndxArr[\''.$PK.'\'];
';
}

$S.='

$Err=0;
foreach ($FldsIndxArr as $Fld=>$Indx) {
  if ($Indx==-1) {
    $Err++;
    echo ("<br> Error $Err: $Fld is not defined ");
  }
}

if ($Err>0) {
  die ("<br> Have $Err errors. Upload stopped ");
}


foreach ($sheetData as $L=> $Arr) {
  echo ("<hr> $L : ");
  print_r($Arr);


  if ($L> $BegLine ) {
    $Vals=array();
';

fwrite($file,$S);

$S='
    foreach ( $FldsIndxArr as $Fld=>$Col ) {
      $Val = addslashes(trim($Arr[$Col]));
      if ($Val==\'#NULL!\') {
        $Val=\'\';
      }


      $Vals[$Fld]=$Val;
    }

  }


}


';

$Ord='';
$LastPkFld='';
foreach ($PKFields as $PK) {
  $i++;
  $S.="if (\$FldsIndxArr['$PK']==-1) {
  die (\"<br> Error: field $PK: {\$ColHeader['$PK']} is not found \");
}
";
};

fwrite($file,$S);

$S="\r\n?>
</body></html>";

fwrite($file,$S);

fclose($file);



//===============================================================
//                        SubLines |  Small List
//===============================================================

$file = fopen("../Forms/{$TabName}SmallList.php","w");
fwrite($file,"<?php\r\n  die();\r\n");
fwrite($file,"  echo(\"<hr><h4>\".GetStr(\$pdo, '{$TabName}').\"</h4>\");\r\n");



  $SF='$'.$PKFields[0];
  
  $Div='';
  $Ord='';
  $LastPkFld='';
  foreach ($PKFields as $PK) {
    $i++;
    $Ord.=$Div.$PK;
    $Div=',';
    $N++;
    $LastPkFld=$PK;
  }

  $query = "select * ".
         "FROM {$TabName} ".
         "where {$PKFields[0]}='$SF' order by $Ord ";  

$S='  $query = "'.$query.'";'."\r\n";
fwrite($file,$S);

$S='  $sql2 = $pdo->query ($query)'."\r\n".
   '            or die("Invalid query:<br>$query<br>" . $pdo->error);'."\r\n";
fwrite($file,$S);

$S="\r\n  echo('<table><tr class=header>');" ;

foreach ($Fields as $Fld=>$Arr) {
  $Pass=0;
  if ($N>1) {
    if ($Fld== $PKFields[0]) {
      $Pass=1;
    }
  }
  
  if ($Pass==0) {      
    $S.="\r\n  echo('<th>'.GetStr(\$pdo, '$Fld').'</th>');";
  }
}

$S.="\r\n".'  $i=0;'."\r\n";
$S.='  while ($dpL = $sql2->fetch_assoc()) {'."\r\n".
    '    $i=NewLine($i);'."\r\n";

foreach ($Fields as $Fld=>$Arr) {
  //echo ("<br><br> -- $Fld: ");
  //print_r( $Arr );

  $Pass=0;
  if ($N>1) {
    if ($Fld== $PKFields[0]) {
      $Pass=1;
    }
  
  }
  if ($Pass==0) {      
    $EndB='</td>';
    if ($Arr['DocParamType']==50) {
      $S.="\r\n".'    echo ("<td align=center>");'. "\r\n";
    }
    else 
    if ($Arr['DocParamType']==20) {
      if ( $Arr['AddParam'] != '') {
        $S.="\r\n".'    echo ("<td align=right>");'. "\r\n";
      }
      else {
        $S.="\r\n".'    echo ("<td align=center>");'. "\r\n";
      }
    }
    else 
    if ($Arr['DocParamType']==30) {
      $S.="\r\n".'    echo ("<td align=center>");'. "\r\n";
    }
    else 
      $S.="\r\n".'    echo ("<td>");'. "\r\n";

    if ($Fld==$LastPkFld) {
      $S.="\r\n    ".'echo("<a href=\''.$TabName.'Card.php?';
      $Div1='';
      foreach ($PKFields as $PK) {
        $S.=$Div1.$PK.'={$dpL[\''.$PK.'\']}';
        $Div1='&';
      }
      $EndB='</a></td>';
      $S.='\'>");'. "\r\n";
    }
    else {
      //$S.="\r\n  ".'echo("';
    }
    
    if ($Arr['DocParamType']==50) {
      $S.= '    echo (GetEnum($pdo, "'.$Arr['AddParam'].'", $dpL[\''.$Fld.'\'])."'.$EndB.'");';    
    }
    else
    if ($Arr['DocParamType']==20) {
      if ( $Arr['AddParam'] != '') {
        $S.='    $OW=number_format($dpL[\''.$Fld.'\'], 2, ".", "\'");'."\r\n";
        $S.='    echo ("$OW'.$EndB.'");'."\r\n";
      }
      else 
        $S.= '    echo ("{$dpL[\''.$Fld.'\']}'.$EndB.'");';    
    }
    else
    if ($Arr['DocParamType']==30) {
      
      $S.='    $Ch="";
      if ($dpL[\''.$Fld.'\']==1) {
        $Ch=" checked ";  
      }
    ';
      $S.='    echo ("<input type=checkbox Name='.$Fld.' value=1 $Ch></td>");
      ';
    }
    else 
      $S.= '    echo ("{$dpL[\''.$Fld.'\']}'.$EndB.'");';    
  }
}
$S.="\r\n    ".
    "\r\n  ".'}'.
    "\r\n  ".'echo("</tr></table>");'.
    "\r\n  ".'echo("<a href=\''.$TabName.'Card.php?New=1&'.
            $PKFields[0].'='.$SF.'\'>".GetStr($pdo, "Add")."</a>");'; 


fwrite($file,$S);

$S="\r\n?>
";

fwrite($file,$S);

fclose($file);


//================================================================
//                              Select -ExtTab-
//================================================================
//echo ("<br>");
//print_r($ExtTab);
echo ("<br>");

foreach ($ExtTab as $TabName=> $I) {

$TabNo='';

$PdoArr = array();
$PdoArr['TabName']= $TabName;

$query = "select \"TabCode\"  from \"AdmTabNames\" ".
         "where (\"TabName\"=:TabName)";
  
$STH = $pdo->prepare($query);
$STH->execute($PdoArr);


if ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $TabNo=$dp2['TabCode'];    
}
else {
  die ("<br> Error: Bad Table Name $TabName ");
}

echo ("<br> Build Select for $TabName ($TabNo) ");

$PdoArr = array();
$PdoArr['TabNo']= $TabNo;

$query = "select * from \"AdmTabFields\" ".
         "where (\"TypeId\"=:TabNo) order by \"Ord\", \"ParamNo\"";
  

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

$Fields=array();
$F2=array();

while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $Fields[$dp2['ParamName']]=$dp2;    
  $F2[$dp2['ParamNo']]=$dp2['ParamName'];    
}

//================================================================
// AdmTabIndxFlds
// TabCode, IndxName, LineNo, FldNo, Ord
// AdmTabIndx
// TabCode, IndxType, IndxName
$query = "select F.* from \"AdmTabIndx\" I, \"AdmTabIndxFlds\" F ".
           "where  (I.\"TabCode\"=:TabNo) and (I.\"TabCode\"=F.\"TabCode\") and ".
           "(I.\"IndxType\"=10) and (F.\"IndxName\"=I.\"IndxName\") ".
           "order by F.\"Ord\", F.\"LineNo\"";
  
//echo ("<br>$query<br>");

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

$PKFields=array();
$Div='';
$PKList='';
$LastPK='';

while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $FldNo = $dp2['FldNo']; 
  $FldName = $F2[$FldNo];


  $PKFields[]= $FldName;
  $PKList.= $Div.$FldName;
  $Div=',';
  $LastPK=$FldName;    
}

//echo ("<br> PkFields: ");
//print_r ($PkFields);
//echo("<br>");

$PdoArr = array();
$PdoArr['Tab2Sel']= "[T:$TabName]";
// Для ссылок на другие таблицы 
$query = "select * from \"AdmTab2Tab\" ".
         "where (\"Tab2\"=:Tab2Sel)";
  
//echo ("<br>$query<br>");
$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

$FOtherTab=array();
$ExtTab=array();

$FromFlds=array();
$ToFlds=array();

$Fld2='';

while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $FOtherTab[$dp2['Id']]=$dp2;
  
  
  $i=0;
  $Str=$dp2['Field2'];
  $Fld2=GetFieldName($pdo, $Str,$i);
  
  $FOtherTab[$dp2['Id']]['Fld2Name']=$Fld2;


  $i=0;
  $Str=$dp2['AddFldsListTo'];
  $Fin=0;
  while (!$Fin) {
    $NewFld=GetFieldName($pdo, $Str,$i);
    if ($NewFld=='') {
      $Fin=1;
    }
    else {
      $ToFlds[$dp2['Id']][]=$NewFld;
    }
  } 
  
  $i=0;
  $Str=$dp2['AddFldsListFrom'];
  $Fin=0;
  while (!$Fin) {
    $NewFld=GetFieldName($pdo, $Str,$i);
    if ($NewFld=='') {
      $Fin=1;
    }
    else {
      $FromFlds[$dp2['Id']][]=$NewFld;
    }
  }
  //----- connections ---------------------
  $i=0;
  $Str=$dp2['AddConnFldTo'];
  $Fin=0;
  
  while (!$Fin) {
    $NewFld=GetFieldName($pdo, $Str,$i);
    if ($NewFld=='') {
      $Fin=1;
    }
    else {
      $ToFldsConn[$dp2['Id']][]=$NewFld;
      $ConnCount++;
    }
  } 
  
  $i=0;
  $Str=$dp2['AddConnFldFrom'];
  $Fin=0;
  while (!$Fin) {
    $NewFld=GetFieldName($pdo, $Str,$i);
    if ($NewFld=='') {
      $Fin=1;
    }
    else {
      $FromFldsConn[$dp2['Id']][]=$NewFld;
    }
  } 

  
  
  $Tab2N=$TabName;

  $FOtherTab[$dp2['Id']]['TabName2']=$Tab2N;
  
  echo ("<br>ExtTab sel: $Tab2N ");

  if ($Tab2N!='') {
    $ExtTab[$Tab2N]=1;
    $HaveRef=1;
  }
   
}

//echo ("<br>FromFlds: ");
//print_r ($FromFlds);
//echo ("<br>ToFlds: ");
//print_r ($ToFlds);
//echo("<br>FOtherTab: ");
//print_r ($FOtherTab);


$file = fopen("../Forms/Select{$TabName}.php","w");

fwrite($file,"<?php\r\n");
fwrite($file,"session_start();\r\n");

$S= '
include ("../setup/common_pg.php");
BeginProc();

$TabName=\''.$TabName.'\';
OutHtmlHeader ($TabName." Select");'."\r\n";
fwrite($file,$S);

$S='
$CurrFile=\'Select'.$TabName.'.php\';
$Frm=\''.$TabName.'\';'.
"\r\n";
fwrite($file,$S);

// ----------------------- Пробуем сделать вид ------------

$ShortList='';
$DivSL='';

$PdoArr = array();
$PdoArr['TabNo']= $TabNo;

$query = "select V.*, \"ParamName\" FROM \"AdmViewField\" V, \"AdmTabFields\" F ".
          " where (V.\"TabNo\"=:TabNo) and  (\"ViewNo\"=1) and (F.\"ParamNo\"=V.\"FieldNo\") and ".
          "(V.\"TabNo\"=F.\"TypeId\") order by V.\"TabNo\", \"Ord\"  ";

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

$DD=0;
while ($dpL = $STH->fetch(PDO::FETCH_ASSOC)) {
  $DD++;
  if ($DD==3) {
    $DD=0;
    $ShortList.= "\r\n        ";
  }

  $ShortList.="$DivSL'{$dpL['ParamName']}'";
  $DivSL=', ';
}

//=============================================================================

$S= '$Fields=array(';
$Div='';

$enS='$enFields= array(';
$enDiv='';

$DD=0;

foreach ($Fields as $Fld=>$Arr) {
  $DD++;
  if ($DD==3) {
    $DD=0;
    $S.= "\r\n        ";
  }
  
  $S.="$Div'$Fld'";
  $Div=',';

  if ($Fields[$Fld]['DocParamType']==50) {
    $SetName= $Fld;
    if ($Fields[$Fld]['AddParam']!='') {
      $SetName= $Fields[$Fld]['AddParam']; 
    } 
    
    $enS.="$enDiv'$Fld'=>'$SetName'";
    $enDiv=', ';
  }
}

$S.=");\r\n";

if ( $ShortList !=''){
  $S= '$Fields=array('. $ShortList. ");\r\n"; 
}

$S.=    $enS.");\r\n".

"CheckRight1 (\$pdo, 'Admin');\r\n\r\n ".
  '$BegPos = $_REQUEST[\'BegPos\']+0;'."\r\n".
  'if ($BegPos==\'\'){'."\r\n".
    '$BegPos=0;'."\r\n".
  '}'."\r\n"."\r\n".

  '$ORD = $_REQUEST[\'ORD\'];'."\r\n".
  'if ($ORD ==\'1\') {'."\r\n".
    '  $ORD = \''.$PKList.'\';
}
else {
  $ORD = \''.$PKList.'\';
}
$ORDS = \' order by  \'; 
if ($ORD !=\'\') {
  $ORDS = \' order by \'.$ORD;
}
else {
  $ORDS = \' order by \'.$ORD;
}

$PdoArr = array();
  
  $WHS = \'\';
  $FullRef=\'?ORD=\'.$ORD;
  foreach ( $Fields as $Fld) {
    $Fltr=$_REQUEST[\'Fltr_\'.$Fld];
    if ($Fltr!=\'\') {
      if ($WHS !=\'\') {
        $WHS.= \' and \';
      }
      
      if ($enFields[$Fld]!=\'\') {
        $PdoArr[$Fld]= $Fltr;
        $WHS.=\'("\'.$Fld."\" = :$Fld)";
      }
      else {
        $WHS.= SetFilter2Fld ($Fld, $Fltr, $PdoArr );
      }
      $FullRef.=\'&Fltr_\'.$Fld.\'=\'.$Fltr ;
    }
  }
'.
"\r\n";
fwrite($file,$S);
//===============================================================
$S='$ElId   = $_REQUEST[\'ElId\'];
$SubStr = $_REQUEST[\'SubStr\'];
$SelId = $_REQUEST[\'SelId\'];
$SelId2 = $_REQUEST[\'SelId2\'];
$SelId3 = $_REQUEST[\'SelId3\'];
$SelId4 = $_REQUEST[\'SelId4\'];
$Par2   = $_REQUEST[\'Par2\'];
';
fwrite($file,$S);

//echo ("<br> -- FromFlds: ");
//print_r($FromFlds);
//echo ("<br>");

foreach ( $FOtherTab as $I => $Arr) {
  $SH='';
  if (!empty($FromFlds[$I])) {
    foreach ( $FromFlds[$I] as $I1 => $FF1) {
      $Fld=$FF1;
      $SH.=", Val$Fld";
    }
  }

$S='if ($SelId== \''.$I.'\') { 
';
if ( $FOtherTab[$I]['CondTab2']!='') {
  $S.='  if ($WHS !=\'\') {
    $WHS.= \' and \';
  }
  $WHS.= ("'.addslashes ($FOtherTab[$I]['CondTab2']). '");
';
}
$S.='
  echo ("<script>".
    "function SetSelect( Val'.$SH.' ) { 
       OW=window.opener;
     
       var elem1 = OW.document.getElementById(\'$ElId\');
       if (elem1) { 
         elem1.value=Val;
       }
';

  if (!empty($FromFlds[$I])) {  
  foreach ( $ToFlds[$I] as $I1 => $FF1) {
    $Fld=$FromFlds[$I][$I1];
    $S.='
       elem1 = OW.document.getElementById(\''.$FF1.'\');
       if (elem1) { 
         elem1.value=Val'.$Fld.';
       }
      ';
  }
  }
$S.='
       window.close();
    }
    </script>");
';
  fwrite($file,$S);

  $S='';
  if (!empty($ToFldsConn[$I])) {  
    $CntFld=2;
    foreach ( $ToFldsConn[$I] as $I1 => $FF1) {
      $S.='  if ($SelId2!="") {
  if ($WHS!="") $WHS=" and ";
    $WHS.= " (\"'.$FF1.'\" = \'$SelId'.$CntFld.'\')"; 
  }  
    ';
      $CntFld++;
    }
  }
  $S.='
}
';
  fwrite($file,$S);
}
//================================================================
$S='  $LN = $_SESSION[\'LPP\'];
  if ($LN==\'\') {
    $LN=20;  
  };

  if ($WHS != \'\') {
    $WHS = \' where \'.$WHS;
  };   

  $query = "select * FROM \"'.$TabName.'\" ".
           "$WHS $ORDS ".AddLimitPos($BegPos, $LN);

  $queryCNT = "select COUNT(*) \"CNT\" FROM \"'.$TabName.'\" ".
              "$WHS ";

  $STH = $pdo->prepare($queryCNT);
  $STH->execute($PdoArr);
  
  $CntLines=0;
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $CntLines=$dp[\'CNT\'];  
  };
  $CurrPage= round($BegPos/$LN)+1;
  $LastPage= floor($CntLines/$LN)+1;

  echo (\'<br><b>\'.GetStr($pdo, \''.$TabName.'\').\' \'.
        GetStr($pdo, \'List\').
        \'</b> \'.$CntLines.\' total lines Page <b>\'.
        $CurrPage.\'</b> from \'. $LastPage) ;
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
   
  echo (\'<form method=post action="\'.$CurrFile.\'"><table><tr>\'.
        "<input type=hidden name=\'ElId\' value=\'$ElId\'>".
        "<input type=hidden name=\'SubStr\' value=\'$SubStr\'>".
        "<input type=hidden name=\'SelId\' value=\'$SelId\'>".
        "<input type=hidden name=\'SelId2\' value=\'$SelId2\'>".
        "<input type=hidden name=\'SelId3\' value=\'$SelId3\'>".
        "<input type=hidden name=\'SelId4\' value=\'$SelId4\'>".
        "<input type=hidden name=\'Par2\' value=\'$Par2\'>");
  $i=0;
  foreach ( $Fields as $Fld) {
    if ($i==3){
      echo(\'</tr><tr>\');
      $i=0;
    }     
    $i++;
    echo("<td align=right>".GetStr($pdo, $Fld).":</td>");

    if ($enFields[$Fld]!=\'\'){
      echo("<td>".EnumSelection($pdo, $enFields[$Fld],\'Fltr_\'.$Fld, $_REQUEST[\'Fltr_\'.$Fld], 1)."</td>");
    }
    else {
      echo("<td><input type=text length=30 size=20 name=\'Fltr_$Fld\' value=\'".
        $_REQUEST[\'Fltr_\'.$Fld]."\'></td>");
    }
  }
  echo ("<td colspan=2><input type=text length=10 name=SubStr value=\'$SubStr\'></td>");
  echo (\'<td><button type="submit">Filter</button></td></tr></table></form>\');
  ';

fwrite($file,$S);

//echo ("<br> OtherTab build select: ");
//print_r($FOtherTab);
//echo ("<br><br>");

$S='//echo (\'<hr><br><form method=post action="'.$TabName.'Card.php">\'.
    //    \'<input type=hidden Name=New VALUE=1>\'.
    //    "<input type=submit Value=\'".GetStr($pdo, \'New\')."\'></form>" );
//--------------------------------------------------------------------------------

echo (\'<table><tr class="header"><th></th>\');

foreach ( $Fields as $Fld) {
  echo("<th>".GetStr($pdo, $Fld)."</th>");
}
echo("</tr>");

$n=0;
while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  
  $classtype="";
  $n++;
  if ($n==2) {
    $n=0;
    $classtype=\' class="even"\';
  }
';
  
  foreach ( $FOtherTab as $I => $Arr) {
    $Fld=$Arr['Fld2Name']; 
    $S.='
  if ($SelId==\''.$I.'\'){
    $Res="\"".addslashes($dp[\''.$Fld.'\'])."\"";
';
    if (!empty($FromFlds[$I])) {    
    foreach ( $FromFlds[$I] as $I1 => $FF1) {
      $Fld=$Arr['FldName'];

      $S.='    $Res.=",\"".addslashes($dp[\''.$FF1.'\'])."\"";
  ';
    }
    } 
    //$FromFlds=array();
    //$ToFlds=array();
$S.='}
';
  }
  $S.='
  echo ("<tr$classtype><td><input type=button value=\'".GetStr($pdo, \'Select\').
       "\' onclick=\'return SetSelect($Res);\'></td>");
';


$S.='
  foreach ( $Fields as $Fld) {
    if ($Fld==\''.$LastPK.'\') {
      echo("<td align=left><a href=\''.$TabName.'Card.php?';

$Div='';
foreach ( $PKFields as $Fld) {
  $S.=$Div.$Fld.'={$dp[\''.$Fld.'\']}';
  $Div='&';  
}

$S.='\'>{$dp[$Fld]}</a></td>");
    }
    else 
    if ($enFields[$Fld]!=\'\'){
      echo("<td>".GetEnum($pdo, $enFields[$Fld], $dp[$Fld])."</td>");
    }
    else {
      echo(\'<td>\'.$dp[$Fld]."</td>");
    }
  }
  echo("</tr>");
};
echo ("</table>");


$PredPage= $BegPos-$LN;
if ($PredPage<0)
  $PredPage = 0; 

$LastPage1= floor($CntLines/$LN) * $LN;
$FullRef.="&SelId=$SelId&ElId=$ElId&SubStr=$SubStr&Par2=$Par2&SelId2=$SelId2&SelId3=$SelId3&SelId4=$SelId4";
echo(\'<table><tr class="header">\');
if ($CurrPage>1) {
  echo(\'<td><a href="\'.$CurrFile.$FullRef.\'&BegPos=0"> << First page </a></td>\' .
       \'<td><a href="\'.$CurrFile.$FullRef.\'&BegPos=\'.$PredPage.\'"> < Pred Page </a></td>\');
};

echo (\'<td>Page \'.$CurrPage.\'</td>\');

if ($CurrPage< $LastPage) {
  echo (\'<td><a href="\'.$CurrFile.$FullRef.\'&BegPos=\'.($BegPos+$LN).\'"> Next Page > > </a></td>\');
};

echo (\'<td><a href="\'.$CurrFile.$FullRef.\'&BegPos=\'.$LastPage1.\'"> Last Page \'.$LastPage.\'>> </a></td>\'.
       \'</tr></table>\');

?>
</body>
</html>
';
fwrite($file,$S);

fclose($file);

echo ("<br><a href='../Forms/Select{$TabName}.php'>Frm Select $TabName</a> ");


}
//--------------------------------------------------------------------------------
//--------------------------------------------------------------------------------

  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

echo ("<br><a href='../Forms/{$TabName}List.php'>Frm List</a>");
?>
</body>
</html>                    