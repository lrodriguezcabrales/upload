<?php
namespace upload\command;

use upload\model\clientsCartagena;
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

class clientesDuplicadosCommand extends Command
{	
	
	public $server = 'http://www.sifinca.net/sifinca/web/app.php/';
	public $serverRoot = 'http://www.sifinca.net/';
	//public $user= "sifinca@araujoysegovia.com";
	public $user= "sifincauno@araujoysegovia.com";
	public $pass="araujo123";
		
	public $colombia = '8701307b-d8bd-49f1-8a91-5d0f7b8046b3';
	public $bolivar = 'a7ff9a96-5a2f-4bba-94aa-d45a15f67f66';
	public $cartagena = '994b009d-50a5-44dd-8060-878a10f4dd00';
	
	public $idTypeCedula = '6f80343e-f629-492a-80d1-a7e197c7cf48';
	
	public $contactTypeOther = 'ac76deff-6371-4264-b724-b24b98803b94';
	
	public  $typeAddressHome = '8b8b75ae-6338-461f-8bbd-fc1283621d83';
	public  $typeAddressCorrespondencia = 'e8e9cc62-ec79-4453-8247-a57cc1cf4712';
    
    protected function configure()
    {
        $this->setName('clientes')
		             ->setDescription('Comando para obtener datos de cliente SF1');
	}
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{

        $output->writeln("Datos de cliente SF1 \n");

        $conn = new data(array(
            'server' =>'10.102.1.3'
            ,'user' =>'hherrera'
            ,'pass' =>'daniela201'
            ,'database' =>'sifinca' 
            ,'engine'=>'mssql'
        ));

        $cb = new clientsCartagena($conn);

        //$this->mapperIdentificaciones($cb);

        //$this->mapperPaises($cb);
        //$this->mapperCiudades($cb);
        //$this->mapperCiudadesNotFound($cb);
        //$this->mapperEstadosCiviles($cb);
        //$this->mapperNivelDeEstudio($cb);
        //$this->mapperTipoDeContribuyente($cb);
       	
		$this->eliminarCliente($clients);
        //print_r($clients);

        //$this->banks($cb);
    }
    
    function eliminarCliente() {
    	
    	$clients = $cb->clientsConEspacio();
    	
    	
    	$urldeleteId = $this->server.'catchment/main/property/'.$inmueble['id_inmueble'].'/status/'.$inmueble['promocion'];
    	
    	foreach ($clients as $c) {
    		
    		
    		
    	}
    }
    
    
    protected function SetupApi($urlapi,$user,$pass){
    
    	$url= $this->server."login";
    	$headers = array(
    			'Accept: application/json',
    			'Content-Type: application/json',
    	);
    
    	$a = new api($url, $headers);
    
    	//print_r($a);
    	 
    	$result= $a->post(array("user"=>$user,"password"=>$pass));
    
    	//      	echo "\nAqui result\n";
    	//     	print_r($result);
    
    
    
    	$data=json_decode($result);
    
    	//print_r($data);
    	 
    	$token = $data->id;
    
    	$headers = array(
    			'Accept: application/json',
    			'Content-Type: application/json',
    			//'x-sifinca:SessionToken SessionID="56619d57ed060f8c57c1109d", Username="sifincauno@araujoysegovia.com"'
    			'x-sifinca: SessionToken SessionID="'.$token.'", Username="'.$user.'"',
    	);
    
    	//     	print_r($headers);
    	 
    	$a->set(array('url'=>$urlapi,'headers'=>$headers));
    
    	//print_r($a);
    	 
    	return $a;
    
    }
    