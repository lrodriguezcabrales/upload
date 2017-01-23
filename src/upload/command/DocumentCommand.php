<?php
namespace upload\command;

use upload\model\documento;
use upload\lib\data;
use upload\lib\api;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use GearmanClient;


class DocumentCommand extends Command
{	
    protected function configure()
    {
        $this->setName('upload:Archivos')
		            ->setDescription('Interface para carga de archivos de sifinca1.0 a sifinca2.0')
                           
                
                                ->addArgument(
                                        'archivador',
                                        InputArgument::REQUIRED,
                                        'Who do you want to greet?'
                                    )

                 ->addOption(
               'tasks',
               null,
               InputOption::VALUE_NONE,
               'Ejecutar como tareas en paralelo'
            )
                
                  ->addOption(
               'optimize',
               null,
               InputOption::VALUE_NONE,
               'Optimizar el PDF output'
            )
                 ->addOption(
               'updatetype',
               null,
               InputOption::VALUE_NONE,
               'Actualizar tipos de documentos'
            )
                ->addOption(
               'download',
               null,
               InputOption::VALUE_NONE,
               'Descargar documentos'
            )
                
                  ->addOption(
               'post',
               null,
               InputOption::VALUE_NONE,
               'Subir documentos a Sifinca2.0'
            )
                ->addOption(
               'path',
               null,
               InputOption::VALUE_REQUIRED,
               'carpeta donde se van guardar los archivos'
            )   
                
                  ->addOption(
               'server',
               null,
               InputOption::VALUE_REQUIRED,
               'Direccion del servidor de bases de datos'
            )   
                
                  ->addOption(
               'destinyUser',
               null,
               InputOption::VALUE_REQUIRED,
               'Usuario destinatario de los archivos'
            )   
                
                 ->addOption(
               'database',
               null,
               InputOption::VALUE_REQUIRED,
               'Nombre de la base de datos'
            )  
             
               ->addOption(
               'user',
               null,
               InputOption::VALUE_REQUIRED,
               'Usuario ?'
            )  
                   ->addOption(
               'pass',
               null,
               InputOption::VALUE_REQUIRED,
               'Contrase�a ?'
            )     
                 ->addOption(
               'container',
               null,
               InputOption::VALUE_REQUIRED,
               'Id Contenedor'
            )  
                ->addOption(
               'begin',
               null,
               InputOption::VALUE_REQUIRED,
               'Documento inicial'
            )  
                
                 ->addOption(
               'end',
               null,
               InputOption::VALUE_REQUIRED,
               'Documento final'
            )  
                ;
            
        
        
        
        
	}
    protected function execute(InputInterface $input, OutputInterface $output)
	{
        
         
        // 

        $tt1 = microtime(true);
        $output->writeln('Descargar documentos de archivadores de sifinca 1.0 y carga en sifinca 2.0 ');
        
        $archivador = $input->getArgument('archivador');
         
        
        if ($archivador) {
           
         ##   conectar a sifinca
         $output->writeln('<info>Conectando a base de datos ...</info>');
            
       /*
         ## conectar a base de zeus -> convertir luego a opciones 
        $conn = new data(array(
          'server' =>'10.102.1.3'
          ,'user' =>'sa'
          ,'pass' =>'i3kygbb2'
          ,'database' =>'docflow' 
          ,'engine'=>'mssql'

         ));
        * */
       
         
          $container ='2e062689-3070-4758-9af5-4ee676b1fd58';
         if($input->getOption('container')){
             $container = $input->getOption('container');
         }
         
         
         $server ='10.102.1.3';
         if($input->getOption('server')){
             $server = $input->getOption('server');
         }
         
         $database='docflow';
           if($input->getOption('database')){
             $database = $input->getOption('database');
         }
        
         
          $user='sa';
           if($input->getOption('user')){
             $user = $input->getOption('user');
         }
         
          $pass='i3kygbb2';
           if($input->getOption('pass')){
             $pass = $input->getOption('pass');
         }
         
  
         
         
    ## conectar a base de zeus -> convertir luego a opciones  ---> MONTERIA
        $conn = new data(array(
          'server' =>$server
          ,'user' =>$user
          ,'pass' =>$pass
          ,'database' =>$database
          ,'engine'=>'mssql'

         ));
        ### descargar documentos
        
         $path= $input->getOption('path');
         
         if($path){
             
            $path = "/var/www/html/upload3/files/".$path."/"; 
             
         }else
         {
             $path ="/var/www/html/upload3/files/".$archivador."/";
          
         }
       if (!file_exists($path)) {
           ##crearlo
           mkdir($path);
       }
       $tasks = $input->getOption('tasks');
      
       $thumano = new documento($conn,$archivador);
    
       if( $input->getOption('download')){
            
              $urlapi1 = "www.sifinca.net/sifinca/web/app.php/archive/main/file";
              $user= "sifinca@araujoysegovia.com";
              $pass="araujo123";
              
             
              $file = $this->SetupApi($urlapi1, $user, $pass);
    
              ### document
              
              $urlapi = "www.sifinca.net/sifinca/web/app.php/archive/main/file/indexing";
              $user= "sifinca@araujoysegovia.com";
              $pass="araujo123";
              
              $document = $this->SetupApi($urlapi, $user, $pass);
            
              $begin =$input->getOption('begin');
              $end =$input->getOption('end');
               if($begin && $end){
                   
                    $data=$thumano->get(array('begin'=> $begin,'end'=>$end));
                   
               }else{
                     ## descargar todos
                $data=$thumano->get(array());
                   
               }
             
                
              

                $token = $this->getToken($user,$pass);

               $count=0;
           foreach($data as $row){
                
               
               $tags=array();
               
               if($tasks){
                   
                   $output->writeln('<info>No se encuentra implementado procesos en paralelos</info>');
                   
                   
               }else{
               
               ### buscar si el documento ya esta -> verficar si esta el "file" y el "documento" 
            
            
              $d=$this->getDocument($row['radicacion'],$container); 
              
             
              
              if($d->total==0)   
              
                    $flag=true; ### no esta
                else
                {
                    $flag = false;
                    
                }
                
                
                $filename= $path.rtrim($row['radicacion']).".tif";
                
                
                if($flag){
                  
                
                $filepdf= $path.rtrim($row['radicacion']).".pdf";
                $filepdf2= $path."pdf/".rtrim($row['radicacion']).".1.pdf";
                $thumano->getFile($row['nkey'], $filename);
                $thumano->tiff2pdf($filename, $filepdf);
                
                if($input->getOption('optimize')){
                    $optimize=true;
                }else
                {
                    $optimize=false;
                }
                
                
                if($optimize){

                    /*$cmd="qpdf --linearize $filepdf tmp.pdf && mv tmp.pdf $filepdf";
                 
                     -o en carpeta ./pdf/<filename>.1.pdf
                     */
                    
                  
                   $cmd="cloudconvert -f pdf -c mode=compress -o /var/www/html/upload/files/arriendos/pdf $filepdf";
                   
                   echo "<pre>".shell_exec($cmd)."</pre>";
                   

                }
                
               /* $cmd ="exiftool -title=".$row['radicacion']." -Author='Araujo y Segovia SA' ".$filepdf;
                $_out = shell_exec($cmd);
                */
                
                
                 ### traer metadata
                 $metadoc = $thumano->getMetadata($row['id']);
                 
                 if( $input->getOption('post')){
                           
                         echo "Nuevo!!!! - \n";
                           
                          $destinyUser=$input->getOption('destinyUser');
                          
                          if( $destinyUser){
                            $user = $destinyUser;     
                        }else{
                            $user = "sifinca@araujoysegovia.com";
                        }
                          
                        $h ="x-sifinca:SessionToken SessionID=\"$token\", Username=\"sifinca@araujoysegovia.com\"";
                        
                         $result=$this->postfile($filepdf2,$user,$h , $urlapi1);
                        
                         // add records to the log
                      
                                             
                      
                         
                        if(is_array($result)){
                       
                            if($result[0]['success']){
                        
                              
                               $fileId= $result['file']['id'];
                                                               
                              
                                
                                $typedoc=  json_decode($this->getIdTypeDoc($row['tipo_doc'], $archivador),true);
                                 
                                
                                
                              
                                ### armar post -> document  estas tag solo funcionan para arriendos
                                foreach ($metadoc as $tag) {
                                     $tags[]  =  array(
                                      array("Contract"=> $tag['contrato'])  ,
                                      array("Own"=> $tag['nom_prop']) ,
                                      array("Agreement"=> $tag['convenio']),
                                      array("Client"=> $tag['nom_inq']),
                                      array("property"=> $tag['inmueble']),  
                                      array("ref1"=> $tag['ref1']), 
                                      array("ref2"=> $tag['ref2']), 
                                      array("ref3"=> $tag['ref3']), 
                                      array("id_soli"=> $tag['id_soli']), 
                                      array("doc_soli"=> $tag['doc_soli']), 
                                         
                                            
                                    ); 
                            }

                       
                            
                        $folio = $row['folio'];
                        
                        if($folio=='VIRTUAL'){
                            $virtual=true;
                        }else{
                            $virtual=false;
                        }
                         
                      
                        $doc = array( "nameDocument"=>$row['radicacion'],
                           "dateDocument"=>  date('Y-m-d',strtotime($row['fecha']) ),
                           "tags"=>$tags,
                           "documentType"=> array("id"=> $typedoc['data'][0]['idTarget']),
                           "file"=>array(array("id"=>$fileId)),
                            "additional"=>$folio,
                            "virtual"=> $virtual
                         );
                        
                        
                         
                        ### post document
                         
                         $result2=$document->post($doc);
                         $msg=json_encode($result2);
                        
                         echo $msg;
                       
                         
                        }else{
                             
                         echo json_encode($result)."\n";
                        }
                 
                   
                    
             
                    } else
                        
                    {   
                        $msg = "Success: false ->".$filepdf;
                       
                        echo $msg."\n";
                       
                        
                    }
               
                 ###
                 
                 
               }
               
               
               
               }  
               
             else {
                   
                    $msg = "Existe ->".$row['radicacion'];
                     echo $msg."\n";
                    
               }
                
            }
        $count++;
           
      
        $output->writeln('<info>'.$count.". ".$filename.'</info>');
       
            
        }
        
                 
                
          $tt2 = microtime(true);
          $r= $tt2-$tt1;
                    
          $output->writeln('<info>Tiempo de  '. $r.' para '.$count.' </info>');
          
    
}   
    else {
           
           $output->writeln('<info>Debe proporcionar el archivador  </info>');       
        }
        
       
}

}



protected function maptag($tag){
    
    $map = array("contrato"=>"Contract",
        
        
        );
    
    if(true){
        
        $result = $map[$tag];
        
    }else{
        
        $result = $tag;
        
    }
    
    
    
    
    return $result;
    
    
}
        
        
        


protected function getIdTypeDoc($id, $archivador){
    
             $urlapi = 'http://www.sifinca.net/sifinca/web/app.php/admin/sifinca/mapper/documentType.'.$archivador.'/';
              $user= "sifinca@araujoysegovia.com";
              $pass="araujo123";
              
              $map = $this->SetupApi($urlapi, $user, $pass);
    
              $result=$map->get($id);
       
    
    return $result;
    
}


protected function postfile($f,$u,$h,$url){
    
    
    
    
    $cmd="curl --form \"filename=@$f\" --form showForIndexed=true --form destinyUser=$u -H '$h'   $url";
    echo $cmd;
    
    $result= shell_exec($cmd);
    
    
    return json_decode($result,true);
}



protected function getDocument($rad, $container) {
    
   $urlapi = 'http://www.sifinca.net/sifinca/web/app.php/archive/main/document/metadata?skip=0&page=1&pageSize=10';
              $user= "sifinca@araujoysegovia.com";
              $pass="araujo123";
              
   $a = $this->SetupApi($urlapi, $user, $pass);

   ### aplica solo thumano
   
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




protected function getToken($user,$pass) {
    
        ## Obtener el token de session = SessionID

    $url="http://www.sifinca.net/sifinca/web/app.php/login";
    $headers = array(
        'Accept: application/json',
        'Content-Type: application/json',

    );

    $a = new api($url, $headers);

    $result= $a->post(array("user"=>$user,"password"=>$pass));  


    $data=json_decode($result);

    $token = $data->id;
        
    return $token;
}

protected function SetupApi($urlapi,$user,$pass){
   
    
    ## Obtener el token de session = SessionID

$url="http://www.sifinca.net/sifinca/web/app.php/login";
$headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
   
);

$a = new api($url, $headers);

$result= $a->post(array("user"=>$user,"password"=>$pass));  


$data=json_decode($result);
  

$token = $data->id;



$headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
    'x-sifinca:SessionToken SessionID="'.$token.'", Username="'.$user.'"'  ,  
);


    $a->set(array('url'=>$urlapi,'headers'=>$headers));
    
   return $a; 
    
}



}