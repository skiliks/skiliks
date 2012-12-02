<?php
$api = 'b5e3f7316085c8ece12832f533c751be';

$fields = array();
$fields['content'] = "@" . __DIR__ . "/rcpb199710000budg291.xls";
$fields['filename'] = 'rcpb199710000budg291.xls';
$fields['id'] = '13';
$fields['format'] = 'xls';
$fields['saveurl'] = urlencode('http://backend.live.skiliks.com/tmp/zoho_save.php');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://sheet.zoho.com/remotedoc.im?apikey='.$api.'&output=editor');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
curl_setopt($ch, CURLOPT_VERBOSE,  1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, true);

$content = curl_exec ($ch);

curl_close ($ch);

$headers = explode("\n", $content);
foreach($headers as $val)
{
    if (stripos($val, 'Location: ') !== false)
    {
        $url = str_replace('Location: ', '', $val);
        echo '<iframe src="'.$url.'" style="width:100%;height:500px;"></iframe>';
    }
}
