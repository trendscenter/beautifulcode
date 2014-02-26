<?php
/**
 * PHPFormatterWrapper.php
 *
 * @package default
 */

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Class to format PHP files using phptidy
 *
 * PHP version 5
 *
 * @author     Dylan Wood <dwood@mrn.org>
 */
require_once 'PHPCodeWrapper.php';
class PHPFormatterWrapper extends PHPCodeWrapper
{
    public $execPath;
    public $defaultOptions;

    /**
     *
     * @return object
     */
    public function tidy()
    {
        $execStr = $this->execPath . ' ';
        foreach ($this->defaultOptions as $defaultOption) {
            $execStr .= escapeshellarg($defaultOption) . ' ';
        }
        exec($execStr, $outputs, $returnVar);
        if (!sizeof($outputs)) {
            $errorMsg = 'Encountered error executing "' . $execStr . '"';
            throw new Exception ($errorMsg);
            die;
        }
        $this->setOutputFile(
            new FileWrapper($this->getFormattedFilenameFromOutput($outputs))
        );

        return $this;
    }

    /**
     *
     * @return object
     */
    public function csFix()
    {
        $execStr = $this->execPath . ' ';
        foreach ($this->defaultOptions as $defaultOption) {
            $execStr .= escapeshellarg($defaultOption) . ' ';
        }
        exec($execStr, $outputs, $returnVar);
        if (!sizeof($outputs)) {
            $errorMsg = 'Encountered error executing "' . $execStr . '"';
            throw new Exception ($errorMsg);
            die;
        }
        return $this;
    }

    /**
     *
     * @param  array  $output
     * @return string
     */
    private function getFormattedFilenameFromOutput($output)
    {
        //the fourth line of output contains the newly written file
        if (isset($output[4])) {
            return str_replace(' saved.', '', trim($output[4]));
        } else {
            $errorMsg = 'Formatted filename not found where expected';
            throw new Exception ($errorMsg);
        }
    }

    /**
     *
     * @param string $test
     */
    public static function run($test)
    {
        $formatter = new PHPFormatterWrapper($test);
        $formatter->execPath = 'php ' . VENDOR_DIR . '/phptidy/phptidy.php';
        $formatter->defaultOptions = array('suffix',
            $formatter->inputFile->get('filename'));
        $formatter->tidy();
        //make new exec path for php-cs-fixer
        $formatter->execPath = VENDOR_DIR . '/Symfony/php-cs-fixer.phar';
        $formatter->defaultOptions = array('fix',
            $formatter->outputFile->get('filename'));
        $formatter->csFix();
        $formatter->setOutputHeaders($formatter->responseFilename . '.csfixed_and_tidy');
        $formatter->streamOutputFile();
        $formatter->destroy();
    }

}
