<?php

// mb_internal_encoding("UTF-8");

date_default_timezone_set('Europe/Moscow');

ini_set('display_errors', 1);
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING );

//if ($_SESSION['DEBUG']==1) {
//  error_reporting(E_ALL & ~E_NOTICE);
//}
//else {
//  error_reporting(E_ERROR | E_PARSE);
//}

require_once 'set#tings.php';

$pdo = null;
try {
    $pdo = new PDO("pgsql:host=$db_host;port=5432;dbname=$db_base;user=$db_user;password=$db_pass");
    //echo '<br>A connection to the PostgreSQL database sever has been established successfully.<br>';
    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    //array(PDO::ATTR_ERRMODE => ) PDO::ERRMODE_WARNING 
    //$pdo->exec("SET time_zone = '3:00'");

} 
catch (PDOException $e) {
    die ("<br> Error: ". $e->getMessage());
}


//echo ("<br>mysqli :" );
//-----------------------------------------------------------------------------------
function Ru2EnTranslit ($TextCyr) { 

  $cyr = [ 'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п',
           'р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
           'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П',
           'Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'];
  $lat = [
            'a','b','v','g','d','e','io','zh','z','i','y','k','l','m','n','o','p',
            'r','s','t','u','f','h','ts','ch','sh','sht','a','i','y','e','yu','ya',
            'A','B','V','G','D','E','Io','Zh','Z','I','Y','K','L','M','N','O','P',
            'R','S','T','U','F','H','Ts','Ch','Sh','Sht','A','I','Y','e','Yu','Ya'
        ];
   
  return (str_replace($cyr, $lat, $TextCyr));
}
// ========================================================================

function OutHtmlHeader ($FrmName) {
$Lang='RU';
if (!empty($_SESSION['lang'])) {
  $Lang=$_SESSION['lang'];
}
echo ('<!DOCTYPE>
<html lang="'.$Lang.'">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>'.$FrmName.'</title></head>
<body>');

}

//-----------------------------------------------------------------------------------
function BeginProc ($HaveOrd=0, $Chapter='') {  
  
  if ($Chapter=='') {
    if ( !isset($_SESSION['login']) ) { 
       
      $Uri=$_SERVER['REQUEST_URI'];
      $LN=base64_encode($Uri);
     
    
    
    die(

    '<html>
    <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" /></head><body>'.
        'You are not login.<br>Вы не вошли<br><br>'.
        '<a href="../FormsI/Login.php?Aftr='.$LN.'">Login page<br>Страничка для входа</a></body></html>');

    }
  }
  else 
  if ($Chapter=='ALL') {
    if ( !isset($_SESSION['login']) and  !isset($_SESSION['PL-login']) and !isset($_SESSION['SR-login'])) { 
       
      $Uri=$_SERVER['REQUEST_URI'];
      $LN=base64_encode($Uri);
     
      print_r($_SESSION);
    
    die(
    '<html>
    <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" /></head><body>'.
    'You are not login.<br>Вы не вошли<br><br>'.
        '<a href="../FormsI/Login.php?Aftr='.$LN.'">Login page<br>Страничка для входа</a><br><br>'.
        "<a href='../ExtProj/Login.php'>Login for External persons</a>".
        '</body></html>');

    }
  }
  else
  if ($Chapter=='SU-CHECK') {
    if ( !isset($_SESSION['SU-login']) ) { 
       
      $Uri=$_SERVER['REQUEST_URI'];
      $LN=base64_encode($Uri);
     
    die(
    '<html>
    <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" /></head><body>'.
    'You are not login.<br>Вы не вошли<br><br>'.
        '<a href="index.php?Aftr='.$LN.'">Login page<br>Страничка для входа</a><br>'.
        '</body></html>');

    }
  }
  else 
   
  if ($Chapter=='NO-CHECK') {
    // ALL can be
  }
  else {
    if ( !isset($_SESSION['PL-login']) ) { 
       
      $Uri=$_SERVER['REQUEST_URI'];
      $LN=base64_encode($Uri);
     
    die(
    '<html>
    <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" /></head><body>'.
        'You are not login.<br>Вы не вошли<br><br>'.
        '<a href="../FormsI/Login.php?Aftr='.$LN.'">Login page<br>Страничка для входа</a></body></html>');

    }
  }
  
  if ($_REQUEST['ProjNo']!='') {
    $_REQUEST['ProjNo']=mb_substr($_REQUEST['ProjNo'],0, 15)+0;  
  }

  if ($_REQUEST['hist_id']!='') {
    $_REQUEST['hist_id']=mb_substr($_REQUEST['hist_id'],0, 15)+0;  
  }

  
  if ($_REQUEST['BegPos']!='') {
    $_REQUEST['BegPos']=mb_substr($_REQUEST['BegPos'],0, 15)+0;
  }
  
  if ($_REQUEST['Ord']!='') {
    if ($HaveOrd==0) {
      $_REQUEST['Ord']='';
    }
    else {
    
    }
  }
  if ($_REQUEST['ORD']!='') {
    if ($HaveOrd==0) {
      $_REQUEST['ORD']='';
    }
    else
      $_REQUEST['ORD']=mb_substr($_REQUEST['ORD'],0, 15)+0;
  }

  foreach ($_REQUEST as $TT=> $Val) {
    if (! is_array($Val)) {
      $_REQUEST[$TT]=strip_tags($Val);
    }
  }
}
//-----------------------------------------------------------------------------------
function addslashSs ($XX) {
  return (addslashes (strip_tags(trim($XX))));
}
//-----------------------------------------------------------------------------------
function ExpNumber ($XX) {
  return (mb_substr($XX,0, 15)+0);
}
//-----------------------------------------------------------------------------------
function GetNewPass () {
  $dig = '0123456789';                      // 3
  $spec= '_-+*^`~!@#$%,.?{}[]()' ;         // 2
  $sm_letter = 'abcdefghijklmnopqrstuvwxyz';// 5
  $bg_letter = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';// 5

  $pos = array ( 0,0,0,0,0, 
                 0,0,0,0,0,
                 0,0,0,0,0, 0 );
  $vals = array ( '_','_','_','_','_',
                  '_','_','_','_','_',
                  '_','_','_','_','_','_');
                     
  //==============================================
  $j=0;

  while ($j<3) {
    $s = rand (1,15);
    if ( $pos [$s] == 0 ) {
      $j = $j+1;
      $pos[$s] = 1;
      $vals[$s] = substr($dig, rand (1,10), 1);
      //echo ('<br>Dig:'.$s.' '.$vals[$s]);   
    } 
  };
  //==============================================
  $j=0;
  while ($j < 2) {
    $s = rand (1,15);
    
    if ($pos[$s] == 0) {      
      $j = $j+1;
      $pos[$s] = 1;      
      $vals[$s]=substr($spec, rand (1,21), 1);   
      //echo ('<br>Spec:'.$s.' '.$vals[$s]);   

    };
  };
  //==============================================
  $j=0;
  while ($j < 5) {
    $s = rand (1,15);
    
    if ($pos[$s]==0) {
      $j=$j+1;
      $pos[$s]=1;
      $vals[$s]=substr($sm_letter, rand (1,26), 1);   
      //echo ('<br>Small:'.$s.' '.$vals[$s]);   
    };
  };
  //==============================================
  $j=0;
  for ($s=0;$s<16;$s++) {
    if ($pos[$s]==0) {
      $j=$j+1;
      $pos[$s]=1;
      $vals[$s]=substr($bg_letter, rand (1,26), 1);   
      //echo ('<br>Big:'.$s.' '.$vals[$s]);   
    };
  };
  //==============================================
  //echo "<br>";
  $passw='';
  for ($s=0;$s<16;$s++) {
    $passw=$passw.$vals[$s];
  };

  return ($passw);
};

//==========================================================================================
function CheckLogin1() {
  isset($_SESSION['login']) or 
    die('You are not login.<br>Вы не вошли<br><br>'.
      '<a href="'.GetBaseHost().'FormsI/Login.php">Login page<br>Страничка для входа</a>');
  //echo ("<br>");
  //print_r($_SESSION);
  //echo ("<br>");
}
//==========================================================================================
function GetFldName (&$db, $TabNo, $FldNo ) {
  $Res='';
  $query = "select \"ParamName\" from \"AdmTabFields\" ".
           "where (\"TypeId\"=:TabNo) and (\"ParamNo\"=:FldNo)";
  
  $arr = array();
  $arr['TabNo']= $TabNo;
  $arr['FldNo']= $FldNo;

  try {
    $STH = $db->prepare($query);
    $STH->execute($arr);

    if ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
      $Res = $dp2['ParamName'];  
    };
  }
  catch (PDOException $e) {
    die ("<br> Error: ".$e->getMessage());
  }

  
  return $Res;                         
};

