<?php
move_uploaded_file($_FILES['content']['tmp_name'], '/var/www/live/backend/tmp/responses/1.xls');
echo 'RESPONSE: saved!';