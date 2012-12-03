<?php
$api = 'b5e3f7316085c8ece12832f533c751be';

$fields = array();
$fields['content'] = "@" . __DIR__ . "/yahoo.PPT";
$fields['filename'] = 'yahoo.PPT';
$fields['id'] = '15';
$fields['format'] = 'ppt';
$fields['saveurl'] = 'http://backend.live.skiliks.com/tmp/zoho_save.php';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://show.zoho.com/remotedoc.im?apikey='.$api.'&output=url');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
curl_setopt($ch, CURLOPT_VERBOSE,  1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, true);

$content = curl_exec ($ch);
curl_close ($ch);


$matches = array();
preg_match('/URL=(.*)/',$content, $matches);
echo '<h1>PPT example</h1>';
echo '<iframe src="'.$matches[1].'" style="width:100%;height:500px;"></iframe>';
