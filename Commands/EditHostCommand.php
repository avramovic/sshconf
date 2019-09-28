<?php namespace Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EditHostCommand extends BaseCommand
{
    protected static $defaultName = 'edit';

    protected function configure()
    {
        $this->setDescription('Updates an existing SSH connection')
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('host', InputArgument::REQUIRED),
                    new InputOption('name', 'm', InputOption::VALUE_REQUIRED),
                    new InputOption('hostname', 'd', InputOption::VALUE_REQUIRED),
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
        $hostname     = $input->getOption('hostname');
        $name         = $input->getOption('name');
        $user         = $input->getOption('user');
        $port         = $input->getOption('port');
        $identityfile = $input->getOption('identityfile');
        $loglevel     = $input->getOption('loglevel');
        $compression  = $input->getOption('compression');

        if (!$this->sshConf->has($host)) {
            $this->error('SSH entry "'.$host.'" does not exist, please use: sshconf add or sshconf ls');
        }

        if (is_null($hostname) && is_null($port) && is_null($name) && is_null($user) && is_null($identityfile) && is_null($loglevel) && is_null($compression)) {
            $this->error('Please specify at least one option to modify!');
        }

        if (!is_null($name)) {
            if (empty($name)) {
                $this->error("You can't rename a SSH connection without a new name!");
            }

            $tmp = $this->sshConf->get($host);
            $this->sshConf->put($name, $tmp)->remove($host);
            $output->writeln('Renamed '.$host.' to: '.$name);
            $host = $name;
        }


        if (!is_null($hostname)) {

            if (empty($hostname)) {
                $this->error("You can't remove the hostname from a SSH connection!");
            }

            $this->sshConf->setValue($host, 'hostname', $hostname);
            $output->writeln('Updated HostName: '.$hostname);
        }

        if (!is_null($port)) {
            if ($port == '') {
                $this->sshConf->setValue($host, 'port', null);
                $output->writeln('Removed Port');
            } else {
                $this->sshConf->setValue($host, 'port', $port);
                $output->writeln('Updated Port: '.$port);
            }
        }

        if (!is_null($user)) {
            if ($user == '') {
                $this->sshConf->setValue($host, 'user', null);
                $output->writeln('Removed User');
            } else {
                $this->sshConf->setValue($host, 'user', $user);
                $output->writeln('Updated User: '.$user);
            }
        }

        if (!is_null($identityfile)) {
            if ($identityfile == '') {
                $this->sshConf->setValue($host, 'identityfile', null);
                $output->writeln('Removed IdentityFile');
            } else {
                $identityfile = realpath($identityfile) ? realpath($identityfile) : $identityfile;
                $this->sshConf->setValue($host, 'identityfile', $identityfile);
                $output->writeln('Updated IdentityFile: '.$identityfile);
            }
        }

        if (!is_null($loglevel)) {
            if ($loglevel == '') {
                $this->sshConf->setValue($host, 'loglevel', null);
                $output->writeln('Removed LogLevel');
            } else {
                $this->sshConf->setValue($host, 'loglevel', $loglevel);
                $output->writeln('Updated LogLevel: '.$loglevel);

            }
        }

        if (!is_null($compression)) {
            if ($compression == '') {
                $this->sshConf->setValue($host, 'compression', null);
                $output->writeln('Removed Compression');
            } else {
                $this->sshConf->setValue($host, 'compression', $compression);
                $output->writeln('Updated Compression');
            }
        }

        $output->writeln('');

        $this->sshConf->save();

        $this->info(sprintf('Successfully edited "%s" SSH connection.', $host));
    }
}