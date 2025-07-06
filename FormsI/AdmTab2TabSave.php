<?php
session_start();

include ("../setup/common_pg.php");

CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

//print_r($_REQUEST);

$FldNames=array('TabName','FldName','LineNo','TabCond','Tab2','Field2',
                'CondTab2','AddFldsListTo','AddFldsListFrom', 
                'AddConnFldTo','AddConnFldFrom', 'SelectViewName');

$New=$_REQUEST['New'];
$TabName=$_REQUEST['TabName'];
if ($TabName==''){ die ("<br> Error:  Empty $TabName");}
$FldName=$_REQUEST['FldName'];
if ($FldName==''){ die ("<br> Error:  Empty $FldName");}
$LineNo=$_REQUEST['LineNo'];
if ($LineNo==''){ 
  if ($New!=1)
    die ("<br> Error:  Empty $LineNo");
}

$dp=array();
$PdoArr = array();
$PdoArr['TabName']= $TabName;
$PdoArr['FldName']= $FldName;
try {

if (!empty ($LineNo)) {  
  $PdoArr['LineNo']= $LineNo;  
  $query = "select * FROM \"AdmTab2Tab\" ".
           "WHERE (\"TabName\"=:TabName) AND (\"FldName\"=:FldName) AND (\"LineNo\"=:LineNo)";
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);  
  
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    if ($New==1){
      echo ("<br>");
      print_r($dp);
      die ("<br> Error: Already have record ");
    }
  
  }
}
  
  if ($New==1){
    
    $query = "select MAX(\"LineNo\") \"MX\" FROM \"AdmTab2Tab\" ".
             "WHERE (\"TabName\"=:TabName) AND (\"FldName\"=:FldName)";
    
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
      
    $LN=0;
    if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
      $LN=$dp['MX'];
    }
    $LN++;
    $LineNo=$LN;
    $_REQUEST['LineNo']=$LN;

    $q='insert into "AdmTab2Tab"(';
    $S1='';
    $S2='';
    $Div='';

    foreach ($FldNames as $F) {
      $PdoArr[$F]= $_REQUEST[$F];

      $S1.=$Div.'"'.$F.'"';
      $S2.="$Div:$F";
      $Div=', ';
    }
    
    $query=$q.$S1.') values ('.$S2.')';
    
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
  }
  else {
    $q='update "AdmTab2Tab" set ';
    $S1='';
    $Div='';

    foreach ($FldNames as $F) {
      $V1=$_REQUEST[$F];
      $V=$_REQUEST[$F];
      
      //echo ("<br>$F: $V1 / {$dp[$F]} ".($V1==$dp[$F]));
      
      if ( $V1 != $dp[$F]) {
        $PdoArr[$F]= $_REQUEST[$F];

        $S1.=$Div.'"'.$F."\"=:$F";
        $Div=', ';
      }
    }  

    if ( $S1 != '' ) {
      $q.=$S1.' WHERE ';
      
      $S1='';

      // $V=addslashes ($_REQUEST['OldTabName']);
      $S1.="(\"TabName\"=:TabName)";
  
      // $V=addslashes ($_REQUEST['OldFldName']);
      $S1.=" and (\"FldName\"=:FldName)";
  
      //$V=addslashes ($_REQUEST['OldLineNo']);
      $S1.=" and (\"LineNo\"=:LineNo)";
  
      $query=$q.$S1;

      //echo ("<br>$q<br>");
      $STH = $pdo->prepare($query);
      $STH->execute($PdoArr);
    }
  }
$LNK='';

  $V=$_REQUEST['TabName'];
  $LNK.="TabName=$V";
  
  $V=$_REQUEST['FldName'];
  $LNK.="&FldName=$V";
  
  $V=$_REQUEST['LineNo'];
  $LNK.="&LineNo=$V";

 }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=AdmTab2TabCard.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');
?>
</body>
</html>