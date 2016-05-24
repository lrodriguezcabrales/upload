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

class PublicarInmuebleCommand extends Command
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
	public $token = null;	
	
	
	protected function configure()
	{
		$this->setName('publicar')
		->setDescription('Comando para publicar inmuebles');
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
	
	
		$this->crearPublicacion();
	}
	
	function crearPublicacion() {
		 
		
		$urlInmueblesSF2 = $this->server.'catchment/main/property/for/publish';
		
		echo "\n".$urlInmueblesSF2."\n";
		
		//$urlInmueblesSF2 = $this->server.'catchment/main/property/light/combobox?filter='.$filter;
		 
		$apiInmueblesSF2 = $this->SetupApi($urlInmueblesSF2, $this->user, $this->pass);
		
		 
		$inmueblesSF2 = $apiInmueblesSF2->get();
		$inmueblesSF2 = json_decode($inmueblesSF2, true);
		
		
		echo "\nTotal inmuebles SF2: ".$inmueblesSF2['total']."\n";
		 
		$totalInmueblesSF2 = $inmueblesSF2['total'];
		
		
		$urlPublication = $this->server.'catchment/main/publication';
		$apiPublication = $this->SetupApi($urlPublication, $this->user, $this->pass);
		
		$startTime= new \DateTime();
		$porDonde = 0;
				
		for ($i = 0; $i < $totalInmueblesSF2; $i++) {
			
			$inmueble = $inmueblesSF2['data'][$i];
									
			$bpublicacion = array(
				'active' => true,
				'datePublication' => $inmueble['consignmentdate'],
				'property' => array('id' => $inmueble['id'])
			);
			
			$json = json_encode($bpublicacion);
			//echo "\n".$json."\n";
					
			$result = $apiPublication->post($bpublicacion);
			
			$result = json_decode($result, true);
			
			
			if($result['success'] == true){
				echo "\nOk, inmueble ".$inmueble['consecutive']."\n";
			
			
			}else{
				echo "\nError ".$inmueble['consecutive']."\n";
			}
			
			$porDonde++;
			echo "\nVamos por: ".$porDonde."\n";
				
		}
		
		$finalTime = new \DateTime();
		
		$diff = $startTime->diff($finalTime);
				
		echo "\n\n Fecha inicial: ".$startTime->format('Y-m-d H:i:s')."\n";
		echo "\n Fecha final: ".$finalTime->format('Y-m-d H:i:s')."\n";
		echo "\n Diferencia: ".$diff->format('%h:%i:%s')."\n";
				
	}
	
	function login() {
	
		if(is_null($this->token)){
	
			echo "\nEntro a login\n";
	
			$url= $this->server."login";
			$headers = array(
					'Accept: application/json',
					'Content-Type: application/json',
			);
			 
			$a = new api($url, $headers);
			 
	
			$result = $a->post(array("user"=>$this->user,"password"=>$this->pass));
			$result = json_decode($result, true);
			 
			//print_r($result);
	
			//echo "\n".$result['id']."\n";
	
	
			if(isset($result['code'])){
				if($result['code'] == 401){
	
					$this->login();
				}
			}else{
	
				if(isset($result['id'])){
	
					$this->token = $result['id'];
				}else{
					echo "\nError en el login\n";
					$this->token = null;
				}
	
			}
		}
	
	
	}
	
	function SetupApi($urlapi,$user,$pass){
	
		$headers = array(
				'Accept: application/json',
				'Content-Type: application/json',
		);
	
		$a = new api($urlapi, $headers);
	
		$this->login();
	
		if(!is_null($this->token)){
	
			$headers = array(
					'Accept: application/json',
					'Content-Type: application/json',
					//'x-sifinca: SessionToken SessionID="56cf041b296351db058b456e", Username="lrodriguez@araujoysegovia.net"'
					'x-sifinca: SessionToken SessionID="'.$this->token.'", Username="'.$this->user.'"',
			);
	
			//     	print_r($headers);
	
			$a->set(array('url'=>$urlapi,'headers'=>$headers));
	
			//print_r($a);
	
			return $a;
	
		}else{
			echo "\nToken no valido\n";
		}
	
	
	}
	
}