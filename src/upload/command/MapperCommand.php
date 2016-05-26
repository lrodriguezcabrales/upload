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

class MapperCommand extends Command
{

	public $server = 'http://162.242.223.46/sifinca/web/app.php/';
	public $serverRoot = 'http://162.242.223.46';
	
	public $localServer = 'http://10.102.1.22/';
		
	public $user= "sifincauno@araujoysegovia.com";
	public $pass="araujo123";
	public $token = null;
		
	protected function configure(){
		$this->setName('mapper')
			 ->setDescription('mapper');
	}
	
	protected function execute(\Symfony\Component\Console\Input\InputInterface $input,
			\Symfony\Component\Console\Output\OutputInterface $output)
	{
	
		$this->mapperEstadoVentaUsados();
	}
	
	function mapperEstadoVentaUsados() {
	
		$json = '[
				  {
				    "name": "statusSaleProperty",
				    "idSource": "0",
				    "idTarget": "b5e911c7-a651-48f0-bd7c-dae6b494e6cc",
				    "label": "En tramite"
				  },
				  {
				    "name": "statusSaleProperty",
				    "idSource": "1",
				    "idTarget": "633dd304-c690-47aa-8c52-8d3cf2528374",
				    "label": "Finalizada"
				  },
				  {
				    "name": "statusSaleProperty",
				    "idSource": "-1",
				    "idTarget": "eb3b2f6f-d6e8-4caf-bd88-6a2f5de3a330",
				    "label": "Desistida"
				  }
				]';
		
		$data = json_decode($json, true);
	
		//print_r($data);
		$total = 0;
		foreach ($data as $d) {
			$urlapiMapper = $this->server.'admin/sifinca/mapper';
			$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
			 
			$apiMapper->post($d);
			$total++;
		}
		 
		echo "Mapeados: ".$total."\n";
	}
	

	/**
	 * Eliminar espacios en blanco seguidos
	 * @param unknown $string
	 * @return unknown
	 */
	function  cleanString($string){
		$string = trim($string);
		$string = str_replace('&nbsp;', ' ', $string);
		$string = preg_replace('/\s\s+/', ' ', $string);
		return $string;
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