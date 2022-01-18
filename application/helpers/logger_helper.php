<?php
/**
 * Logger Helper functions
 *
 * @category    Helper
 */

// --------------------------------------------------------------------------------------
if ( ! defined('PID'))
    define('PID', getmypid());

/**
 * Output 'INFO' log
 *  format: 'INFO - YYY-MM-DD HH:MI:SS --> [PID] : message...'
 *
 * @param   string|array    $message
 */
if ( ! function_exists('log_info'))
{
    function log_info($message='')
    {
        if (is_array($message)) {
            $message = var_export($message, TRUE);
        }
        log_message('info', '['.PID.'] : ' . $message);
    }
}

/**
 * Output 'DEBUG' log
 *  format: 'DEBUG - YYY-MM-DD HH:MI:SS --> [PID] : message...'
 *
 * @param   string|array    $message
 */
if ( ! function_exists('log_debug'))
{
    function log_debug($message='')
    {
        if (is_array($message)) {
            $message = var_export($message, TRUE);
        }
        log_message('debug', '['.PID.'] : ' . $message);
    }
}

/**
 * Output 'ERROR' log
 *  format: 'ERROR - YYY-MM-DD HH:MI:SS --> [PID] : message...'
 *
 * @param   string|array    $message
 */
if ( ! function_exists('log_error'))
{
    function log_error($message='')
    {
        if (is_array($message)) {
            $message = var_export($message, TRUE);
        }
        log_message('error', '['.PID.'] : ' . $message);
    }
}

