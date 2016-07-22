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
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class DocumentCommand extends Command {
	
	public $server = 'www.sifinca.net/monteriaServer/web/app.php/';
	
	protected function configure() {
		$this->setName ( 'upload:Archivos' )->setDescription ( 'Interface para carga de archivo de sifinca1.0 a sifinca2.0' )->

		addArgument ( 'archivador', InputArgument::REQUIRED, 'Who do you want to greet?' )->addOption ( 'tasks', null, InputOption::VALUE_NONE, 'Ejecutar como tareas en paralelo' )->

		addOption ( 'optimize', null, InputOption::VALUE_NONE, 'Optimizar el PDF output' )->addOption ( 'updatetype', null, InputOption::VALUE_NONE, 'Actualizar tipos de documentos' )->

		addOption ( 'download', null, InputOption::VALUE_NONE, 'Descargar documentos' )->

		addOption ( 'post', null, InputOption::VALUE_NONE, 'Subir documentos a Sifinca2.0' )->addOption ( 'path', null, InputOption::VALUE_REQUIRED, 'carpeta donde se van guardar los archivos' )->

		addOption ( 'server', null, InputOption::VALUE_REQUIRED, 'Direccion del servidor de bases de datos' )->

		addOption ( 'destinyUser', null, InputOption::VALUE_REQUIRED, 'Usuario destinatario de los archivos' )->

		addOption ( 'database', null, InputOption::VALUE_REQUIRED, 'Nombre de la base de datos' );
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		
		// create a log channel
		$log = new Logger ( 'name' );
		$log->pushHandler ( new StreamHandler ( '/var/www/html/upload/files/app.log', Logger::WARNING ) );
		
		$tt1 = microtime ( true );
		$output->writeln ( 'Descargar documentos de archivadores de sifinca 1.0 y carga en sifinca 2.0 ' );
		
		$archivador = $input->getArgument ( 'archivador' );
		
		if ($archivador) {
			
			echo "\nEntro 1\n";
			// # conectar a sifinca
			$output->writeln ( '<info>Conectando a base de datos ...</info>' );
			
			// # conectar a base de zeus -> convertir luego a opciones
// 			$conn = new data ( array (
// 					'server' => '10.102.1.3',
// 					'user' => 'sa',
// 					'pass' => 'i3kygbb2',
// 					'database' => 'docflow',
// 					'engine' => 'mssql' 
// 			) );
			
			$conn = new data(array(
					'server' =>'192.168.100.1'
					,'user' =>'sa'
					,'pass' =>'75080508360'
					,'database' =>'docflow'
					,'engine'=>'mssql'
			));
			
			// ## descargar documentos
			
			$path = $input->getOption ( 'path' );
			if ($path) {
				
				$path = "/var/www/html/upload3/files/" . $path . "/";
			} else {
				$path = "/var/www/html/upload3/files/" . $archivador . "/";
			}
			if (! file_exists ( $path )) {
				// #crearlo
				mkdir ( $path );
			}
			
			echo "\nEntro 2\n";
			
			$tasks = $input->getOption ( 'tasks' );
			$thumano = new documento ( $conn, $archivador );
			
			echo "\nEntro 3\n";
			
			if ($input->getOption ( 'download' )) {
				
				$urlapi1 = $this->server . "archive/main/file";
				$user = "sifinca@araujoysegovia.com";
				$pass = "araujo123";
				
				$file = $this->SetupApi ( $urlapi1, $user, $pass );
				
				// ## document
				
				$urlapi = $this->server . "archive/main/file/indexing";
				$user = "sifinca@araujoysegovia.com";
				$pass = "araujo123";
				
				$document = $this->SetupApi ( $urlapi, $user, $pass );
				
				// # descargar todos
				
				
				$data = $thumano->get ( array () );
				$token = $this->getToken ( $user, $pass );
				
				echo "\nEntro 4\n";
				
				//print_r($data);
				
				$count = 0;
				foreach ( $data as $row ) {
					
					echo "\nEntre aqui\n";
					
					$tags = array ();
					
					if ($tasks) {
						
						$output->writeln ( '<info>No se encuentra implementado procesos en paralelos</info>' );
					} else {
						
						// ## buscar si el documento ya esta -> verficar si esta el "file" y el "documento"
						
						echo "\n".$row ['radicacion']."\n";
						$d = $this->getDocument ( $row ['radicacion'] );
						
						print_r ( $d );
						
						if ($d->total == 0){
							echo "\nDocumento no esta\n";
							$flag = true; // ## no esta
						}else {
							$flag = false;
						}
						
						$filename = $path . rtrim ( $row ['radicacion'] ) . ".tif";
						
						if ($flag) {
							
							$filepdf = $path . rtrim ( $row ['radicacion'] ) . ".pdf";
							$thumano->getFile ( $row ['nkey'], $filename );
							$thumano->tiff2pdf ( $filename, $filepdf );
							
							if ($input->getOption ( 'optimize' )) {
								$optimize = true;
							} else {
								$optimize = false;
							}
							
							echo "\nfilepdf\n";
							echo $filepdf."\n";
							if ($optimize) {
// 								$cmd = "qpdf -linearize $filepdf tmp.pdf && mv tmp.pdf $filepdf";
								
// 								echo "\n".$cmd."\n";
// 								$_out = shell_exec ( $cmd );
								
// 								print_r($_out);
							}
							
							// ## traer metadata
							$metadoc = $thumano->getMetadata ( $row ['id'] );
							
							if ($input->getOption ( 'post' )) {
								
								echo "\nNuevo!!!!\n";
								
								$destinyUser = $input->getOption ( 'destinyUser' );
								
								if ($destinyUser) {
									$user = json_encode ( array (
											"destinyUser" => $destinyUser 
									) );
									
									$user = 'destinyUser="'.$destinyUser.'"';
									
								} else {
// 									$user = json_encode ( array (
// 											"destinyUser" => '' 
// 									) );
									$user = null;
								}
								
								$h = "x-sifinca:SessionToken SessionID=\"$token\", Username=\"sifinca@araujoysegovia.com\"";
								
								echo "\nuser ".$user."\n";
								echo "\n".$token."\n";
								echo "\n".$urlapi1."\n";
								
								
								
								$result=$this->postfile($filepdf, $user,$h, $urlapi1);
								
								print_r($result);
								// add records to the log
																								
								if (is_array ( $result )) {
									
									
									if(isset($result [0] ['success'])){
										
										echo "\nCreando doc\n";
										if ($result [0] ['success']) {
										
										
											$typedoc = json_decode ( $this->getIdTypeDoc ( $row ['tipo_doc'], $archivador ), true );
										
											// ## armar post -> document
											foreach ( $metadoc as $tag ) {
												$tags [] = array (
														array (
																"idEmployee" => $tag ['cedula']
														),
														array (
																"nameEmployee" => $tag ['nombre']
														),
														array (
																"idContract" => $tag ['codigo']
														),
														array (
																"third" => $tag ['tercero']
														),
														array (
																"idThird" => $tag ['idtercero']
														)
												);
											}
										
											$folio = $row ['folio'];
										
											if ($folio == 'VIRTUAL') {
												$virtual = true;
											} else {
												$virtual = false;
											}
										
											$rFile = $result['file'];
										
											$arrayFile = array();
											$arrayFile[] = $rFile;
										
											$doc = array (
													"nameDocument" => $row ['radicacion'],
													"dateDocument" => date ( 'Y-m-d', strtotime ( $row ['fecha'] ) ),
													"tags" => $tags,
													"documentType" => array (
															"id" => $typedoc ['data'] [0] ['idTarget']
													),
													"file" => $arrayFile,
													"additional" => $folio,
													"virtual" => $virtual
											);
										
											// ## post document
										
											$result2=$document->post($doc);
											$msg=json_encode($result2);
										
											echo $msg ."\n";
										}
										
									}else{
										
										echo "\nError subiendo file\n";
										print_r($result);
									}
									
									
								} else{
									$msg = "Success: false ->" . $filepdf;
									
									echo $msg . "\n";
								}
								
								// ##
							}
						} 

						else {
							
							$msg = "Existe ->" . $row ['radicacion'];
							echo $msg . "\n";
						}
					}
					$count ++;
					
					$output->writeln ( '<info>' . $count . ". " . $filename . '</info>' );
				}
				
				$tt2 = microtime ( true );
				$r = $tt2 - $tt1;
				
				$output->writeln ( '<info>Tiempo de  ' . $r . ' para ' . $count . ' </info>' );
			} else {
				
				$output->writeln ( '<info>Debe proporcionar el archivador  </info>' );
			}
		}
	}
	protected function getIdTypeDoc($id, $archivador) {
		$urlapi = $this->server . 'admin/sifinca/mapper/documentType.' . $archivador . '/';
		$user = "sifinca@araujoysegovia.com";
		$pass = "araujo123";
		
		$map = $this->SetupApi ( $urlapi, $user, $pass );
		
		$result = $map->get ( $id );
		
		return $result;
	}
	protected function postfile($f, $data, $h, $url) {
		
		
		//$cmd = "curl --form \"filename=@$f\" --data '$data'  -H '$h'  $url";
		

		if($data){
			
			$cmd = "curl --form \"filename=@$f\" --form $data -H '$h' $url";
			
		}else{
			$cmd = "curl --form \"filename=@$f\" -H '$h' $url";
		}
		
		
		
		
		echo "\n".$cmd."\n";
		
		$result = shell_exec ( $cmd );
		
		return json_decode ( $result, true );
	}
	protected function getDocument($rad) {
		$urlapi = $this->server . 'archive/main/document/metadata?skip=0&page=1&pageSize=10';
		$user = "sifinca@araujoysegovia.com";
		$pass = "araujo123";
		
		$a = $this->SetupApi ( $urlapi, $user, $pass );
		// ## aplica solo thumano
		
		$data = array (
				"container" => "47dd8c91-6bf7-4728-87fc-ecf8a4ed1771",
				"documentType" => null,
				"nameDocument" => $rad,
				"initialDate" => null,
				"finishDate" => null,
				"tags" => null 
		);
		
		// $data = array(
		// "container"=> "37d5ff73-83f2-477a-b65c-d8792993a205",
		// "documentType"=> null,
		// "nameDocument"=> $rad,
		// "initialDate"=> null,
		// "finishDate"=> null,
		// "tags"=> null
		// );
		
		$result = $a->post ( $data );
		
		return json_decode ( $result );
	}
	protected function getToken($user, $pass) {
		
		// # Obtener el token de session = SessionID
		$url = $this->server . "login";
		$headers = array (
				'Accept: application/json',
				'Content-Type: application/json' 
		);
		$a = new api ( $url, $headers );
		$result = $a->post ( array (
				"user" => $user,
				"password" => $pass 
		) );
		$data = json_decode ( $result );
		$token = $data->id;
		
		return $token;
	}
	protected function SetupApi($urlapi, $user, $pass) {
		
		// # Obtener el token de session = SessionID
		$url = $this->server . "login";
		$headers = array (
				'Accept: application/json',
				'Content-Type: application/json' 
		);
		$a = new api ( $url, $headers );
		$result = $a->post ( array (
				"user" => $user,
				"password" => $pass 
		) );
		$data = json_decode ( $result );
		
		$token = $data->id;
		$headers = array (
				'Accept: application/json',
				'Content-Type: application/json',
				'x-sifinca:SessionToken SessionID="' . $token . '", Username="' . $user . '"' 
		);
		$a->set ( array (
				'url' => $urlapi,
				'headers' => $headers 
		) );
		
		return $a;
	}
}