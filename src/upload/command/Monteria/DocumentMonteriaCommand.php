<?php
namespace upload\command\Monteria;

use upload\model\documento;
use upload\lib\data;
use upload\lib\api;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use GearmanClient;


class DocumentMonteriaCommand extends Command
{

  public $server = 'http://www.sifinca.net/monteriaServer/web/app.php/';
	//public $server = 'http://10.101.1.152:8080/sifinca/web/app_dev.php/';
  public $user = 'sifinca@araujoysegovia.com';
  public $pass = 'araujo123';
  public $token = null;


  protected function configure()
  {
    $this->setName('documentosMonteria')
    ->setDescription('Documentos - Monteria');
  }

  protected function execute(\Symfony\Component\Console\Input\InputInterface $input,
      \Symfony\Component\Console\Output\OutputInterface $output)
  {

    $output->writeln("Iniciando... \n");

    $conexion = new data(array(
        'server' =>'192.168.100.1'
        ,'user' =>'sa'
        ,'pass' =>'75080508360'
        ,'database' =>'docflow'
        ,'engine'=>'mssql'

    ));



    $this->buildDocument($conexion);

  }

  public function buildDocument($conexion){

    $tt1 = microtime(true);

    $startTime= new \DateTime();
    
    echo "\nDescargar documentos de archivadores de sifinca 1.0 y carga en sifinca 2.0\n";

    //echo "\n".$this->server."\n";
    
    //Arriendos
    $archivador = 'ARRIENDOS';
    $container = '2e062689-3070-4758-9af5-4ee676b1fd58';
     
    $modelDocument = new documento($conexion, $archivador);
     
    $path ="/var/www/html/upload3/src/upload/command/Monteria/files/".$archivador."/";

    if (!file_exists($path)) {
      ##crearlo
      //echo "\n".$path."\n";
      mkdir($path);
    }

    $urlapi1 = $this->server."archive/main/file";

    $file = $this->SetupApi($urlapi1, $this->user, $this->pass);

    ### Document

    $urlapi = $this->server."archive/main/file/indexing";

    $document = $this->SetupApi($urlapi, $this->user, $this->pass);

    $begin = 0;
    $end = 1;

    //Obtener documentos SF1
    if($begin && $end){

      $data = $modelDocument->get(array('begin'=> $begin,'end'=>$end));
       
    }else{
      ## descargar todos
      $data = $modelDocument->get(array());
    }
     
    //$data = $modelDocument->getOneDocument();

    $count=0;
	
    //15000
    //foreach($data as $row){
      for ($u = 33300; $u < count($data); $u++) {
      	
      	$row = $data[$u];
      	
      	$tags=array();
                
        ### Buscar si el documento ya esta -> verficar si esta el "file" y el "documento"

        $d = $this->getDocument($row['radicacion'],$container);

        if($d->total==0){
          $flag = true; ### no esta
        }else{
          $flag = false;
        }

        $filename = $path.rtrim($row['radicacion']).".tif";

        if($flag){

          $filepdf = $path.rtrim($row['radicacion']).".pdf";
          $filepdf2 = $path."pdf/".rtrim($row['radicacion']).".1.pdf";

          //print_r($row);
          
          //Creando arhivo .pdf
          $findFile =  $modelDocument->getFile($row['nkey'], $filename);
          
          if($findFile){
          	
          	$modelDocument->tiff2pdf($filename, $filepdf);
          	
          	
          	### Traer metadata
          	$metadoc = $modelDocument->getMetadata($row['id']);
          	 
          	 
          	echo "\nNuevo POST FILE !!!! - \n";
          	
          	$h ="x-sifinca:SessionToken SessionID=\"$this->token\", Username=\"$this->user\"";
          	
          	//echo "\n".$urlapi1."\n";
          	$result = $this->postfile($filepdf, $this->user,$h , $urlapi1);
          	
          	//print_r($result);
          	
          	// Add records to the log
          	if(is_array($result)){
          	
          		
          		
          		if(isset($result[0])){
          			if($result[0]['success']){
          				 
          				//echo "\nOk\n";
          			
          				$fileId= $result['file']['id'];
          				 
          				$typedoc=  $this->getIdTypeDoc($row['tipo_doc'], $archivador);
          				 
          				//echo "\nMetadatos\n";
          				//print_r($metadoc);
          				 
          				### armar post -> document  estas tag solo funcionan para arriendos
          				foreach ($metadoc as $tag) {
          					$tags[]  =  array(
          							array("Contract"=> $tag['contrato']),
          							array("Own"=> $tag['nom_prop']) ,
          							array("Agreement"=> $tag['convenio']),
          							array("Client"=> $tag['nom_inq']),
          							array("property"=> $tag['inmueble']),
          							array("ref1"=> $tag['ref1']),
          							array("ref2"=> $tag['ref2']),
          							array("ref3"=> $tag['ref3']),
          							array("id_soli"=> $tag['id_soli']),
          							array("doc_soli"=> $tag['doc_soli'])
          					);
          				}
          				 
          				$folio = $row['folio'];
          				 
          				if($folio=='VIRTUAL'){
          					$virtual=true;
          				}else{
          					$virtual=false;
          				}
          				 
          				$doc = array(
          						"nameDocument" => $row['radicacion'],
          						"dateDocument" => date('Y-m-d',strtotime($row['fecha'])),
          						"tags" => $tags,
          						"documentType" => $typedoc,
          						"file" => array(array("id"=>$fileId)),
          						"additional" => $folio,
          						"virtual"=> $virtual
          				);
          				 
          				//print_r($doc);
          			
          				### post document
          			
          				$result2=$document->post($doc);
          				$msg=json_encode($result2);
          				 
          				echo "\nPOST DOCUMENT !!!!!\n";
          				echo "\n".$msg."\n";
          		}else{
          			echo "\nERROR 004\n";
          			          			
          			$dateError = new \DateTime();
          			$dateError = $dateError->format('Y-m-d H:i:s');
          			
          			$arrayError = array(
          					'date' => $dateError,
          					'error' => '004',
          					'radicacion' => $row['radicacion'],
          					'dataError' => $result
          			);
          			$jsonError = json_encode($arrayError);
          			
          			$this->errorJson($jsonError.",");
          			
          			print_r($result);
          			
          		}
          		
          			 
          		}else{
          			echo "\nERROR 001\n";
          			echo "\n".json_encode($result)."\n";
          			           			 
          			$dateError = new \DateTime();
          			$dateError = $dateError->format('Y-m-d H:i:s');
          			 
          			$arrayError = array(
          					'date' => $dateError,
          					'error' => '001',
          					'radicacion' => $row['radicacion'],
          					'dataError' => $doc
          			);
          			$jsonError = json_encode($arrayError);
          			 
          			$this->errorJson($jsonError.",");
          			 
          			//return ;
          		}
          	
          	}else{
          		echo "\nERROR 002\n";
          		$msg = "Success: false -> ".$filepdf;
          		echo "\n".$msg."\n";
          	          	
          		$dateError = new \DateTime();
          		$dateError = $dateError->format('Y-m-d H:i:s');
          		 
          		$arrayError = array(
          				'date' => $dateError,
          				'error' => '002',
          				'radicacion' => $row['radicacion'],
          				'dataError' => $result
          		);
          		$jsonError = json_encode($arrayError);
          		 
          		$this->errorJson($jsonError.",");
          	
          	
          		//return ;
          	
          	}
          	
          }else{
          	          	
          	$dateError = new \DateTime();
          	$dateError = $dateError->format('Y-m-d H:i:s');
          	
          	$arrayError = array(
          			'date' => $dateError,
          			'error' => '003',
          			'radicacion' => $row['radicacion'],
          			'dataError' => "No se encontro ningun archivo en SF1"
          	);
          	$jsonError = json_encode($arrayError);
          	 
          	$this->errorJson($jsonError.",");
          	
          }
          
           
        }else{
          $msg = "\nExiste -> ".$row['radicacion'];
          echo "\n".$msg."\n";
          
        }

      

      $count++;
       
      echo "\n$count.". ".$filename\n";


      $tt2 = microtime(true);

      $r= $tt2-$tt1;

      echo "\n\nTiempo de $r para $count \n";

      echo "\n\nVamos por: $u";
      
    }

    $finalTime = new \DateTime();
    $diff = $startTime->diff($finalTime);
    
    echo "\n\n Inicio: ".$startTime->format('Y-m-d H:i:s')."\n";
    echo "\n Fin: ".$finalTime->format('Y-m-d H:i:s')."\n";
    echo "\n Diferencia: ".$diff->format('%h:%i:%s')."\n";
    
  }

