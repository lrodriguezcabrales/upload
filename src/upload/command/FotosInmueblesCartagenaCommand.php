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

class FotosInmueblesCartagenaCommand extends Command
{	
	
	public $server = 'http://www.sifinca.net/sifinca/web/app.php/';
	public $serverRoot = 'http://www.sifinca.net/';
	
 	//public $server = 'http://10.101.1.95/sifinca/web/app.php/';
 	//public $serverRoot = 'http://10.101.1.95/';
	
// 	public $server = 'http://10.102.1.22/sifinca/web/app.php/';
// 	public $serverRoot = 'http:/10.102.1.22/';
	
	public $user= "sifincauno@araujoysegovia.com";
	public $pass="araujo123";
	public $token = null;
	
	public $headerCurl = null;
	
    protected function configure()
    {
        $this->setName('fotosInmuebles')
		             ->setDescription('Obtener y guardar fotos inmuebles cartagena');
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

        $inmueblesCtg = new inmueblesCartagena($conn);

        
        //$inmuebles = $inmueblesCtg->getInmuebles();
	    //$this->buildInmuebles($inmuebles, $inmueblesCtg);
      
        $this->buildFotos($inmueblesCtg);
        
    }
    
    function buildFotos($inmueblesCtg) {
    
    
    	$urlInmueblesSF2 = $this->server.'catchment/main/property/only/ids';
    	
    	//echo "\n".$urlInmueblesSF2."\n";
    	
    	$apiInmueblesSF2 = $this->SetupApi($urlInmueblesSF2, $this->user, $this->pass);
    
    	//echo "\n\n";
    	//print_r($apiInmueblesSF2);
    	    	
    	//$h ="x-sifinca:SessionToken SessionID=\"$token\", Username=\"sifinca@araujoysegovia.com\"";
    	$h = $this->headerCurl;
    	  	
    	
    	$inmueblesSF2 = $apiInmueblesSF2->get();
    	$inmueblesSF2 = json_decode($inmueblesSF2, true);
    
		//31419
    	echo "\nTotal inmuebles SF2: ".$inmueblesSF2['total']."\n";
    	
    	$totalInmueblesSF2 = $inmueblesSF2['total'];
    	
    	$totalInmueblesSF2 = $totalInmueblesSF2;   	   	
    	
    	$globalFotos = 0;
    	
    	$porDonde = 0;
    	
    	$startTime= new \DateTime();
    	
    	if($totalInmueblesSF2 > 0){
    		
    		for ($i = 0; $i < 100; $i++) {
    			
    				$inmueble = $inmueblesSF2['data'][$i];
    			
    				echo "\nInmueble: ".$inmueble['consecutive']."\n";
    				
    				$fotos = $inmueblesCtg->getFotosDeInmueble($inmueble);
    				 
    				$urlInmueblesSF2 = $this->server.'catchment/main/property/'.$inmueble['id'];
    				 
    				//$urlFotoSF1 = 'http://10.102.1.3:81/publiweb/foto.php?key=C';
    				$urlFotoSF1 = 'http://190.242.98.187/ws-img/foto.php?key=C';
    				
    				$urlapiFile = $this->server."archive/main/file";
    				 
    				$total = 0;

    				echo "\nTotal fotos del inmueble: ".count($fotos)."\n";
    				
    				foreach ($fotos as $foto) {
    						
    					
    					$photoSF2 = $this->searchFoto($foto);
    					
    					//if(is_null($photoSF2)){
    						
    						
    						$path = "/var/www/html/upload3/fotosinmueble/".$foto['id']."/";
    						
    						
    						if (!file_exists($path)) {
    							//Crearlo
    							mkdir($path);
    						}
    						
    						///print_r($foto);
    						$nkey = $foto['nkey'];
    						$url = $urlFotoSF1.$nkey.'.jpg';
    						
    						//echo "url";
    						//echo "\n".$url."\n";
    						
    						
    						$pathFilename= $path.$foto['nkey'].".".$foto['ext'];
    						
    						//echo "path";
    						//echo "\n".$pathFilename."\n\n\n";
    						
    						
    						file_put_contents($pathFilename, file_get_contents($url));
    						
    						$marcadeagua = "/var/www/html/upload/logoAyS6.png";
    						
    						$margen = 20;
    						
    						$this->insertarmarcadeagua($pathFilename,$marcadeagua,$margen);
    						
    						//print_r($foto);
    						
    						$entityId = $inmueble['id'];
    						$photoName = $foto['nkey'].".".$foto['ext'];
    						$photoShow = $foto['publicar'];
    						$showOrder = $foto['orden'];
    						$description = $foto['descripcion'];
    						$nkeysifincaone = $foto['nkey'].".".$foto['ext'];
    						//echo "\nshowOrder\n";
    						//echo $showOrder;
    						//Subir foto a SF2
    						$cmd="curl --form \"filename=@$pathFilename\" --form showForIndexed=false --form entity=Property --form entityId='$entityId' --form photoName='$photoName' --form photoShow='$photoShow' --form showOrder='$showOrder' --form description='$description'  --form nkeysifincaone='$nkeysifincaone' -H '$h'   $urlapiFile";
    						
    						
    						echo "\n".$cmd."\n";
    						
    						$result= shell_exec($cmd);
    						$result = json_decode($result, true);
    						
    						
    						if($result['0']['success'] == true){
    							echo "\nOk";
    							$total++;
    							$globalFotos++;
    								
    						}else{
    						
    							
    						
    							//print_r($result);
    						
    							if($result['message']){
    						
    								echo "\nYa existe\n";
    						
    							}else{
    								
    								echo "\nError -----\n";
    								
    								echo "result";
    								print_r($result);
    								
    								$urlapiMapper = $this->server.'catchment/main/errorphoto';
    								$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    						
    								$error = array(
    										'photo' => $foto['nkey'],
    										'objectJson' => $inmueble['consecutive']
    								);
    						
    								$apiMapper->post($error);
    							}
    						
    								
    						}
    						
    						
//     					}else{
    						
//     						echo "\nLa foto ya existe\n";
//     					}
    					
    				}
    				
    			echo "\nTotal fotos del inmueble ".$inmueble['consecutive']." - ".$total."\n";
    				
    			if(isset($path)){
    				echo "\nBorrar carpeta: ".$path."\n";
    				//rmdir($path);
    				$this->rmdir_recursive($path);
    			}
    			
    				
    		$porDonde++;
    		echo "\nVamos por: ".$porDonde."\n";

    	}
    	
    	$finalTime = new \DateTime();
    	
    	
    	$diff = $startTime->diff($finalTime);
    	
    	
    	echo "\n\n Fecha inicial: ".$startTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Fecha final: ".$finalTime->format('Y-m-d H:i:s')."\n";
    	echo "\n Diferencia: ".$diff->format('%h:%i:%s')."\n";
    	
    	echo "\nGlobal fotos subidas ".$globalFotos."\n";
    			
    	}
    
    }
    
