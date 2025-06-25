<?php
  die();
  echo("<hr><h4>".GetStr($pdo, 'CurrencyExchRate')."</h4>");
  $query = "select * FROM CurrencyExchRate where CurrencyCode='$CurrencyCode' order by CurrencyCode,StartDate ";
  $sql2 = $pdo->query ($query)
            or die("Invalid query:<br>$query<br>" . $pdo->error);

  echo('<table><tr class=header>');
  echo('<th>'.GetStr($pdo, 'StartDate').'</th>');
  echo('<th>'.GetStr($pdo, 'Multy').'</th>');
  echo('<th>'.GetStr($pdo, 'Rate').'</th>');
  echo('<th>'.GetStr($pdo, 'FullRate').'</th>');
  $i=0;
  while ($dpL = $sql2->fetch_assoc()) {
    $i=NewLine($i);

    echo ("<td>");

    echo("<a href='CurrencyExchRateCard.php?CurrencyCode={$dpL['CurrencyCode']}&StartDate={$dpL['StartDate']}'>");
    echo ("{$dpL['StartDate']}</a></td>");
    echo ("<td align=center>");
    echo ("{$dpL['Multy']}</td>");
    echo ("<td align=right>");
    $OW=number_format($dpL['Rate'], 2, ".", "'");
    echo ("$OW</td>");

    echo ("<td align=right>");
    $OW=number_format($dpL['FullRate'], 2, ".", "'");
    echo ("$OW</td>");

    
  }
  echo("</tr></table>");
  echo("<a href='CurrencyExchRateCard.php?New=1&CurrencyCode=$CurrencyCode'>".GetStr($pdo, "Add")."</a>");
?>
