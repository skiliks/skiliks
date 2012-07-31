<?php
header("HTTP/1.0 200 OK");
header('Content-type: text/html; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

$formula = "=Тест!A3";
preg_match_all("/=(\w+!)\((.*)\)/u", $formula, $matches);
var_dump($matches);
die();

$row = 1;
$a=1;
if ($a==2)
$items[] = $row;

var_dump($items);
die();

$a=null;

$expr = "=D7+E7-C12";

$newVars = array(
    'D7'=>'E7',
    'E7'=>'F7',
    'C12' => 'M2'
);

$var='';
$str = '';
$exprLen = strlen($expr);
$i=0;
while($i<$exprLen) {
    $s = $expr[$i];
    if (!preg_match("/[=+\-\*\/\(\)\;\:]/", $s)) {
        $var .= $s;
        echo"var = $var";
        $i++;
    }
    else {
        //$str .= $s;
        echo"var = $var";
        if (isset($newVars[$var])) {
            $str.=$newVars[$var].$s;
            $i=$i+(strlen($var)-1);
            $var = '';
        }
        else { $i++;
            $str.=$s;
        }
        
    }
    
    echo("str=$str<br>");
}
echo("var=$var");
if (isset($newVars[$var])) {
            $str.=$newVars[$var];
         
        }

echo("exp=$str");
die();

$cellName = "E10";
preg_match_all("/([A-Za-zА-Яа-я!]+)(\d+)/", $cellName, $matches); 
var_dump($matches);
die();

$formula = "A1 + a!B3 + C7";
preg_match_all("/([A-Za-А-Яа-я!]+\d+)/", $formula, $matches); 
var_dump($matches);
die();

$expr = '$a=2+;';

$b = @eval($expr);
var_dump($a);
die();

$val = "=Продажи!C611";

$r = preg_match_all("/([A-Za-А-Яа-я]+\!\w+\d+)/", $val, $matches); 
var_dump($r);
die();

$r = preg_match_all("/([+\-\*\/]+)/u", $val, $matches); 
var_dump($r);
var_dump($matches);
die();

$cellName = 'Логистика!L7'; //+Прочее!L7	
preg_match_all("/(\w+)!(\w+)(\d+)/u", $cellName, $matches); 
var_dump($matches);
die();

if (preg_match_all("/(\w+)(\d+)/", $cellName, $matches)) {
            $result = array(
                'column' => $matches[1][0],
                'string' => (int)$matches[2][0]
            );
            return $result;
        } 

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