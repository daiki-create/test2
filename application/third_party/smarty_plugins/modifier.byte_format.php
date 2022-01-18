<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifier
 */

/**
 * Smarty byte_format modifier plugin
 *
 * Type:     modifier<br>
 * Name:     format_byte<br>
 * Purpose:  Format byte(s)
 *
 * @param integer $bytes
 * @param integer $precision
 * @param array $units
 */
function smarty_modifier_byte_format($bytes, $precision = 2, array $units = null) {
    if (!$bytes || !is_numeric($bytes)) {
        return '0 B';
    }
    $bytes = intval($bytes);
    if (abs($bytes) < 1024) {
        $precision = 0;
    }

    if (is_array($units) === false) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    }

    if ($bytes < 0) {
        $sign = '-';
        $bytes = abs($bytes);
    } else {
        $sign = '';
    }

    $exp = floor(log($bytes) / log(1024));
    if ($exp >= count($units)) {
        $exp = count($units) - 1;
    }
    $unit = $units[$exp];
    $bytes = $bytes / pow(1024, floor($exp));
    if (!is_numeric($precision) || intval($precision) < 0) {
        $precision = 2;
    }
    $precision = intval($precision);
    $bytes = sprintf('%.' . $precision . 'f', $bytes);

    return $sign . $bytes . ' ' . $unit;
}

