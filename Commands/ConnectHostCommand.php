<?php namespace Commands;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ConnectHostCommand extends BaseCommand
{
    protected static $defaultName = 'connect';

    protected function configure()
    {
        $this->setDescription('Initiates a SSH connection')
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('host', InputArgument::OPTIONAL),
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->appName($output);

        $host = $input->getArgument('host');

        if (empty($host)) {
            if ($this->sshConf->count() < 1) {
                //show help
                $this->error('No SSH connections found, showing help!', null);
                $this->info('');
                return $this->runCommand('help');
            }

            if ($this->sshConf->count() == 1) {
                $host = array_keys($this->sshConf->all())[0];
            } else {
                $helper = $this->getHelper('question');
                $question = new ChoiceQuestion(
                    'Please select SSH connection:',
                    // choices can also be PHP objects that implement __toString() method
                    array_merge(['CANCEL'], array_keys($this->sshConf->all())),
                    0
                );
                $question->setErrorMessage('SSH connection #%s is invalid.');

                $host = $helper->ask($input, $output, $question);

                if ($host == 'CANCEL') {
                    $this->info('<error>Exit.</error>');
                    return 0;
                }

                $output->writeln(sprintf('Connecting to %s... ',$host));
            }
        }

        if (!$this->sshConf->has($host)) {
            $this->error( 'SSH entry "'.$host.'" not found please use: sshconf ls');
        }

        proc_close(proc_open('ssh '.$host, [STDIN, STDOUT, STDOUT], $_));

        return 0;
    }
}