//======================================================================================== 
function NewLine( $i, $color='even' ) {
  $Res=0;
  if ($i == 1) {
    echo ("</tr><tr class=$color>");
  }
  else {
    echo ("</tr><tr>");
    $Res=1;
  }
  return $Res;
}
//==========================================================================================
function GetSfId ($Str) {
  $Res='';
  //echo ($Str.'<br>');
  if ($Str!='') {
    mb_internal_encoding("UTF-8");
    $i=-1;
    $pos=0;
    while ( ($pos=mb_strpos($Str, '[', $pos))!==false) {
      $i=$pos;  
      $pos++;
    }

    if ($i!= -1) {
      $i++;
      if ( ($pos=mb_strpos($Str, ']', $i))!==false) {
        $Res=mb_substr ($Str, $i, $pos-$i);
      }   
    }
  }
  return $Res;
}
//==========================================================================================
function DivTxt ($Str, $len=45) {
  $Res='';
  
  mb_internal_encoding("UTF-8");
  
  //echo ($Str.'<br>');
  $DivArr=array (" ","\t","\r","\n","(",")","[","]",",","-","+",";","."); 

  $Last = mb_strlen ($Str);
  
  $Need=true;
  $i=0;
  $Str1=$Str;
  
  while($Need) {
    //echo ("<br>$Last $Str1");
    $FindDiv=false;
    
    if ( $Last > $len ) {
      //echo ("<br>$Last $Str1");
      $pos=$len-1;
      $FindDiv=false;
      while ( ($pos>0) AND !$FindDiv and ($Last>$len)) {
        $Ch= mb_substr( $Str1, $pos, 1);
        //echo ("<br>Pos:$pos $Last $Ch/".in_array($Ch, $DivArr)." $Str1|Res: $Res");

        if (in_array($Ch, $DivArr)) {
          $FindDiv=true;
          $Res.= mb_substr($Str1, 0, $pos+1).'<br>';
          $Str1= mb_substr($Str1, $pos+1);
          $Last= mb_strlen ($Str1);
          //echo ("Find div: $pos $Str1 | $Res"); 
        }
        else {
          $pos--;
        } 
      } 
    }
    else {
      $Res.= $Str1;
      $Last=0;
      $Str1='';
    }


    if (!$FindDiv and ($Last > $len )) {
      $Res.= mb_substr($Str1, 0, $len).'<br>';
      $Str1= mb_substr($Str1, $len);
      $Last= mb_strlen ($Str1);
      //echo ("<br> Last:$Last $Str1");
    }

    if ($Last <= $len ) {
      $Res.= $Str1;
      $Need = false;
    }
  }
  
  return $Res;
}
//-----------------------------------------------------------------------------------
function To1251($Str){
  return iconv( 'UTF-8', 'Windows-1251', $Str);
};
//-----------------------------------------------------------------------------------
function ToUtf ($Str) {
  return iconv('Windows-1251', 'UTF-8', $Str);
}
//-----------------------------------------------------------------------------------
function MakeAdminRec (&$db, $vUsr, $vCode, $vParam1, $vParam2, $vTxt) {
  
  //$itemNo = iconv( "WINDOWS-1251", "UTF-8", 
  // mysql_real_escape_string($itemNo));
      
  $query = "INSERT INTO \"admin_protocol\" (\"code\", \"param1\", \"param2\", \"description\", \"user_id\") 
           VALUES 
             (:vCode, :vParam1, :vParam2, :vTxt, :vUsr)";

  $PdoArr = array();
  $PdoArr['vCode']= $vCode;
  $PdoArr['vParam1']= $vParam1;
  $PdoArr['vParam2']= $vParam2;
  $PdoArr['vTxt']= $vTxt;
  $PdoArr['vUsr']= $vUsr;

  $Res=0;

  try {
    $STH = $db->prepare($query);
    $STH->execute($PdoArr);
    $Res=$db->lastInsertId();
  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }
         
  return $Res;
};
//===========================================================================

//-----------------------------------------------------------------------------------

function MakeAdminRecEP (&$db, $vUsr, $vCode, $vParam1, $vParam2, $vTxt) {
  $Usr=$vUsr;
  if ( $Usr == '') {
    $Usr = $_SESSION['login'];
  }
      
  $query = "INSERT INTO admin_protocol_EP (code, param1, param2, description, user_id) 
           VALUES 
             ('$vCode', '$vParam1', '$vParam2', '$vTxt', '$Usr')";

  $sql2 = $db->query($query)
        or die("Invalid query:<br>$query<br>" . $db->error);
         
  return $db->insert_id;
};

//-----------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------
function GetUserInfoAdm (&$mysqli, $UserName, $FldNo='') {
  $Res='';

  $query="select * from usrs where usr_id='$UserName'";

  $sql2 = $mysqli->query ($query) 
                or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);

  if ($dp = $sql2->fetch_assoc()) {
    $dp['usr_pwd']=' * * * * ';
    if ( $FldNo!='') {
      $Res=$dp[$FldNo];
    }
    else {
      $Res = $dp;
    }
  }
  return $Res;
}
//-----------------------------------------------------------------------------------
function AdminFooter () {
  echo ('<hr>
  <br><a href="http://intrasite.it-russia.org/legrand/ftp_view_uly/index.php">EDI сервер Firelec</a>
  <br>Данные по свободному остатку на складе<br>'.
        '<a href="../indx1.php">Item Qty check/Кол-во артикула проверка</a><br>'.
        '<a href="../Labels/frm_upload_file.php">Upload MO/Загрузить производственные заказы</a><br>'.
        '<a href="../Labels/Label_list.php">MO Label list/Просмотр талонов на производство</a><br>');

  if (isset($_SESSION['admin_login'])) {

  echo ('<hr><br>Administrative/Административная часть<br>'.
        '<a href="../admin/indx1.php">Upload csv data/Загрузка данных CSV</a><br>'.
        '<a href="../admin/upload_fir.php">Upload zipped XML file from Firelec COM data/Загрузка ZIP файла Фирелек</a><br>'.
        '<a href="../admin/new_user.php">Insert new user/Добавить нового пользователя</a><br>'.
        '<a href="../admin/au_BT_file_ftp_get.php">Upload Firelec data</a><br>'.
        '<a href="../admin/sf.php" target="SalesForce"><b>Sales Force</b></a><br>'.
        '<a href="../FormsI/usrsList.php">User list/Список пользователей</a><br>'.
        '<a href="../admin/protocol_view_admin.php">Protocol view admin/Протокол работы администратора</a><br>'.
        '<a href="../admin/protocol_view.php">Protocol view/Протокол работы</a><br>'.
        '<a href="../reports/Report_list.php">Report setup/Настройка отчетов</a><br>'.
        '<hr><a href="../setup/MakeDump.php">Database dump</a><br>'
        );

  };
  echo ('<a href="../admin/index.php?logout">Logout/Выход</a>');
};
//==========================================================================================

