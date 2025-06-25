<?php

session_start();

include ("../setup/common_pg.php");

?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>XML tables upload</title></head>
<body>
<?php

CheckRight1 ($pdo, 'Admin');



$FileName='XmlTables';
echo("
 Information Vladislav +7(903) 736 7000
 <form method='post' action='upload_$FileName.php' enctype='multipart/form-data'>
   <table border='0'>
   
   <tr>
     <td>Upload $FileName: XML file with Tables description</td>
    </tr>
    <tr>
      <td><input type='file' value='File name:' name='userfile'></td>
    </tr>
    <tr>
      <td align=right><input type='submit' value='Upload'></td>
    </tr> 
    </table>
 </form>");
?>
</body>
</html>
