<?php
  die();
  echo("<hr><h4>".GetStr($pdo, 'Vendors')."</h4>");
  $query = "select * FROM Vendors where Id='$Id' order by Id ";
  $sql2 = $pdo->query ($query)
            or die("Invalid query:<br>$query<br>" . $pdo->error);

  echo('<table><tr class=header>');
  echo('<th>'.GetStr($pdo, 'VendorType').'</th>');
  echo('<th>'.GetStr($pdo, 'VendorName').'</th>');
  echo('<th>'.GetStr($pdo, 'ShortName').'</th>');
  echo('<th>'.GetStr($pdo, 'INN').'</th>');
  echo('<th>'.GetStr($pdo, 'KPP').'</th>');
  echo('<th>'.GetStr($pdo, 'Country').'</th>');
  echo('<th>'.GetStr($pdo, 'PostIndx').'</th>');
  echo('<th>'.GetStr($pdo, 'City').'</th>');
  echo('<th>'.GetStr($pdo, 'Address').'</th>');
  echo('<th>'.GetStr($pdo, 'Phone').'</th>');
  echo('<th>'.GetStr($pdo, 'WebLink').'</th>');
  echo('<th>'.GetStr($pdo, 'DefaultDeliveryPoint').'</th>');
  echo('<th>'.GetStr($pdo, 'Description').'</th>');
  echo('<th>'.GetStr($pdo, 'Status').'</th>');
  echo('<th>'.GetStr($pdo, 'Holding').'</th>');
  echo('<th>'.GetStr($pdo, 'Position').'</th>');
  echo('<th>'.GetStr($pdo, 'Director').'</th>');
  echo('<th>'.GetStr($pdo, 'Accountant').'</th>');
  echo('<th>'.GetStr($pdo, 'GeneralBusinessGroup').'</th>');
  echo('<th>'.GetStr($pdo, 'TaxBusinessGroup').'</th>');
  echo('<th>'.GetStr($pdo, 'Blocked').'</th>');
  $i=0;
  while ($dpL = $sql2->fetch_assoc()) {
    $i=NewLine($i);

    echo ("<td align=center>");
    echo (GetEnum($pdo, "VendorType", $dpL['VendorType'])."</td>");
    echo ("<td>");
    echo ("{$dpL['VendorName']}</td>");
    echo ("<td>");
    echo ("{$dpL['ShortName']}</td>");
    echo ("<td>");
    echo ("{$dpL['INN']}</td>");
    echo ("<td>");
    echo ("{$dpL['KPP']}</td>");
    echo ("<td>");
    echo ("{$dpL['Country']}</td>");
    echo ("<td>");
    echo ("{$dpL['PostIndx']}</td>");
    echo ("<td>");
    echo ("{$dpL['City']}</td>");
    echo ("<td>");
    echo ("{$dpL['Address']}</td>");
    echo ("<td>");
    echo ("{$dpL['Phone']}</td>");
    echo ("<td>");
    echo ("{$dpL['WebLink']}</td>");
    echo ("<td>");
    echo ("{$dpL['DefaultDeliveryPoint']}</td>");
    echo ("<td>");
    echo ("{$dpL['Description']}</td>");
    echo ("<td align=center>");
    echo (GetEnum($pdo, "StatusNUZ", $dpL['Status'])."</td>");
    echo ("<td align=center>");
    $Ch="";
      if ($dpL['Holding']==1) {
        $Ch=" checked ";  
      }
        echo ("<input type=checkbox Name=Holding value=1 $Ch></td>");
      
    echo ("<td>");
    echo ("{$dpL['Position']}</td>");
    echo ("<td>");
    echo ("{$dpL['Director']}</td>");
    echo ("<td>");
    echo ("{$dpL['Accountant']}</td>");
    echo ("<td align=center>");
    echo (GetEnum($pdo, "GeneralBusinessGroup", $dpL['GeneralBusinessGroup'])."</td>");
    echo ("<td align=center>");
    echo (GetEnum($pdo, "TaxBusinessGroup", $dpL['TaxBusinessGroup'])."</td>");
    echo ("<td align=center>");
    $Ch="";
      if ($dpL['Blocked']==1) {
        $Ch=" checked ";  
      }
        echo ("<input type=checkbox Name=Blocked value=1 $Ch></td>");
      
    
  }
  echo("</tr></table>");
  echo("<a href='VendorsCard.php?New=1&Id=$Id'>".GetStr($pdo, "Add")."</a>");
?>