function GetStr (&$db, $Str) {
  $ResStr = '';
  $Save=0;
  if (! empty ($_SESSION ['STI_'.$Str])) {
    $ResStr = $_SESSION ['STI_'.$Str]; 
  };
  
  if ($ResStr =='') {
    
    $lang='';
    if (!empty($_SESSION ['LANG']) ) {
      $lang=$_SESSION ['LANG'];
    };

    if ( $lang=='') 
      $lang= 'RU';
    
    $query = "select \"TextVal\" from \"TranslationText\" ".
             "where (\"ID\"=:Str) and (\"Lang\"=:lang )";
    
    
    $arr = array();
    $arr['Str']  = $Str;
    $arr['lang'] = $lang;

    try {
      $STH = $db->prepare($query);
      $STH->execute($arr);

      if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
        //echo ("<br>");
        //print_r($dp);
        //echo("<hr>");

        $_SESSION ['STI_'.$Str]=$dp['TextVal'];
        $ResStr = $_SESSION ['STI_'.$Str];
      }
      else {
        
        if ((!empty ($_SESSION['login']) ) and ($_SESSION['login']=='vlad_lev')) {
          $ResStr = "<a href='".GetBaseHost()."FormsI/TranslateFrm.php?Enum=$Str' target=Translate>_</a>$Str";    
        }
        else {
          $ResStr = "_$Str";    
        }
      };
    }
    catch (PDOException $e) {
      die ("<br> Error: ".$e->getMessage());
    }


  };
  return $ResStr;
};

//=========================================================================

function GetMultiStr (&$mysqli, $FrmName, $Str, $EnumId) {

  // RenameFlds
  // FormName, FldName, TypeId, NewFld
  $Res=$Str;
  $query = "select NewFld from RenameFlds ". 
           "where (FormName='$FrmName') and (FldName='$Str') and (TypeId = '$EnumId') "; 

  $sql2 = $mysqli->query ($query) 
            or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
  if ($dp2 = $sql2->fetch_assoc()) {
    $Res=$dp2['NewFld'];
  }

  return $Res;

}
//-------------------------------------------------------------------------

function GetEnum (&$db, $EnumName, $EnumVal, $LangDef='') {
  $lang= $LangDef;
  if ($lang=='') {
    $lang= $_SESSION ['LANG'];
    if ( $lang=='') 
      $lang= 'RU';
  }
  
  
  $ResStr = $_SESSION [$lang]['STL_ENUM_'.$EnumName."_$EnumVal"];
  
  if ($ResStr =='') {
    
    $query = "select \"EnumDescription\" from \"EnumValues\" ".
             "where \"EnumName\"=:EnumName and \"EnumVal\"=:EnumVal and \"Lang\"=:Lang";
  
    $PdoArr = array();
    $PdoArr['EnumName']= $EnumName;
    $PdoArr['EnumVal']= $EnumVal;
    $PdoArr['Lang']= $lang;

    try {
      $STH = $db->prepare($query);
      $STH->execute($PdoArr);

    
    
    
    if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
      $_SESSION [$lang]['STL_ENUM_'.$EnumName."_$EnumVal"]=$dp['EnumDescription'];
      $ResStr = $_SESSION [$lang]['STL_ENUM_'.$EnumName."_$EnumVal"];
    }
    else {
      $ResStr = "$EnumName _$Str";    
    };
  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }


  };
         
  return $ResStr;
};
//==========================================================================================

function EnumToInt (&$db, $EnumName, $EnumStr) {
  $lang= $_SESSION ['LANG'];
  $Res=-9999;
  if ( $lang=='') 
    $lang= 'RU';
  
  $query = "select EnumVal from EnumValues ".
             "where EnumName='$EnumName' and EnumDescription='$EnumStr' ";

  //echo ("<br>$query<br>");

  $sql2 = $db->query($query)
            or die("Invalid query:<br>$query<br>" . $db->error);
  if ($dp = $sql2->fetch_assoc()) {
    $Res =$dp['EnumVal'];
  }
         
  return $Res;
};

//==========================================================================================

function GetAccDescr (&$mysqli, $PlanNo, $AccNo){
  $Res='';
  // AccountChart
  // PlanNo, IsBalance, AccountNo, AccName, HeadAcc, HaveChild, AnaliticName, AnaliticFld
  $query = "select AccName FROM AccountChart ".
         " WHERE (PlanNo='$PlanNo') AND (AccountNo='$AccNo') ";
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
  
  if ($dp = $sql2->fetch_assoc()) {
    $Res=$dp['AccName']; 
  }
  return $Res;
}
//==========================================================================================
function GetExchRate (&$mysqli, $CurrCode, $OpDate=''){
  $Res=1;
  if ($OpDate=='') {
    $OpDate=date('Y-m-d');
  }
  $query = "select * FROM CurrencyExchRate ".
         " WHERE (CurrencyCode='$CurrCode') AND ".
         "(StartDate<='$OpDate') order by StartDate desc LIMIT 0, 1 ";
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
  
  if ($dp = $sql2->fetch_assoc()) {
    $Res=$dp['FullRate']; 
  }
  return $Res;
}
//==========================================================================================
//-------------------------------------------------------------------------
function EnumSelection (&$db, $EnumName, $Name, $StdVal, $Empty=0 ) {
  $ResStr = '';
  if ($ResStr =='') {
    $ResStr="<select name=$Name>";
    $lang= $_SESSION ['LANG'];
    if ( $lang=='') 
      $lang= 'RU';
    
    if($Empty==1) {
      $ResStr.="<option value=''>".
                 GetStr($db, 'EMPTY').'</option>'; 
    }

      $query = "select \"EnumVal\", \"EnumDescription\" from \"EnumValues\" ".
               "where \"EnumName\"=:EnumName and \"Lang\"=:Lang ORDER BY \"EnumVal\"";

      $PdoArr = array();
      $PdoArr['EnumName']= $EnumName;
      $PdoArr['Lang']= $lang;

      try {
        $STH = $db->prepare($query);
        $STH->execute($PdoArr);


      
      $other = '';
      while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
          $Sel='';
          if($dp['EnumDescription'] == 'Другое') {
              $other = $dp;
              continue;
          }
          if ( $dp['EnumVal'] == $StdVal ) {
              $Sel= ' selected ';
          }
          $ResStr.="<option value=".$dp['EnumVal']." $Sel>".
              $dp['EnumDescription'].'</option>';

      }

      if(!empty($other)) {
          if ( $other['EnumVal'] == $StdVal ) {
              $Sel= ' selected ';
          }
          $ResStr.="<option value=".$other['EnumVal']." $Sel>".
              $other['EnumDescription'].'</option>';
      }

      $ResStr .= '</select>';

      //$ResStr .= " <button title='".GetStr($db, 'MultiSelect').
      //           "' onclick='return MSSet(\"$Name\");'>MS</button>";
  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

  
  };
         
  return $ResStr;
};
//-------------------------------------------------------------------------
//-------------------------------------------------------------------------
function EnumSelectionM (&$db, $EnumName, $Name, $StdVal, $Empty=0, $SelSize=5 ) {
  $ResStr = '';
  
  if ($ResStr =='') {
    $ArrStd = array ();
    foreach ($StdVal as $V) {
      $ArrStd[$V]=1;
    }
    
    $ResStr="<select name=$Name multiple size=$SelSize>";
    $lang= $_SESSION ['LANG'];
    if ( $lang=='') 
      $lang= 'RU';
    
    if($Empty==1) {
      $ResStr.="<option value=''>".
                 GetStr($db, 'EMPTY').'</option>'; 
    }

      $query = "select EnumVal, EnumDescription from EnumValues ".
          "where EnumName='$EnumName' and Lang='$lang' ORDER BY EnumVal";

      $sql2 = $db->query ($query)
      or die("Invalid query:<br>$query<br>" . $db->error);
      $other = '';
      while ($dp = $sql2->fetch_assoc()) {
          $Sel='';
          if($dp['EnumDescription'] == 'Другое') {
              $other = $dp;
              continue;
          }
          
          if ( $ArrStd [$dp['EnumVal']] == 1 ) {
              $Sel= ' selected ';
          }
          $ResStr.="<option value=".$dp['EnumVal']." $Sel>[{$dp['EnumVal']}] ".
              $dp['EnumDescription'].'</option>';

      }

      if(!empty($other)) {
          if ( $other['EnumVal'] == $StdVal ) {
              $Sel= ' selected ';
          }
          $ResStr.="<option value=".$other['EnumVal']." $Sel>".
              $other['EnumDescription'].'</option>';
      }

      $ResStr .= '</select>';

      //$ResStr .= " <button title='".GetStr($db, 'MultiSelect').
      //           "' onclick='return MSSet(\"$Name\");'>MS</button>";

    $sql2->free();
  };
         
  return $ResStr;
};
//-------------------------------------------------------------------------




