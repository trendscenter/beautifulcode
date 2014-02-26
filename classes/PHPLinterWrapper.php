<?php
/**
 * PHPLinterWrapper.php
 *
 * @package default
 */

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
     *
     * @param string $test
     */
    public static function run($test)
    {
        $formatter = new PHPLinterWrapper($test);
        $formatter->execPath = 'php ' . VENDOR_DIR . '/PHP_CodeSniffer/scripts/phpcs';
        $formatter->defaultOptions = array(
            '--report-file=' . $formatter->outputFile->get('filename'),
            '--standard=' . VENDOR_DIR . '/../COINSStandard',
            $formatter->inputFile->get('filename'));
        $formatter->lint();
        $formatter->setOutputHeaders($formatter->responseFilename
            . '.psr2codesniff');
        $formatter->streamOutputFile();
        $formatter->destroy();
    }

}
