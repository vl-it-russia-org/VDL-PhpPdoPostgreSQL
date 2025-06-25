<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();

//isset($_SESSION['admin_login']) or die('You are not login as Admin/�� �� ����� ��� Admin');
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Content-Language" content="ru">
    <link rel="stylesheet" type="text/css" href="../style.css">
    <link rel="icon" href="../favicon.ico" type="image/x-icon">
    <title>Admin menu</title></head>
<body>
<?php

CheckLogin1();

echo ('<table><tr valign=top><td><a href="../FormsI/RigtsSetup.php"> 0. '.GetStr($pdo, 'SetupRights').
    '</a><br>'.
    'SetupRights<br>');

echo ('<a href="EMailSubstFrm.php"> -- 0.1. '.GetStr($pdo, 'ReplaceEMails').
    '</a><br>'.
    'Replace e-mail for persons who leave company<br>');

echo ('<a href="../FormsI/TranslateFrm.php"> -- 0.2. Translate strings'.
    '</a><br>');
echo ('<a href="../FormsI/EnumFrm.php"> -- 0.3. Translate enum'.
    '</a><br>');

echo ('<a href="../FormsI/TabList.php"> -- 0.4. Table list'.
    '</a><br>');

echo ('<a href="../FormsI/AdmMatrixHeadList.php"> -- -- 0.4.1. Dependent fields'.
    '</a><br>');

echo ('<a href="../FormsI/AdmViewsList.php"> -- -- 0.4.2. View'.
    '</a><br>');



echo ('<a href="../FormsI/MenuList.php"> -- 0.5. Menu'.
    '</a><br>');

echo ('<a href="../FormsI/AdmNumberSeqList.php"> -- 0.8. Number sequence'.
    '</a><br>');

echo ('<a href="../FormsI/ComConstList.php"> -- 0.9. Const'.
    '</a><br>');
echo ('<a href="../FormsI/ParamsList.php"> -- 0.10. UserParams'.
    '</a><br>');
echo ('<a href="../FormsI/FrmSql.php"> -- 0.11. FrmSQL'.
    '</a><br><br>');




echo ('<a href="frm_upload_file.php"> 1. '.GetStr($pdo, 'UPLOAD_ITEM_XLS').
    '</a><br>'.
    'All items upload<br><br>');

echo ('<a href="frm_upload_mapics_items.php">2. Upload Item file from Mapics Item Interface</a><br>'.
    '<small>Base information about item: ItemNo, Item Description, stock UOM and Item type</small><br><br>');

echo ('<a href="../FormsI/email_recepientsList.php">3. Edit list of e-mail recepients</a><br>'.
    '<br>');

echo ('<a href="Location_list.php">4. Edit list of Locations</a><br>'.
    'Location code, Location decription for printing documents<br><br>');

echo ('<a href="ItemList.php">5. Edit list of Items</a> / <a href="SeriesList.php">5. Edit list of Series</a> <br>'.
    'ItemNo, Item Description, packaging options<br><br>');
echo ('<a href="UOM_List.php">6. Edit list of UOM</a><br>'.
    'UOM, Description UOM, Code UOM<br><br></td><td>');
echo ('<a href="MapicsOpers.php">7. Mapics opers</a><br> '.
    '<a href="MapicsApply.php"> -- 7.1. Mapics Apply Receipt (RP)</a><br> '.
    '<a href="ApplyReceiptPlan.php"> -- 7.2. Apply Plan Receipt</a><br>'.
    '<a href="SAUpply.php"> -- 7.3. Apply Mapics SA (shipment)</a><br> '.
    '<a href="SaveTransferLines.php"> -- 7.4. Copy to Sales Force and close (shipment)</a><br>'.
    'Operations from Mapics   <br><hr>');

echo ('<a href="../Items/Admin.php" target="_blank">8. Item MS</a>');

echo ('<br><a href="../ItemReq/" target=Items> -- 8.1. Item requests</a>');
echo ('<br><a href="../Log/" target=Logist> -- 8.2. Item logistics parameters</a>');

echo ('<br><a href="FrmUpload_MpxDiscount.php" target=MpxDsc>9. Upload discount</a>');

echo ('<br><br><a href="../SFMini/" target=__blank>10. <b>Sales Force Mini</b></a><br>'.
    'Projects, Meetings, Organizations, Contacts<br><br>');

echo ('<a href="../General/ReportNamesList.php" target=__blank>11. <b>SF Reports setup</b></a><br>'.
    'Setup standard report e-mail recepients<br><br>');

echo ('<a href="admin_protocolList.php" target=__blank>12. <b>Admin Log view</b></a><br>'.
    '');
