<?php
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
     * Format the input file using tidy the phptidy process and write new file to output file
     *
     * @return object
     */
    public function format()
    {
        $execStr = $this->get('execPath') . ' ';
        foreach ($this->get('defaultOptions') as $defaultOption) {
            $execStr .= escapeshellarg($defaultOption) . ' ';
        }
        exec($execStr, $outputs, $returnVar);
        if ($returnVar != 0) {
            $errorMsg = 'Encountered error executing "' . $execStr . '"';
            throw new Exception ($errorMsg);
            die;
        }
        //phptidy writes to its own output file path. retrieve that filepath
	if (preg_match('/phptidy/', $this->get('execPath'))) {
            $this->setOutputFile(
                new TempFile($this->getFormattedFilenameFromOutput($outputs))
            );
        }

        return $this;
    }

    /**
     * Parse the filename of the output file from phptidy from command outptut
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
     * Statically build a formatter and format the uploaded file using both phptidy and php-cs-fixer
     *
     * @param string $test
     */
    public static function run($appRouter, $test)
    {
        $formatter = new PHPFormatterWrapper($appRouter, $test);
        $formatter->set(
            'execPath',
            'php ' . VENDOR_DIR . '/phptidy/phptidy.php'
        );
        $formatter->set(
            'defaultOptions',
             array('suffix', $formatter->get('inputFile')->get('filename'))
        );
        $formatter->format();
        //copy output to input for another round of formatting with cs-fixer...
	copy(
            $formatter->get('outputFile')->get('filename'),
            $formatter->get('inputFile')->get('filename')
        );
        //make new exec path for php-cs-fixer
        $formatter->set('execPath', VENDOR_DIR . '/Symfony/php-cs-fixer.phar');
        $formatter->set(
            'defaultOptions',
            array('fix', $formatter->inputFile->get('filename'))
        );
        $formatter->format();
	//cs-fixer just writes its changes to the input file, so copy it over the output file
	copy(
            $formatter->get('inputFile')->get('filename'),
            $formatter->get('outputFile')->get('filename')
        );
        $formatter->setResponseHeaders($formatter->responseFilename . '.csfixed_and_tidy');
        $formatter->streamOutputFile();
        $formatter->destroy();
    }

}
