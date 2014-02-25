<?php
/**
 * FileWrapper.php
 *
 * @package default
 */

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Class to aid in processing uploaded files for beautification and linting
 *
 * PHP version 5
 *
 * @author     Dylan Wood <dwood@mrn.org>
 */
class FileWrapper
{
    public $filename;

    /**
     *
     * @param  string $filename (optional)
     * @return object FileWrapper
     */
    public function __construct($filename = null) 
    {
        if ($filename === null) {
             $filename = static::generateTmpFilename();
        }



        $this->filename = $filename;

        return $this;
    }

    /**
     *
     * @return string
     */
    public static function generateTmpFilename()
    {
        return '/tmp/filewrapper_tmpfile_' . str_replace(' ', '_', microtime());
    }

    /**
     *
     * @param  boolean $testMode
     * @return object  FileWrapper
     */
    public static function factoryFromUpload($testMode)
    {
        $tmpFilename = static::generateTmpFilename();
        if ($testMode) {
            copy(__FILE__, $tmpFilename);

            return new FileWrapper($tmpFilename);
        }
        $formFieldName = static::getUploadedFileFieldName();
        $moveSuccessful = move_uploaded_file(
            $_FILES[$formFieldName]['tmp_name'],
            $tmpFilename
        );
        if ($moveSuccessful) {
            return new FileWrapper($tmpFilename);
        } else {
            $errorMsg = 'Unable to locate uploade file under field "'
                . $formName;
            throw new Exception($errorMsg);
        }
    }

    /**
     *
     * @param  string  $key
     * @return unknown
     */
    public function get($key)
    {
        return $this->$key;
    }

    /**
     *
     * @param  string $data
     * @return int,   boolean
     */
    public function write($data)
    {
        return file_put_contents($this->get('filename'), $data);
    }

    /**
     *
     * @return string
     */
    public function read()
    {
        return file_get_contents($this->get('filename'));
    }

    /**
     *
     * @return boolean
     */
    public function destroy()
    {
        $filename = $this->get('filename');

        return unlink($this->filename);
    }

    /**
     *
     * @param  boolean $testMode
     * @return string
     */
    public static function getUploadedFileName($testMode)
    {
        if ($testMode) {
            return basename(__FILE__);
        }
        $formFieldName = static::getUploadedFileFieldName();

        return $_FILES[$formFieldName]['name'];
    }

    /**
     *
     * @return string
     */
    public static function getUploadedFileFieldName()
    {
        if (empty($_FILES)) {
            $errorMsg = 'Unable to locate uploaded file"';
            throw new Exception($errorMsg);
        }
        $keys = array_keys($_FILES);

        return $keys[0];
    }
}
