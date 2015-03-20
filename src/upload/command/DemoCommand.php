<?php
namespace upload\command;

use Knp\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
class DemoCommand extends Command
{	
    protected function configure()
    {
        $this->setName('demo')
		             ->setDescription('Comando para proposito de pruebas')
                            ->addArgument(
                                   'name',
                                   InputArgument::OPTIONAL,
                                   'nombre ?'
                               )
                 ->addOption(
               'upper',
               null,
               InputOption::VALUE_NONE,
               'Salida en mayusculas?'
            )
                 ->addOption(
        'iterations',
        null,
        InputOption::VALUE_REQUIRED,
        'Cuantos mensajes ?',
        1
    );
        
        
	}
    protected function execute(InputInterface $input, OutputInterface $output)
	{
            
        
        $output->writeln('Comando demostrativo-> salida de texto');
        
          $name = $input->getArgument('name');
         
           if ($name) {
            $text = 'Hola '.$name;
                } else {
                    $text = 'Hola!';
               }
        
             
             if ($input->getOption('upper')) {
            $text = strtoupper($text);
        }
              
         for ($i = 0; $i < $input->getOption('iterations'); $i++) {
                 $output->writeln($text);
         }
         
        // green text
       $output->writeln('<info>foo</info>');

       // yellow text
       $output->writeln('<comment>foo</comment>');

       // black text on a cyan background
       $output->writeln('<question>foo</question>');

       // white text on a red background
       $output->writeln('<error>foo</error>');
       
         
        }
        
        
        
}