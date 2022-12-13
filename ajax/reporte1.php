<?php

require_once "../modelos/Querymodelos.php";
//llamado  a la base de datos
$reporte1 = new Consultas();
//Variables a utilizar
$fecha_inicio=$_REQUEST["fecha_inicio"];
$fecha_fin=$_REQUEST["fecha_fin"];
//Trasformacion de fecha
$inicio=strtotime($fecha_inicio);
$fin=strtotime($fecha_fin);
$curso=$_REQUEST["curso"];

if($curso=='seleccionacurso'){
  $curso="";
}

$rspta=$reporte1->reportegeneral($inicio, $fin, $curso );
        $data= Array(); 
          foreach ($rspta as $key => $valor) {
            // TODO: cambiar enlace por el sitio original
            $link = "<a href='http://localhost:8080/grade/report/grader/index.php?id=$valor->courseid' target='_blank'>Visualizar alumnos</a>";
              $data[]=array(
                "0"=>$valor->id,
                "1"=>$valor->curso,  
                "2"=>$valor->totalusuarios,
                "3"=>$valor->avancetotal, 
                "4"=>$valor->usuariosfin, 
                "5"=>$valor->porfinusuarios, 
                "6"=>$valor->usuariosnofin,
                "7"=>$link, 
                );
              }

        $results = array(
            "sEcho"=>1, //Información para el datatables
            "iTotalRecords"=>count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
            "aaData"=>$data);
        echo json_encode($results);

  exit;