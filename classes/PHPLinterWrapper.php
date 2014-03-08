<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Class to lint PHP files using CodeSniffer
 *
 * PHP version 5
 *
 * @author     Dylan Wood <dwood@mrn.org>
 */
require_once 'PHPCodeWrapper.php';
class PHPLinterWrapper extends PHPCodeWrapper
{
    public $execPath;
    public $defaultOptions;

    /**
     * run a linting process on the input file and write the results to the output file
     *
     * @return object
     */
    public function lint()
    {
        $execStr = $this->execPath . ' ';
        foreach ($this->defaultOptions as $defaultOption) {
            $execStr .= escapeshellarg($defaultOption) . ' ';
        }
        exec($execStr, $outputs, $returnVar);
        if (!file_exists($this->outputFile->get('filename'))) {
            $errorMsg = 'Output of Codesniffer not found. Ran: "'
                . $execStr . '"';
            throw new Exception ($errorMsg);
            die;
        }

        return $this;
    }

    /**
     * Statically create new linting object and use it to lint an uploaded file
     *
     * @param Slim Object $routerObject
     * @param string $test
     */
    public static function run($routerApp, $test)
    {
        $linter = new PHPLinterWrapper($routerApp, $test);
        $linter->execPath = 'php ' . VENDOR_DIR . '/PHP_CodeSniffer/scripts/phpcs';
        $linter->defaultOptions = array(
            '--report-file=' . $linter->outputFile->get('filename'),
            '--standard=' . VENDOR_DIR . '/../COINSStandard',
            $linter->inputFile->get('filename'));
        $linter->lint();
        $linter->setResponseHeaders($linter->responseFilename
            . '.psr2codesniff');
        $linter->streamOutputFile();
        $linter->destroy();

	return;
    }
}
