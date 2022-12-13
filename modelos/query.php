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
    public function reportegeneral($inicio, $fin, $codigo, $curso, $area)
    {
        global $DB, $USER;
        $query = "";
        $query .= "  SELECT   @s:=@s+1 id,Concat(u.firstname,' ',u.lastname) as nombre,u.username as codigo, u.email, ";
        $query .= "  DATE_FORMAT(DATE_ADD(FROM_UNIXTIME(usrcourse.timestart ,'%Y-%m-%d %h:%i'),INTERVAL 2 HOUR),'%d/%m/%Y %H:%i')as inicio, ";
        $query .= "  DATE_FORMAT(DATE_ADD(FROM_UNIXTIME(c.enddate ,'%Y-%m-%d %h:%i'),INTERVAL 2 HOUR),'%d/%m/%Y %H:%i')as final, ";
        $query .= "  DATE_FORMAT(DATE_ADD(FROM_UNIXTIME(cc.timecompleted ,'%Y-%m-%d %h:%i'),INTERVAL 2 HOUR),'%d/%m/%Y %H:%i')as completado, ";
        $query .= "      CASE When ";
        $query .= "      notas.finalgrade<=0 or notas.finalgrade is null ";
        $query .= "          THEN ";
        $query .= "          '0' ";
        $query .= "              ELSE ";
        $query .= "      notas.finalgrade ";
        $query .= "      END AS nota, ";
        $query .= "  CASE when compl.num<=0 or compl.num is null THEN '0%' ";
        $query .= "  ELSE concat(cast(((compl.num*100)/act.num)as decimal(16,0)),'%') ";
        $query .= "  END AS avance, ";
        $query .= "  c.fullname as curso,";
        $query .= "  u.country as pais,";
        $query .= "  u.idnumber ,";
        $query .= "  area.data as area ,";
        $query .= "  cargo.data as 'cargo' ,";
        $query .= "  DATE_FORMAT(DATE_ADD(FROM_UNIXTIME(mul1.timeaccess ,'%Y-%m-%d %h:%i'),INTERVAL 2 HOUR),'%d/%m/%Y %H:%i')as ultimo ";
        $query .= "  from (SELECT @s:= 0) AS s,mdl_user u ";
        $query .= "  inner join (Select distinct ue.userid, e.courseid,ue.timestart from mdl_user_enrolments ue ";
        $query .= "  inner join mdl_enrol e ";
        $query .= "  on ue.enrolid=e.id ";
        $query .= "  ) as usrcourse ";
        $query .= "  on u.id=usrcourse.userid ";
        $query .= "  left join (Select gi.courseid,gg.userid,gg.finalgrade ";
        $query .= "  from mdl_grade_items gi ";
        $query .= "  inner join mdl_grade_grades gg ";
        $query .= "  on gg.itemid=gi.id ";
        $query .= "  where gi.itemtype='course')as notas ";
        $query .= "  on u.id=notas.userid and usrcourse.courseid=notas.courseid ";
        $query .= "  left join mdl_course_completions cc ";
        $query .= "  on u.id=cc.userid and usrcourse.courseid=cc.course ";
        $query .= "  left join(Select count(id)as num,course from mdl_course_completion_criteria ";
        $query .= "  group by course) as act ";
        $query .= "  on act.course=usrcourse.courseid ";
        $query .= "  left join (Select userid,course,count(id)as num from mdl_course_completion_crit_compl ";
        $query .= "  group by course, userid)as compl ";
        $query .= "  on compl.userid=u.id and compl.course=usrcourse.courseid ";
        $query .= "  left join mdl_course c ";
        $query .= "  on usrcourse.courseid = c.id ";
        $query .= "  left join (select muid.userid, muid.data from mdl_user_info_data muid where muid.fieldid =12) cargo on cargo.userid=u.id ";
        $query .= "  left join (select muid.userid, muid.data from mdl_user_info_data muid where muid.fieldid =14) area on area.userid=u.id ";
        $query .= "  left join mdl_user_lastaccess mul1 on mul1.courseid = c.id and u.id = mul1.userid ";
        $query .= "  where ";
        $query .= "  u.deleted=0 ";
        $query .= "  and u.suspended=0 ";
        $query.="    and area.data like '%$area%' ";
        $query .= "  and c.fullname like '%$curso%' ";
        $query .= "  and usrcourse.timestart>='$inicio' "; 
        $query .= "  and usrcourse.timestart<='$fin' ";
	   $data  = $DB->get_records_sql($query);
        
        return $data;
    }
    
    
    public function reportegeneral2($inicio, $fin, $codigo, $curso, $empresa)
    {
        
        global $DB, $USER;
        
        $data = $DB->get_records_sql("SELECT   @s:=@s+1 id,Concat(u.firstname,' ',u.lastname) as nombre,
                                                                        u.id as codigo,
                                                                        c.fullname as curso,
                                                                        empresa.data as empresa,
                                                                      DATE_FORMAT(DATE_ADD(FROM_UNIXTIME(c.startdate ,'%Y-%m-%d %h:%i'),INTERVAL 7 HOUR),'%d/%m/%Y %H:%i')as inicio,
                                                                        DATE_FORMAT(DATE_ADD(FROM_UNIXTIME(c.enddate ,'%Y-%m-%d %h:%i'),INTERVAL 7 HOUR),'%d/%m/%Y %H:%i')as final,
                                                                        DATE_FORMAT(DATE_ADD(FROM_UNIXTIME(cc.timecompleted ,'%Y-%m-%d %h:%i'),INTERVAL 7 HOUR),'%d/%m/%Y %H:%i')as completado,
                                                                                    CASE When
                                                                                        notas.finalgrade<=0 or notas.finalgrade is null
                                                                                      THEN
                                                                                        '0'
                                                                                         ELSE
                                                                                       notas.finalgrade
                                                                                    END AS nota,
                                                                                CASE when compl.num<=0 or compl.num is null THEN '0%'
                                                                                ELSE concat(cast(((compl.num*100)/act.num)as decimal(16,0)),'%')
                                                                                END AS avance
                                                                                from (SELECT @s:= 0) AS s,mdl_user u
                                                                                inner join (Select distinct ue.userid, e.courseid,ue.timestart from mdl_user_enrolments ue
                                                                                inner join mdl_enrol e
                                                                                on ue.enrolid=e.id
                                                                                ) as usrcourse
                                                                                on u.id=usrcourse.userid
                                                                                LEFT join (Select userid,data from mdl_user_info_data d
                                                                        inner join mdl_user_info_field c
                                                                        on d.fieldid=c.id
                                                                where c.shortname ='empresa' and data <> '') as empresa
                                                                on empresa.userid=u.id
                                                                                left join (Select gi.courseid,gg.userid,gg.finalgrade
                                                                                from mdl_grade_items gi
                                                                                inner join mdl_grade_grades gg
                                                                                on gg.itemid=gi.id
                                                                                where gi.itemtype='course')as notas
                                                                                on u.id=notas.userid and usrcourse.courseid=notas.courseid
                                                                                left join mdl_course_completions cc
                                                                                on u.id=cc.userid and usrcourse.courseid=cc.course
                                                                                left join(Select count(id)as num,course from mdl_course_completion_criteria
                                                                                group by course) as act
                                                                                on act.course=usrcourse.courseid
                                                                                left join (Select userid,course,count(id)as num from mdl_course_completion_crit_compl
                                                                                group by course, userid)as compl
                                                                                on compl.userid=u.id and compl.course=usrcourse.courseid
                                                                                left join mdl_course c
                                                                                on usrcourse.courseid = c.id
                                                                                where
                                                                                 u.deleted=0
                                                                                 and u.suspended=0
                                                                                 and u.id like '%" . $codigo . "%'
                                                                                 and empresa.data like '%" . $empresa . "%'
                                                                                 and c.shortname like '%" . $curso . "%'
                                                                                 and usrcourse.timestart>='" . $inicio . "'
                                                                                 and usrcourse.timestart<='" . $fin . "'");
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
        
        $data = $DB->get_records_sql("SELECT distinct(fullname),id from mdl_course
                                               where fullname is not null  and id<>1 or fullname !='' order by fullname asc");
        return $data;
    }
    
    
    public function areas()
    {
        global $DB, $USER;
        
        $data = $DB->get_records_sql("SELECT distinct(empresa.data)as empresa
                                                          from mdl_user u left join
                                                          (Select userid,data
                                                          from mdl_user_info_data d
                                                          inner join mdl_user_info_field c on d.fieldid=c.id
                                                          where c.shortname ='empresa')
                                                          as empresa on empresa.userid=u.id where empresa.data <>''");
        return $data;
    }
    
    public function areas2()
    {
        global $DB, $USER;
        $data = $DB->get_records_sql("SELECT distinct(empresa.data)as empresa
                                                          from mdl_user u left join
                                                          (Select userid,data
                                                          from mdl_user_info_data d
                                                          inner join mdl_user_info_field c on d.fieldid=c.id
                                                          where c.shortname ='empresa' and d.userid = $USER->id)
                                                          as empresa on empresa.userid=u.id where empresa.data <>''");
        return $data;
    }
    
    public function getAreas()
    {
        global $DB, $USER;
        $query = "";
        $query .= " select ";
        $query .= "     distinct muid.data "; 
        $query .= " from ";
        $query .= "     mdl_user_info_data muid "; 
        $query .= " where muid.fieldid =14 order by muid.data asc";
        $data  = $DB->get_records_sql($query);
        return $data;
    }



        SELECT  @s:=@s+1 id, c.fullname AS curso, 
        COUNT(usrcourse.userid ) AS totalusuarios, 
        CONCAT(CAST((SUM((IFNULL(compl.num, 0) *100)/act.num) / COUNT(usrcourse.userid)) as DECIMAL(16,0)) ,'%') 
        as avancetotal, 
        SUM(if(IFNULL(compl.num, 0) = act.num, 1, 0)) AS usuariosfin, 
        CONCAT(CAST(SUM(IF(compl.num IS null, (act.num * 100) / act.num,  
        IF(compl.num < act.num, (compl.num * 100) / act.num, 0) )) 
        / COUNT(usrcourse.userid) as DECIMAL(16,0)) ,'%') AS porfinusuarios, 
        SUM(IF(compl.num IS NULL OR compl.num < act.num, 1, 0)) AS usuariosnofin 
        FROM (SELECT @s:= 0) AS s, mdl_user u 
        INNER JOIN (Select distinct ue.userid, e.courseid,ue.timestart  
        FROM mdl_user_enrolments ue 
        INNER join mdl_user u on ue.userid = u.id 
        INNER join mdl_enrol e on ue.enrolid=e.id  
        INNER JOIN mdl_role_assignments as asg on asg.userid = u.id 
        INNER JOIN mdl_context as con on asg.contextid = con.id 
        INNER JOIN mdl_role r on asg.roleid = r.id 
        WHERE r.shortname = 'student' 
        ) AS usrcourse  
        ON u.id=usrcourse.userid  
        LEFT JOIN(Select count(id)as num,course from mdl_course_completion_criteria  
        GROUP BY course) as act on act.course=usrcourse.courseid  
        LEFT JOIN (Select userid, course, count(id) AS num  
        FROM mdl_course_completion_crit_compl  
        GROUP BY course, userid) AS compl  
        ON compl.userid=u.id AND compl.course=usrcourse.courseid  
        LEFT JOIN mdl_course c ON usrcourse.courseid = c.id 
        inner join mdl_customfield_data cd on c.id = cd.instanceid 
        inner join mdl_customfield_field cf on cf.id = cd.fieldid 
        where cf.shortname = 'reporte' and cd.intvalue = 1 
        GROUP BY fullname 
        ORDER BY id ASC 

        <?php
        require_once("{$CFG->libdir}/formslib.php");
        require_once "../modelos/Querymodelos.php";
    
        $reporte1 = new Consultas();
        $rspta=$reporte1->reportegeneral(); 
    
        $valoresY= array(); 
        $valoresX= array(); 
    
        while($ver=mysqli_fetch_row($rspta)){
            $valoresY[] = $ver[2];
            $valoresX[] = $ver[3]; 
        }
    
        $datosX=json_encode($valoresX);
        $datosY=json_encode($valoresY);
    ?>


// datosX= crearCadenaLineal(' <?php echo $datosX ?>');
// datosY= crearCadenaLineal(' <?php echo $datosY ?>');


}

?>