<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Class to lint JS files using Google Closure Linter
 *
 * JS version 5
 *
 * @author     Dylan Wood <dwood@mrn.org>
 */
require_once 'CodeWrapper.php';
class JSLinterWrapper extends CodeWrapper
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
	//lint results are printed directly to std out. redirect them to output file
        $execStr .= '> ' . escapeshellarg($this->outputFile->get('filename'));
        exec($execStr, $outputs, $returnVar);
        if (!file_exists($this->outputFile->get('filename'))) {
            $errorMsg = $returnVar . 'Output of Closure Linter not found. Ran: "'
                . $execStr . '"';
            throw new Exception ($errorMsg);
            die;
	//jshint CLI returns a non-zero response code if it finds errors in the doc
        //it also returns zero output if it doesn't find any errors to report
	//so, check for a successful exit code, and empty output and return an all clear
        } elseif ($returnVar == 0 && sizeof($outputs) === 0 ) {
            file_put_contents ($this->outputFile->get('filename'),
                'No Errors Found');
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
        $linter = new JSLinterWrapper($routerApp, $test, 'js');
/*
        $linter->execPath = 'gjslint';
        $linter->defaultOptions = array(
            '--nojsdoc',
            '--strict',
            $linter->inputFile->get('filename')
        );
*/
        $linter->execPath = 'jshint';
        $linter->defaultOptions = array(
            '--config',
            VENDOR_DIR . '/../COINSStandard/jshintconf.js',
            $linter->inputFile->get('filename')
        );
        $linter->lint();
        $linter->setResponseHeaders($linter->responseFilename
            .'.jshint');
        //remove the input filename from the output file to hide clutter
	file_put_contents(
            $linter->outputFile->get('filename'),
            str_replace(
                $linter->inputFile->get('filename') . ': ',
                '',
                file_get_contents($linter->outputFile->get('filename'))
            )
        );
        $linter->streamOutputFile();
        $linter->destroy();

	return;
    }
}
