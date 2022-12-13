<?php
//Incluímos inicialmente la conexión a la base de datos
require_once('../../../config.php');
Class Consultas
{
    protected $global;
    
    //Implementamos nuestro constructor
    public function __construct()
    {
        global $DB;
        $this->global = $DB;
    }

    //Datos generales
    public function reportegeneral($inicio, $fin, $curso )
    {
        global $DB, $USER;
        $query = "";
        $query .=" SELECT  @s:=@s+1 id, c.shortname AS curso, c.id AS courseid, ";
        $query .=" COUNT(usrcourse.userid ) AS totalusuarios, ";
        $query .=" CONCAT(CAST((SUM((IFNULL(compl.num, 0) *100)/act.num) / COUNT(usrcourse.userid)) as DECIMAL(16,0)) ,'%') ";
        $query .=" as avancetotal, ";
        $query .=" SUM(if(IFNULL(compl.num, 0) = act.num, 1, 0)) AS usuariosfin, ";
        $query .=" CONCAT(CAST(SUM(IF(compl.num IS null, (act.num * 100) / act.num,  ";
        $query .=" IF(compl.num < act.num, (compl.num * 100) / act.num, 0) )) ";
        $query .=" / COUNT(usrcourse.userid) as DECIMAL(16,0)) ,'%') AS porfinusuarios, ";
        $query .=" SUM(IF(compl.num IS NULL OR compl.num < act.num, 1, 0)) AS usuariosnofin ";
        $query .=" FROM (SELECT @s:= 0) AS s, mdl_user u ";
        $query .=" INNER JOIN (Select distinct ue.userid, e.courseid,ue.timestart  ";
        $query .=" FROM mdl_user_enrolments ue ";
        $query .=" INNER join mdl_user u on ue.userid = u.id ";
        $query .=" INNER join mdl_enrol e on ue.enrolid=e.id  ";
        $query .=" INNER JOIN mdl_role_assignments as asg on asg.userid = u.id ";
        $query .=" INNER JOIN mdl_context as con on asg.contextid = con.id ";
        $query .=" INNER JOIN mdl_role r on asg.roleid = r.id ";
        $query .=" WHERE r.shortname = 'student' ";
        $query .=" ) AS usrcourse  ";
        $query .=" ON u.id=usrcourse.userid  ";
        $query .=" LEFT JOIN(Select count(id)as num,course from mdl_course_completion_criteria  ";
        $query .=" GROUP BY course) as act on act.course=usrcourse.courseid  ";
        $query .=" LEFT JOIN (Select userid, course, count(id) AS num  ";
        $query .=" FROM mdl_course_completion_crit_compl  ";
        $query .=" GROUP BY course, userid) AS compl  ";
        $query .=" ON compl.userid=u.id AND compl.course=usrcourse.courseid  ";
        $query .=" LEFT JOIN mdl_course c ON usrcourse.courseid = c.id ";
        $query .=" inner join mdl_customfield_data cd on c.id = cd.instanceid ";
        $query .=" inner join mdl_customfield_field cf on cf.id = cd.fieldid ";
        $query .=" where cf.shortname = 'reporte' and cd.intvalue = 1 ";
        $query .=" and c.shortname like '%$curso%' "; 
        $query .=" and c.startdate >= $inicio ";
        $query .=" and c.startdate <= $fin ";
        $query .=" GROUP BY fullname ";
        $query .=" ORDER BY id ASC ";
        $data  = $DB->get_records_sql($query);
        
        return $data;
    }

    public function reportGrafic($inicio, $fin, $curso){
        
        global $DB, $USER;
        $query = "";
        $query .=" SELECT  @s:=@s+1 id, c.shortname AS curso, c.id AS courseid, ";
        $query .=" COUNT(usrcourse.userid ) AS totalusuarios, ";
        $query .=" CAST((SUM((IFNULL(compl.num, 0) *100)/act.num) / COUNT(usrcourse.userid)) as DECIMAL(16,0)) ";
        $query .=" as avancetotal, ";
        $query .=" SUM(if(IFNULL(compl.num, 0) = act.num, 1, 0)) AS usuariosfin, ";
        $query .=" CAST(SUM(IF(compl.num IS null, (act.num * 100) / act.num,  ";
        $query .=" IF(compl.num < act.num, (compl.num * 100) / act.num, 0) )) ";
        $query .=" / COUNT(usrcourse.userid) as DECIMAL(16,0)) AS porfinusuarios, ";
        $query .=" SUM(IF(compl.num IS NULL OR compl.num < act.num, 1, 0)) AS usuariosnofin ";
        $query .=" FROM (SELECT @s:= 0) AS s, mdl_user u ";
        $query .=" INNER JOIN (Select distinct ue.userid, e.courseid,ue.timestart  ";
        $query .=" FROM mdl_user_enrolments ue ";
        $query .=" INNER join mdl_user u on ue.userid = u.id ";
        $query .=" INNER join mdl_enrol e on ue.enrolid=e.id  ";
        $query .=" INNER JOIN mdl_role_assignments as asg on asg.userid = u.id ";
        $query .=" INNER JOIN mdl_context as con on asg.contextid = con.id ";
        $query .=" INNER JOIN mdl_role r on asg.roleid = r.id ";
        $query .=" WHERE r.shortname = 'student' ";
        $query .=" ) AS usrcourse  ";
        $query .=" ON u.id=usrcourse.userid  ";
        $query .=" LEFT JOIN(Select count(id)as num,course from mdl_course_completion_criteria  ";
        $query .=" GROUP BY course) as act on act.course=usrcourse.courseid  ";
        $query .=" LEFT JOIN (Select userid, course, count(id) AS num  ";
        $query .=" FROM mdl_course_completion_crit_compl  ";
        $query .=" GROUP BY course, userid) AS compl  ";
        $query .=" ON compl.userid=u.id AND compl.course=usrcourse.courseid  ";
        $query .=" LEFT JOIN mdl_course c ON usrcourse.courseid = c.id ";
        $query .=" inner join mdl_customfield_data cd on c.id = cd.instanceid ";
        $query .=" inner join mdl_customfield_field cf on cf.id = cd.fieldid ";
        $query .=" where cf.shortname = 'reporte' and cd.intvalue = 1 ";
        $query .=" and c.shortname like '%$curso%' "; 
        $query .=" and c.startdate >= $inicio ";
        $query .=" and c.startdate <= $fin ";
        $query .=" GROUP BY fullname ";
        $query .=" ORDER BY id ASC ";
        $data  = $DB->get_records_sql($query);

        return $data;
    }
  
    public function getRol($id)
    {
        global $DB;
        $query = "";
        $query .= " SELECT  distinct r.shortname as rol FROM";
        $query .= " (select @s:=0) as s,";
        $query .= " mdl_user u";
        $query .= " INNER JOIN mdl_role_assignments as asg on asg.userid = u.id";
        $query .= " INNER JOIN mdl_context as con on asg.contextid = con.id";
        $query .= " INNER JOIN mdl_course c on con.instanceid = c.id";
        $query .= " INNER JOIN mdl_role r on asg.roleid = r.id";
        $query .= " where  u.id = $id";
        return $DB->get_records_sql($query);
    }
    
    public function cursos()
    {
        global $DB, $USER;
        $query = "";
        $query .= " SELECT distinct(c.shortname), c.id from mdl_course c ";
        $query .= " inner join mdl_customfield_data cd on c.id = cd.instanceid ";
        $query .= " inner join mdl_customfield_field cf on cf.id = cd.fieldid ";
        $query .= " where c.shortname is not null  and c.id<>1 or c.shortname !=''  ";
        $query .= " and cf.shortname = 'reporte' and cd.intvalue = 1  ";
        $query .= " order by shortname asc ";
        return $DB->get_records_sql($query);;
    }
}

?>