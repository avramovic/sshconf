<?php namespace Commands;

use SSHConf\SSHConf;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends Command
{
    protected static $defaultName = null;
    protected SSHConf $sshConf;
    protected OutputInterface $output;

    public function __construct()
    {
        parent::__construct(static::$defaultName);
        $this->sshConf = new SSHConf();
    }

    public function error($msg, $exitCode = 1)
    {
        $this->output->writeln(sprintf("<error>ERROR: %s</error>", $msg));

        if (is_numeric($exitCode)) {
            exit((int)$exitCode);
        }
    }

    public function info($msg)
    {
        $this->output->writeln(sprintf("<fg=green>%s</>", $msg));
    }

    public function appName(OutputInterface &$output)
    {
        $this->setOutput($output);
        $this->output->writeln($this->getApplication()->getName().' <fg=green>'.$this->getApplication()->getVersion().'</>');
        $this->output->writeln('');
    }

    public function setOutput(&$output)
    {
        $this->output = $output;
    }

    public function runCommand(string $cmd, ?array $args = [])
    {
        $command = $this->getApplication()->find($cmd);

        return $command->run(new ArrayInput($args), $this->output);
    }
}