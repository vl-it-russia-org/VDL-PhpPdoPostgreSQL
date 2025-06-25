<?php
session_start();
?><html>
<head><title>User update</title></head>
<body>
<?php
include ("../setup/common.php");
include ("../commoni.php");
  
  $usrlogin = addslashes ($_REQUEST['usr']);
  if ($usrlogin== '') {
    die ('No login');
  }
  echo ("<br>$usrlogin<br>");
  if (!empty($usrlogin)) {
    $query="select SFUser, usr_pwd, usr_pwd, email, passwd_last_change  from usrs where usr_id='$usrlogin'";
    $sql = $mysqli->query ($query) 
                or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);

    if ($dp = $sql->fetch_object()) {
      $SFUser='';//$dp->SFUser;
      $Pwd   =$dp->usr_pwd;

      //require_once ('../../SF-PHP/soapclient/SforceEnterpriseClient.php');

      //$mySforceConnection = new SforceEnterpriseClient();
      //$mySforceConnection->createConnection("../../SF-PHP/wsdl.xml");


      //require_once ('../../SF-PHP/login_sf.php');
      //$mylogin = MakeLoginSF ($mySforceConnection);

      set_time_limit(180);
      //if ($SFUser=='') {
      //  $query = "select Id, WebAddPass__c ".
      //          "from User where EnglishName__c='$usrlogin'";       
      //         
      //  $response1 = $mySforceConnection->query($query);          
      //  
      //  foreach ($response1->records as $record1) {
      //    $SFUser=$record1->Id;
      //    $sql = mysql_query ("update usrs set SFUser='$SFUser' where usr_id='$usrlogin'");
      //    echo ("<br>updated SFUser $usrlogin: $SFUser");   
      //  }
      //}
      //$UpdArr= array ();
      //$UpdArr[0]= new stdclass ();
      //$UpdArr[0]->Id = $SFUser;
      //$UpdArr[0]->WebAddPass__c = $Pwd;
      
     // $response = $mySforceConnection->update($UpdArr, 'User');
     // echo("<br> SF Web Pwd updated <br>");
     // print_r ( $response ); 

     // echo ('<br>SF user '.$usrlogin.' has been updated.');
      //======================================================================
      $NeedChange= GetUserParam ($mysqli, 'CalculServer', '0002', $usrlogin );
      echo ("<br>Calcul server update ? -- $NeedChange ");
      if ( $NeedChange=='Yes') {
        echo ("<br>Start Calcul server update ");

        $ch = curl_init();

        $Url="https://calcul.kontaktor.ru/FormsI/SetUserPass.php";
        curl_setopt($ch, CURLOPT_URL, $Url);
        curl_setopt($ch, CURLOPT_POST, 1);
        
        //curl_setopt($ch, CURLOPT_POSTFIELDS,
        //            "postvar1=value1&postvar2=value2&postvar3=value3");

        // in real life you should use something like:

        $DateT=date("Y-m-d H:i:s");
        $Usr=$usrlogin;
        $Usr2 ='vlad_lev';
        
        $HId = MakeAdminRec ($mysqli, '', "CalcSrvPass", $Usr, $DateT, 'Update Calc server pass');
      
        $Chk=md5( "$Usr $Usr2 $DateT $HId");

        $Sec=base64_encode($Pwd);
        $email=base64_encode($dp->email);
        $passwd_last_change=base64_encode($dp->passwd_last_change);

        
        curl_setopt($ch, CURLOPT_POSTFIELDS, 
                  http_build_query( array ('Usr'=>$Usr, 'HId'=>$HId, 'Sec'=>$Sec,  
                                           'DT'=> $DateT, 'Chk'=>$Chk, 'email'=>$email, 
                                           'passwd_last_change'=>$passwd_last_change)));

        // receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec ($ch);

        echo ( "<br> Return server: ");
        
        echo ("SO=$server_output ");
        
        //die ();

        $Srv= mb_substr($server_output, 0, 5);
        
        //echo ("<br>Srv: $Srv<br>");

        if ( $Srv =='CODE=') {
          //print_r( $server_output);
          $Rest= substr($server_output, 5);
          
          $Res=  base64_decode($Rest);

          echo ("<br> Result = $Res ");
          //foreach ($ResArr as $Art=> $Arr) 
          //  $ItemNo = addslashes ($Art);
          //  $Price  = addslashes ($Arr['Price']);
          //  $Curr   = addslashes ($Arr['Currency']);
        }
      }
      //======================================================================
      echo ("<br><hr><a href='users_list.php?usrlogin=$usrlogin'>User card</a>");
    
    
    }
    else {
      echo ("Error: No user with name $usrlogin");
    }
  }
  else {
    echo ('Error: Empty user');
  }
  AdminFooter ();

?>
</body>
</html>
  
  
 


  
