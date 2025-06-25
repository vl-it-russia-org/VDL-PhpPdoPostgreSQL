<?php
session_start();
include ("../setup/common_pg.php");
BeginProc();

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>User setup</title></head>
<body>
<?php
//echo '<br>User: ' . $_SESSION['login'];
  //print_r ($_POST);

$UserId = $_REQUEST['UserId'];
if ($UserId != '' ) {
  if ($UserId != $_SESSION['login']) {
    CheckAdmin ();
  }
} 
else  {
  $UserId=$_SESSION['login'];
};

$query = "select * FROM \"usrs\" ".
          "WHERE \"usr_id\"=:UserId ";

$PdoArr = array();
$PdoArr['UserId']= $UserId;

try {
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);


  
$UserName='';
$UserMail='';
$UserPhone='';

echo ('<h3>'.GetStr($pdo, 'User preferences').' '.$UserID.'<h3>');
  
if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  //print_r ($dp);
  $UserName =$dp['description'];
  $UserMail =$dp['email'];
  $UserPhone=$dp['phone'];
  $WC='';
  if ($dp['WebCookie']==1) {
    $WC=' checked';
  }

  echo ('<form method=post action="UserSetupChanged.php">'.
       '<input type="hidden" name=UserId Value="'.$UserId.'"><table><tr>'.
       '<td align="right">'.GetStr($pdo, 'UserName')."</td><td><b>".$UserName."</b></td></tr><tr>".
       '<td align="right">'.GetStr($pdo, 'UserMail').'</td><td><input type=email size=50'.
       ' length=50 name=UserMail value='.$UserMail.'></td></tr><tr>'.
       '<td align="right">'.GetStr($pdo, 'UserPhone').'</td><td><input type=phone size=30'.
       ' length=50 name=UserPhone value="'.$UserPhone.'"></td></tr>'.
       '<tr><td align="right">WebCookie</td>'.
       "<td><input type=checkbox Name=WebCookie value=1 $WC></td>");
  if ( $dp['WebCookie']) {
          
          if ( $_COOKIE['AL'] != '') {
            echo ("<td> | <a href='SetWebLogin.php?Reset=1'>Reset Web Login</a></td>");
          }
          else {
            echo ("<td> | <a href='SetWebLogin.php'>Set Web Login</a></td>");
          } 
   }

       
   echo ("</tr>".
       '</table>'.
       '<input type="submit" Value="'.
             GetStr($pdo, 'CHANGE').'"></td></form><hr>');

echo ("<script>
function checkPass()
{
    //Store the password field objects into variables ...
    var pass1 = document.getElementById('pass1');
    var pass2 = document.getElementById('pass2');
    //Store the Confimation Message Object ...
    var message = document.getElementById('confirmMessage');
    //Set the colors we will be using ...
    var goodColor = '#66cc66';
    var badColor = '#ff6666';
    //Compare the values in the password field 
    //and the confirmation field
    if(pass1.value == pass2.value){
        //The passwords match. 
        //Set the color to the good color and inform
        //the user that they have entered the correct password 
        pass2.style.backgroundColor = goodColor;
        message.style.color = goodColor;
        message.innerHTML = 'Passwords Match!'
    }else{
        //The passwords do not match.
        //Set the color to the bad color and
        //notify the user.
        pass2.style.backgroundColor = badColor;
        message.style.color = badColor;
        message.innerHTML = 'Passwords Do Not Match!'
    }
}  
</script>");  
  
  echo ('<form method=post action="SetWebLogin.php"><table>'.
         '<tr><td align="right">Reset</td>'.
         "<td><input type=checkbox Name=Reset value=1 $WC></td></tr>".
         '<tr><td align="right">Password (at least 4 digits):</td>'.
         "<td><input type=password Name=Pass1 size=10 id='pass1'></td></tr>".
         '<tr><td align="right">Password again:</td>'.
         "<td><input type=password Name=Pass2 size=10 id='pass2' onkeyup=\"checkPass(); return false;\"><br>
          <span id=\"confirmMessage\" class=\"confirmMessage\"></span>
         </td></tr>".
         '<tr><td colspan=2 align="right"><input type=submit></td></tr></table></form>');

  //---------------------------------------------------------------------------------
  $query = "select * from \"Params\" where \"ParamType\"='0001'"; 
  
  $STH = $pdo->prepare($query);
  $STH->execute();


  $query = "select \"Value\" from \"ParamVal\" where ".
           "\"ParamType\"='0001' and \"ParamNo\"=:ParamNo and \"ID\"=:UserId";
  
  $STHPar = $pdo->prepare($query);
  
  $PdoArr = array();
  $PdoArr['UserId']= $UserId;
  $PdoArr['ParamNo']= "";
      
  $n=0;
  $r=0;                 
  echo ('<form method=post action="UserSetupParamChanged.php">'.
       '<input type="hidden" name=UserId Value="'.$UserId.'">'.
       '<input type="hidden" name=ParamType Value="0001"><table>');
  while ($dp1 = $STH->fetch(PDO::FETCH_ASSOC)) {
    //echo ("<br> ");
    //print_r($dp1);
    
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

    $PdoArr['ParamNo']= $dp1['ParamNo'];
    
    $STHPar->execute($PdoArr);

    if ($dp3 = $STHPar->fetch(PDO::FETCH_ASSOC)) {
      $CurrVal=$dp3['Value'];    
    };

    echo('<td>'.GetStr($pdo, 'PAR_'.$dp1['ParamType'].'_'.$dp1['ParamNo']).':');
    if ($dp1['ValueType'] !='select') {
      echo ('<input type=text size=20'.
                   ' name="PAR_'.$dp1['ParamType'].'_'.$dp1['ParamNo'].'" value="'.$CurrVal.'">');
    }
    else{
      $Vals=array();
      $Buf =$dp1['ValuePossibleList'];

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
      echo( '<select name="PAR_'.$dp1['ParamType'].'_'.$dp1['ParamNo'].'">');

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
             GetStr($pdo, 'CHANGE').'"></td></tr></form>');
};    
  //echo ('<td><a href="ToPrintMO.php?LabNo='.$LabNo.'">Print MO</a></td>'.
  //      '</tr></table>');


  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }



?>
</body>
</html>
				       