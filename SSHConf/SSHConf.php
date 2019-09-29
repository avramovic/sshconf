<?php namespace SSHConf;

class SSHConf
{


    /**
     * @var null
     */
    protected $sshConfFile;
    public $sshConf = [];

    public function __construct($sshConfFile = null)
    {
        if (!$sshConfFile) {
            $sshConfFile = $_SERVER['HOME'].'/.ssh/config';
        }

        $this->sshConfFile = $sshConfFile;
        if (is_file($sshConfFile) && is_readable($sshConfFile)) {
            $this->parse($sshConfFile);
        }
    }

    protected function parse($file)
    {
        $data = [];

        $sshConf  = explode(PHP_EOL, file_get_contents($file));
        $oldGroup = null;

        foreach ($sshConf as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            if (stripos($line, 'host ') === 0) {
                $oldGroup = substr($line, 5);
                if (!isset($data[$oldGroup])) {
                    $data[$oldGroup] = [];
                }
                continue;
            }

            $spacePos = strpos($line, ' ');
            if ($spacePos !== false) {
                $key   = strtolower(substr($line, 0, $spacePos));
                $value = substr($line, $spacePos + 1);
                if (isset($data[$oldGroup])) {
                    $data[$oldGroup][$key] = $value;
                }
            }

        }

        $this->sshConf = $data;

        return $this;

    }

    public function setValue($host, $key, $value)
    {
        $this->sshConf[$host][$key] = $value;

        return $this;
    }

    public function has($host)
    {
        return isset($this->sshConf[$host]);
    }

    public function get($host)
    {
        return $this->sshConf[$host];
    }

    public function put($host, array $data = [])
    {
        $this->sshConf[$host] = $data;
        return $this;
    }

    public function remove($host)
    {
        unset($this->sshConf[$host]);
        return $this;
    }

    public function all()
    {
        return $this->sshConf;
    }

    public function dump()
    {
        $text = '';


        foreach ($this->sshConf as $host => $conf) {
            $text .= 'Host '.$host.PHP_EOL;

            foreach ($conf as $key => $value) {
                if (empty($value) && !is_numeric($value)) {
                    continue;
                }

                $text .= "\t".ucfirst($key).' '.$value.PHP_EOL;
            }

            $text .= PHP_EOL;
        }

        return $text;
    }

    public function save($file = null)
    {
        return file_put_contents($file ?? $this->sshConfFile, $this->dump());
    }

}