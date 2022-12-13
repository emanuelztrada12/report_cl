<?php
require_once("{$CFG->libdir}/formslib.php");
require_once('../modelos/Querymodelos.php');
class administrador extends moodleform
{
    
    function definition()
    {
        global $USER, $DB;
        $reporte1 = new Consultas();               
        $nombre  = $USER->id;        
        $admin   = is_siteadmin();        
        $cursosinfo = $reporte1->cursos();
        $arraycurso['seleccionacurso'] = 'Selecciona tu curso';
        $area = array_keys( $area); 
        foreach ($cursosinfo as $key => $value) {
            $arraycurso[$key] = $value->shortname;
        }

        $mform =& $this->_form;
        $mform->addElement('header', 'displayinfo', get_string('textfields', 'block_estandarcl'));
        $mform->addElement('date_selector', 'fecha_inicio', get_string('from'));
        $mform->addElement('date_selector', 'fecha_fin', get_string('to'));
        $mform->addElement('select', 'curso', get_string('curso', 'block_estandarcl'), $arraycurso);
        
    }
    
}

?>