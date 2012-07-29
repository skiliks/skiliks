<?php
header("HTTP/1.0 200 OK");
header('Content-type: text/html; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

echo number_format ( '10000000.23' ,  2 , '.' , ' ' );
die();

$a  = "(12 + 14 - 20*13)/23";
preg_match_all("/([A-Z]+\d+)/", $a, $matches); 
var_dump($matches); die();


$str = "=сумма(C4;D4;B4)";
preg_match_all("/=([а-я]+)\((.*)\)/u", $str, $matches);
var_dump($matches); die();


$formula = "B6:M6";
if (preg_match_all("/(\w)(\d)\:(\w)(\d)/", $formula, $matches)) {
            var_dump($matches);
        }
        
die();        


$formula = "=SUM(B6:M6)";
if (preg_match_all("/([A-Z]+)\((.*)\)/", $formula, $matches)) {
            var_dump($matches);
        }
        
die();        

$t = "0:00";

echo date_format(0, 'H:i');
die();
        
echo date_format($t, 'H:i');
die();
echo(sprintf("%01:%01", $t) );die();

preg_match_all("/P(\d+)/", "P3", $matches);

var_dump($matches);
die();

//$str = "-          � �� ���� �������� �������! ��� ����� �� ������ ��� ���� �� ���� �����!";
$str = preg_replace("/^-(\s)+/", "- ", $str);
echo($str);
?>