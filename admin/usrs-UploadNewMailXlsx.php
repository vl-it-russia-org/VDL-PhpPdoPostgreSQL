<?php  
session_start();
include ("../setup/common.php");
BeginProc();

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>Upload xlsx to usrs</title></head>
<body>
<?php
echo '<br>User: ' . $_SESSION['login'].'<br>';

//print_r($_FILES);
//echo ("<br>");

//print_r($_REQUEST);
//echo ("<br>");

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include "common_func.php";
require '../../composer/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;


CheckRight1 ($mysqli, 'ExtProj.Admin');

$FileName='usrs';

$real_name = "$TmpFilesDir/SIUpl/$FileName.xlsx";

echo ("<br>File $real_name<br>");
ini_set('memory_limit', '2048M');

//=============================================================================================
// Copy file to temp dir 

$size = $_FILES['userfile']['size'];
$name_temp = $_FILES['userfile']['tmp_name'];
$type = $_FILES['userfile']['type'];

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

if (move_uploaded_file($name_temp, $real_name)) {
  echo ("Document downloaded $Firm<br>");

   MakeAdminRec ($mysqli, $_SESSION['login'], 'UploadXlsx', $sizeStr, 
                        $FileName, 'Uploaded xlsx file');
  
}
else {
  die ("<br> Error: Uploading is not ok file:".__FILE__." line:".__LINE__);
}

//=============================================================================================

// Запоминаем параметры (в какой колонке хранятся)
$ColsArr=array ();

$PkIndx='';

$L=3;

$HeadersArr=array ();

$objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($real_name);

$sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
//  Get worksheet dimensions


echo("<hr><h4> Upload Xlsx file for ".GetStr($mysqli, 'usrs')."</h4>");

$FldsIndxArr= array ( 'usr_id'=>-1,  'usr_pwd'=>-1,  'description'=>-1,  'admin'=>-1, 
           'email'=>-1,  'phone'=>-1,  'passwd_duedate'=>-1,  'new_passwd'=>-1,  'passwd_last_change'=>-1, 
           'SFUser'=>-1,  'Blocked'=>-1,  'WebCookie'=>-1,  'Position'=>-1,  'Department'=>-1, 
           'Company'=>-1,  'FirstName'=>-1,  'LastName'=>-1,  'PatronymicName__c'=>-1,  'PwdCoded'=>-1);

$ColHeader= array ( 'usr_id'=>'Код пользователя',  'usr_pwd'=>'<a href=\'../FormsI/TranslateFrm.php?Enum=usr_pwd\' target=Translate>_</a>usr_pwd',  'description'=>'Описание',  'admin'=>'Admin', 
           'email'=>'Email',  'phone'=>'Телефон',  'passwd_duedate'=>'<a href=\'../FormsI/TranslateFrm.php?Enum=passwd_duedate\' target=Translate>_</a>passwd_duedate',  'new_passwd'=>'<a href=\'../FormsI/TranslateFrm.php?Enum=new_passwd\' target=Translate>_</a>new_passwd',  'passwd_last_change'=>'Последнее изменение пароля', 
           'SFUser'=>'Пользователь SF',  'Blocked'=>'Заблокирован',  'WebCookie'=>'Веб-куки',  'Position'=>'Должность',  'Department'=>'Департамент', 
           'Company'=>'Компания',  'FirstName'=>'Имя',  'LastName'=>'Фамилия',  'PatronymicName__c'=>'Отчество',  'PwdCoded'=>'<a href=\'../FormsI/TranslateFrm.php?Enum=PwdCoded\' target=Translate>_</a>PwdCoded');

$sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

$Cnt = count($sheetData);
echo ("<br>Rows= $Cnt<br>");

$L=3;

$rowData = $sheetData[$L];
foreach ( $rowData as $Indx=> $Val) {
  //echo ("<br> $Indx: $Val");
    
}


$UpdCnt=0;
$Lines=0;
$Warr=0;
foreach ($sheetData as $R => $Arr) {
  
  if ($R>3) {
    
    //echo ("<br> $R: ");
    //print_r( $Arr);

    $UsrId= addslashes($Arr['A']);
    if ($UsrId != '') {
    $Lines++;
  
    // usrs
    // usr_id, usr_pwd, description, admin, 
    // email, phone, passwd_duedate, new_passwd, passwd_last_change, 
    // SFUser, Blocked, WebCookie, Position, Department, 
    // Company, FirstName, LastName, PatronymicName__c, PwdCoded
    $query = "select * from usrs ". 
             "where (usr_id = '$UsrId')"; 

    $sql2 = $mysqli->query ($query) 
              or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
    if ($dp2 = $sql2->fetch_assoc()) {
      
      $NewMail = addslashes ($Arr['C']);
      $OldMail = addslashes ($Arr['B']);
      
      if ($dp2['email'] != $Arr['B']) {
        
        if ($dp2['email']== $Arr['C']) {
          // Already done    
        }
        else {
          echo ("<br> Error: line $R, user $UsrId : Old Email is not match Have: {$dp2['email']} in file {$Arr['B']}");  
          $Warr++;
        }
      
      }
      else {


        // usrs
        // usr_id, usr_pwd, description, admin, 
        // email, phone, passwd_duedate, new_passwd, passwd_last_change, 
        // SFUser, Blocked, WebCookie, Position, Department, 
        // Company, FirstName, LastName, PatronymicName__c, PwdCoded
        $query = "select * from usrs ". 
                 "where (usr_id != '$UsrId') and (email = '$NewMail')"; 

        $sql22 = $mysqli->query ($query) 
                  or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);

        
        if ($dp22 = $sql22->fetch_assoc()) {
          $Warr++;

          
          echo (" <br> Warring: user {$dp22['usr_id']} also have email $NewMail as $UsrId");
        }
        
        MakeAdminRec ($mysqli, $_SESSION['login'], 'USR', $UsrId, 
                          'ChMail', 'Mail changed '.$OldMail.'->'.$NewMail);
      
        // usrs
        // usr_id, usr_pwd, description, admin, 
        // email, phone, passwd_duedate, new_passwd, passwd_last_change, 
        // SFUser, Blocked, WebCookie, Position, Department, 
        // Company, FirstName, LastName, PatronymicName__c, PwdCoded
        $query = "update usrs set email='$NewMail' ". 
                 "where (usr_id = '$UsrId')"; 

        $sql27 = $mysqli->query ($query) 
                  or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
      
        echo ("<br> $UsrId: $OldMail -> $NewMail changed ");
        $UpdCnt++;
      }
    
      // Move rights

      // RespPersons
      // ObjId, Param1, enContactType, ContactId, 
      // Description, Rank
      $query = "update RespPersons set ContactId='$NewMail' ". 
               "where (ContactId='$OldMail' ) "; 

      $sql29 = $mysqli->query ($query) 
                or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);


    }
    else {
      die ( "<br> Error: Line $R $UserId is not found");
    }
    
    
    }
  
  
  
  }


}


echo ("<hr>Total $Lines lines: $UpdCnt changed, $Warr warring and errors");



?>
</body></html>