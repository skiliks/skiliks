<?php

header('Content-Type: text/html; charset=UTF-8');

$name = explode('-', $_FILES['content']['name']);

$simId = $name[0];
$documentID = $name[1];

unset($name[0], $name[1]);

$realFileName = implode('-', $name);

$pathToUserFile = sprintf(
    '../documents/excel/%s/%s/%s',
    $simId,
    $documentID,
    $realFileName
);

move_uploaded_file($_FILES['content']['tmp_name'], $pathToUserFile);

echo 'RESPONSE: Файл успешно сохранён.';

//move_uploaded_file($_FILES['content']['tmp_name'], '../tmp/responses/'.$_FILES['content']['name'] );
//echo 'RESPONSE: saved!';