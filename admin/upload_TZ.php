
<html>
<head><title>Upload Time zone</title></head>
<body>
<?php
include ("../setup/config.php");
echo ("1<br>");

  $handle = @fopen("TZ.txt", "r");
  if ($handle) {
    $i = 0;
    $j = 0;
    $InsertedLines=0;
    $UpdatedLines=0;
    $MaxVal=6;
    while (!feof($handle)) {
      $i++;
      $buffer = fgets($handle, 4096);
      //echo "<br>".$buffer;
      if (strlen ($buffer) !=0) {

        list($AddTime,$TZName) =  split(";", $buffer, 2);
        
        $TZ="";
        $pos=stripos ($TZName,'(');
        if ( $pos!==false) {
          $indx_beg=$pos+1;
          $pos=stripos ($TZName,')');
          if ( $pos!==false) {
            $TZ= substr ($TZName, $indx_beg, $pos-$indx_beg);
          }
          $TZName=iconv('Windows-1251', 'UTF-8', $TZName);       
        }
        
        //echo "<br>".$TZ;

        if ( $TZ != "") {
  	  $query = "select TZ_Name FROM TZ WHERE TZ_Name='$TZ'";
          $sql2 = mysql_query ($query)
                 or die("Invalid query:<br>$i $query<br>" . mysql_error());
 
          if ($dp = mysql_fetch_object($sql2)) {
            echo (" Update $TZ <br>");
            $query = "update TZ set TZ_Description='$TZName', AddToGMT='$AddTime'
              	         where (TZ_Name='$TZ')";
            $UpdatedLines++;
            $sql2 = mysql_query ($query)
	                or die("Invalid query:<br>$i $query<br>" . mysql_error()); ;
          }
          else {
            $query = "insert into TZ (TZ_Name, TZ_Description, AddToGMT) values ".
                    "('$TZ', '$TZName', '$AddTime')";
            $InsertedLines++;
            $sql2 = mysql_query ($query)
	                or die("Invalid query:<br>$i $query<br>" . mysql_error()); ;

          };
        }
      }
    }
    echo("uploaded $i lines<br>");
    echo("Inserted $InsertedLines lines<br>");
    echo("Updated  $UpdatedLines lines<br>");
    fclose($handle);
  }
?>
</body>
</html>
				         