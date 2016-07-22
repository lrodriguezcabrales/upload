<?php
namespace upload\command;

use upload\model\ventas;
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

class VentaUsadosCommand extends Command
{	
	
	public $server = 'http://162.242.223.46/sifinca/web/app.php/';
	public $serverRoot = 'http://162.242.223.46';
	
	public $localServer = 'http://10.102.1.22/';
	
	
	public $user= "sifincauno@araujoysegovia.com";
	public $pass="araujo123";
	public $token = null;
		
	
    protected function configure()
    {
        $this->setName('ventaUsados')
		             ->setDescription('Comando para pasar venta de usados');
	}
	
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, 
							   \Symfony\Component\Console\Output\OutputInterface $output)
	{

        $conn = new data(array(
            'server' =>'10.102.1.3'
            ,'user' =>'hherrera'
            ,'pass' =>'daniela201'
            ,'database' =>'sifinca' 
            ,'engine'=>'mssql'
        ));

        $conexion = new ventas($conn);

        $this->crearVentaUsados($conexion);
    }
        
    public function crearVentaUsados($conexion) {
    	
    	$urlVenta = $this->server.'catchment/main/saleproperty';
    	
    	$api = $this->SetupApi($urlVenta, $this->user, $this->pass);
    	
    	$ventas = $conexion->getVentas();
    	
    	$totalVentas = count($ventas);
    	
    	for ($i = 0; $i < $totalVentas; $i++) {
    		
    		$venta = $ventas[$i];
    		
    		$ventaSF2 = $this->searchVenta($venta['id_log_venta']);
    		
    		if(is_null($ventaSF2)){
    			
    			$inmueble = $this->searchProperty($venta['id_inmueble']);
    			
    			if($inmueble){
    				 
    				$asesor = $this->searchUsuarioByEmail($venta['email']);
    				$estado = $this->searchEstadoVenta($venta['estado']);
    				$vendedor = $this->searchOwner($inmueble['id']);
    				$comprador = $this->searchClient($venta['id_comprador']);
    				$oficina = $this->searchOffice($venta['id_sucursal']);
    				 
    				$fechaVenta = new \DateTime($venta['fecha_venta']);
    				$fechaVenta = $fechaVenta->format('Y-m-d');
    				
    				$bVenta = array(
    						'consecutive' => $venta['id_log_venta'],
    						'propertyValue' => $venta['valor_venta'],
    						'saleValue'=> $venta['valor_venta'],
    						'percentageCommission' => $venta['comision'],
    						'asesor' => $asesor,
    						'property' => $inmueble,
    						'statusSaleProperty' => $estado,
    						'seller' => $vendedor,
    						'buyer' => $comprador,
    						'office' => $oficina,
    						'saleDate' => $fechaVenta
    				);
    				 
    				$bjson = json_encode($bVenta);
    			
    				$result = $api->post($bVenta);
    			
    				$result = json_decode($result, true);
    				 
    				if($result['success'] == true){
    					echo "\nOk venta ".$venta['id_log_venta']."\n";
    					 
    				}else{
    					echo "\nError\n";
    						
    					print_r($result);
    					//echo "\n".$bjson."\n";
    					 
    				}
    			}
    			
    		}else{
    			echo "\nLa venta ya existe: ".$venta['id_log_venta']."\n";
    		}
    		
    		
    		
    		
    		
    	}
    }
    
    /**
     * Buscar inmueble en sifinca2 por codigo de sifinca1
     * @param unknown $propertySF1
     * @return NULL
     */
    function searchVenta($consecutive) {
    
    	$consecutive = $this->cleanString($consecutive);
    
    	$filter = array(
    			'value' => $consecutive,
    			'operator' => '=',
    			'property' => 'consecutive'
    	);
    	$filter = json_encode(array($filter));
    
    	$url = $this->server.'catchment/main/saleproperty?filter='.$filter;
    
    	$api = $this->SetupApi($url, $this->user, $this->pass);
    
    	$sale = $api->get();
    	$sale = json_decode($sale, true);
      
    	if($sale['total'] > 0){
    		 
    		return $sale['data'][0];
    		 
    	}else{
    		return null;
    	}
    
    }
    
    
    /**	
     * Buscar usuario en sifinca2 por el correo registrado en sifinca1
     * @param string $email
     * @return user
     */
    function searchUsuarioByEmail($email) {
    
    	$email = $this->cleanString($email);
    	 
    	if($email == 'inmobiliaria@araujoysegovia.com'){
    		$user = array('id' => 1);
    		return $user;
    	}
    	 
    	if($email == 'hherrera@araujoysegovia.com'){
    		$user = array('id' => 1);
    		return $user;
    	}
    	 
    	$filter = array(
    			'value' => $email,
    			'operator' => '=',
    			'property' => 'email'
    	);
    	
    	$filter = json_encode(array($filter));
    
    	$urlUser = $this->server.'admin/security/user?filter='.$filter;
    	 
    	$apiUser = $this->SetupApi($urlUser, $this->user, $this->pass);
    
    	$user = $apiUser->get();
    	$user = json_decode($user, true);
    
    
    	if($user['total'] > 0){
    		 
    		return $user['data'][0];
    		 
    	}else{
    		return $user = array('id' => 203);
    	}
    	 
    }
    
    /**	
     * Buscar inmueble en sifinca2 por codigo de sifinca1
     * @param unknown $propertySF1
     * @return NULL
     */
    function searchProperty($propertySF1) {
    
    	$propertySF1 = $this->cleanString($propertySF1);
    
    	$filter = array(
    			'value' => $propertySF1,
    			'operator' => '=',
    			'property' => 'consecutive'
    	);
    	$filter = json_encode(array($filter));
    
    	$urlProperty = $this->server.'catchment/main/property?filter='.$filter;
    
    	$apiProperty = $this->SetupApi($urlProperty, $this->user, $this->pass);
    
    	$property = $apiProperty->get();
    	$property = json_decode($property, true);
    
    
    	if($property['total'] > 0){
    		 
    		return $property['data'][0];
    		 
    	}else{
    		return null;
    	}
    
    }
        
    function searchEstadoVenta($estado) {
    	
    	if($estado == '0'){
    		//En tramite
    		$target = array('id'=>'b5e911c7-a651-48f0-bd7c-dae6b494e6cc');
    	}else{
    		$url = $this->server.'admin/sifinca/mapper/statusSaleProperty/'.$estado;
    		
    		//echo "\n".$url."\n";
    		 
    		$api = $this->SetupApi($url, $this->user, $this->pass);
    		
    		$mapper = $api->get();
    		$mapper = json_decode($mapper, true);
    		 
    		$target = null;
    		 
    		if($mapper['total'] > 0){
    		
    			$target = $mapper['data']['0']['idTarget'];
    		
    			if(!is_null($target)){
    				 
    				$target = array('id'=>$target);
    				 
    			}
    		}
    	}
    	
    	return $target;
    	
    }

    function searchClient($identificacion){
    
    	$identificacion = trim($identificacion);
    	 
    	$relations = array(
    			'identity'
    	);
    	$relations = json_encode($relations);
    	 
    	$filter = array(
    			'value' => $identificacion,
    			'operator' => 'equal',
    			'property' => 'identity.number'
    	);
    	$filter = json_encode(array($filter));
    
    	 
    	$urlClientSF2 = $this->server.'crm/main/zero/client?relations='.$relations.'&filter='.$filter;
    	 
    	$apiClientSF2 = $this->SetupApi($urlClientSF2, $this->user, $this->pass);
  	     
    	$clientSF2 = $apiClientSF2->get();
    
    	$clientSF2 = json_decode($clientSF2, true);
    	 
    	if($clientSF2['total'] > 0){
    
    		return $clientSF2['data'][0];
    
    	}else{
    		return null;
    	}
    
    }
    
    /**	
     * Buscar el propietario del inmueble
     * @param $propertyId
     */
    function searchOwner($propertyId) {
    	
    	$url = $this->server.'catchment/main/owner/by/property/'.$propertyId;
    	
    	$api = $this->SetupApi($url, $this->user, $this->pass);
    	
    	$owner = $api->get();
    	
    	$owner = json_decode($owner, true);
    	
    	if($owner['total'] > 0){
    	
    		return $owner['data'][0]['agreement']['owner'][0]['client'];
    	
    	}else{
    		return null;
    	}
    	
    }

	function searchOffice($oficina) {
		
		$url = $this->server.'admin/sifinca/mapper/propertyOffice.CTG/'.$oficina;
		
		$api = $this->SetupApi($url, $this->user, $this->pass);
		 
		$mapper = $api->get();
		$mapper = json_decode($mapper, true);
		//print_r($stratumMapper);
		$target = null;
		if($mapper['total'] > 0){
			$target = $mapper['data']['0']['idTarget'];
			if(!is_null($target)){
		
				$target = array('id'=>$target);		
		
			}
		}
		
		return $target;
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