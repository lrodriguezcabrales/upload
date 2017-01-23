<?php 
namespace upload\command\Avaluos;

use upload\model\avaluo;
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

class UpdateAvaluosCommand extends Command
{
	public $server = 'http://www.sifinca.net/sifinca/web/app.php/';
	public $serverRoot = 'http://www.sifinca.net/';	
	public $localServer = 'http://10.102.1.22/';	
	public $user = "sifincauno@araujoysegovia.com";
	public $pass = "araujo123";
	public $token = null;
	
	protected function configure()
	{
		$this->setName('avaluosUpdate')->setDescription('Comando para modificaciones de avaluos y sus clientes asociados');
	}
	
	protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
	{	
		$output->writeln("Modificacion de clientes de avaluos en SF1 \n");	
		$conn = new data(
			array(
				'server' => '10.102.1.3',
				'user' => 'hherrera',
				'pass' => 'daniela201',
				'database' => 'sifinca',
				'engine' => 'mssql'
			)
		);
			
		// $avaluosUpdate = new avaluo($conn);		 
		// $this->getAvaluosUpdate($avaluosUpdate);	
	}
	
	public function getAvaluosUpdate($avaluosUpdate)
	{
		$relaciones = array("client");
		$filter = array();		
		$filter[] = array(
			'value' => '09-12-2016',
			'operator' => '+>=',
			'property' => 'entrydate'
		);
		 
		$filter = json_encode($filter);
		$relaciones = json_encode($relaciones);
		
		$urlAvaluosUpdate = $this->server.'appraisals/main/zero/appraisals?relations='.$relaciones."&filter=".$filter;
		
		echo "\n-----------------------------";
		echo "\n".$urlAvaluosUpdate."\n";
		
		$apiAvaluos = $this->SetupApi($urlAvaluosUpdate, $this->user, $this->pass);
		$avaluo = $apiAvaluos->get();
		$avaluo = json_decode($avaluo, true);			
		
		foreach ($avaluo['data'] as $kAvaluo => $vAvaluo)
    	{    		
    		$oneAvaluo = array();
    		
    		if ($vAvaluo['entrydate'])
    		{
    			$date = $vAvaluo['entrydate']['date'];
    			$oneAvaluo['fecha_solicitud'] = date("Y-m-d H:i:s", strtotime($date));
    		
    			echo "\n------------------------------\n";
    			echo "Fecha solicitud ".$oneAvaluo['fecha_solicitud']."\n";
    			echo "Avaluo: ".$vAvaluo['appraisalNumber']."\n";
    		}
    		
    		if ($vAvaluo['client'])
    		{
    			if ($vAvaluo['client']['identity'])
    			{
    				$identificacionClient = $vAvaluo['client']['identity']['number'];
    				 
    				if ($avaluosUpdate->exitClient($identificacionClient))
    				{
    					echo "El cliente ".$vAvaluo['client']['name']." SI existe.\n";
    					$this->updateCliente($avaluosUpdate, $vAvaluo['client']);
    				}    				
    			}
    		}
    	}
	}
	
	public function updateCliente($avaluo, $cliente)
	{		
		if ($cliente['client_type'] == 3)
		{
			$param = array(
				'id_cliente' => $cliente['identity']['number'],				
				'nom_empresa' => $cliente['comercialName']
			);
	
			$avaluo->updateClientesJuridico($param);
		}
		else if ($cliente['client_type'] == 2)
		{
			$param = array(
				'id_cliente' => $cliente['identity']['number'],				
				'nombre' => $cliente['firstName']." ".$cliente['secondName'],
				'apellido'=> $cliente['lastName']." ".$cliente['secondLastname']
			);
	
			$avaluo->updateClientes($param);	
		}
	}
	
	function login()
	{
		if (is_null($this->token))
		{
			echo "\nEntro a login\n";
	
			$url= $this->server."login";
			$headers = array(
				'Accept: application/json',
				'Content-Type: application/json',
			);
			 
			$a = new api($url, $headers);
	
			$result = $a->post(array("user"=>$this->user,"password"=>$this->pass));
			$result = json_decode($result, true);
			
			if (isset($result['code']))
			{
				if ($result['code'] == 401)
				{
					$this->login();
				}
			}
			else
			{
				if (isset($result['id']))
				{
					$this->token = $result['id'];
				}
				else
				{
					echo "\nError en el login\n";
					$this->token = null;
				}
			}
		}
	}
	
	function SetupApi($urlapi, $user, $pass)
	{
		$headers = array(
			'Accept: application/json',
			'Content-Type: application/json',
		);
	
		$a = new api($urlapi, $headers);
	
		$this->login();
	
		if (!is_null($this->token))
		{
			$headers = array(
				'Accept: application/json',
				'Content-Type: application/json',				
				'x-sifinca: SessionToken SessionID="'.$this->token.'", Username="'.$this->user.'"',
			);
	
			$a->set(array('url'=>$urlapi,'headers'=>$headers));
	
			return $a;
		}
		else
		{
			echo "\nToken no valido\n";
		}
	}
}
?>