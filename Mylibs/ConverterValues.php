<?php

namespace App\Mylibs;

use App\Certificate;
use App\Settings;
use Carbon\Carbon;
use App\Cours;

class ConverterValues
{
    
    public static function getValuesToWords($value)
    {
        $valueToWords = explode(".", $value);
        $valueToWords[1] = isset($valueToWords[1]) == false ? 0 : $valueToWords[1];
        $index = self::valuesToWords($valueToWords[0]);
        $mantissa = self::valuesToWords($valueToWords[1]);

        return $index . "złotych, " . $valueToWords[1] ."/100 groszy";   
    }

    public static function valuesToWords($digits)
    {
        $jednosci = Array( 'zero', 'jeden', 'dwa', 'trzy', 'cztery', 'pięć', 'sześć', 'siedem', 'osiem', 'dziewięć' );
        $dziesiatki = Array( '', 'dziesięć', 'dwadzieścia', 'trzydzieści', 'czterdzieści', 'pięćdziesiąt', 'sześćdziesiąt', 'siedemdziesiąt', 'osiemdziesiąt', 'dziewięćdziesiąt' );
        $setki = Array( '', 'sto', 'dwieście', 'trzysta', 'czterysta', 'pięćset', 'sześćset', 'siedemset', 'osiemset', 'dziewięćset' );
        $nastki = Array( 'dziesięć', 'jedenaście', 'dwanaście', 'trzynaście', 'czternaście', 'piętnaście', 'szesnaście', 'siedemnaście', 'osiemnaście', 'dziewiętnaście' );
        $tysiace = Array( 'tysiąc', 'tysiące', 'tysięcy' );

        $digits = (string) $digits;
        $digits = strrev( $digits );
        $i = strlen( $digits );
    
        $string = '';

        if ($i > 5 && $digits[5] > 0 )
            $string .= $setki[ $digits[5] ] . ' ';
        if ($i > 4 && $digits[4] > 1 )
            $string .= $dziesiatki[ $digits[4] ] . ' ';
        elseif($i > 3 && isset($digits[4]) && $digits[4] == 1 )
            $string .= $nastki[$digits[3]] . ' ';
        if ($i > 3 && $digits[3] > 0 && isset($digits[4]) && $digits[4] != 1 )
            $string .= $jednosci[ $digits[3] ] . ' ';
        elseif ($i > 3 && $digits[3] > 0 && !isset($digits[4]) )
        $string .= $jednosci[ $digits[3] ] . ' ';

        $tmpStr = substr(strrev( $digits ), 0, -3 );
        if (strlen( $tmpStr ) > 0 ) {
            $tmpInt = (int) $tmpStr;
            if ($tmpInt == 1 )
                $string .= $tysiace[0] . ' ';
            elseif (( $tmpInt % 10 > 1 && $tmpInt % 10 < 5 ) && ( $tmpInt < 10 || $tmpInt > 20 ) )
            {
                if ((strlen( $tmpStr ) > 2) && ($tmpStr[1] == '1')) {
                    $string .= $tysiace[2] . ' ';
                } else {
                    $string .= $tysiace[1] . ' ';
                }
            }
            else
                $string .= $tysiace[2] . ' ';
        }

        if ($i > 2 && $digits[2] > 0 )
            $string .= $setki[$digits[2]] . ' ';
        if ($i > 1 && $digits[1] > 1 )
            $string .= $dziesiatki[$digits[1]] . ' ';
        elseif ($i > 0 && isset($digits[1]) && $digits[1] == 1 )
            $string .= $nastki[$digits[0]] . ' ';
        if ($digits[0] > 0 && isset($digits[1]) && $digits[1] != 1 )
            $string .= $jednosci[$digits[0]] . ' ';
        elseif ($digits[0] >= 0 && !isset($digits[1]) )
        $string .= $jednosci[$digits[0]] . ' ';
        return $string;
    }

}
