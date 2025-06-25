
<html>
<head><title>Upload Items from XML file</title></head>
<body>
<?php
include ("../setup/config.php");
echo ("1<br>");

$size = $_FILES['userfile']['size'];
$name_temp = $_FILES['userfile']['tmp_name'];
$type = $_FILES['userfile']['type'];
$dir = BASE_DIR;
$real_name = "$dir/admin/tmp/file_tmp.xml";

echo ("$real_name<br>");


if (file_exists  ( $real_name )) {
  unlink ($real_name);
};

if (move_uploaded_file($name_temp, $real_name)) {
  echo ("Document downloaded<br>");
  
  $handle = @fopen($real_name, "r");
  if ($handle) {
    $i = 1;
    $j = 0;
    $InsertedLines=0;
    $UpdatesLines=0;
    $Vals=array ("", "", "", "", "", "", "");
    $MaxVal=6;
    while (!feof($handle)) {
      $buffer = fgets($handle, 4096);
      if (strlen ($buffer) !=0) {
        $pos=stripos ($buffer,'<Row');
        if ( $pos!==false) {
          $indx=0;
          $EndRow=false;
          while (! $EndRow ) {
          if (!feof($handle)) {  
            $buffer = fgets($handle, 4096);
            $pos=stripos ($buffer,'<Cell');
            if ( $pos!==false) {
              if ($indx < $MaxVal) {
                $pos=stripos ($buffer,'>', $pos+6);
                $buffer=substr ($buffer, $pos+1);
                $More=true;
                do {
                  $pos= stripos ($buffer,'</Data>');
                  if ($pos !== false) {
                    $More=false;
                    $Vals[$indx]= substr ($buffer, 0, $pos);
                    $indx++; 
                  } 
                  else {
                    if (!feof($handle)) { 
                      $buffer = $buffer+fgets($handle, 4096);
                    }
                    else {
                      $More=false;
                      die ('XML error.');
                    };
                  }
                } while ($More);
              }
            }
            else {
              $pos4=stripos ($buffer,'</Row>');
              if ($pos4 !== false) {
                $EndRow = true;
              };
            }
          }
          else {
            $EndRow = true;
            die ('XML error 2.');
          };
          };

	        $brand = '';
	        $art   = $Vals[0];
	        $descr = $Vals[1];

	        
	        for ($l=0; $l<5; $l++) {
	          echo ($l.':'.$Vals[$l] . ' ');
	        };
	        echo ('<br>');
	        
	        if ($i > 1 ) {    
	          if ($Vals[3]=='2') {
	            $brand='BT';
	          } 
	          else $brand='FIR';
	        

	          $query = "select ItemNo, Description FROM CO_ItemName WHERE FIRM='$brand' and ItemNo='$art'";
         
            $sql2 = mysql_query ($query)
                 or die("Invalid query:<br>$i $query<br>" . mysql_error());
    
 
            if ($dp = mysql_fetch_object($sql2)) {
              $NeedChange=false;
              if (($dp->Description != $descr)) {
                $NeedChange=true;
                echo ($i.'line: Have '.$dp->ItemNo);
                echo ('Need change<br>');
              }
              else {
                //echo (' Ok<br>');
              };
             
              if ($NeedChange) {
                $UpdatesLines=$UpdatesLines+1;
      
              	$query = "update CO_ItemName set Description='$descr'
              	          where (Firm='$brand') and (ItemNo='$art')";
                $sql2 = mysql_query ($query)
	                or die("Invalid query:<br>$i $query<br>" . mysql_error()); ;
              };
            }
            else {
              $InsertedLines=$InsertedLines+1;

              $query = "insert into CO_ItemName (Firm, ItemNo, Description, FamilyNo) 
                  values ('$brand', '$art', '$descr', 0)";  
              $sql2 = mysql_query ($query)
	               or die("Invalid query:<br>$i $query<br>" . mysql_error()); ;
	            
	            echo ($i.'line: Have '.$art); 
	            echo (' Inserted<br>');
            };

          }
	        $i = $i + 1;
          $j = $j + 1;
        };
      }
    }
    echo("uploaded $i lines<br>");
    echo("Inserted $InsertedLines lines<br>");
    echo("Updated  $UpdatesLines lines<br>");
    fclose($handle);
  }
};

?>
</body>
</html>
				         