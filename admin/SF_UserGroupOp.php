<?php
session_start();

include "../setup/common.php";
BeginProc();
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<meta http-equiv="Content-Language" content="ru">
<title>SF_User Card</title></head>
<body>
<?php
//CheckLogin1 ();
CheckRight1 ($mysqli, "ExtProj.Admin");

//print_r($_REQUEST);
//die();


if (is_array($_REQUEST['Chk'])) { 
  $Res='';
  $Div='';
  foreach ( $_REQUEST['Chk'] as $Indx=> $Val) {
    
      $PKValArr=json_decode(base64_decode($Val), true);
      
    $V="'".addslashes($PKValArr['UnId'])."'";
      $Res.="$Div($V)";
      $Div=",";
    }
    if ($_REQUEST['OpType']== GetStr($mysqli, 'Delete')) {
      $query = "delete from SF_User ".
               " WHERE ( (UnId) in ($Res) )";
      $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error());
      
    }  
    
}
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=SF_UserList.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');
?>
</body>
</html>