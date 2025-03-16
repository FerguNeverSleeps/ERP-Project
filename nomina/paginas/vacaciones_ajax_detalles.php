<?php 
	include("../lib/common.php");
	include ("func_bd.php") ;
    $id  = $_GET['id'];
    $sql = "SELECT b.* FROM  periodos_vacaciones as a, dias_incapacidad as b WHERE a.id_periodo_vacacion='$id' AND  a.id_periodo_vacacion=b.id";
    
   
  $result =sql_ejecutar($sql); 
  
    
	
//echo json_encode( $fila);
?>


<table class="table table-bordered table-hover" style="width:100%">
    <thead>
        <tr>            
            <th width="25%"> Fecha </th>
            <th width="70%"> Observación </th>
            <th width="5%"> Días </th>            
        </tr>
    </thead>
    <tbody>

    	<?php
    	while ($fila = fetch_array($result))
        { ?>
	        <tr>
	            <td rowspan="2"> <?= $fila['fecha']?> </td>
	            <td> <?= $fila['observacion']?> </td>
	            <td> <?= $fila['dias']?> </td>	            
	        </tr>
	    <? } ?>
	</tbody>
</table>
                                                