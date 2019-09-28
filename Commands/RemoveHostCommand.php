<?php namespace Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveHostCommand extends BaseCommand
{
    protected static $defaultName = 'rm';

    protected function configure()
    {
        $this->setDescription('Removes an existing SSH connection')
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

        $this->sshConf->remove($host)->save();

        $this->info(sprintf('Successfully removed "%s" SSH connection.', $host));
    }
}