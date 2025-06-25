<?php
$record = geoip_record_by_name('212.17.21.242');
if ($record) {
    print_r($record);
}
?>
