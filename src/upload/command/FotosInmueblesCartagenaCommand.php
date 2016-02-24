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
    

    	echo "\nInmuebles SF2\n";
    	echo $inmueblesSF2['total']."\n";
    	
    	$totalInmueblesSF2 = $inmueblesSF2['total'];
    	
    	$totalInmueblesSF2 = $totalInmueblesSF2;   	   	
    	
    	$globalFotos = 0;
    	
    	$porDonde = 0;
    	
    	$startTime= new \DateTime();
    	
    	if($totalInmueblesSF2 > 0){
    		
    		for ($i = 1000; $i < 2000; $i++) {
    			
    			$inmueble = $inmueblesSF2['data'][$i];
    			
    				
    				$fotos = $inmueblesCtg->getFotosDeInmueble($inmueble);
    				 
    				$urlInmueblesSF2 = $this->server.'catchment/main/property/'.$inmueble['id'];
    				 
    				$urlFotoSF1 = 'http://10.102.1.3:81/publiweb/foto.php?key=C';
    				
    				$urlapiFile = $this->server."archive/main/file";
    				 
    				$total = 0;
    				 
    				foreach ($fotos as $foto) {
    						
    					
    					$photoSF2 = $this->searchFoto($foto);
    					
    					if(is_null($photoSF2)){
    						
    						
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
    						
    						$entityId = $inmueble["id"];
    						$photoName = $foto['nkey'].".".$foto['ext'];
    						$photoShow = $foto['publicar'];
    						$showOrder = $foto['orden'];
    						$description = $foto['descripcion'];
    						//echo "\nshowOrder\n";
    						//echo $showOrder;
    						//Subir foto a SF2
    						$cmd="curl --form \"filename=@$pathFilename\" --form showForIndexed=false --form entity=Property --form entityId='$entityId' --form photoName='$photoName' --form photoShow='$photoShow' --form showOrder='$showOrder' --form description='$description' -H '$h'   $urlapiFile";
    						
    						$result= shell_exec($cmd);
    						$result = json_decode($result, true);
    						
    						
    						if($result['0']['success'] == true){
    							echo "\nOk";
    							$total++;
    							$globalFotos++;
    								
    						}else{
    						
    							echo "\nError -----\n";
    						
    							//print_r($result);
    						
    							if($result['message']){
    						
    								echo "\nYa existe\n";
    						
    							}else{
    								echo "\nInmueble: ".$inmueble['consecutive']."\n";
    						
    						
    								$urlapiMapper = $this->server.'catchment/main/errorphoto';
    								$apiMapper = $this->SetupApi($urlapiMapper, $this->user, $this->pass);
    						
    								$error = array(
    										'photo' => $foto['nkey'],
    										'objectJson' => $inmueble['consecutive']
    								);
    						
    								$apiMapper->post($error);
    							}
    						
    								
    						}
    						
    						
    					}else{
    						
    						echo "\nLa foto ya existe\n";
    					}
    					

    				}
    				
    				echo "\nTotal fotos del inmueble ".$inmueble['consecutive']." - ".$total."\n";
    				
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
    			'value' => $nkey,
    			'operator' => 'equal',
    			'property' => 'nKeySifincaOne'
    	);
    	$filter = json_encode(array($filter));
    	 
    	$urlPhoto = $this->server.'catchment/main/photos?filter='.$filter;
    	 
    	$apiPhoto = $this->SetupApi($urlPhoto, $this->user, $this->pass);
    	 
    	$photo = $apiPhoto->get();
    	$photo = json_decode($photo, true);
    	 
    	
    	if($photo['total'] > 0){
    		 
    		$c = array('id' => $photo['data'][0]['id']);
    		return $c;
    		 
    	}else{
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
    
    protected function SetupApi($urlapi,$user,$pass){
    
    	$url= $this->server."login";
    	$headers = array(
    			'Accept: application/json',
    			'Content-Type: application/json',
    	);
    
    	$a = new api($url, $headers);
    
    	$result= $a->post(array("user"=>$user,"password"=>$pass));
    	 
//     	echo "\nresult\n";
//     	print_r($result);
    
    	 
    	$data=json_decode($result);
    
    
    	$token = $data->id;
    
    	$this->headerCurl = 'x-sifinca:SessionToken SessionID="'.$token.'", Username="'.$user.'"';
    	
    	$headers = array(
    			'Accept: application/json',
    			'Content-Type: application/json',
    			'x-sifinca:SessionToken SessionID="'.$token.'", Username="'.$user.'"'  ,
    	);
    
    	$a->set(array('url'=>$urlapi,'headers'=>$headers));
    
    	return $a;
    
    }
    
}