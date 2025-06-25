<?php
session_start();
isset($_SESSION['login']) or die('вы не вошли');
echo 'Здравствуйте, ' . $_SESSION['login'].'<br>';
include "set_passw.php"
?>

<html>
<head><title>Vladislav +7(903) 736 7000</title></head>
<body>
<?php


echo("
 Information Vladislav +7(903) 736 7000
 <form method='post' action='upload_fir_data.php' enctype='multipart/form-data'>
   <table border='0'>
   <tr>
     <td>Upload data into Free stock (csv file)</td>
    </tr>
    <tr>
      <td>Choose ZIP file with Free stock (XML)</td> 
      <td><input type='file' size=70 value='File name' name='userfile'></td>
    </tr>
    <tr>
      <td><input type='submit' value='Upload'></td>
    </tr> 
    </table>
 </form>
    ");

 AdminFooter ();
?>
</body>
</html>
