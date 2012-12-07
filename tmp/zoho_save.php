<?php
$f = fopen('Log.txt', 'a+');
fwrite($f, "*** \n");
fwrite($f, serialize($_FILES));
fclose($f);
move_uploaded_file($_FILES['content']['tmp_name'], '/var/www/live/backend/tmp/responses/'.$_FILES['content']['name'] );
echo 'RESPONSE: saved!';