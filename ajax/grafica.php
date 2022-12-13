<?php 
    require_once "../modelos/Querymodelos.php";
    $reporte1 = new Consultas();

    $fecha_inicio=$_GET["fecha_inicio"];
    $fecha_fin=$_GET["fecha_fin"];
    $inicio=strtotime($fecha_inicio);
    $fin=strtotime($fecha_fin);
    
    $curso = $_GET["curso"]; 
    if($curso=='seleccionacurso'){
        $curso="";
      }
      
    $rspta=$reporte1->reportGrafic($inicio, $fin, $curso);

    $valoresY= array(); 
    $valoresX= array(); 
    $cursosName= array(); 
    
    foreach ($rspta as $key => $valor) {
        $valoresY[] = $valor->avancetotal;
        $valoresX[] = $valor->porfinusuarios; 
        $cursosName[] = $valor->curso; 
    }

    $datosX=json_encode($valoresX);
    $datosY=json_encode($valoresY);
    $cursos=json_encode($cursosName);

    
?>

<div id="myPlot"></div>
<script type="text/javascript">
function crearCadenaLineal(json) {
    var parsed = JSON.parse(json);
    var arr = [];
    for (var x in parsed) {
        arr.push(parsed[x]);
    }
    return arr;
}
</script>

<script>
datosX = crearCadenaLineal(' <?php echo $datosX ?>');
datosY = crearCadenaLineal(' <?php echo $datosY ?>');
cursos = crearCadenaLineal(' <?php echo $cursos ?>');

var trace = {
    type: 'bar',
    x: cursos,
    y: datosY,
    name: 'Porcentaje de usuarios finalizados'
    // hoverinfo: "skip",
    // mode: 'markers',
    // marker: {color: "DarkSlateGrey", size: 20},
};

var trace2 = {
    type: 'bar',
    x: cursos,
    y: datosX,
    name: 'Porcentaje de usuarios no finalizados'
    // hoverinfo: "skip",
    // mode: 'markers',
    // marker: {color: "DarkSlateGrey", size: 20},
};

var data = [trace, trace2];

var layout = {
    title: "Avance de curso",
      yaxis: { title: 'Porcentaje' },
      xaxis: { title: 'Nombre de curso'},
};

var config = {
    responsive: true
};

Plotly.newPlot("myPlot", data, layout, config, {
    displayModeBar: false
});
</script>