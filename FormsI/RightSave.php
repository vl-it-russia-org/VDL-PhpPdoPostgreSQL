<?php
session_start();
include ("../setup/common_pg.php");
BeginProc();

CheckRight1 ($pdo, 'RIGHT_EDIT');

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=RightList.php">
<title>Save rights</title></head>
<body>
<?php
echo '<br>User: ' . $_SESSION['login'];

//print_r ($_REQUEST);
//die ();

$TabName='Rights';
$FldsArr= array ('RightType', 'RightDescription', 'HelpLink', 'NeedLocation', 'EnumRight', 
                 'HaveTable', 'TabName', 'FieldName', 'FldDescription');

$Arr=array ();

foreach ($FldsArr as $F) {
  $Arr[$F]='';
  if (!empty ($_REQUEST[$F]) ) {
    $Arr[$F]=$_REQUEST[$F];
  }
}

$NumArr= array ('NeedLocation', 'HaveTable');
foreach ($NumArr as $F) {
  if (empty($Arr[$F])) {
    $Arr[$F]=0;
  }
}

$PKArr=array ('RightType');

$Res = UpdateTable ($pdo, $TabName, $FldsArr, $Arr, $PKArr, 1);

echo ("<br> Res=$Res");
//MakeAdminRec ($pdo, '', 'RM', $RightType, '', 'Right created');

?>
</body>
</html>
				       