echo ('<a href="SqlLogList.php" target=__blank> -- 12.05 <b>Tables structure SQL changes report</b></a>'.
    '<br>');
echo ('<a href="PrintUser_RightList.php" target=__blank> -- 12.10 <b>User rights report</b></a><br>'.
    '<br>');




echo ('<a href="../SI/" target=__blank>13. <b>Sales invoices</b></a><br>'.
    '<br>');

echo ('<a href="../ItemsStockLevels/" target=__blank>14. <b>Stock levels</b></a><br>');
echo (' -- <a href="../ItemsStockLevels/chk_have_stock_arr.php?usr=legrand&chk=legrand2014" target=__blank>14.1. <b> XML stock levels check</b></a><br>'.
    '<br>');

echo ('<a href="../ExtProj/" target=__blank>15. <b>External projects</b></a><br>'.
    //'<a href="../ExtWh/" target=__blank>15.W <b>External warehouse order</b></a><br>'.
    '<a href="../ExtProj/ExtProjectsList.php" target=__blank> -- 15.1 <b>External projects list dispatch</b></a><br>'.
    //'<a href="../ExtWh/ExtProjectsList.php" target=__blank> -- 15.1.W <b>External warehouse orders dispatch</b></a><br>'.
    '<a href="../ExtProj/ExtProj_NewsList.php" target=__blank> -- 15.2 <b>External projects News</b></a><br>'.
    '<a href="../ExtProj/SF_PricebookEntryList.php" target=__blank> -- 15.3 <b>Price list entry</b></a><br>'.
    '<a href="../ExtProj/AdminFrm.php" target=__blank> -- 15.10 <b>ExtProj Admin</b></a><br>'.
    '<br>');



echo ('<a href="../CustOrder/" target=__blank>16. <b>Mapics CO list (Orders)</b></a><br>'.
    '<a href="../Other/" target=__blank> -- 16.1. <b>Other countries Mapics CO list (Orders)</b></a><br>'.
    '<br>'.
    '</td><td>');

echo ('<a href="../WMS/" target=__blank>17. <b>WMS</b></a><br><br>');

echo ('<a href="../Docs/" target=__blank>18. <b>Documents confirmation</b></a><br>'.
    '<a href="../DocE/" target=__blank>-- 18.1. <b>Docs external</b></a><br>'.
    '<a href="../CustCert/" target=__blank>-- 18.2. <b>Customer certificates</b></a><br><br>'.
    '<a href="../Sert/" target=__blank>19. <b>Sertificates</b></a><br>'.
    '<a href="../Volga/FormsI/" target=__blank>20. <b>Labels Volga</b></a><br>'.
    ' -- <a href="../Volga/kontaktor/FormsI/" target=__blank>20.05 <b>Labels Kontaktor</b></a><br>'.
    '<br>'.
    '<a href="../Process/" target=__blank>21. <b>Process</b></a><br>'.
    '<br>'.
    '<a href="../ETIM/UserXMLList.php" target=__blank>22. <b>XML users</b></a><br>'.
    '<br>'.
    '<a href="../Tasks/TasksList.php" target=__blank>25. <b>Tasks</b></a><br>'.
    ' -- <a href="../Tasks/TasksNachList.php" target=__blank> 25.01. <b>Tasks Nachinov department</b></a><br>'.
    '<br>'.
    '<a href="../EMailSend/" target=__blank>26. <b>EMail sends</b></a><br>'.
    '<a href="StopMail.php"> -- 26.01 <b>Stop mail send</b></a><br>'.
    '<a href="SetDebug.php"> -- 26.05 <b>Debug SET/Clear</b></a><br>'.
    '<a href="../EMailSend/RepMailReceipientsList.php" target=__blank> -- 26.10 <b>Report mail receipients</b></a><br>'.

    '<br>'.
    '<a href="../ReportChk/" target=__blank>27. <b>Report check</b></a><br>'.
    '<a href="../DMX/" target=__blank>28. <b>DMX</b></a><br>'.
    '<a href="../Coupon/" target=__blank>29. <b>Coupon</b></a><br>'.
    '<a href="../KLADR/KladrBaseUpload.php" target=__blank>30. <b>KLADR</b></a><br>'.
    '<br>'
);

//===================================================================================
echo ('<hr><a href="../dump/MakeDump.php" target=__blank>50. <b>Save DB</b></a><br>'.

      '<br>'.
      '<hr><a href="../FormsI/EdiDispatchedFiles.php" target=__blank>51. <b>File struct</b></a><br>'.
      '<br>'
      );

echo ('</td></tr></table>');

?>
</body>
</html>
