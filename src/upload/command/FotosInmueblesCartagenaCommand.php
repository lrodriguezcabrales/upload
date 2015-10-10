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
	
// 	public $server = 'http://10.102.1.22/sifinca/web/app.php/';
// 	public $serverRoot = 'http:/10.102.1.22/';
	
	public $user= "sifinca@araujoysegovia.com";
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
    	
    	echo "\n\n";
    	print_r($h);
    	echo "\n\n";
    	
    	
    	$inmueblesSF2 = $apiInmueblesSF2->get();
    	$inmueblesSF2 = json_decode($inmueblesSF2, true);
    
    	//echo "\nInmuebles SF2\n";
    	//print_r($inmueblesSF2);
    	
    	//$totalInmueblesSF2 = $inmueblesSF2['total'];
    	
    	$totalInmueblesSF2 = 1;   	   	
    	
    	if($totalInmueblesSF2 > 0){
    		
    		for ($i = 0; $i < $totalInmueblesSF2; $i++) {
    			
    			$inmueble = $inmueblesSF2['data'][$i];
    			
    			//echo "\nInmueble\n";
    			//print_r($inmueble);
    			
    			$fotos = $inmueblesCtg->getFotosDeInmueble($inmueble);
    			
    			//echo "\nFotos\n";
    			//print_r($fotos);
    			
    			$urlFotoSF1 = 'http://10.102.1.3:81/publiweb/foto.php?key=C';
    			    			
    			$urlapiFile = $this->server."archive/main/file";
    			
    			foreach ($fotos as $foto) {
    				 
    				$path = "/var/www/html/upload/fotosinmueble/".$foto['id']."/";
    				 
    			
    				if (!file_exists($path)) {
    					//Crearlo
    					mkdir($path);
    				}
    				 
    				///print_r($foto);
    				$nkey = $foto['nkey'];
    				$url = $urlFotoSF1.$nkey.'.jpg';
    				 
    				echo "url";
    				echo "\n".$url."\n";
    			
    				 
    				$pathFilename= $path.$foto['nkey'].".".$foto['ext'];
    				 
    				echo "path";
    				echo "\n".$pathFilename."\n\n\n";
    				 
    				
    				file_put_contents($pathFilename, file_get_contents($url));
    				
    				$entityId = $inmueble["id"];
    				$photoName = $foto['nkey'].".".$foto['ext'];
    				$photoShow = true;
    				//Subir foto a SF2
    				$cmd="curl --form \"filename=@$pathFilename\" --form showForIndexed=false --form entity=Property --form entityId='$entityId' --form photoName='$photoName' --form photoShow='$photoShow' -H '$h'   $urlapiFile";
    				
    				$result= shell_exec($cmd);
    				
    				//echo "\n\nresult\n";
    				print_r($result);
    			
    		}
    		
    	}
    	
    			
    	}
    
    }
    

    
    protected function SetupApi($urlapi,$user,$pass){
    
    	$url= $this->server."login";
    	$headers = array(
    			'Accept: application/json',
    			'Content-Type: application/json',
    	);
    
    	$a = new api($url, $headers);
    
    	$result= $a->post(array("user"=>$user,"password"=>$pass));
    	 
    	//     	echo "aqui";
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