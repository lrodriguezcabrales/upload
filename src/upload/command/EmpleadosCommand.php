<?php
namespace upload\command;


use upload\model\empleados;
use upload\lib\data;
use upload\lib\api;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class EmpleadosCommand extends Command
{	
    protected function configure()
    {
        $this->setName('upload:Empleados')
		 ->setDescription('carga de datos de empleados')
                            
                 ->addOption(
               'all',
               null,
               InputOption::VALUE_NONE,
               'todos los registros'
            )
                
                 ->addOption(
               'id',
               null,
               InputOption::VALUE_REQUIRED,
               'Cedula del empleado'
            )
                
                 ->addOption(
               'update',
               null,
               InputOption::VALUE_REQUIRED,
               'Actualizar base de empleados'
            )
                
                ->addOption(
                'post',
                null,
                InputOption::VALUE_REQUIRED,
                'Nombre del Archivo '
                
            );
        
        
	}
    protected function execute(InputInterface $input, OutputInterface $output)
	{
            
        
        $output->writeln('Cargar datos de empleados de metadatos de Archivos de Sifinca 2.0 ');
        
   
                     
           
        $conn = new data(array(
             'server' =>'10.102.1.3'
          ,'user' =>'sa'
          ,'pass' =>'i3kygbb2'
          ,'database' =>'Nomina_AyS' 
          ,'engine'=>'mssql'

         ));
           
           
           $e = new empleados($conn);
           
           
           if($input->getOption('all')){
               
               $empleados=$e->getEmpleados(array());
             
           }else
           {
               $id = $input->getOption('id');
                 
                $empleados=$e->getEmpleados(array('id'=>$id));       
               
           }
           
           if($input->getOption('terceros')){
             $terceros =$e->getTerceros();
           }
          
           if($input->getOption('post')){
               
               $this->postEmpleados($empleados);
               
                  if($terceros){
                      
                      $this->postTerceros($terceros);
                  }         
               
           }  else {
               
               print_r($empleados );  
               
               if($terceros){
                      
                   print_r($terceros);
                  }   
               
               }
      
           
       
          
        }
        
        
        protected function postEmpleados($empleados){
            
            $url='http://www.sifinca.net/sifinca/web/app.php/archive/main/history/payroll';
            $user='sifinca@araujoysegovia.com';
            $pass='araujo123';
            $api = $this->SetupApi($url,$user,$pass);
            
            foreach ($empleados as $row) {
                
               
                $map = array(
                "idContract"=> $row['Codigo'],
                "idEmployee"=> $row['Identificacion'],
                "nameEmployee"=> $row['Nombres'],
                "idThird"=> "",
                "third"=> "" 
             );
            
           
             print_r($api->post($map));
            }
            
            
             echo "\n";       
            
            
            
        }
        
        
        
        
        protected function postTerceros($terceros){
            
            $url='http://www.sifinca.net/sifinca/web/app.php/archive/main/history/payroll';
            $user='sifinca@araujoysegovia.com';
            $pass='araujo123';
            $api = $this->SetupApi($url,$user,$pass);
            
            foreach ($terceros as $row) {
                
               
                $map = array(
                "idContract"=> "",
                "idEmployee"=> "",
                "nameEmployee"=> "",
                "idThird"=> $row['Codigo'],
                "third"=> $row['Nombre'] 
             );
            
           
             print_r($api->post($map));
            }
            
            
             echo "\n";       
            
            
            
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

     ## cargar objetos para inyectar a workers

     $headers = array(
         'Accept: application/json',
         'Content-Type: application/json',
         'x-sifinca:SessionToken SessionID="'.$token.'", Username="'.$user.'"'  ,  
     );


         $a->set(array('url'=>$urlapi,'headers'=>$headers));

        return $a; 

     } 
            
  
        
        
}