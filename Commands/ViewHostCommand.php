<?php namespace Commands;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ViewHostCommand extends BaseCommand
{
    protected static $defaultName = 'view';

    protected function configure()
    {
        $this->setDescription('Lists existing SSH connections')
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('host', InputArgument::REQUIRED),
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->appName($output);

        $host = $input->getArgument('host');

        if (!$this->sshConf->has($host)) {
            $this->error(sprintf('SSH entry "%s" does not exist, use: sshconf ls', $host));
        }

        $entry = $this->sshConf->get($host);

        foreach ($entry as $key => $value) {
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

        return 0;
    }
}