//-------------------------------------------------------------------------
function EnumMultiSelect(&$db, $EnumName, $Name, $StdVal, $Cols=2) {
  
  $i=0;
  $ResStr = '<table><tr>';
  
  $lang= $_SESSION ['LANG'];
  if ( $lang=='') { 
    $lang= 'RU';
  }
    
  $query = "select EnumVal, EnumDescription from EnumValues ".
           "where EnumName='$EnumName' and Lang='$lang' ORDER BY EnumVal";

  $sql2 = $db->query ($query)
          or die("Invalid query:<br>$query<br>" . $db->error);
  
  while ($dp = $sql2->fetch_assoc()) {

    $CV= $dp['EnumVal'];
    $Ch='';
    if (strpos( $StdVal, ';'.$CV.';') !== false) {
      $Ch=' checked ';
    }  

    $ResStr.="<td><input type=checkbox Name='Arr".$Name."[".$CV."]' Value=1 $Ch>".
              " {$dp['EnumDescription']}</td>";

    $i++;

    if ($i>=$Cols) {
      $i=0;
      $ResStr.="</tr><tr>";
    }
  }

  $ResStr.="</tr></table>";
  return $ResStr;
}
//-------------------------------------------------------------------------

function GetEnumMulti(&$db, $EnumName, $StdVal) {
  $i=0;
  $ResStr = '';
  $Div='';
  
  $lang= $_SESSION ['LANG'];
  if ( $lang=='') 
    $lang= 'RU';

  $Arr= explode(';', $StdVal);

  foreach ($Arr as $F) {
    if ($F!= '') {
      $El = GetEnum ($db, $EnumName, $F);
      $ResStr.="$Div$El";
      $Div='; ';
    }
  }
  return $ResStr;
};




//-------------------------------------------------------------------------
function EnumSelectionEmpty (&$db, $EnumName, $Name, $StdVal ) {
  $ResStr = '';
  $Empty=1;
  if ($ResStr =='') {
    $ResStr="<select name=$Name >";
    $lang= $_SESSION ['LANG'];
    if ( $lang=='') 
      $lang= 'RU';
    
    if($Empty==1) {
      $ResStr.="<option value=''>".
                 GetStr($db, 'EMPTY').'</option>'; 
    }
    
    $query = "select EnumVal, EnumDescription from EnumValues ".
             "where EnumName='$EnumName' and Lang='$lang'";

    $sql2 = $db->query ($query)
        or die("Invalid query:<br>$query<br>" . $db->error);
    
    while ($dp = $sql2->fetch_assoc()) {
      $Sel='';
      if ( $dp['EnumVal'] == $StdVal ) {
        $Sel= ' selected ';
      }
      $ResStr.="<option value=".$dp['EnumVal']." $Sel>".
               $dp['EnumDescription'].'</option>'; 
      
    }
    $ResStr .= '</select>';
    $sql2->free();
  };
         
  return $ResStr;
};
//==========================================================================================
function HaveRight1 (&$db, $Right, $SubRight='-') {
  $User = $_SESSION['login'];
  
  $Res= HaveRight2($db, $User, $Right, $SubRight);
  return $Res;
};

//==========================================================================================
function GetItemName (&$db, $ItemNo) {
  // PL_Items
  // BrandId, Reference, ItemName, Family, ReqType, MinQty, StatKoef, ItemStatus, 
  // PhotoStatus, FullRef
  $Res='';
  $query = "select ItemName FROM PL_Items ".
           "where (Reference='$ItemNo')";
  
  $sql5 = $db->query ($query)
                 or die("Invalid query:<br>$query<br>" . mysql_error());
  if ($dp5 = $sql5->fetch_assoc()) {
      $Res= $dp5['ItemName'];
  };
  
  if ($Res=='') {

    // Lab_Mapics_Items
    // ItemNo, ItemName, ItemType, BaseUOM, 
    // FamilyCode, ItemStatus, TNVED
    $query = "select ItemName from Lab_Mapics_Items ". 
             "where (ItemNo='$ItemNo')"; 

    $sql2 = $db->query ($query) 
              or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
    if ($dp2 = $sql2->fetch_assoc()) {
      $Res= $dp2['ItemName'];
    }
  }
  
  $sql5->free();
  //echo ("<br>$User, $Right, $SubRight $Res");
  return $Res;
};
//========================================================================================
function GetItemRem (&$mysqli, $ItemNo, $WH='') {
  $WhTxt=$WH;
  if ($WH=='') {
    $WhTxt = "'FIR'";
  }

  $Res=0;
  // reservation
  // ord, reference, Company, Qty1, 
  // Qty2
  $query = "select sum(Qty1) Res from reservation ". 
           "where ( reference='$ItemNo')and (Company in ($WhTxt)) "; 

  $sql2 = $mysqli->query ($query) 
            or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
  if ($dp2 = $sql2->fetch_assoc()) {
    $Res=$dp2['Res'];
  }

  return $Res;

}
//========================================================================================

