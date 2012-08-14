<?php
header("HTTP/1.0 200 OK");
header('Content-type: text/html; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

$str = "re:re:re:testre:re:";

$str = preg_replace("/^(re:)*/u", '', $str);
echo($str); die();

if (preg_match_all("/^(re:)*/u", $str, $matches)) {
    
    var_dump($matches);
    
    $re = $matches[0][0];
    $re = explode(':', $re);
    $count = count($re) - 1;
    var_dump($count);
}



$messages = array();
$messages[1] = 'Тест4';
$messages[2] = 'Тест2';
$messages[3] = 'Тест3';
$messages[4] = 'Тест';

sort($messages);
var_dump($messages);

die();


$a='$b=0+0+18 000+2 000';

$formula = "=L7-D7";
preg_match_all("/([A-Za-zА-Яа-я\!]+\d+)/u", $formula, $matches); 
var_dump($matches);die();

class Calc {
    
    private $t=666;
    
    function callback2($str) {
        echo("{$this->t} callback2 : ".var_export($str, true).'<br>');
    }
    
    function run() {
        $formula = "=SUM(H13:H14)-AVG(I13;I14)";

        echo("<hr/>");
        echo preg_replace_callback("/([A-Z]+)\(([A-Z0-9\:\;]+)\)/u", 'self::callback2', $formula);
    }
}

$calc = new Calc();
$calc->run();
die();




$vars = array (
  'D11' => 'F11',
  'D14' => 'F14',
);

function callback($str) {
        global $vars;
        if (isset($vars[$str[1]]))
            return $vars[$str[1]];
        return '';
    }

function replace($formula, $vars ) {
    
    return preg_replace_callback("/(\w*\!*\w+\d+)/u", 'callback', $formula);
}

echo  replace($formula, $vars);

echo  replace($formula, $vars);
die();
function replaceVars($expr, $newVars) {
        $var='';
        $str = '';
        $exprLen = strlen($expr);
        $i=0;
        while($i<$exprLen) {
            $s = $expr[$i];
            if (!preg_match("/[=\+\-\*\/\(\)\;\:]/", $s)) {
                $var .= $s;
                //echo"var = $var";
                $i++;
            }
            else {
                //$str .= $s;
                echo"var = $var<br/>";
                if (isset($newVars[$var])) {
                    $str.=$newVars[$var].$s;
                    $i=$i+(strlen($var)-1);
                    $var = '';
                    $i--;
                }
                else {
                    $i++;    
                    $str.=$s;
                }

            }

            //echo("str=$str<br>");
        }
        //echo("var=$var");
        if (isset($newVars[$var])) {
                    $str.=$newVars[$var];

        }
        return $str;
    }

$expr = "=(D14+E13-G11/G14)*C14/J14";

$vars = array (
  'D14' => 'F13',
  'E13' => 'G12',
  'G11' => 'I10',
  'G14' => 'I13',
  'C14' => 'E13',
  'J14' => 'L13',
);                 

$r = replaceVars($expr, $vars);
echo($r); die();

$cellName = 'Продажи!B12';
if (preg_match_all("/(\w*)!([A-Z]+)(\d+)/u", $cellName, $matches)) {
    
}
var_dump($matches);
die();

$formula = "=Тест!A3";
preg_match_all("/=(\w+)\((.*)\)/u", $formula, $matches);
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