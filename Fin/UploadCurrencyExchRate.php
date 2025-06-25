<?php
// $fo = fopen("kurs/all.txt", "w");
include ("../setup/common_pg.php");

$days = 45;

if (!empty ($_REQUEST['Days'])) {
  $days = $_REQUEST['Days'];
}

//http://www.cbr.ru/scripts/XML_daily.asp?date_req=02/03/2020

function GetNextVal (&$Buf, $Clause, &$Beg) {
  $len=strlen ($Clause);
  $Res='';
  $i= strpos($Buf, "<$Clause", $Beg);
  if ($i===false) {
    
  }
  else {
    $end1 = strpos($Buf, ">", $i);
    $end2 = strpos($Buf, "</$Clause>", $end1);

    $Res = substr ($Buf, $end1+1, $end2-1-$end1);
    $Beg=$end2+$len;  
  }
  return $Res;
}



$ResArr=array();

$CurrArr= array (1=>'USD', 5=>'EUR', 20=>'KZT', 30=>'UAH', 100=>'CNY');

$PdoArr = array();
$PdoArr['CCode']= $CCode;

// CurrencyExchRate
$FldsArr=array("CurrencyCode", "StartDate", "Multy", "Rate", "FullRate");
$PKArr=array("CurrencyCode", "StartDate");


$DT=date ('Y-m-d');
while ($days>=0) {
  
  $NewDate = date('Y-m-d', strtotime("-$days day", strtotime($DT)));

  echo ("<br>$days : $NewDate");
  $ChkDate= substr($NewDate, 8, 2).'/'.substr($NewDate, 5, 2).'/'.substr($NewDate, 0, 4);
  echo (" $ChkDate ");

  $FL="http://www.cbr.ru/scripts/XML_daily.asp?date_req=$ChkDate";
  echo (" $FL ");
  
  $Buf = file_get_contents($FL);
  $days--;

  $j=0;
  $S=1;
  $Date = $NewDate;


  while ($S==1) {
    
    $Res=GetNextVal ($Buf, 'Valute', $j);
    if ($Res=='') {
      $S=0;
    }
    else {
      //echo ("\r\n<br> $j: $Res <br>\r\n");
    

      foreach ($CurrArr as $CCode=>$Currency) {
        $ii=0;
        $CurrCode=GetNextVal ($Res, 'CharCode', $ii);
        if ( $CurrCode == $Currency) {
          //echo ("\r\n<br>  $CurrCode  $j: $Res <br>\r\n"); 
        
          $Curr=$CurrCode;
          $ii=0;
          $Multi= GetNextVal ($Res, 'Nominal', $ii);
          $ii=0;
          $kurs = GetNextVal ($Res, 'Value', $ii);
          
          $kurs = str_replace ( ',', '.', $kurs);

          echo ("$Curr;$Date;$kurs;$Multi\r\n");
          $ResArr[$Curr][$Date]['Multi']=$Multi;
          $ResArr[$Curr][$Date]['Kurs']=$kurs;
      
          //================================================================================
          // CurrencyExchRate
          //$FldsArr=array("CurrencyCode", "StartDate", "Multy", "Rate", "FullRate");
          //$PKArr=array("CurrencyCode", "StartDate");

          // CurrencyExchRate
          // CurrencyCode, StartDate, Multy, Rate, FullRate
          $Arr['CurrencyCode']= $CCode;
          $Arr['StartDate']= $Date;
          $Arr['Multy']= $Multi;
          $Arr['Rate']= $kurs;
          $Arr['FullRate']= ROUND($kurs/$Multi, 6);

          $Res=UpdateTable ($pdo, "CurrencyExchRate", $FldsArr, $Arr, $PKArr, 1);
          if ($Res=='I') {
            echo (" inserted ");
          }
          if ($Res=='U') {
            echo (" updated ");
          }

          echo ("<br>");
        }
        //====================================================================================      
      }
      //array (1=>'USD', 5=>'EUR', 20=>'KZT', 30=>'UAH');
    }
  }

  echo ("\r\n ------------------------------------- \r\n");
  //----------------------------------------------------
}


$ResTxt='';
foreach ($ResArr as $Curr => $Arr1) {
  foreach ($Arr1 as $Date => $Arr2) {
    $kurs=$Arr2['Kurs'];
    $Multi=$Arr2['Multi'];

    $ResTxt.="$Curr;$Date;$kurs;$Multi\r\n";  
  }
}
  
  
file_put_contents ("all.txt" , $ResTxt);

?>