function GetStockItem (&$db, $ItemNo, $WH='') {
  $ResArr=array();
  $SId='';
  $SDate='2010-01-01';

  $WW='';
  if($WH !='') {
    $WW=" and (WH_ID='$WH')";
  } 

  $query = "select * FROM ItemStockPoints ".
           "where (Id in (select max(Id) FROM ItemStockPoints))";

  $sql2 = $db->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
    
  if ($dp = $sql2->fetch_assoc()) {
    $SId   = $dp['Id'];  
    $SDate = $dp['OpDate'];  
  };

  // ItemStockLevels
  // Id, WH_ID, ItemNo, Qty
  $query = "select * FROM ItemStockLevels where (Id='$SId')$WW and(ItemNo='$ItemNo')";
  $sql2 = $db->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);

  while ($dp = $sql2->fetch_assoc()) {
    $ResArr[$dp['ItemNo']]['Qty' ] = $ResArr[$dp['ItemNo']]['Qty' ] + $dp['Qty'];  
    $ResArr[$dp['ItemNo']]['Cost'] = $ResArr[$dp['ItemNo']]['Cost'] + $dp['Cost'];  
  };
  //--------------------------------------------------------------------

  $query = "select ItemNo, sum(SQty) SQ, sum(SCost) SC FROM ItemLedger ".
           "where (ItemNo='$ItemNo')$WW and(OpDate>'$SDate') group by ItemNo";
  
  //echo("<br>$query<br>");
  
  $sql2 = $db->query ($query)
                   or die("Invalid query:<br>$query<br>".__LINE__.' line '.
                   __FILE__.' file<br>'. $mysqli->error);


  while ($dp = $sql2->fetch_assoc()) {
    $ResArr[$dp['ItemNo']]['Qty' ] = $ResArr[$dp['ItemNo']]['Qty' ] + $dp['SQ'];  
    $ResArr[$dp['ItemNo']]['Cost'] = $ResArr[$dp['ItemNo']]['Cost'] + $dp['SC'];  
  }
  //--------------------------------------------------------------------
  
  //print_r($ResArr);

  return $ResArr;
}
//==========================================================================================
function HaveRight2(&$db, $User, $Right, $SubRight){
  $Res= 0;
  $query = "select \"Val\" FROM \"UsrRights\" where ".
           "(\"UsrName\"=:User) and (\"RightType\"=:Right) and (\"RightSubType\"=:SubRight)";
  
  $PdoArr = array();
  $PdoArr['User']= $User;
  $PdoArr['Right']= $Right;
  $PdoArr['SubRight']= $SubRight;
  
  try {
    $STH = $db->prepare($query);
    $STH->execute($PdoArr);
  
  
  if ($dp5 = $STH->fetch(PDO::FETCH_ASSOC)){
      $Res= $dp5['Val'];
  };
  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }


  return $Res;
};
//==========================================================================================
function CheckRight1 (&$db, $Right) {
  if ( ! HaveRight2 ($db, $_SESSION['login'], $Right, '-')) {
    die ("<br>Have no right $Right to user: {$_SESSION['login']}");
  }    
};
//==========================================================================================
function CheckRight2 (&$db, $Right, $SubRight) {
  if ( ! HaveRight2 ($db, $_SESSION['login'], $Right, $SubRight)) {
    die ("<br>Have no right $Right ($SubRight) to user: {$_SESSION['login']}");
  }    
};  
//==========================================================================================
function UserAutoLogin ( &$mysqli, $UserName1, $HId, $Chk ) {
  $Res=false;
  
  $UserName = addslashes ($UserName1);
  //echo ('<br>'.$UserName.'/'.$UserPass.'<br>');

  //print_r ($_SESSION);
  //echo ("<br>");

  if (!isset($_SESSION['login'])) {
    
    if (($UserName1=='') or (SHId=='')) {
      if (isset($_REQUEST ['HId'])) {       
           echo ("<br> $UserName1 :");
           die('Please contact to Your administrator You have NO RIGHTS.<br>'.
                  iconv('Windows-1251', 'UTF-8', 
                      'У Вас нет прав обратитесь к администратору.').'<br><br>');
      }
      else {
        echo ('
        <html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Content-Language" content="ru">
        <title>Error login</title></head><body>
        ');
        die ('You are not login.<br>'.
               iconv('Windows-1251', 'UTF-8', 'Вы не вошли').'<br><br>'.
               '<a href="../FormsI/Login.php">Login page<br>'.
               iconv('Windows-1251', 'UTF-8', 'Страничка для входа').'</a></body></html>');
      }
    }

    $query = "select usr_pwd from usrs where usr_id='$UserName'";
  
    $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . mysql_error());

    if ($dp2 = $sql2->fetch_assoc()) {
      $Pwd =$dp2['usr_pwd'];
      $Chk2 = base64_encode (md5( "$UserName1 $Pwd $HId"));
      if ($Chk2 == $Chk) {
        if (ChkOk ($mysqli, $HId, "AutoLogin" )) { 
          $_SESSION['login'] = $UserName;
          $Res= true;
          $login=addslashes ($UserName);
          
          $query = "INSERT INTO admin_protocol (code, param1, param2, description, user_id) 
             VALUES 
             ('LOGIN', '$login', 'Login', 'Login $login from Adv', '$login')";

          $sql5 = $mysqli->query ($query)
             or die("Invalid query:<br>$i $query<br>" . mysql_error());
        }
        else {
          die ("<br> Error: Bad check ".__LINE__." line ".__FILE__);
        }
      }
      else {

        die('You are not login.<br>'.
            iconv('Windows-1251', 'UTF-8', 'Вы не вошли').'<br><br>'.
            '<a href="../FormsI/Login.php">Login page<br>'.
            iconv('Windows-1251', 'UTF-8', 'Страничка для входа').'</a>');
      }
    }
    else {
      die('You are not login.<br>'.
            iconv('Windows-1251', 'UTF-8', 'Вы не вошли').'<br><br>'.
            '<a href="../FormsI/Login.php">Login page<br>'.
            iconv('Windows-1251', 'UTF-8', 'Страничка для входа').'</a>');
    }
  }
  else {
  }
}
//==========================================================================================
function ChkOk ( &$mysqli, $HId, $Name ) {
  $Res=0;
  $query = "select * FROM ChkTab where ChkCode='$Name' ";
  $sql2 = $mysqli->query ($query)
            or die("Invalid query:<br>$query<br>" . $mysqli->error);

  echo ( "<br>$query<br>" );
  if ($dpL = $sql2->fetch_assoc()) {
    echo ("<br> {$dpL['ChkVals']} < $HId ". ($dpL['ChkVals']< $HId) . "<br>");
    
    if ($dpL['ChkVals']< $HId) {
      $query = "update ChkTab set ChkVals='$HId' where ChkCode='$Name' ";
      $sql2 = $mysqli->query ($query)
                   or die("Invalid query:<br>$query<br>" . $mysqli->error);

      $Res=1;
    }
  }
  return $Res;
}
//==============================================================
function AddLimitPos($BegPos, $LPP) {
  $BP = $BegPos+0;
  $LPP = $LPP+0;
  return " LIMIT $LPP OFFSET $BP ";  
}
//==============================================================
function UpdateTable (&$pdo, $TabName, &$FldsArr, &$Arr, $PKArr, $CanUpd=1, $AutoIns='') {
  $StrWhere='';
  $Div='';

  echo ("<br> UpdTable: Arr:");
  print_r($Arr);
  echo ("<hr> PkArr:");
  print_r($PKArr);
  echo ("<hr>");

  $Res='';
  $PdoArr = array();
  foreach ($PKArr as $F) {
    $PdoArr[$F]=$Arr[$F];
    $StrWhere.="$Div(\"$F\"=:$F)";
    $Div=' and '; 
  }
  
  if ($StrWhere=='') {
    die ("<br> Error: No PK");
  }

  $dp3=array(); 
  $query = "select * FROM \"$TabName\" ".
           "WHERE $StrWhere";
        
  
  $InsOnly=0;
  try {
    
    if ($AutoIns!='') {
      if (empty($Arr[$AutoIns])) {
        $InsOnly=1;
      }
    }

    if ($InsOnly==0) {
      $STH = $pdo->prepare($query);
      $STH->execute($PdoArr);
      if ($dp3 = $STH->fetch(PDO::FETCH_ASSOC)){
        $InsOnly=0;
      }
      else {
        $InsOnly=1;
      }
      //echo ("<br> Line ".__LINE__.": $query<br>");
      //        print_r($PdoArr);
      //        echo ("<br>");
  
    }
  
  if ($InsOnly==0) {
    if ($CanUpd) {
      $UpdStr='';
      $Div='';
      foreach ($FldsArr as $Fld) {
        if ($Arr[$Fld]!= $dp3[$Fld]) {
          $PdoArr[$Fld]= $Arr[$Fld];
          $UpdStr.="$Div\"$Fld\"=:$Fld";
          $Div=', ';
        }
      }
      if ($UpdStr!='') {
        $Res='U';
        $query = "update \"$TabName\" set $UpdStr ".
                 "where $StrWhere";
        
        //echo ("<br>$query");
        $STH4 = $pdo->prepare($query);
        $STH4->execute($PdoArr);
        
        $UpdCnt++;
      }
    }
  }
  else {
    $InsStr1='';
    $InsStr2='';
    $Div='';
    $PdoArr=array();
    foreach ($FldsArr as $Fld) {
      if ($Fld == $AutoIns) {
        // AutoInsert
      }
      else { 
        $InsStr1.="$Div\"$Fld\"";
        $PdoArr[$Fld]= $Arr[$Fld];

        $InsStr2.="$Div:$Fld";
        $Div=', ';
      }
    }
    
    $Res='I';
  
    $query = "insert into \"$TabName\" ($InsStr1) values ".
             "($InsStr2)";

    //echo ("<br> Line ".__LINE__.": $query<br>");
    //          print_r($PdoArr);
    //          echo ("<br>");

    
    //echo ("<br>$query");
    $STH4 = $pdo->prepare($query);
    $STH4->execute($PdoArr);

    if ($AutoIns!='') {
      $Arr[$AutoIns] = $pdo->lastInsertId();  
    }

    //$ErrArr = $STH4->errorInfo();
    //echo ("<br> Line ".__LINE__.":ErrArr: ");
    //print_r($ErrArr);
    //echo ("<br>");

    
    $InsCnt++;
  }

  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

  return $Res;
}
//==============================================================


function SetFilter2Field ( $FldName, $Filter, &$PdoArr) {
  return SetFilter2Fld ( $FldName, $Filter, $PdoArr); 
}

//==============================================================
function SetFilter2Fld ( $FldName, $Filter, &$PdoArr) {
  $Res=" (\"$FldName\" like '%$Filter%') ";
  if ( ($Filter == '!**empty**')or($Filter == '!**пусто**')) {
    $Res= " (\"$FldName\"!='') ";

  }
  else 
  if ( ($Filter == '**empty**')or($Filter == "''..''")or($Filter == '**пусто**')) {
    $Res= " (\"$FldName\"='') ";

  }
  else {
  
  $Pos= mb_strpos ($Filter, '|') ;
  if ($Pos !== false ) {
    $List='';
    $Div='';

    $Arr = explode ('|', $Filter);
    $Indx=1;
    foreach ($Arr as $V) {
      $ZN=$FldName.'_V_'.$Indx;
      $Indx++;
      $PdoArr[$ZN]=$V;
      $List.= "$Div:$ZN";
      $Div=', ';
    }
    $Res=" (\"$FldName\" in ($List)) ";
  }
  else {


  
  $Pos= mb_strpos ($Filter, '..') ;
  
  if ($Pos !== false ) {
    $BegVal = trim(mb_substr($Filter, 0, $Pos));
    $EndVal = trim(mb_substr($Filter, $Pos+2));

    //echo ("<br> $Filter BegVal=$BegVal EndVal=$EndVal $Pos ");

    $Res= ''; 

    if ( $BegVal!='') {
      if ( $EndVal!='') {
        $ZNB=$FldName.'_V_Beg';
        $PdoArr[$ZNB]=$BegVal;
        
        $ZNE=$FldName.'_V_End';
        $PdoArr[$ZNE]=$EndVal;

        $Res= " ( \"$FldName\" between :$ZNB and :$ZNE) ";  
      }
      else {
        $ZN=$FldName;
        $PdoArr[$ZN]=$BegVal;
        
        $Res= " ( \"$FldName\" >= :$ZN) ";  
      }
    }
    else {
      if ( $EndVal!='') {
        $ZN=$FldName;
        $PdoArr[$ZN]=$EndVal;
        $Res= " (\"$FldName\" <= :$ZN) ";  
      }
    }
  }
  }
  }
  return $Res; 
}
//==============================================================
function SetMSFilter2Fld ($FldName, $Filter) {
  $Res='';
  if (is_array($Filter) ) {
    $Res= "($FldName in (";
    $Div='';
    foreach ($Filter as $V) {
      $Res.= "$Div'$V'";
      $Div=', ';  
    }
    $Res.='))';
    if ($Div==''){
      $Res='';
    }
  }
  else { 
    $Res=" (\"$FldName\"='$Filter') ";
  }

  return $Res; 
}

//==============================================================
function GetFileCount(&$mysqli, $DocNo, $DocType) {
  $res='[0]';
  
  $query = "select count(LINE) LN from AttachedFiles ".
             "where TYPEF='$DocType' and NO='$DocNo'";

  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
  
  if ($dp = $sql2->fetch_assoc()) {
    $res='['.$dp['LN'].']';
  }
  
  return $res;

}
//==========================================================================================
function CanSendMail(&$mysqli, $Email) {
  // Вернет 1, если не обнаружится в списке запретных почт (EMailSendsRefuse)
  $Res=1;
  // EMailSendsRefuse
  // EMail, OpDate, IpAddr, ConfirmCode, 
  // ESId, SendDate, ConfirmDate, RefuseReason
  if ($_SESSION['DEBUG']==1) 
      echo ("<br>Can send mail $Email ");
  
  $query = "select OpDate from EMailSendsRefuse ". 
           "where ( EMail='$Email' )"; 

  
  $sql2 = $mysqli->query ($query) 
          or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);

  if ($dp2 = $sql2->fetch_assoc()) {
    $Res=0;
    if ($_SESSION['DEBUG']==1) 
      echo (" Not Send ");
  }
  return $Res;

}
//==========================================================================================
//-----------------------------------------------------------------------------------
function GetConst(&$mysqli, $ConstName, $OpDate) {
  $Res='';

  $query = "select ConstType from ComConst ".
           "where ConstName='$ConstName'";

  if ($_SESSION['DEBUG']==1) {
    echo ("<br> GetConst: $query <br>"); 
  }
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
  
  if ($dp = $sql2->fetch_assoc()) {
    $F='';
    if ($dp['ConstType']==5) {
      $F='Value';
    }  
    if ($dp['ConstType']==10) {
      $F='ValueDate';
    }  
    if ($dp['ConstType']==15) {
      $F='ValueTxt';
    }  
    
    //ComConstValues
    //ConstName, OpDate, ValidTill, Value, ValueDate, ValueTxt
    $query = "select $F R from ComConstValues ".
             "where (ConstName='$ConstName') and (OpDate<='$OpDate') and ".
             " ( (ValidTill ='0000-00-00') OR (ValidTill>='$OpDate')) order by OpDate desc limit 0,1";

    if ($_SESSION['DEBUG']==1) {
      echo ("<br> GetConst: $query <br>"); 
    }
    
    
    $sql3 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
  
    if ($dp1 = $sql3->fetch_assoc()) {
      $Res= $dp1['R'];
    }
  }
  return $Res;
}
//-----------------------------------------------------------------------------------


