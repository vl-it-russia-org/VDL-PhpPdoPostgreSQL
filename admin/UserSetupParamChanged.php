<?php
session_start();
include ("../setup/common.php");
BeginProc();
//ini_set('display_errors', TRUE);
CheckRight1 ($mysqli, 'ExtProj.Admin');

$UserId = $_POST['UserId'];
$ParamType= $_POST['ParamType'];
if (($UserId == '') or ($ParamType=='')) {
  die ('Update error');
};

function SetParam (&$mysqli, $ParamType, $ParamNo, $ID, $ParamVal) {
  $CurrVal='';
  //echo ("<br>ParamType:$ParamType, ParamNo: $ParamNo, ID: $ID, ParamVal: $ParamVal");
  
  $query = "select Value from ParamVal where ".
           "ParamType='$ParamType' and ParamNo='".$ParamNo."' and ID='$ID'";
  
  $sql3=$mysqli->query($query)
                  or die ("Invalid query:<br>$query<br>Line:".__LINE__." ".$mysqli->error);  
  if ($dp3 = $sql3->fetch_object()) {
    $CurrVal=$dp3->Value;    
  }
  else {
    MakeAdminRec ($mysqli, $_SESSION['login'], 'USR', $UserId, 
            'UPDPARAM', "Insert param $ParamType, $ParamNo to ".
              $ParamVal);
    
    $query = "insert into ParamVal ".
            "( ParamType, ParamNo, ID, Value) values ".
            "('$ParamType','".$ParamNo."','$ID', '$ParamVal')";
  
    
    $sql3=$mysqli->query($query)
                  or die ("Invalid query:<br>$query<br>Line:".__LINE__." ".$mysqli->error);    
    echo ("<br>Insert param $ParamNo");

  };
  
  if ( $CurrVal != $ParamVal) {
    MakeAdminRec ($mysqli, $_SESSION['login'], 'USR', $UserId, 
            'UPDPARAM', "Update param $ParamType, $ParamNo to ".
              $ParamVal);
    
    $query = "update ParamVal set Value='$ParamVal' where ".
             "ParamType='$ParamType' and ParamNo='".$ParamNo."' and ID='$ID'";
  
    
    $sql3=$mysqli->query($query)
                  or die ("Invalid query:<br>$query<br>Line:".__LINE__." ".$mysqli->error);    
    echo ("<br>Update param $ParamNo");
  }

} ;     
    
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<META HTTP-EQUIV="REFRESH" CONTENT="2;URL=user_setup.php?UserId='.$UserId.'">
<title>Mnf Label Print</title></head>
<body>');

//print_r($_POST);

echo '<H3>User: ' . $_SESSION['login'].'</h3>';

$l=strlen ("PAR_".$ParamType) + 1;
foreach ( $_POST as $K=>$Val) {
  $i = strpos ($K, "PAR_$ParamType");
  if ($i!== false) {
    $ParamNo = substr($K, $l);
    //echo ("<br>$ParamNo $Val<br>");
    SetParam ($mysqli, $ParamType, $ParamNo, $UserId, $Val);
  };     
}; 
?>
</body>
</html>
				       