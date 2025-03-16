<?php
$importe=429.52;
 
echo "<p>El cambio de la cantidad ".$importe." es:</p>";
 
// indicamos todas las monedas posibles
$monedas=array(20.00, 10.00, 5.00, 1.00, 0.50, 0.25, 0.10, 0.05, 0.01);
 
// creamos un array con la misma cantidad de monedas
// Este array contendra las monedas a devolver
$cambio=array(0,0,0,0,0,0,0,0,0);
 
// Recorremos todas las monedas
for($i=0; $i<count($monedas); $i++)
{
    // Si el importe actual, es superior a la moneda
    if($importe>=$monedas[$i])
    {
 
        // obtenemos cantidad de monedas
        $cambio[$i]=floor($importe/$monedas[$i]); 
        // actualizamos el valor del importe que nos queda por didivir
        $importe=$importe-($cambio[$i]*$monedas[$i]);
        echo "Importe: ".$importe.";<br>";
    }
}
 
// Bucle para mostrar el resultado
for($i=0; $i<count($monedas); $i++)
{
    if($cambio[$i]>0)
    {
        if($monedas[$i]>=5)
            echo "Hay: ".$cambio[$i]." billetes de: ".$monedas[$i]." &euro;<br>";
        else
            echo "Hay: ".$cambio[$i]." monedas de: ".$monedas[$i]." &euro;<br>";
    }
}
?>