//==========================================================================================
function GetFamilyNameArr (&$mysqli, $FamilyNo, &$SubArr ) {
  $Res='--';
  if ($SubArr[$FamilyNo] !='') {
    $Res = $SubArr[$FamilyNo];
  }
  else {
    $query = "select FamilyName FROM PL_ItemFamily ".
             "WHERE (FamilyNo='$FamilyNo')";
  
    $sql2 = $mysqli->query ($query) 
              or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
    if ($dp2 = $sql2->fetch_assoc()) {
      $Res=$dp2['FamilyName'];
    }

    $SubArr[$FamilyNo]=$Res;
  };   
  return $Res;                         
};

//==========================================================================================
function GetUserParam2 (&$mysqli, $Usr, $ParType, $ParamName) {
  $ResStr = '';
  $query = "select Value from ParamVal ".
           "where ParamType='$ParType' and ParamNo='$ParamName' and ID='$Usr'";

  $sql2 = $mysqli->query ($query) 
          or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
  if ($dp =  $sql2->fetch_assoc()) {
    $ResStr = $dp['Value'];
  }      
  return $ResStr;
};
//===========================================================================================
function EnumSelectionSomeVals (&$mysqli, $EnumName, $Name, $StdVal, $ArrVals) {
  $ResStr = '';
  $ResStr="<select name=$Name >";
  foreach ( $ArrVals as $Val ) {
    $Sel='';
    if ( $Val == $StdVal ) {
        $Sel= ' selected ';
    }
    $ResStr.="<option value='".$Val."' $Sel>".
               GetEnum($mysqli, $EnumName, $Val).'</option>'; 
      
  }
  $ResStr .= '</select>';
  return $ResStr;
};
//=============================================================================================
function SelectionArr ($Name, $StdVal, &$ArrVals) {
  $ResStr = '';
  
  $ResStr="<select name=$Name >";
  foreach ( $ArrVals as $Val=>$Txt ) {
    $Sel='';
    if ( $Val == $StdVal ) {
        $Sel= ' selected ';
    }
    $ResStr.="<option value='".$Val."' $Sel>".
               $Txt.'</option>'; 
      
  }
  $ResStr .= '</select>';
  return $ResStr;
};
//=============================================================================================
/*
function EnumMultiSelect (&$mysqli, $EnumName, $SelName, $StrVals) {
  // StrVals -- какие должны быть выделены позиции строка: 0,3,5,6
  // Передает на сервер : 1)  _MaxQty -- сколько всего шт выбора
  //                      2) _Sel_3] => 20  -- какие опции выбрал пользователь
  // Array ( [TestUOM_MaxQty] => 6 [TestUOM_Sel_3] => 20 [TestUOM_Sel_5] => 40 
  $Res='';
  $VArr = array ();
  $i = strpos ($StrVals, ',');
  $Div=',';
  if ($i===false) {
    $Div=';';
  }

  $Arr=explode ($Div, $StrVals);
  foreach($Arr as $Indx=>$V) {
    $Tr=trim($V);
    if ($Tr!='') {
      $VArr[$Tr]=1;
    }
  }

  $Lang=$_SESSION['LANG'];
  if ($Lang=='') {
    $Lang='RU';
  }
  
  //EnumValues
  //EnumName, EnumVal, Lang, EnumDescription
  $query = "select count(*) CNT from EnumValues ".
           " WHERE (EnumName='$EnumName') and (Lang='$Lang')";

  $sql2 = $mysqli->query ($query) 
                  or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);

  $Cnt=0;
  if ($dp2 = $sql2->fetch_assoc()) {
    $Cnt=$dp2['CNT'];
  }

  if ($Cnt==0) {
    die ("Error: No values for EnumName='$EnumName' and Lang='$Lang' ");
  }


  //EnumValues
  //EnumName, EnumVal, Lang, EnumDescription
  $query = "select EnumVal, EnumDescription from EnumValues ".
           " WHERE (EnumName='$EnumName') and (Lang='$Lang') order by EnumVal ";

  $sql2 = $mysqli->query ($query) 
                  or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);

  echo("<input type=hidden Name={$SelName}_MaxQty ID={$SelName}_MaxQty value=$Cnt>".
        "<table><tr class=header><td><input type=checkbox Name={$SelName}_SelAll ".
         "ID={$SelName}_SelAll onclick='MSChangeVal(\"$SelName\");'></td>".
        "<td align=center><input type=button onclick='return MSClrVal(\"$SelName\");' value='".
           GetStr($mysqli, 'Clear')."'></td>");


  $n=0;
  $i=0;
  while ($dp2 = $sql2->fetch_assoc()) {
    $n=NewLine($n);
    $i++;
    $Ch='';
    if ( !empty ( $VArr[$dp2['EnumVal']]) ) {
      $Ch=' checked';
    }
    $Clr=addslashes ($dp2['EnumDescription']);
    echo ("<td><input type=checkbox Name={$SelName}_Sel_$i$Ch Id={$SelName}_SEL_$i value='{$dp2['EnumVal']}'></td>".
          "<td>{$dp2['EnumDescription']}</td>");  
  }
  echo("</tr></table>");
}
*/
//-----------------------------------------------------------------------------------

