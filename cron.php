<?php
define('RECRM_CRON', 'Y');

$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__)) . '/../../..';
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

@set_time_limit(0);
@ignore_user_abort(true);

global $wpdb;
$wpdb->query('set wait_timeout = 3600');

recrm_import();