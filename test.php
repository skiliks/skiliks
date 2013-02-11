<?php
var_dump(iconv_get_encoding());
iconv_set_encoding("internal_encoding", "WINDOWS-1251");
iconv_set_encoding("input_encoding", "WINDOWS-1251");
iconv_set_encoding("output_encoding", "WINDOWS-1251");
mb_internal_encoding("WINDOWS-1251");
mb_http_output("WINDOWS-1251");
mb_http_input("WINDOWS-1251");
var_dump(iconv_get_encoding());
if(file_exists("Почта.txt")){
echo "Yes";
} else {
echo "No";
}