function HistSave (&$mysqli, $vCode, $vParam1, $vParam2, $vTxt) {
  $Usr = $_SESSION['login'];
      
  $query = "INSERT INTO admin_protocol (code, param1, param2, description, user_id) 
           VALUES 
             ('$vCode', '$vParam1', '$vParam2', '$vTxt', '$Usr')";

  $sql2 = $mysqli->query ($query) 
            or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
         
  return $mysqli->insert_id;
};
//-----------------------------------------------------------------------------------
function FilterStr ( $Fld, $Txt ) {
  $Res='';
  $DD=$Txt;       
  $IY=mb_strpos($DD, '..');
  //echo ("<br>IY:$IY ");
  $BegVal='';
  $EndVal='';
      
  $WHADD='';
  if ($IY!== false) {
    if ($IY>0) {
      $BegVal=trim(substr($DD, 0, $IY));
      if ($BegVal != '') {
        $BegVal=addslashes($BegVal);
        $WHADD="$Fld>='$BegVal'";  
      }
    }
    
    $EndVal=trim(substr($DD, $IY+2));
    if ($EndVal!= '') {
      $EndVal=addslashes ($EndVal);
      if ($WHADD!='') {
        $WHADD = " ( $WHADD ) and ( $Fld<='$EndVal') ";
      }
      else {
        $WHADD.= "$Fld<='$EndVal'";
      }
    }
  }
  else {
    $WHADD= " $Fld like '%".addslashes($DD)."%'";   
  }
    
    
  if ($WHADD!= '') {    
    $Res=" and ($WHADD)";
  }
  
  return $Res;
}
//-----------------------------------------------------------------------------------
function EnumSelectionTab (&$mysqli, $Tab, $Name, $StdVal) {
  $ResStr = '';
  $Opt ='';
  $query = '';
  if ($Tab='Lab_Item_Series') {
    $query = "select SeriaNo from $Tab WHERE allow = 1 order by SeriaNo ";
  }
    
  $sql2 = $mysqli->query ($query) 
                or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
    
  

  while ($dp = $sql2->fetch_array()) {
    $Sel='';
    if ( $dp[0] == $StdVal ) {
        $Sel= ' selected ';
    }
    $Opt.="<option value='".$dp[0]."' $Sel>".
               $dp[0].'</option>'; 
  }

  $ResStr="<select name=$Name $AddInfo>" . $Opt . '</select>';
  return $ResStr;
};
//-----------------------------------------------------------------------------------

//function GetUserParam (&$mysqli, $ParamName, $PT='', $vUsr='') {
//  if ($PT=='') {
//      $PT='0001';
//  }
//
//  $ResStr = $_SESSION['UsrParam-'.$PT][$ParamName][$vUsr] ?? '';
//
//  if ($ResStr =='') {
//      $USR=$vUsr;
//      if ($USR=='') {
//          $USR =  $_SESSION['login'];
//      }
//      //echo ( "<br> User:$USR ");
//      $query = "select Value from ParamVal ".
//          "where ParamType='$PT' and ParamNo='$ParamName' and ID='$USR'";
//
//      //echo ( "<br>$query<br>");
//      $sql2 = $mysqli->query ($query)
//      or die("Invalid query:<br>$query<br>" . $mysqli->error);
//      if ($dp = $sql2->fetch_object()) {
//          $_SESSION ['UsrParam-'.$PT][$ParamName][$vUsr]=$dp->Value;
//          $ResStr = $_SESSION ['UsrParam-'.$PT][$ParamName][$vUsr];
//      }
//      else {
//          $_SESSION['UsrParam-'.$PT][$ParamName][$vUsr]="_$ParamName";
//          $ResStr = "_$ParamName";
//      };
//  };
//
//  //echo ( " $ParamName: $ResStr ");
//
//  return $ResStr;
//};
//-------------------------------------------------------------------------
function ReadXlsDec ($DigitalStr) {
  
  
  $search  = array(' ', ',', "'");
  $replace = array('', '', '');
  return addslashes(trim(str_replace($search, $replace, $DigitalStr)));
}

//===============================================================================================

