<?php namespace Commands;

use Symfony\Component\Console\Helper\Table;
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
                    new InputOption('name', 'N', InputOption::VALUE_REQUIRED),
                    new InputOption('hostname', 'H', InputOption::VALUE_REQUIRED),
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
        $hostname     = $input->getOption('hostname');
        $name         = $input->getOption('name');
        $user         = $input->getOption('user');
        $port         = $input->getOption('port');
        $identityfile = $input->getOption('identityfile');
        $extras       = $input->getOption('extra');
        $values       = $input->getOption('value');

        if (count($extras) != count($values)) {
            $this->error('Number of extras and values must be equal!');
        }

        if (!$this->sshConf->has($host)) {
            $this->error('SSH entry "'.$host.'" does not exist, please use: sshconf add or sshconf ls');
        }

        if (is_null($hostname) && is_null($port) && is_null($name) && is_null($user) && is_null($identityfile) && empty($extras) && empty($values)) {
            $this->error('Please specify at least one option to modify!');
        }

        $data = [];

        $old = $this->sshConf->get($host);

        if (!is_null($name)) {
            if (empty($name)) {
                $this->error("You can't rename a SSH connection without a new name!");
            }

            $data[] = ['HOST', $host, $name];

            $this->sshConf->put($name, $old)->remove($host);
            $host = $name;
        }



        if (!is_null($hostname)) {

            if (empty($hostname)) {
                $this->error("You can't remove the hostname from a SSH connection!");
            }

            $this->sshConf->setValue($host, 'hostname', $hostname);

            $data[] = ['HOSTNAME', $old['hostname'], $hostname];
        }

        if (!is_null($port)) {
            if ($port == '') {
                $this->sshConf->setValue($host, 'port', null);
            } else {
                $this->sshConf->setValue($host, 'port', $port);
            }

            $data[] = ['HOSTNAME', $old['port'] ?? '<options=bold>NULL</>', empty($port) ? '<options=bold>NULL</>' : $port];

        }

        if (!is_null($user)) {
            if ($user == '') {
                $this->sshConf->setValue($host, 'user', null);
            } else {
                $this->sshConf->setValue($host, 'user', $user);
            }


            $data[] = ['USER', $old['user'] ?? '<options=bold>NULL</>', empty($user) ? '<options=bold>NULL</>' : $user];

        }

        if (!is_null($identityfile)) {
            if ($identityfile == '') {
                $this->sshConf->setValue($host, 'identityfile', null);
            } else {
                $this->sshConf->setValue($host, 'identityfile', $identityfile);
            }

            $data[] = ['IDENTITYFILE', $old['identityfile'] ?? '<options=bold>NULL</>', empty($identityfile) ? '<options=bold>NULL</>' : $identityfile];

        }

        foreach ($extras as $key => $extra) {
            $value = $values[$key] ?? null;

            if (!is_null($value)) {
                if ($value == '') {
                    $this->sshConf->setValue($host, $extra, null);
                } else {
                    $this->sshConf->setValue($host, $extra, $value);
                }
            }

            $data[] = [strtoupper($extra), $old[strtolower($extra)] ?? '<options=bold>NULL</>', empty($value) ? '<options=bold>NULL</>' : $value];

        }

        $this->sshConf->save();

        $table = new Table($output);

        $table
            ->setHeaders(['Option', 'Old value', 'New value'])
            ->setRows($data);

        $table
            ->setHeaderTitle('SSH: '.$host)
            ->setFooterTitle('Total: '.count($data))
            ->render();

        $output->writeln('');
        $this->info(sprintf('Successfully edited "%s" SSH connection.', $host));
    }
}