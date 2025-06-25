<?php
session_start();

include ("../setup/common_pg.php");


BeginProc();
CheckRight1 ($pdo, 'Admin');

include "../js_SelAll.js";

$FldNames=array('TypeId','ParamNo','ParamName','NeedSeria','DocParamType',
                'NeedBrand','Ord','AddParam','DocParamsUOM','CalcFormula',
                'AutoInc','Description','BinCollation','ShortInfo');


$Company=$_REQUEST['Company'];
$SubStr=$_REQUEST['SubStr'];

$FoundCompany=0;
if ($Company=='LVL') {
  $FoundCompany=1;
}


echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="style.css">'.
'<title>FileStruct</title></head>
<body>');


$real_extract = '/var';

//if (!file_exists($real_extract.'/done')) {
//  mkdir( $real_extract.'/done');
//}

$Dir1='';
if (!empty ($_REQUEST['Dir1'])) {
  $Dir1=$_REQUEST['Dir1'];  
};

$Dir2='';
if (!empty ($_REQUEST['Dir2'])) {
  $Dir2=$_REQUEST['Dir2'];  
};


$AddPack='';
if (!empty ($_REQUEST['AddPack'])) {
  $AddPack=$_REQUEST['AddPack'];

};
$SelectArr=array();
$SelectDescr='';
if ($AddPack != '') {
  $SelectArr= json_decode ( base64_decode($AddPack), 1) ;
  //echo ("<br> SelectArr: ");
  //print_r($SelectArr);
  if (  $SelectArr['FName']!= '') {
    $SelectDescr= "FileName filter: <b>".$SelectArr['FName']."</b> ";  
  }
  
  if (  $SelectArr['FInFile']!= '') {
    $SelectDescr.= "Text in file: <b>".$SelectArr['FInFile']."</b> ";  
  }
}
//&AddPack=".base64_encode(json_encode($FilterArr))
//echo ("<br>SelectDescr:$SelectDescr");

if ($Dir1=='') {
  $Dir1= $real_extract;
}

if ($Dir2=='') {
  $Dir2= $real_extract;
}


$Arr= scandir ( $Dir1 );

echo ("<table><tr valign=top><td>" );
echo ("<b>$Dir1</b> <a href='EdiDispatchedFiles.php?Dir1=$Dir1&Dir2=$Dir1'>Set same</a>");
//-------------------------------------------------------------
// Fav Dirs:
$UserId=$_SESSION['login'];


$HaveFavDirs=0;
$query = "select * FROM \"FavPlaces\" where \"UserId\"=:UserId order by \"UserId\",\"Ord\" ";

$PdoArr = array();
$PdoArr['UserId']=$UserId;


try {
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);




echo('<table><tr>');
while ($dpL = $STH->fetch(PDO::FETCH_ASSOC)) {
  $HaveFavDirs=1;
  echo ("<td align=center>".
        "<a href='EdiDispatchedFiles.php?Dir1={$dpL['DirName']}&Dir2=$Dir2' ".
        "title='{$dpL['Tip']}'>{$dpL['ShortName']}</a></td>");
}
echo("<td><a href='FavPlacesAdd.php?New=1&Dir=$Dir1&Dir2=$Dir2&Dir1=$Dir1' title='Add to Favorite places'> + </a>");
if ($HaveFavDirs==1) {
  echo (" <a href='FavPlacesList.php' title='Edit favorite places' target=EditFavPlaces>...</a>");
}

echo("</td></tr></table>");

}
catch (PDOException $e) {
  echo ("<hr> Line ".__LINE__."<br>");
  echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
  print_r($PdoArr);	
  die ("<br> Error: ".$e->getMessage());
}





echo ("<form metod=post action=CreateDir.php>".
      "<input type=hidden name=HDir value='$Dir1'>".
      "<input type=hidden name=Dir2 value='$Dir2'>".
      "<input type=submit value='Create dir:'>".
      "<input type=text Name=DirName size=30 placeholder='new dir name' required>".
      " | <a href='ToServerUploadFile.php?Dir=$Dir1' target=ToServerFileUpload>File upload</a>".
      "</form>");

