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

            if (stripos($line, 'host ') === 0) {
                $oldGroup = substr($line, 5);
                if (!isset($data[$oldGroup])) {
                    $data[$oldGroup] = [];
                }
            }

            if (stripos($line, 'hostname ') === 0) {
                $value = substr($line, 9);
                if (isset($data[$oldGroup])) {
                    $data[$oldGroup]['hostname'] = $value;
                }
            }

            if (stripos($line, 'user ') === 0) {
                $value = substr($line, 5);
                if (isset($data[$oldGroup])) {
                    $data[$oldGroup]['user'] = $value;
                }
            }

            if (stripos($line, 'identityfile ') === 0) {
                $value = substr($line, 13);
                if (isset($data[$oldGroup])) {
                    $data[$oldGroup]['identityfile'] = $value;
                }
            }

            if (stripos($line, 'port ') === 0) {
                $value = substr($line, 5);
                if (isset($data[$oldGroup])) {
                    $data[$oldGroup]['port'] = $value;
                }
            }

            if (stripos($line, 'loglevel ') === 0) {
                $value = substr($line, 9);
                if (isset($data[$oldGroup])) {
                    $data[$oldGroup]['loglevel'] = $value;
                }
            }

            if (stripos($line, 'compression ') === 0) {
                $value = substr($line, 12);
                if (isset($data[$oldGroup])) {
                    $data[$oldGroup]['compression'] = $value;
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

            if (isset($conf['hostname'])) {
                $text .= "\t".'HostName '.$conf['hostname'].PHP_EOL;
            }

            if (isset($conf['port'])) {
                $text .= "\t".'Port '.$conf['port'].PHP_EOL;
            }

            if (isset($conf['user'])) {
                $text .= "\t".'User '.$conf['user'].PHP_EOL;
            }

            if (isset($conf['identityfile'])) {
                $text .= "\t".'IdentityFile '.$conf['identityfile'].PHP_EOL;
            }

            if (isset($conf['loglevel'])) {
                $text .= "\t".'LogLevel '.$conf['loglevel'].PHP_EOL;
            }

            if (isset($conf['compression'])) {
                $text .= "\t".'Compression '.$conf['compression'].PHP_EOL;
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