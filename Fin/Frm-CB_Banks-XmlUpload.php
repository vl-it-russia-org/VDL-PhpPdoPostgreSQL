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
<title>CB_Banks Card</title></head>
<body>
<?php
// Checklogin1();
CheckRight1 ($pdo, 'Fin');

 
  echo ("<form method='post' action='CB_Banks-UploadXml.php' enctype='multipart/form-data'>
   <table border='0'>
   <tr>
     <td>Upload xml file to CB_Banks from CB RF reference file:<a href='https://cbr.ru/s/newbik'>CB RF file</a></td>
    </tr>
    <tr>
      <td><input type='file' accept='text/xml' value='File name:' name='userfile'></td>
    </tr> ".
    "<tr>".
     //"<td>Add lines to project: <input type=checkbox name='AddToProject' value=1></td>".
     //accept='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    "</tr>".

    "<tr>
      <td align=right><input type='submit' value='Upload'></td>
    </tr> 
    </table>
 </form>");
  //---------------------------------------------------------------------------------  
?>
</body></html>