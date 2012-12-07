<?php
move_uploaded_file($_FILES['content']['tmp_name'], '/var/www/live/backend/tmp/responses/'.$_FILES['content']['tmp_name']);
echo 'RESPONSE: saved!';