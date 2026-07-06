<?php

if (! function_exists('bn_num')) {

    /**
     * ইংরেজি সংখ্যা/টেক্সটকে বাংলা সংখ্যায় কনভার্ট করে।
     * Example: bn_num(1250.00) => '১,২৫০.০০'
     */
    function bn_num($value)
    {
        $enDigits = ['0','1','2','3','4','5','6','7','8','9'];
        $bnDigits = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];

        return str_replace($enDigits, $bnDigits, (string) $value);
    }
}

if (! function_exists('bn_date')) {

    /**
     * Carbon date কে d-m-Y ফরম্যাটে বাংলা সংখ্যায় দেখায়।
     */
    function bn_date($date, $format = 'd-m-Y')
    {
        if (! $date) {
            return '';
        }

        return bn_num($date->format($format));
    }
}