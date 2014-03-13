<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Router for code cleanup and linting services
 *
 * @author     Dylan Wood <dwood@mrn.org>
 */

define('VENDOR_DIR', realpath(dirname(__FILE__)) . '/vendor');

require VENDOR_DIR . '/Slim/Slim/Slim.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

//specify PHP routes
$app->any('/php/format(/:test)', function($test = false) use ($app) {
    require_once 'classes/PHPFormatterWrapper.php';
    PHPFormatterWrapper::run($app, $test);
});

$app->any('/php/lint(/:test)', function($test = false) use ($app) {
    require_once 'classes/PHPLinterWrapper.php';
    PHPLinterWrapper::run($app, $test);
});

//specify JS routes
$app->any('/js/format(/:test)', function($test = false) use ($app) {
    require_once 'classes/JSFormatterWrapper.js';
    JSFormatterWrapper::run($app, $test);
});

$app->any('/js/lint(/:test)', function($test = false) use ($app) {
    require_once 'classes/JSLinterWrapper.js';
    JSLinterWrapper::run($app, $test);
});

$app->run();
