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

class UpdateInmuebleCommand extends Command
{	
	
 	public $server = 'http://www.sifinca.net/sifinca/web/app.php/';
 	public $serverRoot = 'http://www.sifinca.net/';
	
	public $localServer = 'http://10.102.1.22/';
	
// 	public $server = 'http://10.102.1.22/sifinca/web/app.php/';
// 	public $serverRoot = 'http:/10.102.1.22/';

	//public $server = 'http://162.242.245.207/sifinca/web/app_dev.php/';
	//public $serverRoot = 'http:/162.242.245.207/';
	
	public $user= "sifincauno@araujoysegovia.com";
	public $pass="araujo123";

	
    protected function configure()
    {
        $this->setName('updateInmuebles')
		             ->setDescription('Comando para obtener datos de inmuebles SF1');
	}
	
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{

        $output->writeln("Datos de inmuebles SF1 \n");

        $conn = new data(array(
            'server' =>'10.102.1.3'
            ,'user' =>'hherrera'
            ,'pass' =>'daniela201'
            ,'database' =>'sifinca' 
            ,'engine'=>'mssql'
        ));

      

        //$this->mapperTipoInmueble($inmueblesCtg);
        //$this->mapperTipoInscripcion($inmueblesCtg);
        //$this->mapperCiudades($inmueblesCtg);  
        //$this->mapperBarrios($inmueblesCtg);
        //$this->mapperDestinacion($inmueblesCtg);
        //$this->mapperEstratos($inmueblesCtg);
        //$this->mapperSucursales($inmueblesCtg);
        //$this->mapperClasificaciones($inmueblesCtg);
        //$this->mapperRazonesDeRetiro($inmueblesCtg);
        //$this->mapperCaracteristicas($inmueblesCtg);
        //$this->mapperEstadosInmueble($inmueblesCtg);
        //$this->mapperTiposServicio($inmueblesCtg);
        
        $inmuebles = $inmueblesCtg->getInmueblesUpdate();
	   // $this->buildInmuebles($inmuebles, $inmueblesCtg);
	   
        $this->updateInmuebles($inmuebles, $inmueblesCtg);
              
        //$this->buildFotos($inmueblesCtg);

        //$this->subirInmueblesConError();

        //$this->mapperTipoEdificio($inmueblesCtg);
        //$this->buildEdificios($inmueblesCtg);
        
    }
    
    
    function updateTiposDeDocumento() {
    	
    }
    
    
    function searchDocumentType() {
    	
    
    	$filter = array(
    			'value' => 'Arriendos',
    			'operator' => 'equal',
    			'property' => 'container.name'
    	);
    	$filter = json_encode(array($filter));
    
    	$urlDocumentType = $this->server.'archive/main/documenttype?filter='.$filter;
    	 
    	 
    	$apiDocumentType = $this->SetupApi($urlDocumentType, $this->user, $this->pass);
    
    	 
    	$documentType = $apiDocumentType->get();
    	$documentType = json_decode($documentType, true);
    
    	if($documentType['total'] > 0){
    		 
    		return $documentType['data'];
    		 
    	}else{
    		return null;
    	}
    }
    
}