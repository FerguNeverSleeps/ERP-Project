<?php
error_reporting(E_ALL);
// variables
$host = 'localhost';
$name = 'planilla_configuracion';
$user = 'root';
$pass = 'Ny5HYzfSfbnBwZkU9%zaH5_J7-yX3r';
$dir = dirname(__FILE__) . '/files/';

function backupDatabaseTables($dbHost,$dbUsername,$dbPassword,$dbName,$tables = '*'){
    //connect & select the database
    $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName); 

    //get all of the tables
    if($tables == '*'){
        $tables = array();
        $result = $db->query("SHOW TABLES");
        while($row = $result->fetch_row()){
            $tables[] = $row[0];
        }
    }else{
        $tables = is_array($tables)?$tables:explode(',',$tables);
    }

    //loop through the tables
    foreach($tables as $table){
        $result = $db->query("SELECT * FROM $table");
        $numColumns = $result->field_count;

        $return .= "DROP TABLE $table;";

        $result2 = $db->query("SHOW CREATE TABLE $table");
        $row2 = $result2->fetch_row();

        $return .= "\n".$row2[1].";\n";

        for($i = 0; $i < $numColumns; $i++){
            while($row = $result->fetch_row()){
                $return .= "INSERT INTO $table VALUES(";
                for($j=0; $j < $numColumns; $j++){
                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = ereg_replace("\n","\n",$row[$j]);
                    if (isset($row[$j])) { $return .= '"'.$row[$j].'"' ; } else { $return .= '""'; }
                    if ($j < ($numColumns-1)) { $return.= ','; }
                }
                $return .= ");\n";
            }
        }

        $return .= "\n\n\n";
    }

    //save file
    $handle = fopen($dbName.'_backup_'.time().'.sql','w+');
    fwrite($handle,$return);
    fclose($handle);
}

$db = new mysqli($host,$user,$pass,'planilla_configuracion'); 
$result = $db->query("SELECT nombre,bd_nomina FROM planilla_configuracion.nomempresa order by codigo desc");

$i=1;
while($row = $result->fetch_row()){
    $output=null;
    $empresa = $row[0];
    $database = $row[1];
    echo $i."- RESPALDANDO: ".$empresa." <br>";
    $i++;

    $backup_file = $dir . $database ."_". date("Y-m-d-H-i-s") . '.sql';

    // comando a ejecutar    
    
    $command = "mysqldump -v --opt --events --routines --triggers --default-character-set=utf8 -h $host -u $user -p $pass $database > $backup_file";

    // ejecución y salida de éxito o errores
    echo "<h3>Backing up database to `<code>{$backup_file}</code>`</h3>";

    exec("mysqldump --user={$user} --password={$pass} --host={$host} {$database} --result-file={$backup_file} 2>&1", $output);

    var_dump($output);

    //$output = shell_exec($command);
    ob_end_clean();
    //echo $output;
}
echo "<br>OPERACION FILNALIZADA";