    function rmdir_recursive($dir) {
    	foreach(scandir($dir) as $file) {
    		if ('.' === $file || '..' === $file) continue;
    		if (is_dir("$dir/$file")) rmdir_recursive("$dir/$file");
    		else unlink("$dir/$file");
    	}
    	rmdir($dir);
    }
    
    function insertarmarcadeagua($imagen,$marcadeagua,$margen){   	
   	
    	//Cargar la estampa
    	$estampa = imagecreatefrompng($marcadeagua);

    	//Cargar la foto
    	$im = imagecreatefromjpeg($imagen);
    	
    	$x = imagesx($im);
    	$y = imagesy($im);
    	    	    	
    	//Establecer los m‡rgenes para la estampa y obtener el alto/ancho de la imagen de la estampa

    	$sx = imagesx($estampa);
    	$sy = imagesy($estampa);
    	
    	$m1 = ($x-$sx) / 2;
    	$m2 = ($y-$sy) / 2;
    	

//     	echo "\n".$m1."\n";
//     	echo "\n".$m2."\n";
    	
    	$margen_dcho = $m1;
    	$margen_inf = $m2;
    	
    	// Copiar la imagen de la estampa sobre nuestra foto usando los ’ndices de m‡rgen y el
    	// ancho de la foto para calcular la posici—n de la estampa.
    	imagecopy($im, $estampa, imagesx($im) - $sx - $margen_dcho, imagesy($im) - $sy - $margen_inf, 0, 0, imagesx($estampa), imagesy($estampa));
    	    	
    	// Guardar y liberar memoria
    	imagepng($im, $imagen);
     	imagedestroy($im);
    	    	
    }

    function searchFoto($foto) {
    	
    	$nkey = $this->cleanString($foto['nkey']);
    	    	    	
    	$filter = array(
    			'value' => $foto['nkey'].".".$foto['ext'],
    			'operator' => 'equal',
    			'property' => 'nKeySifincaOne'
    	);
    	$filter = json_encode(array($filter));
    	 
    	$urlPhoto = $this->server.'catchment/main/photos?filter='.$filter;
    	 
    	//ECHO "\n".$urlPhoto."\n";
    	$apiPhoto = $this->SetupApi($urlPhoto, $this->user, $this->pass);
    	 
    	$photo = $apiPhoto->get();
    	$photo = json_decode($photo, true);
    	 
    	
    	if($photo['total'] > 0){
    		 
    		//ECHO "ENTRO 1";
    		$c = array('id' => $photo['data'][0]['id']);
    		return $c;
    		 
    	}else{
    		
    		//ECHO "ENTRO 2";
    		return null;
    	}
    	
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
    
    		$this->headerCurl = 'x-sifinca: SessionToken SessionID="'.$this->token.'", Username="'.$this->user.'"';
    		
    		$a->set(array('url'=>$urlapi,'headers'=>$headers));
    
    		//print_r($a);
    
    		return $a;
    
    	}else{
    		echo "\nToken no valido\n";
    	}
    
    
    }
    
}