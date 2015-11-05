<?php
namespace upload\command;
use Knp\Command\Command;
class DemoCommand extends Command
{	
    protected function configure()
    {
        $this->setName('demo')
		             ->setDescription('Generic all-purpose work script');
	}
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{
            $output->writeln('demo command');
        }
        
        
}