<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Class to provide helper methods to process PHP files with 
 * linters and formatters
 *
 * PHP version 5
 *
 * @author     Dylan Wood <dwood@mrn.org>
 */
require_once 'TempFile.php';
class CodeWrapper
{
    public $inputFile;
    public $outputFile;
    public $testMode;
    public $routerApp;
    public $responseFilename;

    /**
     * Construct new PHPCodeWrapper object
     * the $routerApp can be used to change e.g. response headers
     * (optionally in test mode, which will result in a local file being used instead of an uploaded one)
     *
     * @param Slim Object $routerApp
     * @param string $test
     */
    public function __construct($routerApp, $testMode = '')
    {
        $this->setRouterApp($routerApp);
        $this->setTestMode($testMode === 'test');
        $this->setInputFile(TempFile::createFromUpload($this->testMode));
        $this->setOutputFile(new TempFile());
        $this->responseFilename = TempFile::getUploadedFilename($this->testMode);
    }

    /**
     * Generically set any attribute
     *
     * @param string $key
     * @param unknown $value
     */
    public function set($key, $value)
    {
        $this->$key = $value;
        return $this;

    }
    /**
     * Generically get any attribute
     *
     * @param string $key
     */
    public function get($key)
    {
        return $this->$key;

    }

    /**
     * Set routerApp of object
     *
     * @param Slim Object $routerApp
     */
    public function setRouterApp($routerApp)
    {
        $this->routerApp = $routerApp;

        return $this;
    }

    /**
     * Set testMode property of object
     *
     * @param boolean $testMode
     */
    public function setTestMode($testMode)
    {
        $this->testMode = $testMode;

        return $this;
    }

    /**
     * Set inpuFile property of object: the inputFile is a resource handle for the file to be processed
     *
     * @param resource $file
     */
    public function setInputFile($file)
    {
        $this->inputFile = $file;

        return $this;
    }

    /**
     * Set the outputFile property of object
     * The output file is where processing results are stored temporarily until they are read to the response
     *
     * @param resource $file
     */
    public function setOutputFile($file)
    {
        $this->outputFile = $file;

        return $this;
    }

    /**
     * Read the output file to the response
     *
     */
    public function streamOutputFile()
    {
        echo $this->outputFile->read();

        return $this;
    }

    /**
     * Set the output headers for the response
     *
     * @param string $filename
     */
    public function setResponseHeaders($filename)
    {
	$app = $this->get('routerApp');
        $app->response->headers->set('Content-Description', 'File Transfer');
        $app->response->headers->set('Content-Type', 'application/octet-stream');
        $app->response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
        $app->response->headers->set('Expires', '0');
        $app->response->headers->set('Cache-Control', 'must-revalidate');
        $app->response->headers->set('Pragma', 'public');

    return $this;
    }

    /**
     * Cleanup when finished: remove temporary input and output files from disc
     *
     */
    public function destroy()
    {
        $this->outputFile->destroy();
        $this->inputFile->destroy();

        return;
    }
}
