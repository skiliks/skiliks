<?php
move_uploaded_file($_FILES['content']['tmp_name'], '/tmp/'.$_FILES['content']['tmp_name']);
echo 'RESPONSE: saved!';