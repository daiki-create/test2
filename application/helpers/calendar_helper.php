<?php

if ( ! function_exists('calendar'))
{
    function calendar($since_date, $days)
    {
        $wd = ['日', '月', '火', '水', '木', '金', '土'];
        $calendar = array();

        for ($i=0; $i<$days; $i++)
        {
            $date = date('Y-m-d', strtotime("{$since_date} +{$i}days"));
            $d    = date('j', strtotime($date));
            $m    = date('n', strtotime($date));
            $w    = date('w', strtotime($date));

            $calendar[] = array(
                'date'  => $date,
                'wday'  => $wd[$w],
                'w'     => $w,
                'd'     => $d,
                'm'     => $m,
            );
        }

        return $calendar;
    }

    function weekday_ja($wd)
    {
        $wd_ja = ['日', '月', '火', '水', '木', '金', '土'];
        return isset($wd_ja[$wd]) ? $wd_ja[$wd] : '';
    }

}

