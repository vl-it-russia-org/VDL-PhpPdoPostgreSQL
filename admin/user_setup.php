<?php
session_start();

include ("../setup/common.php");
BeginProc();

echo ('<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>User params setup</title></head>
<body>');

//echo '<br>User: ' . $_SESSION['login'];
$UserId=addslashes($_REQUEST['UserId']);
if ($UserId==''){
  $UserId=$_SESSION['login'];
}
else {
  CheckRight1 ($mysqli, 'ExtProj.Admin');
}

$query = "select description, email, phone, WebCookie ". 
          "FROM usrs ".
          "WHERE usr_id='$UserId' ";
  
$sql2=$mysqli->query($query)
                  or die ("Invalid query:<br>$query<br>Line:".__LINE__." ".$mysqli->error);

$UserName='';
$UserMail='';
$UserPhone='';

echo ('<h3>'.GetStr($mysqli, 'User preferences').' '.$UserID.'</h3>');
  
if ($dp = $sql2->fetch_object()) {
  //print_r ($dp);
  $UserName =$dp->description;
  $UserMail =$dp->email;
  $UserPhone=$dp->phone;
  $WC='';
  if ($dp->WebCookie==1) {
    $WC=' checked';
  }

  echo ('<form method=post action="UserSetupChanged.php">'.
       '<input type="hidden" name=UserId Value="'.$UserId.'"><table><tr>'.
       '<td align="right">'.GetStr($mysqli, 'UserName')."</td><td><b>".$UserName."</b></td></tr><tr>".
       '<td align="right">'.GetStr($mysqli, 'UserMail').'</td><td><input type=email size=30'.
       ' length=50 name=UserMail value='.$UserMail.'></td></tr><tr>'.
       '<td align="right">'.GetStr($mysqli, 'UserPhone').'</td><td><input type=phone size=30'.
       ' length=50 name=UserPhone value="'.$UserPhone.'"></td></tr>'.
       '<tr><td align="right">WebCookie</td>'.
       "<td><input type=checkbox Name=WebCookie value=1 $WC></td>");
       if ( $dp->WebCookie) {
          
          if ( $_COOKIE['AL'] != '') {
            echo ("<br><a href='SetWebLogin.php?Reset=1'>Reset Web Login</a>");
          }
          else {
            echo ("<br><a href='SetWebLogin.php'>Set Web Login</a>");
          } 
       }
  echo ('</tr></table>'.
       '<input type="submit" Value="'.
             GetStr($mysqli, 'CHANGE').'"></td>'.
             "<td><a href='CopyFIO.php?UserId=$UserId'><img src='../Img/Sort2.png' title='".GetStr($mysqli, 'CopyFIO')."'></a></td>".
             '</form>');
  
  $ArrParams = array ( '0001', '0002') ;

  foreach ( $ArrParams as $ParamGroup ) {
  
  //---------------------------------------------------------------------------------
  $query = "select * from Params where ParamType='$ParamGroup'"; 
  
  $sql=$mysqli->query($query)
                  or die ("Invalid query:<br>$query<br>Line:".__LINE__." ".$mysqli->error);      
  $n=0;
  $r=0;

  echo ('<hr><form method=post action="UserSetupParamChanged.php">'.
       '<input type="hidden" name=UserId Value="'.$UserId.'">'.
       '<input type="hidden" name=ParamType Value="'.$ParamGroup.'"><table>');
  while ($dp1 = $sql->fetch_object()) {
    $classVal=''; 
    if ($r==2) {
      $r=0; 
      echo ('</tr>');
    };

    if ($r==0) {
      $n++;
      if ($n==2) {
        $classVal=' class="even"';
        $n=0;
      };
      echo ("<tr$classVal>");
    };
    
    $r++;
    
    $CurrVal='';
    $query = "select Value from ParamVal where ".
             "ParamType='$ParamGroup' and ParamNo='".$dp1->ParamNo."' and ID='$UserId'";
  
    $sql3=$mysqli->query($query)
                  or die ("Invalid query:<br>$query<br>Line:".__LINE__." ".$mysqli->error);    
    if ($dp3 = $sql3->fetch_object()) {
      $CurrVal=$dp3->Value;    
    };

    echo('<td>'.GetStr($mysqli, 'PAR_'.$dp1->ParamType.'_'.$dp1->ParamNo).':');
    if ($dp1->ValueType !='select') {
      echo ('<input type=text size=30'.
       ' length=50 name="PAR_'.$dp1->ParamType.'_'.$dp1->ParamNo.'" value="'.$CurrVal.'">');
    }
    else{
      $Vals=array();
      $Buf =$dp1->ValuePossibleList;

      while ( $Buf != '') {
        $i=strpos ($Buf, ',');
        if ($i!==false) {
          if ( $i>0) {
            $Val= substr ($Buf, 0, $i);
            $Vals[]= $Val;
            $Buf=substr ($Buf, $i+1);
          }
          else {
            $Vals[]= $Buf;
            $Buf='';
          };
        }
        else {
          $Vals[]= $Buf;
          $Buf='';          
        }; 
      };
      echo( '<select name="PAR_'.$dp1->ParamType.'_'.$dp1->ParamNo.'">');

      foreach ($Vals as $Val) {
        $Sel='';
        if ($Val == $CurrVal) {
          $Sel=' selected ';
        };
        
        echo ('<option '.$Sel.' value="'. $Val .'">'.$Val.'</option>');
      };  
      echo ('</select></td>');   
    }  
  };
  
  $classVal=''; 
  if ($r==2) {
    $r=0; 
    echo ('</tr>');
  };

  if ($r==0) {
    $n++;
    if ($n==2) {
      $classVal=' class="even"';
      $n=0;
    };
    echo ("<tr$classVal>");
  };

  echo ('<td><input type="submit" Value="'.
             GetStr($mysqli, 'CHANGE').'"></td></tr></form></table>');
}

//--------------------------------------------------------------------------
  if ($UserMail != '' ) {

    echo("<hr><h4>".GetStr($mysqli, 'RespPersons')."</h4>");
    $query = "select * FROM RespPersons where ContactId='$UserMail' order by ObjId,Param1,enContactType,ContactId ";
    $sql2 = $mysqli->query ($query)
              or die("Invalid query:<br>$query<br>" . $mysqli->error);

    echo('<table><tr class=header>');
    echo('<th>'.GetStr($mysqli, 'ObjId').'</th>');
    echo('<th>'.GetStr($mysqli, 'Param1').'</th>');
    echo('<th>'.GetStr($mysqli, 'enContactType').'</th>');
    echo('<th>'.GetStr($mysqli, 'Description').'</th>');
    echo('<th>'.GetStr($mysqli, 'Rank').'</th>');
    $i=0;
    while ($dpL = $sql2->fetch_assoc()) {
      $i=NewLine($i);

      echo ("<td>{$dpL['ObjId']}</td>");
      echo ("<td>{$dpL['Param1']}</td>");
      echo ("<td align=center>");
      echo (GetEnum($mysqli, "ContactType", $dpL['enContactType'])."</td>");
      echo ("<td>{$dpL['Description']}</td>");
      echo ("<td align=center>");
      echo (GetEnum($mysqli, "RespRank", $dpL['Rank'])."</td>");
      
    }
    echo("</tr></table>");
    echo("<a href='RespPersonsCard.php?New=1&ObjId=$ObjId'>".GetStr($mysqli, "Add")."</a>");


  }
};
//========================================================================== 
  //echo ('<td><a href="ToPrintMO.php?LabNo='.$LabNo.'">Print MO</a></td>'.
  //      '</tr></table>');
AdminFooter (1);

?>
</body>
</html>
				       