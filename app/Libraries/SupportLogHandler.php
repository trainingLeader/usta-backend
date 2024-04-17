<?php

namespace App\Libraries;

use Exception;
//use CodeIgniter\Log\Handlers\BaseHandler;
use CodeIgniter\Libraries;

class SupportLogHandler 
{
    /**
     * Path to save log files to.
     *
     * @var string
     */
    protected $path;

    /**
     * File extension
     *
     * @var string
     */
    protected $fileExtension;

    /**
     * File permissions
     *
     * @var integer
     */
    protected $filePermissions = 0644;

    /**
     * Date format used for log files.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(?string $path = null)
    {       

        $this->path = WRITEPATH . 'logs' . DIRECTORY_SEPARATOR.$path;

        if (! is_dir($this->path) && ! mkdir($concurrentDirectory = $this->path, 0777, true) && ! is_dir($concurrentDirectory)) {
            throw new Exception('SupportLogHandler: ' . $this->path . ' directory doesn\'t exist');
        }
        $this->fileExtension = 'log';
        $this->filePermissions = 0644;
    }

    /**
     * Write a $level message to the log file.
     *
     * @param string $level
     * @param string $message
     *
     * @return boolean
     */
    public function message($level, $message): bool
    {
        $filepath = $this->path.DIRECTORY_SEPARATOR.'support-' . date('Y-m-d') . '.' . $this->fileExtension;

        $message = '[' . date($this->dateFormat) . '] ' . strtoupper($level) . ': ' . $message . PHP_EOL;

        try {
            if (! file_exists($this->path)) {
                mkdir($this->path, 0755, true);
            }

            if (! file_exists($filepath)) {
                file_put_contents($filepath, '');
                chmod($filepath, $this->filePermissions);
            }

            if (! $fp = fopen($filepath, 'ab')) {
                return false;
            }

            flock($fp, LOCK_EX);
            fwrite($fp, $message);
            flock($fp, LOCK_UN);
            fclose($fp);

            chmod($filepath, $this->filePermissions);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    public function info($message): bool
    {
        return $this->message('info',$message);
    }
    public function error($message): bool
    {
        return $this->message('error',$message);
    }
    public function debug($message): bool
    {
        return $this->message('debug',$message);
    }
    public function warning($message): bool
    {
        return $this->message('warning',$message);
    }
    public function critical($message): bool
    {
        return $this->message('critical',$message);
    }
    
}
