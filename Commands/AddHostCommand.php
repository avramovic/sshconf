<?php namespace Commands;

use Symfony\Component\Console\Helper\Table;
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
                    new InputOption('user', 'U', InputOption::VALUE_REQUIRED),
                    new InputOption('port', 'P', InputOption::VALUE_REQUIRED),
                    new InputOption('identityfile', 'I', InputOption::VALUE_REQUIRED),
                    new InputOption('extra', 'E', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY),
                    new InputOption('value', 'A', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY),
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
        $extras       = $input->getOption('extra');
        $values       = $input->getOption('value');

        if ($this->sshConf->has($host)) {
            $this->error('SSH entry "'.$host.'" already exists, please use: sshconf edit');
        }

        if (count($extras) != count($values)) {
            $this->error('Number of extras and values must be equal!');
        }

        $conf = [
            'hostname' => $hostname,
        ];

        if (!empty($user)) {
            $conf['user'] = $user;
        }

        if (!empty($port)) {
            $conf['port'] = $port;
        }

        if (!empty($identityfile)) {
            $conf['identityfile'] = $identityfile;
        }

        foreach ($extras as $key => $extra) {
            $value = $values[$key] ?? null;

            if (!is_null($value)) {
                if ($value != '') {
                    $conf[strtolower($key)] = $value;

                }
            }
        }

        $this->sshConf->put($host, $conf)->save();

        $data = [];

        foreach ($conf as $key => $value) {
            $data[] = [strtoupper($key), $value];
        }

        $table = new Table($output);

        $table
            ->setHeaders(['Option', 'Value'])
            ->setRows($data);

        $table
            ->setHeaderTitle('SSH: '.$host)
            ->setFooterTitle('Total: '.count($data))
            ->render();

        $output->writeln('');
        $this->info(sprintf('Successfully created new "%s" SSH connection.', $host));

        return 0;
    }
}