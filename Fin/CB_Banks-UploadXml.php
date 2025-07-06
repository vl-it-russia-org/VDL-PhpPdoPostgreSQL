<?php  
session_start();
include ("../setup/common_pg.php");
include ("../XmlRead.php");
BeginProc();

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>Upload XML to CB_Banks</title></head>
<body>
<?php
echo '<br>User: ' . $_SESSION['login'].'<br>';

print_r($_FILES);
echo ("<br>");

//print_r($_REQUEST);
//echo ("<br>");

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

CheckRight1 ($pdo, 'Fin');

//================================================================
function ReadBankInfo (&$pdo, &$i, &$Contents) {
  // \"CB_Banks\"
  // \"BIK\", \"BankName\", \"BankTransitAcc\", \"City\"
  //-------------------------------
  // (\"BIK\"=:BIK)
  // $PdoArr["BIK"]= $BIK;

  $ArrAll = array ('BIK', 'BankName', 'BankTransitAcc', 'City');
  $PKArr = array ('BIK');

  
  $BankInfo = GetXMLVal ( $Contents, "BICDirectoryEntry", $i);
  $Res='';
  $AccArr=array();
  if ( $BankInfo!= '') {
    echo ("\r\n<hr>BankInfo: \r\n<hr><br>");  
    $Arr=array();
    $BegPos=0;
    ReadValsArr($BankInfo, $BegPos, $Arr);
    $PI=0;
    $PartInfo = GetXMLVal ( $BankInfo, "ParticipantInfo", $PI);
    if ($PartInfo!='') {
      $BegPos=0;
      ReadValsArr($PartInfo, $BegPos, $Arr);
      CleanupArr($Arr);
      echo("\r\n<br>Arr: ");
      //print_r($Arr);

      foreach ($Arr as $Name=>$Val) {
        echo ("<br>|$Name| = |$Val|");
      }

      $BIC = $Arr['BIC'];
      $LastDig = mb_substr($BIC, -3);
      echo ("<br>|LastDig| = |$LastDig|");

      
      $More=1;
      while ($More==1) {
        $AccInfo = GetXMLVal ( $BankInfo, "Accounts", $PI);
        if ($AccInfo == '') {
          $More=0;
        }
        else {
          $BegPos=0;
          $AccArr=array();
          ReadValsArr($AccInfo, $BegPos, $AccArr);
          CleanupArr($AccArr);
          echo ("<br> AccInfo: ");
          print_r($AccArr);
          echo ("<br>");
          $AccLD = mb_substr($AccArr['Account'], -3);
          if ($AccLD == $LastDig) {
            $More=2;
          }
        }
      }
      if ($More==2) {
        echo ("\r\n<br> Transit account = ".$AccArr['Account']."<br>");
        
        $UpdArr = array ();
        
        $UpdArr['BIK']=$Arr['BIC'];
        $UpdArr['BankName']=$Arr['NameP'];
        $UpdArr['BankTransitAcc']=$AccArr['Account'];
        $UpdArr['City']=$Arr['Tnp'].'. '.$Arr['Nnp'];

        $Res1=UpdateTable ($pdo, 'CB_Banks', $ArrAll, $UpdArr, $PKArr, 1);
        if ($Res1=='I') {
          echo (" - Inserted... ");
        }
        else 
        if ($Res1=='U') {
          echo (" - Updated... ");
        }
      }

      echo("\r\n<br>");




    }   
    
    
    $Res='Have';
  }
  return $Res;
} 
//================================================================

$FileName='CB_Banks';

$real_name = "$TmpFilesDir/SIUpl/$FileName.xml";

echo ("<br>File $real_name<br>");
ini_set('memory_limit', '2048M');

//=============================================================================================
// Copy file to temp dir 

$size = $_FILES['userfile']['size'];
$name_temp = $_FILES['userfile']['tmp_name'];
$type = $_FILES['userfile']['type'];

if ($type != 'text/xml') {
  die ("<br> Error: type XML expected ");
} 


$dir = "$TmpFilesDir/SIUpl";

$sizeStr='';
if ($size> (1024*1024) ) {
  $sizeStr = round($size/1024/1024, 1).'M';
  if ($Size > 10000000 ) {
    die ("<br> file size $sizeStr is not Allowed try upload less");
  }
}else{
  if ($size>1024) {
    $sizeStr = round ($size/1024, 1).'K';
  }
  else
    $sizeStr = $size.'b';
};

echo ("File: $real_name $sizeStr<br>");

if (file_exists  ( $real_name )) {
  unlink ($real_name);
};

//die ();

if (move_uploaded_file($name_temp, $real_name)) {
  echo ("Document downloaded $Firm<br>");

  MakeAdminRec ($pdo, $_SESSION['login'], 'UploadXlsx', $sizeStr, 
                        $FileName, 'Uploaded xlsx file');
  
  $handle = fopen($real_name, "rb");
  if (FALSE === $handle) {
      exit("Failed to open stream to URL");
  }

  $Contents = '';
  $i=0;
  $j=0;
  $RL=0;
  
  while (!feof($handle)) {
    $Buf= fread($handle, 8192);
    $RL++;
    
    $Len = mb_strlen ($Contents);
    echo ("<br>Len=$Len ReadLines=$RL<br>");
    $Last = "";
    
    if ($i<$Len) {
      $Last = mb_substr ($Contents, $i);
    }

    $Contents=$Last.ToUtf ($Buf);
    $Len = mb_strlen ($Contents);
    $MaxLen= $Len-1000;
    $i=0;

    while ($i< $MaxLen) {
      $j++;
      echo ("<br> j=$j: Len=$Len : I=$i <br>");
      
      $Res=ReadBankInfo ($pdo, $i,$Contents);
      if ($Res=='') {
        $i=$MaxLen;
      }
      echo ("<br> j=$j: Len=$Len : I=$i <br>");
    }
  }
  fclose($handle);  
  
  $Len = mb_strlen ($Contents);
  while ($i< $Len) {
    $Res=ReadBankInfo ($pdo, $i,$Contents);
      $j++;
      echo ("<br> j=$j: Len=$Len : I=$i <br>");
    if ($Res=='') {
      $i=$Len;
    }
  }

}
else {
  die ("<br> Error: Uploading is not ok file:".__FILE__." line:".__LINE__);
}

//=============================================================================================

?>
</body></html>