echo ("<form method=post action=GroupFilesOpers.php>".
      "<input type=hidden name=HDir value='$Dir1'>".
      "<input type=hidden name=Dir2 value='$Dir2'>".
      "<input type=checkbox ID=CheckAll onclick='return SelAll();'> ".
      "<input type=text id=SubStr Name=SubStr placeholder='File name substring' value='$SubStr'> ".
      "<input type=text id=HaveInFile Name=HaveInFile placeholder='Have in file'>".
      "<input type=submit Name=Filter value=Filter>");

$j=0;

if ($SelectDescr!= '') {
  echo ("<h3>Selection: $SelectDescr</h3>");
} 

foreach ( $Arr as $Indx => $FN) {
  $FullName=$Dir1."/".$FN;
  $OutF=1;
  
  if ($SubStr!='') {
    $CPos=strpos ( $FN, $SubStr) ;
    if ( $CPos===false ) {
      $OutF=0;
    }
  }


  if ($SelectDescr!= '') {
    if ( ! in_array($FN, $SelectArr, 1)) {
      $OutF=0;
    }
  } 
  if ($OutF==1) {
  if ( is_dir ($FullName ) ) {
    if ( $FN=='.') {
    }
    else
    if ( $FN=='..') {
      $Pos=strrpos($Dir1, '/');
      $Res=$Dir1;
      if ($Pos===false) {
        $Res=$FullName;  
      }
      else {
        //echo ("<br> Pos=$Pos ");
        $Res=substr($Dir1, 0, $Pos);
      }
      echo ("<br> -- <a href='EdiDispatchedFiles.php?Dir1=$Res&Dir2=$Dir2'>$FN</a>");
    }
    else {
      echo ("<br> -- <a href='EdiDispatchedFiles.php?Dir1=$FullName&Dir2=$Dir2'>$FN</a> | ".
            "<a href='SetDirPerm.php?D=$FullName' target=DirPerm title='Set dir permission'>...</a>");
    }
  }
  else {
    $j++;
    
    $FLTxt=base64_encode($FullName);

    echo ("<br> -- <input type=checkbox Name=Chk[$j] value='$FN' ID=Chk_$j> ".
          "$FN <a href='FileInfo.php?FL=$FullName&Dir1=$Dir1&Dir2=$Dir2' ".
          "title='File info'>i</a> | ".
          "<a href='FileEdit.php?FL=$FullName' title='File edit' target=FEdit >e</a> | ".
          "<a href='UploadFile.php?FL=$FLTxt' target=FGet$j title='File Get' target=FEdit >G</a>");
  }
  }
}
echo ("<input type=hidden Name=AllCnt value='$j' ID=AllCnt>");

echo("<br><input type=Submit Name=ButVal value='Delete'>".
     " | <input type=Submit Name=ButVal value='Copy'>".
     " | <input type=Submit Name=ButVal value='Move'>".
     " | <input type=Submit Name=ButVal value='Get'>".
     "</form>");

echo ("</td><td>");
echo ("<b>$Dir2</b> <a href='DeleteDir.php?Dir1=$FullName&Dir2=$Dir2' ".
      "onclick='return confirm(\"Delete directory $Dir2? \");'>Delete Dir</a>".
      " | <a href='ZipFolder.php?Dir1=$FullName&Dir2=$Dir2' ".
      "onclick='return confirm(\"Zip directory $Dir2? \");'>Zip Dir</a>"
      
      
      );
$Arr= scandir ( $Dir2 );

foreach ( $Arr as $Indx => $FN) {
  $ShowFile=1;
  $FullName=$Dir2."/".$FN;
  if ( is_dir ($FullName ) ) {
    if ( $FN=='.') {
    }
    else
    if ( $FN=='..') {
      $Pos=strrpos($Dir2, '/');
      $Res=$Dir2;
      if ($Pos===false) {
        $Res=$FullName;  
      }
      else {
        //echo ("<br> Pos=$Pos ");
        $Res=substr($Dir2, 0, $Pos);
      }
      echo ("<br> <a href='EdiDispatchedFiles.php?Dir2=$Res&Dir1=$Dir1'>$FN</a>");
    }
    else {
      echo ("<br> <a href='EdiDispatchedFiles.php?Dir2=$FullName&Dir1=$Dir1'>$FN</a>");
    }
  }
  else {
    echo ("<br> -- $FN ");
  }
}


?>
</body>
</html>