function ShowMenu (&$mysqli, $Level, $StartPoint, $ColumnNo, $StartDir, $MenuType=0) {

      if ($_SESSION['DEBUG']==1) {
        echo ("<br>Show Menu Level=$Level, Start=$StartPoint, Col=$ColumnNo, Dir=$StartDir, MenuType=$MenuType<br>");
      }
  
  if ($Level>15) {
    die("<br> Error: Menu level > 15. StartPoint= $StartPoint");
  }
  
  if ($Level==0) {
    echo ("<table width=95%><tr valign=top>");

    for ($i=0; $i<3;$i++) {
      echo ("<td>");
      ShowMenu ($mysqli, 1, $StartPoint, $i+1, $StartDir, $MenuType);
      echo ("</td>");
    }
    echo ("</tr></table>");
  
  }
  else {
    try {
      // SELECT `Id`, `MenuName`, `Description`, `Link`, `NewWindow`, 
      // `ParentId`, `Ord` FROM `Menu` WHERE 
      $PdoArr = array();

      $query = "SELECT * FROM \"Menu\" ".
               "where (\"ParentId\"=:StartPoint) and (\"MenuType\"=:MenuType) and ".
                     "(\"ColumnNo\"=:ColumnNo)  order by \"Ord\"";
      
      $PdoArr['StartPoint']= $StartPoint;
      $PdoArr['MenuType']= $MenuType;
      $PdoArr['ColumnNo']= $ColumnNo;
      
      if ($_SESSION['DEBUG']==1) {
        echo ("<br>$query<br>");
      }
      
      $STH = $mysqli->prepare($query);
      $STH->execute($PdoArr);

      echo ("<ul>");
      while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
         
        if ($_SESSION['DEBUG']==1) {
          //echo ("<br>");
          //print_r($dp);
        }
        
        if ( HaveRight1 ($mysqli, "Menu", $dp['Id'])) {
          if ($_SESSION['DEBUG']==1) {
            echo (" --  HAVE RIGHT to Menu {$dp['Id']} -- ");
          }
          if ( ($dp['Link']!= '') or ($dp['Description']!= '')) {
            $HRefB='';
            if ($dp['Link']!= '') {
              $HRefB=" href='$StartDir{$dp['Link']}'";  
            }
            $Tit='';
            if ($dp['Description']!= '') {
              $Tit=" title='".GetStr($mysqli, $dp['Description'])."'";
            }

            $Target='';
            if ($dp['NewWindow']==1) {
              $Target=" target=Frm{$dp['Id']}";
            }

            echo ("<li id=Men{$dp['Id']}>{$dp['MenuCode']} <a$HRefB$Tit$Target>".GetStr($mysqli, $dp['MenuName']).'</a>');
          }
          else {
            echo ("<li id=Men{$dp['Id']}>{$dp['MenuCode']} ".GetStr($mysqli, $dp['MenuName']));
          }
        
          ShowMenu ($mysqli, $Level+1, $dp['Id'], $ColumnNo, $StartDir, $MenuType);
          echo ("</li>");  
        } 
      }
      echo ("</ul>");
    }
    catch (PDOException $e) {
      echo ("<hr> Line ".__LINE__."<br>");
      echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
      print_r($PdoArr);	
      die ("<br> Error: ".$e->getMessage());
    }

    
  }
}
//===============================================================================================
function GetUserRec (&$mysqli, $Usr) {
  $Rec=array();
  $query = "SELECT email, description, FirstName, LastName FROM usrs ".
           "WHERE (usr_id='$Usr')";
             
  $sql=$mysqli->query($query)
                  or die ("Invalid query:<br>$query<br>Line:".__LINE__." ".$mysqli->error);    
  
  if ($dp1 = $sql->fetch_assoc()) {
    if ($dp1['FirstName']=='') {
      $Rec['Name']= $dp1['description'];
    }
    else {
      $Rec['Name']= $dp1['FirstName'].' '.$dp1['LastName'];
    }
    $Rec['Mail']= $dp1['email'];
  }
  return $Rec;
}

//-------------------------------------------------------------------------
function GetXlCol ($ColNo) {
               // 1   2   3   4   5     6   7   8   9   10
  $Let = array ( 'A','B','C','D','E',  'F','G','H','I','J',
             
                 'K','L','M','N','O',  'P','Q','R','S','T',
                 'U','V','W','X','Y',  'Z');
  
  if ($ColNo> 256 ) {
    die ("<br> Error: Xls Column No = 256 or less. Ask $ColNo"); 
  }
  if ($ColNo<1 ) {
    die ("<br> Error: Xls Column No = 1 or more. Ask $ColNo");  
  }

  if ($ColNo<27) {
    return $Let[$ColNo-1];
  }
  else {
    $FL=FLOOR($ColNo/26);
    $LAST=$ColNo- ($FL*26);
    
    if ($LAST==0) {
      return $Let[$FL-2].$Let[25];  
    }
    else {
      return $Let[$FL-1].$Let[$LAST-1];  
    }
  }
}
//==================================================================================
//==========================================================================================
function GetItemStatus (&$db, $ItemNo) {
  // PL_Items
  // BrandId, Reference, ItemName, Family, ReqType, MinQty, StatKoef, ItemStatus, 
  // PhotoStatus, FullRef
  $Res='';
  $query = "select ItemStatus FROM PL_Items ".
           "where (Reference='$ItemNo')";
  
  $sql5 = $db->query ($query)
                 or die("Invalid query:<br>$query<br>" . mysql_error());
  if ($dp5 = $sql5->fetch_assoc()) {
      $Res= $dp5['ItemStatus'];
  };
  return $Res;
}

//==========================================================================================
function LegRegionByISO (&$mysqli, $IsoGeoCode, $IsTxt=0) {
  $Res='';

  // GeoDivIso
  // GeoCode, Country, GeoPartName, RegionStatus, 
  // Region
  $query = "select * from GeoDivIso ". 
           "where (GeoCode = '$IsoGeoCode') "; 

  $sql2 = $mysqli->query ($query) 
            or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
  while ($dp2 = $sql2->fetch_assoc()) {
    $Reg=$dp2['Region'];

    // ExtProjs_Regions
    // RegionNo, RegionName
    $query = "select * from ExtProjs_Regions ". 
             "where (RegionNo = '$Reg' )"; 

    $sql22 = $mysqli->query ($query) 
              or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
    if ($dp22 = $sql22->fetch_assoc()) {
      $Res1= $dp22['RegionName'];
      if ( $IsTxt==0) {
        $Res=$Res1;
      }
      else {
        $Res=GetEnum($mysqli, 'SF.Region', $Res1);
      }
    }
  }
  return $Res;
}
//==========================================================================================
//==========================================================================================

function SaveHist2 (&$mysqli,  $TabName, $WhereClause, $LogMsg, $LogType, $LogParam1, $LogParam2) {
  
  $HistId=MakeAdminRec ($mysqli, $_SESSION['login'], $LogType, $LogParam1, 
                        $LogParam2, $LogMsg);

  
  $query = "  insert into ".$TabName."_hist (".
         "select $HistId, L.* ".
         "FROM $TabName L ".
         " WHERE L.$WhereClause)";
  
  $sql2 = $mysqli->query ($query) 
                or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);

  $LinesArr= array ( 'WMS_Receipt_Done'=>'WMS_Receipt_Done_Line',
                     'LAB_LABEL'=> 'Lab_Label_MO');

  if ($LinesArr[$TabName]!='') {
    $TabName1=$LinesArr[$TabName];
    $query = "  insert into ".$TabName1."_hist (".
           "select $HistId, L.* ".
           "FROM $TabName1 L ".
           " WHERE L.$WhereClause)";
  
    $sql2 = $mysqli->query ($query) 
                or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
  
  }

  //SELECT `hist_id`, `TransferId`, `TransferLineNo`, `CanbanId`, `ItemNo`, 
  //`ItemName`, `Qty`, `Ord`, `ReasonCode` FROM `Lab_Transfer_Line_hist` WHERE 1
                        
};

//==========================================================================================



?>