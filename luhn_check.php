<?php

function luhn_check($ccNum){

    $str = '';

    foreach( array_reverse( str_split( $ccNum ) ) as $i => $c ) $str .= ($i % 2 ? $c * 2 : $c );

    return array_sum( str_split($str) ) % 10 == 0;
}
