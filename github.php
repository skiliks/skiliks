<?php
if ($_GET['paranoia'] !== 'erb-yan-oj-al-c')
    die();

exec("sudo -u skiliks /usr/local/bin/git-update-backend &");