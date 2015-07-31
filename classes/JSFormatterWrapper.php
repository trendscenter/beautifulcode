<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Class to format JS files using Google Closure Linter
 *
 * JS version 5
 *
 * @author     Dylan Wood <dwood@mrn.org>
 */
require_once 'CodeWrapper.php';
class JSFormatterWrapper extends CodeWrapper
{
    public $execPath;
    public $defaultOptions;

    /**
     * run a formating process on the input file and write the results to the output file
     *
     * @return object
     */
    public function format()
    {
        $execStr = $this->execPath . ' ';
        foreach ($this->defaultOptions as $defaultOption) {
            $execStr .= escapeshellarg($defaultOption) . ' ';
        }
        exec($execStr, $outputs, $returnVar);
        if (!file_exists($this->outputFile->get('filename'))) {
            $errorMsg = 'Output of Closure Formatter not found. Ran: "'
                . $execStr . '"';
            throw new Exception ($errorMsg);
            die;
        }

        return $this;
    }

    /**
     * Statically create new formating object and use it to format an uploaded file
     *
     * @param Slim Object $routerObject
     * @param string $test
     */
    public static function run($routerApp, $test)
    {
        $formatter = new JSFormatterWrapper($routerApp, $test, 'js');
/*
        $formatter->execPath = 'fixjsstyle';
        $formatter->defaultOptions = array(
            '--nojsdoc',
            '--strict',
            $formatter->inputFile->get('filename')
        );
*/
        $formatter->execPath = 'js-beautify';
        $formatter->defaultOptions = array(
            '--config',
            VENDOR_DIR . '/../COINSStandard/js-beautifyconf.js',
            '--outfile',
            $formatter->outputFile->get('filename'),
            '--file',
            $formatter->inputFile->get('filename')
        );
        $formatter->format();
/*
        file_put_contents(
            $formatter->outputFile->get('filename'),
            file_get_contents($formatter->inputFile->get('filename'))
        );
*/
        $formatter->setResponseHeaders($formatter->responseFilename
            . '.jsbeautify');
        $formatter->streamOutputFile();
        $formatter->destroy();

	return;
    }
}
