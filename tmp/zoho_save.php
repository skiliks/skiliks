<?php

header('Content-Type: text/html; charset=UTF-8');

$name = explode('-', $_FILES['content']['name']);

$simId = $name[0];
$documentID = $name[1];

unset($name[0], $name[1]);

$realFileName = implode('-', $name);

$pathToUserFile = sprintf(
    utf8_encode ('../documents/excel/%s/%s/%s'),
    $simId,
    $documentID,
    $realFileName
);

move_uploaded_file($_FILES['content']['tmp_name'], $pathToUserFile);

echo utf8_encode ('RESPONSE: Saved.');

//move_uploaded_file($_FILES['content']['tmp_name'], '../tmp/responses/'.$_FILES['content']['name'] );
//echo 'RESPONSE: saved!';