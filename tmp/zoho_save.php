<?php
move_uploaded_file($_FILES['content']['tmp_name'], '/tmp/result.xls');
echo 'RESPONSE: yes!';