  protected function maptag($tag){

    $map = array("contrato"=>"Contract");

    if(true){

      $result = $map[$tag];

    }else{

      $result = $tag;

    }

    return $result;


  }


  protected function getIdTypeDoc($id, $archivador){

//     $urlapi = $this->server.'admin/sifinca/mapper/documentType.'.$archivador.'/';
     
//     $map = $this->SetupApi($urlapi, $this->user, $this->pass);

//     $result=$map->get($id);

//     return $result;

  	
  	echo "\n".$id."\n";
  	
  	echo "\n".$archivador."\n";
  	
  	$typeDoc = null;
  	
  	$filter = array();
  	
  	$f1 = array(
  			"operator" => "equal",
  			"value" => $archivador,
  			"property" => "container.name"
  	);
  	
  	$f2 = array(
  			"operator" => "equal",
  			"value" => $id,
  			"property" => "code"
  	);
  	
  	$filter[] = $f1;
  	$filter[] = $f2;
  	
  	$filter = json_encode($filter);  	
  	
  	$urlapi = $this->server.'archive/main/zero/documenttype?filter='.$filter;
  	 
  	//echo "\n".$urlapi."\n";
  	
  	$api = $this->SetupApi($urlapi, $this->user, $this->pass);
  	
  	$result = $api->get();
  	
  	$result = json_decode($result, true);
  	
  	//print_r($result);
  	if(isset($result['data'])){
  		if($result['data'] > 0){
  				
  			$typeDoc = $result['data'][0];
  				
  		}
  	}
  	
  	
  	return $typeDoc;

  }


