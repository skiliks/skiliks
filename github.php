<?php
if ($_GET['paranoia'] !== 'erb-yan-oj-al-c')
    die();

if($_GET['code'] === 'true'){
    `sudo bash /usr/local/bin/git-update-backend`;
} else {
    `sudo -u skiliks /usr/local/bin/git-update-backend`;
}

exec('sudo ls', $output, $ret);
print_r($output);