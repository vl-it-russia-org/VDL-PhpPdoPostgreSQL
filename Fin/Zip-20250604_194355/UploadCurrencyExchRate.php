<?php
// $fo = fopen("kurs/all.txt", "w");
include ("../setup/common.php");

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
          // CurrencyCode, StartDate, Multy, Rate, FullRate
          $query = "select * ". 
                  "FROM CurrencyExchRate where CurrencyCode='$CCode' and StartDate='$Date'";
       
          $sql2 = $mysqli->query($query)
                       or die("Invalid query:<br>$query<br>" . $mysqli->error);

          if ($dp = $sql2->fetch_assoc()) {
            
            $Full= $dp['FullRate'];
            $Full1 =$kurs/$Multi;
            
            echo ("  $Full  $Full1 ");
             
            if ( ($Full!= $Full1) or($dp['Multy']!= $Multi) or ($dp['Rate']!= $kurs)) {
            
              $query = "update CurrencyExchRate SET Rate='$kurs', Multy='$Multi', FullRate='$Full1' ".
                       "where CurrencyCode='$CCode' and StartDate='$Date'";
       
              //echo ("<br>$query<br>");

              $sql2 = $mysqli->query($query)
                         or die("Invalid query:<br>$query<br>" . $mysqli->error);
             echo (" updated ");
            } 
          }
          else {
            $Full1 =$kurs/$Multi;
            $query = "insert into CurrencyExchRate (CurrencyCode, StartDate, Rate, Multy, FullRate) ".
                     " values ('$CCode','$Date','$kurs','$Multi', '$Full1')";
       
            $sql2 = $mysqli->query($query)
                         or die("Invalid query:<br>$query<br>" . $mysqli->error);
            
            echo (" inserted ");
            
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
  
  
file_put_contents ("../../legrand/msg_ins/ftpfiles/kurs/all.txt" , $ResTxt);

?>