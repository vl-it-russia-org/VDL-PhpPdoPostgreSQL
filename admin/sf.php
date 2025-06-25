<?php
session_start();

include ("../setup/config.php");
include ("set_passw.php");
?>
<html>
<head>
<title>Vladislav +7 (903) 736 7000</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
</head>
<body>
<?php

// >>> точка входа <<<
echo("Vladislav +7 (903) 736 7000<br>");

$adm='';

if (isset($_SESSION['login'])) {
  if ( isset($_SESSION['admin_login'])) {
    $adm='adm';
  } ;
}
else 
  die(); // здесь если функция вернула false то выполняется die()

echo 'Good day!, ' . $_SESSION['login'];
?>
<H2>Sales Force</H2>
<br><a href="../SF-PHP/frm_upload_file.php">Upload price list to Sales Force</a>
<br><a href="../SF-PHP/frm_upload_file_napr.php">Upload Napravlenia</a>
<br><a href="../SF-PHP/frm_upload_file_napr_new.php">New Upload Napravlenia</a>
<br><a href="../SF-PHP/frm_upload_file_series.php">Upload Kontaktor Series</a>
<hr>
<H3>Sales Force report</h3>
<ul>
<li><a href="../SFReports/FrmUpload_AllZip.php">Upload all zip file</a></li>
<li><a href="../SFReports/FrmUpload_Account.php">Upload Account file</a></li>
<li><a href="../SFReports/FrmUpload_Product2.php">Upload Product file</a></li>
<li><a href="../SFReports/FrmUpload_Opportunity.php">Upload Opportunity file</a></li>
<li><a href="../SFReports/FrmUpload_ProjectMember.php">Upload Project member file</a></li>
<li><a href="../SFReports/FrmUpload_User.php">Upload User file</a></li>
<li><a href="../SFReports/FrmAddUsersToSFRepFilter.php">Add User Filter</a></li>
</ul>
<hr>
<ul>
<li><a href="../SFReports/PrintTest.php?RepTypeId=1" target='SF-Rep'>Start Krivobokov report</a></li>
<li><a href="../SFReports/PrintReport.php?RepTypeId=5" target='SF-Rep'>Start Maksimov 1 report SKS+KNS</a></li>
<li><a href="../SFReports/PrintReport.php?RepTypeId=6" target='SF-Rep'>Start Maksimov 2 report Offices</a></li>
<li><a href="../SFReports/PrintReport.php?RepTypeId=7" target='SF-Rep'>Start Farkhutddinov report (Ural)</a></li>
<li><a href="../SFReports/PrintReport.php?RepTypeId=8" target='SF-Rep'>Start Beresneva report (Center)</a></li>
<li><a href="../SFReports/PrintReport.php?RepTypeId=9" target='SF-Rep'>Start Kashuba report (South)</a></li>
<li><a href="../SFReports/PrintReport.php?RepTypeId=10" target='SF-Rep'>Start Komarov report (North-West)</a></li>
<li><a href="../SFReports/PrintReport.php?RepTypeId=11" target='SF-Rep'>Start Nurtdinov report (Volga)</a></li>
<li><a href="../SFReports/PrintReport.php?RepTypeId=12" target='SF-Rep'>Start Severukhin report (W.Siberia)</a></li>
<li><a href="../SFReports/PrintReport.php?RepTypeId=13" target='SF-Rep'>Start Udalov report (East Sib, Far East)</a></li>
<li><a href="../SFReports/PrintReport.php?RepTypeId=14" target='SF-Rep'>Start Chatarova Rep1 (Lux)</a></li>
<li><a href="../SFReports/PrintReport.php?RepTypeId=15" target='SF-Rep'>Start Chatarova Rep2 (Hotels)</a></li>
</ul>

</body>
</html>
															