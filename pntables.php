<?php
/**
 * Tareas Module for Zikula
 *
 * @copyright (c) 2011, Krator
 * @link http://www.heroesofmightandmagic.es
 * @version $Id: pntables.php 2011-05-18 $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
*/

/**
 * Populate pntables array
 *
 * @author Krator
 * @return array pntables array
 */
function Tareas_pntables()
{
    $pntable = array();

    // Tabla que contiene todas las tareas de la web
    $pntable['Tareas'] = DBUtil::getLimitedTablename('Tareas');
    $pntable['Tareas_column'] = array(	'ID'   	 		=> 'ID',
										'Titulo'		=> 'Titulo',
										'Creado_Por'    => 'Creado_Por',
										'Prioridad'   	=> 'Prioridad',
										'Asignado_A'	=> 'Asignado_A',
										'Estado' 		=> 'Estado',
										'Descripcion'	=> 'Descripcion',
										'Motivo'		=> 'Motivo',
										'Creacion'	    => 'Creacion',
										'Modificacion'  => 'Modificacion',											
										);
	//http://community.zikula.org/index.php?module=Wiki&tag=ADOdbDataDictionary
    $pntable['Tareas_column_def'] = array(	'ID'   	 		=> "I NOTNULL AUTO PRIMARY",
											'Titulo'  		=> "C(30) NOTNULL",
											'Creado_Por'  	=> "C(25) NOTNULL",
											'Prioridad'  	=> "C(7) NOTNULL",
											'Asignado_A'	=> "C(25) NULL",
											'Estado' 		=> "C(11) NOTNULL",
											'Descripcion' 	=> "X NULL",
											'Motivo' 		=> "X NULL",
											'Creacion'	 	=> "D NOTNULL",
											'Modificacion'	=> "D NULL"
										    );
    $pntable['Tareas_column_idx'] = array('ID' => 'ID');
		
	return $pntable;
}
