<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();

//ini_set('display_errors', TRUE);

$UserId = $_POST['UserId'];
$ParamType= $_POST['ParamType'];

if (($UserId == '') or ($ParamType=='')) {
  die ('<br> Error: Update error');
};

if ($UserId != $_SESSION['login']) {
  CheckAdmin ();   
};

echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=user_setup.php?UserId='.$UserId.'">
<title>Mnf Label Print</title></head>
<body>');

//include ('common_lab.php');

//print_r($_POST);

echo '<H3>User: ' . $_SESSION['login'].'</h3>';

$Arr=array ();
$Arr['ParamType']= $ParamType;
$Arr['ID']=$UserId;

$PKArr=array ('ParamType', 'ParamNo', 'ID');
$FldsArr=array ('ParamType', 'ParamNo', 'ID', 'Value');




$l=strlen ("PAR_".$ParamType) + 1;
foreach ( $_POST as $K=>$Val) {
  $i = strpos ($K, "PAR_$ParamType");
  if ($i!== false) {
    $ParamNo = substr($K, $l);
    $Arr['ParamNo']=$ParamNo;
    $Arr['Value']=$Val;

    //echo ("<br>$ParamNo $Val<br>");
    $Res=UpdateTable ($pdo, "ParamVal", $FldsArr, $Arr, $PKArr, 1);
    if ($Res=='I') {
      MakeAdminRec ($pdo, $_SESSION['login'], 'USR', $UserId, 
            'UPDPARAM', "Insert param $ParamType, $ParamNo to ".
              $Val);
      echo ("<br>Insert param $ParamNo");
    }
    else 
    if ($Res=='U') {
      MakeAdminRec ($pdo, $_SESSION['login'], 'USR', $UserId, 
            'UPDPARAM', "Update param $ParamType, $ParamNo to ".$Val);
    
      echo ("<br>Update param $ParamNo");
    }
  };
}; 
?>
</body>
</html>
				       