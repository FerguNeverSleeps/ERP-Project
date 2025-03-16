<?php
session_start();  
$cedula      = ( isset($_GET['ci']) ) ? $_GET['ci'] : '' ;
require_once('../../lib/database.php');
$dir = "{$_GET['ci']}";
$name = "{$_GET['nom']}";
// Run the recursive function 

$response = scan('archivos/'.$dir);


// This function scans the files folder recursively, and builds a large array

function scan($dir){

	$files = array();

	// Is there actually such a folder/file?

	if(file_exists($dir)){
	
		foreach(scandir($dir) as $f) {
		
			if(!$f || $f[0] == '.') {
				continue; // Ignore hidden files
			}

			if(is_dir($dir . '/' . $f)) {

				// The path is a folder
				$files[] = array(
					"name" => $f,
					"type" => "folder",
					"path" => $dir . '/' . $f,
					"items" => scan($dir . '/' . $f) // Recursively get the contents of the folder
				);
			}
			
			else {

				// It is a file

				$db    = new Database($_SESSION['bd']);
				$ruta  =  $dir . '/' . $f;
				$query       = "SELECT 
				IFNULL((SELECT GROUP_CONCAT(id_documento) id_documento
				FROM expediente_documento
				WHERE url_documento LIKE '%{$f}%'), '-') ID_DOCUMENTO, 
			   IFNULL((SELECT GROUP_CONCAT(id_adjunto) id_adjunto 
							   FROM expediente_adjunto
							   WHERE archivo LIKE '%{$f}%') , '-') ID_ADJUNTO;";
				$filas = $db->query($query)->fetch_assoc();
				$ids_docs   = explode(",",$filas['ID_DOCUMENTO']);
				$ids_adjs   = explode(",",$filas['ID_ADJUNTO']);
				if($ids_docs[0] != "-"){
					
					foreach ($ids_docs as $key => $id) 
					{
						$files[] = array(
							"id" => $id,
							"name" => $f,
							"tipo" => "documento",
							"type" => "file",
							"path" => $dir . '/' . $f,
							"size" => filesize($dir . '/' . $f) // Gets the size of this file
						);
					}
				}
				if($ids_adjs[0] != "-"){
					foreach ($ids_adjs as $key => $id)
					{		
						$files[] = array(
							"id" => $id,
							"name" => $f,
							"tipo" => "adjunto",
							"type" => "file",
							"path" => $dir . '/' . $f,
							"size" => filesize($dir . '/' . $f) // Gets the size of this file
						);
					}
				}				
			}
		}
	
	}

	return $files;
}



// Output the directory listing as JSON

header('Content-type: application/json');

echo json_encode(array(
	"name" => $dir,
	"type" => "folder",
	"path" => $dir,
	"persona"=> $name,
	"items" => $response
));
