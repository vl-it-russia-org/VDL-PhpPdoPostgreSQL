<?php
session_start();

include "../setup/common_pg.php";
BeginProc();
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<meta http-equiv="Content-Language" content="ru">
<title>Countries Card</title></head>
<body>
<?php
//CheckLogin1 ();
CheckRight1 ($pdo, "Admin");

//print_r($_REQUEST);
//die();


if (is_array($_REQUEST['Chk'])) { 
  $Res='';
  $Div='';
  foreach ( $_REQUEST['Chk'] as $Indx=> $Val) {
    
      $PKValArr=json_decode(base64_decode($Val), true);
      
    $V="'".addslashes($PKValArr['Code2'])."'";
      $Res.="$Div($V)";
      $Div=",";
    }
    if ($_REQUEST['OpType']== GetStr($pdo, 'Delete')) {
      $query = "delete from Countries ".
               " WHERE ( (Code2) in ($Res) )";
      $sql2 = $pdo->query ($query)
                 or die("Invalid query:<br>$query<br>" . $pdo->error());
      
    }  
    
}
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=CountriesList.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');
?>
</body>
</html>