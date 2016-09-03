<?php
namespace upload\command;

use upload\model\inmueblesCartagena;
use upload\lib\data;
use upload\lib\api;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use GearmanClient;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class DeleteFolderFotosCommand extends Command
{	
	
 	public $server = 'http://www.sifinca.net/bogotaServer/web/app.php/';
 	public $serverRoot = 'http://www.sifinca.net/';
	
	public $localServer = 'http://10.102.1.22/';
	
	public $user= "sifincauno@araujoysegovia.com";
	public $pass="araujo123";
	public $token = null;
	
	public $headerCurl = null;
	
    protected function configure()
    {
        $this->setName('deleteFolderFotos')
		             ->setDescription('Eliminar folders de fotos');
	}
	
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{

        
		$result= shell_exec('rm -rf /var/www/html/upload3/fotosinmueble/*');
		$result= shell_exec('rm -rf /var/www/html/upload3/fotosinmuebleMonteria/*');
		$result= shell_exec('rm -rf /var/www/html/upload3/fotosinmuebleBogota/*');
		
		$result= shell_exec('chmod -R 777 /var/www/html/upload3/fotosinmueble');
		$result= shell_exec('chmod -R 777 /var/www/html/upload3/fotosinmuebleMonteria');
		$result= shell_exec('chmod -R 777 /var/www/html/upload3/fotosinmuebleBogota');
        
    }
    
    function buildFotos($inmueblesCtg) {}
}
    