<?php
namespace upload\command;

use upload\model\contable;
use upload\lib\data;
use upload\lib\api;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use GearmanClient;
class ContableCommand extends Command
{	
    protected function configure()
    {
        $this->setName('upload:Contable')
		            ->setDescription('Funciones carga datos contables')
                            ->addArgument(
                                   'Document',
                                   InputArgument::OPTIONAL,
                                   'Traer documentos los documentos de contabilidad '
                               )
                 ->addOption(
               'tasks',
               null,
               InputOption::VALUE_NONE,
               'Ejecutar como tareas en paralelo'
            )
                
                 ->addOption(
               'id',
               null,
               InputOption::VALUE_REQUIRED,
               'id')
                
                 ->addOption(
               'year',
               null,
               InputOption::VALUE_REQUIRED,
               'Año'
            )
                
                ->addOption(
                'month',
                null,
                InputOption::VALUE_REQUIRED,
                'Mes',
                1
            );
        
        
	}
    protected function execute(InputInterface $input, OutputInterface $output)
	{
            
        $tt1 = microtime(true);
        $output->writeln('Recuperar y cargar datos de los empleados en base de metadatos de Archivos de Sifinca 2.0 ');
        
        $name = $input->getArgument('Document');
         
        if ($name) {
           
            $text = 'Aqui debe instanciar clase empleados ';
           
         ##   conectar a sifinca
         $output->writeln('<info>1.Conectando a base de datos</info>');
        
            
        ## conectar a base de zeus
        $conn = new data(array(
          'server' =>'10.102.1.3'
          ,'user' =>'sa'
          ,'pass' =>'i3kygbb2'
          ,'database' =>'mca' 
          ,'engine'=>'mssql'

         ));
        
        
         $output->writeln('<info>2.Trayendo Egresos</info>');
           
         $contable = new contable($conn);
           
           
         $year= $input->getOption('year');
         $month= $input->getOption('month');
           
         $periodo = $year.str_pad($month, 2, "0", STR_PAD_LEFT);
           
         $param=array('ANODCTO'=>$periodo);
        
       
         
         $datos1=$contable->getEgresos($param);
//          print_r($datos1);
//          return null;
         
           // cargar resto de documentos
          $output->writeln('<info>3.Trayendo comprobantes contables </info>'); 
           
          $datos2 = $contable->getDocument($param);
          
        
         
          $datos=array_merge($datos2, $datos1);
          $total =count($datos);
          $output->writeln('<info>Total registros = '. $total.' </info>');
          
          
          
        if ($input->getOption('tasks')) {
                ### Agregar tareas a Job
              ### Client de Jobs
              $client = new GearmanClient();       

              $this->addTasks($datos,$client);

        }else   {
            
                ## obtener $api

              $api= $this->setupApi();

              $this->postTasks($datos, $api);
            
        }
          
          
          
          $tt2 = microtime(true);
          $r= $tt2-$tt1;
                    
          $output->writeln('<info>Tiempo de  '. $r.' para '.$total.' </info>');
          
    
}    else {
           $text = 'NO GETDOCUMENT';
           $output->writeln('<info>'.$text.'</info>');       
        }
        
       
}        
public function setupApi(){
    
    
## Obtener el token de session = SessionID

$url="http://www.sifinca.net/sifinca/web/app.php/login";
$headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
   
);

$a = new api($url, $headers);
$result= $a->post(array("user"=>"sifinca@araujoysegovia.com","password"=>"araujo123"));  

$data=json_decode($result);
  
$token = $data->id;

## cargar objetos para inyectar a workers
$url="http://www.sifinca.net/sifinca/web/app.php/archive/main/history/accounting";
$headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
    'x-sifinca:SessionToken SessionID="'.$token.'", Username="sifinca@araujoysegovia.com"'  ,  
);


$a->set(array('url'=>$url,'headers'=>$headers));


 return $a;  
    
    
}


public function map($value){
    
     $v = $value['FNTEDCTO'].'-'.$value['NUMEDCTO'];

                $result = array("voucher"=> $value['NUMEDCTO'] ,
                            "voucherSource"=> $v ,
                            "source"=> $value['DESFUENTE'],
                            "sourceCode"=> $value['FNTEDCTO'],
                            "description"=> $value['DESCDCTO'],
                            "thirdIdentity"=> $value['IDTERCERO'],
                            "third"=> $value['tercero'] ,
                            "date"=>$value['FECHDCTO']
                 );
    
    
    return $result;

}


public function postTasks($datos,$api){
    
       $count=0;
            // ciclo ejecucion;
            foreach($datos as $key => $value)
            {
               
                $map = $this->map($value);
                $count++; 
                
                $api->post($map);
                 echo ".";   
            }
    
}


        public function complete($task) 
        { 
          echo  "COMPLETE: " . $task->unique()  . "\n"; 
        }

        public function status($task)
        {
            echo "STATUS: " . $task->unique() . ", " . $task->jobHandle() . " - " . $task->taskNumerator() . 
                 "/" . $task->taskDenominator() . "\n";
        }

        
        protected  function addTasks ($datos, $client )
        {
                       
            //por defecto el localhost
            $client->addServer();

            $client->setCompleteCallback(array( $this, "complete")); 
            $client->setStatusCallback(array( $this, "status"));

            
            $count=0;
            // ciclo ejecucion;
            foreach($datos as $key => $value)
            {
                $map = $this->map($value);
                $count++; 

                $json = json_encode($map);
                
              
            // add task
               $job_handle = $client->addTask("insDocument",$json,null,$map['voucherSource']);
            
        }
        
        ##Ejecutar las tareas
          if(!$client->runTasks())
            {
                echo "ERROR " . $client->error() . "\n";
                exit;
            }

            
        
        
      } 
}