<?php
/**
 * PHPCodeWrapper.php
 *
 * @package default
 */

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Class to aid in processing PHP files
 *
 * PHP version 5
 *
 * @author     Dylan Wood <dwood@mrn.org>
 */
require_once 'TempFile.php';
class PHPCodeWrapper
{
    public $inputFile;
    public $outputFile;
    public $testMode;
    public $responseFilename;

    /**
     *
     * @param string $test
     */
    public function __construct($test)
    {
        $this->testMode = ($test === 'test');
        $this->setInputFile(TempFile::createFromUpload($this->testMode));
        $this->setOutputFile(new TempFile());
        $this->responseFilename = TempFile::getUploadedFilename($this->testMode);
    }

    /**
     *
     * @param resource $file
     */
    public function setInputFile($file)
    {
        $this->inputFile = $file;

    return $this;
    }

    /**
     *
     * @param resource $file
     */
    public function setOutputFile($file)
    {
        $this->outputFile = $file;

    return $this;
    }

    /**
     *
     */
    public function streamOutputFile()
    {
        echo $this->outputFile->read();

    return $this;
    }

    /**
     *
     * @param string $filename
     */
    public function setOutputHeaders($filename)
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

    return $this;
    }

    /**
     *
     */
    public function destroy()
    {
        $this->outputFile->destroy();
        $this->inputFile->destroy();

        return;
    }
}
