<?php 


function add_2_decimals($number){

    $euro = number_format(($number/100), decimals:2, decimal_separator:',', thousands_separator:'');
    return $euro . '&euro';
}




?>