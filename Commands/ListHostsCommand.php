<?php namespace Commands;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListHostsCommand extends BaseCommand
{
    protected static $defaultName = 'ls';

    protected function configure()
    {
        $this->setDescription('Lists existing SSH connections');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->appName($output);

        $data = [];

        foreach ($this->sshConf->all() as $host => $conf) {
            $data[] = [$host, $conf['hostname'] . (isset($conf['port']) ? ':'.$conf['port'] : ''), $conf['user'] ?? get_current_user()];
        }

        $table = new Table($output);

        $table
            ->setHeaders(['Host', 'HostName', 'User'])
            ->setRows($data);


        $table
            ->setHeaderTitle('SSH connections')
            ->setFooterTitle('Total: '.count($this->sshConf->all()))
            ->render();

        return 0;
    }
}