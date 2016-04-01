<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2001, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: pninit.php 31 2008-12-23 20:55:41Z Guite $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

function Tareas_init()
{
    $tables = array('Tareas');
    foreach ($tables as $table) {
        if (!DBUtil::createTable($table)) {
            return false;
        }
    }
    
    pnModSetVar('Tareas', 'modulestylesheet', 'Tareas.css');
	pnModSetVar('Tareas', 'itemsperpage', '25');
	pnModSetVar('Tareas', 'prioridad', "Crítica/Alta/Media/Baja");
	pnModSetVar('Tareas', 'estado', "Creada/Asignada/En progreso/Detenida/Rechazada/Finalizada");
	
    return true;
}

function Tareas_upgrade($oldversion)
{
    return true;
}

function Tareas_delete()
{
    $tables = array('Tareas');
    foreach ($tables as $table) {
        if (!DBUtil::dropTable($table)) {
            return false;
        }
    }
    
    pnModDelVar('Tareas');
    return true;
}