  protected function postfile($f,$u,$h,$url){

  	
    $cmd="curl --form \"filename=@$f\" --form showForIndexed=true --form destinyUser=$u -H '$h'   $url";
    echo "\n".$cmd."\n";

    $result= shell_exec($cmd);

    //print_r($result);
    
    echo "\n\n";

    return json_decode($result,true);
  }


  /**
   * Buscar documento en SF2
   */
  protected function getDocument($rad, $container) {

    $urlapi = $this->server.'archive/main/document/metadata?skip=0&page=1&pageSize=10';

    echo "\n".$urlapi."\n";
    
    
    $a = $this->SetupApi($urlapi, $this->user, $this->pass);

    $data = array(
        "container"=> $container,
        "documentType"=> null,
        "nameDocument"=> $rad,
        "initialDate"=> null,
        "finishDate"=> null,
        "tags"=> null
    );


    $result = $a->post($data);

    
    
    return json_decode($result);

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

      //      print_r($headers);

      $this->headerCurl = 'x-sifinca: SessionToken SessionID="'.$this->token.'", Username="'.$this->user.'"';

      $a->set(array('url'=>$urlapi,'headers'=>$headers));

      //print_r($a);

      return $a;

    }else{
      echo "\nToken no valido\n";
    }

  }
  
  /**
   * Log Error
   * @param unknown $numero
   * @param unknown $texto
   */
  function error($numero,$texto){
  	
  	$ddf = fopen('ErrorDocumentMonteria.log','a');
  	fwrite($ddf,"[".date("r")."] Error $numero: $texto\r\n");
  	fclose($ddf);
  	
  }
  
  /**
   * Log Error
   * @param unknown $numero
   * @param unknown $texto
   */
  function errorJson($texto){
  	 
  	$ddf = fopen('/var/www/html/upload3/errorlog/ErrorJsonDocumentMonteria.log','a');
  	fwrite($ddf,"$texto\r\n");
  	fclose($ddf);
  	 
  }


  }