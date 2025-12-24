<?php
// lib/numberToWords.php

function numberToWords($number) {
    // تبدیل عدد به حروف فارسی
    $yekan = ['', 'یک', 'دو', 'سه', 'چهار', 'پنج', 'شش', 'هفت', 'هشت', 'نه'];
    $dahgan = ['', '', 'بیست', 'سی', 'چهل', 'پنجاه', 'شصت', 'هفتاد', 'هشتاد', 'نود'];
    $sadgan = ['', 'یکصد', 'دویست', 'سیصد', 'چهارصد', 'پانصد', 'ششصد', 'هفتصد', 'هشتصد', 'نهصد'];
    $dah = ['ده', 'یازده', 'دوازده', 'سیزده', 'چهارده', 'پانزده', 'شانزده', 'هفده', 'هجده', 'نوزده'];
    $base = ['', 'هزار', 'میلیون', 'میلیارد', 'تریلیون'];

    $result = '';
    $groups = array_reverse(str_split(str_pad($number, ceil(strlen($number) / 3) * 3, '0', STR_PAD_LEFT), 3));

    foreach ($groups as $index => $group) {
        $part = '';
        $group = (int)$group;
        $hundreds = floor($group / 100);
        $tens = floor(($group % 100) / 10);
        $ones = $group % 10;

        if ($hundreds > 0) {
            $part .= $sadgan[$hundreds] . ' و ';
        }

        if ($tens > 1) {
            $part .= $dahgan[$tens];
            if ($ones > 0) {
                $part .= ' و ' . $yekan[$ones];
            }
        } elseif ($tens == 1) {
            $part .= $dah[$ones];
        } elseif ($ones > 0) {
            $part .= $yekan[$ones];
        }

        if (!empty($part)) {
            if ($index > 0) {
                $result = $base[$index] . ' و ' . $result;
            }
            $result = $part . ' ' . $result;
        }
    }
    
    // تمیز کردن و اضافات
    $result = trim(str_replace('  ', ' ', str_replace(' و و', ' و', $result)));
    return empty($result) ? 'صفر' : $result;
}
?>