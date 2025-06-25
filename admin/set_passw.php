<?php  
mb_internal_encoding("UTF-8");
//---------------------------------------------------------------------------
function UserLogin () {
isset($_SESSION['login']) or 
  die('You are not login.<br>'.
        iconv('Windows-1251', 'UTF-8', '�� �� �����').'<br><br>'.
      '<a href="../admin/index.php">Login page<br>'.
      iconv('Windows-1251', 'UTF-8', '��������� ��� �����').'</a>');
};
//---------------------------------------------------------------------------
//-----------------------------------------------------------------------------------
function ToUtf ($Str) {
  return iconv('Windows-1251', 'UTF-8', $Str);
}
//-----------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------
function BeginProc ($HaveOrd=0, $Chapter='') {  
  
  //echo ("<br>Ch:$Chapter<br>");

  if ($Chapter=='') {
    if ( !isset($_SESSION['login']) ) { 
       
      $Uri=$_SERVER['REQUEST_URI'];
      $LN=base64_encode($Uri);
     
    die(
    '<html>
    <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" /></head><body>'.
    'You are not login.<br>'.ToUtf('�� �� �����').'<br><br>'.
        '<a href="../admin/index.php?Aftr='.$LN.'">Login page<br>'.ToUtf('��������� ��� �����').
        '</a></body></html>');

    }
  }
  else 
  if ($Chapter=='ALL') {
    if ( !isset($_SESSION['login']) and  !isset($_SESSION['PL-login']) ) { 
       
      $Uri=$_SERVER['REQUEST_URI'];
      $LN=base64_encode($Uri);
     
    die(
    '<html>
    <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" /></head><body>'.
    'You are not login.<br>'.ToUtf('�� �� �����').'<br><br>'.
        '<a href="../adv/admin/index.php?Aftr='.$LN.'">Login page<br>'.ToUtf('��������� ��� �����').
        '</a><br><br>'.
        "<a href='../ExtProj/Login.php'>Login for External persons</a>".
        '</body></html>');

    }
  }
  else {
    if ( !isset($_SESSION['PL-login']) ) { 
       
      $Uri=$_SERVER['REQUEST_URI'];
      $LN=base64_encode($Uri);
     
    die(
    '<html>
    <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" /></head><body>'.
    'You are not login.<br>'.ToUtf('�� �� �����').'<br><br>'.
        '<a href="../ExtProj/Login.php?Aftr='.$LN.'">Login page<br>'.ToUtf('��������� ��� �����').
        '</a></body></html>');

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

  //echo ("<br>ch2:$Chapter<br>");

  foreach ($_REQUEST as $TT=> $Val) {
    if (! is_array($Val)) {
      $_REQUEST[$TT]=strip_tags($Val);
    }
  }
}
//-----------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------
function addslashSs ($XX) {
  return (addslashes (strip_tags(trim($XX))));
}
//-----------------------------------------------------------------------------------
function ExpNumber ($XX) {
  return (mb_substr($XX,0, 15)+0);
}
//-----------------------------------------------------------------------------------


function MakeAdminRec ($vUsr, $vCode, $vParam1, $vParam2, $vTxt) {
  
  //$itemNo = iconv( "WINDOWS-1251", "UTF-8", 
  // mysql_real_escape_string($itemNo));
      
  $query = "INSERT INTO admin_protocol (code, param1, param2, description, user_id) 
           VALUES 
	         ('$vCode', '$vParam1', '$vParam2', '$vTxt', '$vUsr')";

  $sql2 = mysql_query ($query)
	    or die("Invalid query:<br>$i $query<br>" . mysql_error());
	     
  return mysql_insert_id();
};

function CheckAdmin () {
  if (!isset($_SESSION['admin_login'])) {
    die ('Not admin');
  }
};

function GetUserParam ($ParamName) {
  $ResStr = $_SESSION [$ParamName];
  if ($ResStr =='') {
    $USR=  $_SESSION['login'];
    
    $query = "select Value from ParamVal ".
             "where ParamType='0001' and ParamNo='$ParamNo' and ID='$USR'";

    $sql2 = mysql_query ($query)
	    or die("Invalid query:<br>$query<br>" . mysql_error());
    if ($dp = mysql_fetch_object($sql2)) {
      $_SESSION [$ParamName]=$dp->TextVal;
      $ResStr = $_SESSION [$ParamName];
    }
    else {
      $_SESSION [$ParamName]="_$ParamName";
      $ResStr = "_$ParamName";    
    };
  };
	     
  return $ResStr;
};

function GetStr ($Str, $LangP='') {
  $ResStr = '';
  $lang='';

  if ($LangP!='') {
    $lang=$LangP;
  }
  else {
    $lang= $_SESSION ['LANG'];
    if ( $lang=='') {
      $_SESSION ['LANG'] = GetUserParam ('LANG');
      $lang= $_SESSION ['LANG'];
    };
  }  
  
  if ( $lang=='') {
    $lang= 'RU';
  };

  //echo (" AdvLang:$lang LangP:$LangP $Str ");

  if (!empty($_SESSION['STR_'.$Str][$lang])){
    $ResStr = $_SESSION ['STR_'.$Str][$lang]; 
  };


  if ($ResStr =='') {
    $query = "select TextVal from TranslationText ".
             "where ID='$Str' and Lang='$lang'";

    $sql2 = mysql_query ($query)
	    or die("Invalid query:<br>$query<br>" . mysql_error());
    if ($dp = mysql_fetch_object($sql2)) {
      $_SESSION ['STR_'.$Str][$lang]=$dp->TextVal;
      $ResStr = $_SESSION['STR_'.$Str][$lang];
    }
    else {
      $ResStr = "_$Str";    
    };
  };
	     
  return $ResStr;
};

function GetEnum ($EnumName, $EnumVal, $Lang='') {
  $lang= $_SESSION ['LANG'];
  if ($Lang!='') {
    $lang= $Lang;
  }
  if ( $lang=='') { 
    $lang= 'RU';
  }
  
  $ResStr = $_SESSION ['ENUM_'.$EnumName."_$EnumVal"][$lang];
  
  if ($ResStr =='') {

    $query = "select EnumDescription from EnumValues ".
             "where EnumName='$EnumName' and EnumVal='$EnumVal' and Lang='$lang'";

    $sql2 = mysql_query ($query)
	    or die("Invalid query:<br>$query<br>" . mysql_error());
    if ($dp = mysql_fetch_object($sql2)) {
      $_SESSION ['ENUM_'.$EnumName."_$EnumVal"][$lang]=$dp->EnumDescription;
      $ResStr = $_SESSION ['ENUM_'.$EnumName."_$EnumVal"][$lang];
    }
    else {
      $ResStr = "$EnumName _$EnumVal";    
    };
  };
	     
  return $ResStr;
};
//-------------------------------------------------------------------------
function EnumSelection ($EnumName, $Name, $StdVal ) {
  $ResStr = '';
  if ($ResStr =='') {
    $ResStr="<select name=$Name >";
    $lang= $_SESSION ['LANG'];
    if ( $lang=='') 
      $lang= 'RU';
    
    $query = "select EnumVal, EnumDescription from EnumValues ".
             "where EnumName='$EnumName' and Lang='$lang'";

    $sql2 = mysql_query ($query)
	    or die("Invalid query:<br>$query<br>" . mysql_error());
    
    while ($dp = mysql_fetch_array($sql2)) {
      $Sel='';
      if ( $dp['EnumVal'] == $StdVal ) {
        $Sel= ' selected ';
      }
      $ResStr.="<option value=".$dp['EnumVal']." $Sel>".
               $dp['EnumDescription'].'</option>'; 
      
    }
    $ResStr .= '</select>';
  
  };
	     
  return $ResStr;
};
//-------------------------------------------------------------------------
function EnumSelectionEmpty ($EnumName, $Name, $StdVal ) {
  $ResStr = '';
  if ($ResStr =='') {
    $ResStr="<select name=$Name >";
    
    $ResStr.="<option value=''>".
               GetStr('EMPTY').'</option>'; 
    
    $lang= $_SESSION ['LANG'];
    if ( $lang=='') 
      $lang= 'RU';
    
    $query = "select EnumVal, EnumDescription from EnumValues ".
             "where EnumName='$EnumName' and Lang='$lang'";

    $sql2 = mysql_query ($query)
	    or die("Invalid query:<br>$query<br>" . mysql_error());
    
    while ($dp = mysql_fetch_array($sql2)) {
      $Sel='';
      if ( $dp['EnumVal'] == $StdVal ) {
        $Sel= ' selected ';
      }
      $ResStr.="<option value=".$dp['EnumVal']." $Sel>".
               $dp['EnumDescription'].'</option>'; 
      
    }
    $ResStr .= '</select>';
  };
  return $ResStr;
};


//-------------------------------------------------------------------------
//-------------------------------------------------------------------------


function GetStr3 ($Str, $Val1, $Val2, $Val3) {
  $ResStr = GetStr ($Str);
  return printf ($ResStr, $Val1, $Val2, $Val3);
};



function GetNewPass () {
  $dig = '0123456789';                      // 3
  $spec= '_-+*^`~!@#$%,.><?{}[]()' ;         // 2
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
      $vals[$s]=substr($spec, rand (1,23), 1);   
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

//------------------------------------------------
function AdminFooter () {
  echo ('<hr>
  <br><a href="http://intrasite.it-russia.org/legrand/ftp_view_uly/index.php">EDI ������ Firelec</a>
  <br>������ �� ���������� ������� �� ������<br>'.
        '<a href="../indx1.php">Item Qty check/���-�� �������� ��������</a><br>'.
        '<a href="../Labels/frm_upload_file.php">Upload MO/��������� ���������������� ������</a><br>'.
        '<a href="../Labels/Label_list.php">MO Label list/�������� ������� �� ������������</a><br>');

  if (isset($_SESSION['admin_login'])) {

  echo ('<hr><br>Administrative/���������������� �����<br>'.
        '<a href="../admin/indx1.php">Upload csv data/�������� ������ CSV</a><br>'.
        '<a href="../admin/upload_fir.php">Upload zipped XML file from Firelec COM data/�������� ZIP ����� �������</a><br>'.
        '<a href="../admin/new_user.php">Insert new user/�������� ������ ������������</a><br>'.
        '<a href="../admin/au_BT_file_ftp_get.php">Upload Firelec data</a><br>'.
        '<a href="../admin/sf.php" target="SalesForce"><b>Sales Force</b></a><br>'.
        '<a href="../admin/users_list.php">User list/������ �������������</a><br>'.
        '<a href="../admin/protocol_view_admin.php">Protocol view admin/�������� ������ ��������������</a><br>'.
        '<a href="../admin/protocol_view.php">Protocol view/�������� ������</a><br>'.
        '<a href="../reports/Report_list.php">Report setup/��������� �������</a><br>'.
        '<hr><a href="../setup/MakeDump.php">Database dump</a><br>'
        );

  };
  echo ('<a href="../admin/index.php?logout">Logout/�����</a>');

};

//==========================================================================================
//==========================================================================================
function Able ( $TestId, $TestSubId ) {
  $Res= false ;

  $UserId= $_SESSION['login'];
  $query = "select HaveRight from URValues ".
           "where (UserId='$UserId') and (UserRight='$TestId') and (SubId='$TestSubId') ";
  
  $sql2 = mysql_query ($query)
                 or die("Invalid query:<br>$query<br>" . mysql_error());

  if ($dp2 = mysql_fetch_array($sql2)) {
    if ( $dp2['HaveRight']==1) 
      $Res = true;  
  }
  return $Res;                         
};
//==========================================================================================
function GrantAble ( $TestId, $TestSubId ) {
  $Res= false ;

  $UserId= $_SESSION['login'];
  $query = "select GrantOption from URValues ".
           "where (UserId='$UserId') and (UserRight='$TestId') and (SubId='$TestSubId') ";
  
  $sql2 = mysql_query ($query)
                 or die("Invalid query:<br>$query<br>" . mysql_error());

  if ($dp2 = mysql_fetch_array($sql2)) {
    if ( $dp2['GrantOption']==1) 
      $Res = true;  
  }
  return $Res;                         
};
//==========================================================================================
function HaveRight ( $TestId, $TestSubId ) {
  $Res= Able ($TestId, $TestSubId ) ;
  if (! $Res) {
    die ( '<br>'.GetStr('NoUserRight').$TestId.' '.$TestSubId );
  }
};
//==========================================================================================
function GrantHaveRight ( $TestId, $TestSubId ) {
  $Res= GrantAble ($TestId, $TestSubId ) ;
  if (! $Res) {
    die ( '<br>'.GetStr('GrantNoUserRight').$TestId.' '.$TestSubId );
  }
};

//==========================================================================================
//==========================================================================================
//==========================================================================================
function GetUserFullName ( $user_id ) {
  $usrlogin=$user_id;
  $Res='';
  if (!empty($usrlogin)) {
    $sql = mysql_query ("select description 
           from usrs where usr_id='$usrlogin'");
    if ($dp = mysql_fetch_object($sql)) {

      $Res=$dp->description;
    }
  }

  return $Res;
};

//------------------------------------------------


require_once('sendmail.php');

function SendPasswToUser ( $user_id ) {
  $usrlogin=$user_id;
  if (!empty($usrlogin)) {
    $sql = mysql_query ("select usr_id, email, usr_pwd 
           from usrs where usr_id='$usrlogin'");
    if ($dp = mysql_fetch_object($sql)) {

      if (!empty($dp->email)) { 
        $m= new Mail; // create the mail
        $m->From( "vladislav.levitskiy@kontaktor.ru" );
        $m->To( $dp->email );
        $m->Subject( "Your passwd to Legrand Labels server" );

        $message= "Good day!\nYour login is:".
                   $user_id."\nYour passwd:".$dp->usr_pwd."\n".
                   "https://project.kontaktor.ru/legrand/Labels/ \n".
                   "Support and info: Vladislav +7 (903) 736 7000\nThanks.\n\n";
  
        $m->Body( $message);      // set the body
        $m->Send();               // send the mail
      }
    }
  }
};

//=============================================================================

function SendPasswToUser2 ( $user_id, $Passwd ) {
  $usrlogin=$user_id;
  if (!empty($usrlogin)) {
    $sql = mysql_query ("select * 
           from TestUsers where UserId='$usrlogin'");
    if ($dp = mysql_fetch_array($sql)) {

      if (!empty($dp['UsrEMail'])) { 
        $m= new Mail; // create the mail
        $m->From( "vladislav.levitskiy@kontaktor.ru" );
        $m->To( $dp['UsrEMail'] );
        $m->Subject( "Your password to Legrand Test server" );


        $message= "Good day!\n<br>Your login is:".
                   $user_id."\n<br>Your passwd:".$Passwd."\n<br>".
                   "https://gator3196.hostgator.com/~vladlev/legrand/Training/run.php\n\n<br>".
                   "Support and info: Vladislav +7 (903) 736 7000\nThanks.";
  
        $m->Body( $message);      // set the body
        $m->Attach( "../admin/Instruction.pdf", "application/pdf") ;  
        $m->Send();               // send the mail
      }
    }
  }
};


?>