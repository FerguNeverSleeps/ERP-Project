<?php
$importe=429.52;
 
echo "<p>El cambio de la cantidad ".$importe." es:</p>";
 
// indicamos todas las monedas posibles
$monedas=array(20.00, 10.00, 5.00, 1.00, 0.50, 0.25, 0.10, 0.05, 0.01);
 
// creamos un array con la misma cantidad de monedas
// Este array contendra las monedas a devolver
$cambio=array(0,0,0,0,0,0,0,0,0);
 
// Recorremos todas las monedas
$i=0;

$cambio[$i]=floor(($importe*100)/($monedas[$i]*100)); 
$importe=($importe*100)%($monedas[$i]*100);

while($importe>0)
{
    $i++;
    $cambio[$i]=floor($importe/($monedas[$i]*100)); 
    $importe=($importe)%($monedas[$i]*100);
    echo "Importe: ".$importe.";<br>";
   
    
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

