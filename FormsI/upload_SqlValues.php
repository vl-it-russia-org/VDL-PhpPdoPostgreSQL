<?php
session_start();

include ("../setup/common_pg.php");

?>
<html>
<head><title>Uploaded SQL values file</title></head>
<body>
<?php

CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

echo ("<br>");
print_r( $_FILES);
echo ("<br><br>");

$size = $_FILES['userfile']['size'];
$name_temp = $_FILES['userfile']['tmp_name'];
$type = $_FILES['userfile']['type'];

$Firm='SqlValues';

$real_name = "$TmpFilesDir/$Firm.txt";

$sizeStr='';
if ($size> (1024*1024) ) {
  $sizeStr = round($size/1024/1024, 1).'M';
}else{
  if ($size>1024) {
    $sizeStr = round ($size/1024, 1).'K';
  }
  else
    $sizeStr = $size.'b';
};

if ($size==0) {
  die ("<br> Error: file size = 0");
}

echo ("File: $real_name $sizeStr<br>");


if (file_exists  ( $real_name )) {
  unlink ($real_name);
};

if (move_uploaded_file($name_temp, $real_name)) {
  echo ("Document downloaded $Firm<br>");

   MakeAdminRec ($pdo, $_SESSION['login'], $Firm, $sizeStr, 
                        $Firm, "Uploaded $Firm file");
  
   //============================================================


$Firm='SqlValues';

$InsFldCnt=0;
$InsTabCnt=0;


$real_name = "$TmpFilesDir/$Firm.txt";

if (!file_exists  ( $real_name )) {
  die ("<br> Error: File is not exists $real_name ") ;
};

// AdmTabNames
// TabName, TabDescription, TabCode, TabEditable

$lines = file($real_name);
$count = 0;

$Sql='';
$Run=0;
foreach($lines as $line) {
  $count++;

  echo "<br>".str_pad($count, 5, 0, STR_PAD_LEFT).": ".$line;
  $TL = trim ($line);

  if ($TL=='') {
    if ($Sql!= '') {
      $Run++;
      echo ("<br><hr><br> Run sql:  $Run");
      
      try {
      $query = $Sql;
      $STH = $pdo->prepare($query);
      $STH->execute();
      
      
      }
      catch (PDOException $e) {
        echo ("<hr> Line ".__LINE__."<br>");
        echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
        print_r($PdoArr);	
        die ("<br> Error: ".$e->getMessage());
      }
      

      $Sql='';
    }
  }
  else {
    $Sql.= "\r\n$line";
  }


}

if ($Sql!= '') {
  $Run++;
  echo ("<br> Run sql:  $Run");
  try {
    $query =$Sql ; 
    $STH = $pdo->prepare($query);
    $STH->execute();
  
  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }
  
  $Sql='';
}



   // ============================================================
};

?>
</body>
</html>				       