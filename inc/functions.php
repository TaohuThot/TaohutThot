<?php
function formatThaiDate($timestamp, $short = false) {
    $thai_months = array(
        1 => 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 
        'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
    );
    // $thai_days = array(
    //     'Sun' => 'อา.', 'Mon' => 'จ.', 'Tue' => 'อ.', 
    //     'Wed' => 'พ.', 'Thu' => 'พฤ.', 'Fri' => 'ศ.', 'Sat' => 'ส.'
    // );
    
    // $day = $thai_days[date('D', $timestamp)];
    $date = date('j', $timestamp);
    $month = $thai_months[date('n', $timestamp)];
    $year = date('Y', $timestamp) + 543;
    
    if ($short) {
        return "$date $month";
    } else {
        $time = date('H:i', $timestamp);
        return "$date $month $year $time";
    }
}

function formatThaiMonth($month_number) {
    $thai_months = array(
        1 => 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 
        'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
    );
    return $thai_months[$month_number];
}


function getWeeklyRanges($year, $month) {
    $start_date = new DateTime("$year-$month-01");
    $end_date = (clone $start_date)->modify('last day of this month');
    $weeks = [];

    while ($start_date <= $end_date) {
        $week_start = clone $start_date;
        $week_end = (clone $week_start)->modify('next saturday');
        if ($week_end > $end_date) {
            $week_end = clone $end_date;
        }
        $weeks[] = [
            'start' => $week_start->format('Y-m-d'),
            'end' => $week_end->format('Y-m-d')
        ];
        $start_date->modify('next sunday');
    }
    return $weeks;
}
?>

