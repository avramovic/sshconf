<?php namespace Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AddHostCommand extends BaseCommand
{
    protected static $defaultName = 'add';

    protected function configure()
    {
        $this->setDescription('Adds a new SSH connection')
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('host', InputArgument::REQUIRED),
                    new InputArgument('hostname',InputArgument::REQUIRED),
                    new InputOption('user', 'u', InputOption::VALUE_REQUIRED),
                    new InputOption('port', 'p', InputOption::VALUE_REQUIRED),
                    new InputOption('identityfile', 'i', InputOption::VALUE_REQUIRED),
                    new InputOption('loglevel', 'l', InputOption::VALUE_REQUIRED),
                    new InputOption('compression', 'c', InputOption::VALUE_REQUIRED),
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->appName($output);

        $host         = $input->getArgument('host');
        $hostname     = $input->getArgument('hostname');
        $user         = $input->getOption('user');
        $port         = $input->getOption('port');
        $identityfile = $input->getOption('identityfile');
        $loglevel     = $input->getOption('loglevel');
        $compression  = $input->getOption('compression');

        if ($this->sshConf->has($host)) {
            $this->error('SSH entry "'.$host.'" already exists, please use: sshconf edit');
        }

        $conf = [
            'hostname' => $hostname,
            'user'     => $user,
        ];

        if (!empty($port)) {
            $conf['port'] = $port;
        }

        if (!empty($identityfile)) {
            $conf['identityfile'] = !empty(realpath($identityfile)) ? realpath($identityfile) : $identityfile;
        }

        if (!empty($loglevel)) {
            $conf['loglevel'] = $loglevel;
        }

        if (!empty($compression)) {
            $conf['compression'] = $compression;
        }

        $this->sshConf->put($host, $conf)->save();

        $this->info(sprintf('Successfully created new "%s" SSH connection.', $host));
    }
}