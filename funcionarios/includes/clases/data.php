<?php
session_start();
        try {
            $rutaFile = dirname(dirname(dirname(__FILE__)));
            $rutaFile = str_replace('\\', '/' , $rutaFile);
            require_once($rutaFile . '/generalp.config.inc.php');
            //require_once($_SERVER['DOCUMENT_ROOT'].'/selectra_planilla/generalp.config.inc.php');

                    $conn= new PDO('mysql:host='.DB_HOST.';dbname='.$_SESSION['bd'],DB_USUARIO,DB_CLAVE);

            $sql = "SELECT * FROM nomprofesiones";
            $result = $conn->prepare($sql) or die ($sql);

            if (!$result->execute()) return false;

            if ($result->rowCount() > 0) {
                $json = array();
                while ($row = $result->fetch()) {
                    $codorg=$row['codorg'];
                    $descrip=$row['descrip'];
                    $enlace= stripslashes   ("<a id='editar' href='profesion-edit.php?codorg=$codorg&descrip=$descrip'><div class='glyphicon glyphicon-edit' aria-hidden='true'></div></a><div id='eliminar' class='glyphicon glyphicon-trash' aria-hidden='true'></div>");
                    $json[] = array(
                        'codorg' => $row['codorg'],
                        'descrip' => $row['descrip'],
                        'enlaces' => $enlace

                    );
                }

                $json['success'] = true;
                echo json_encode($json);
            }
        } catch (PDOException $e) {
            echo 'Error: '. $e->getMessage();
        }
    
?>
