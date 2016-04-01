<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2001, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: pnadminapi.php 31 2008-12-23 20:55:41Z Guite $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

/**
* Obtener tareas
* @param $args['id'] ID 
* @return bool true on success, false on failure
*/ 
function Tareas_adminapi_getAll($args)
{

	// Optional arguments.
	if (!isset($args['startnum']) || empty($args['startnum'])) {
		$args['startnum'] = 1;
	}
	if (!isset($args['numitems']) || empty($args['numitems'])) {
		$args['numitems'] = -1;
	}
	if (!isset($args['order']) || empty($args['order'])) {
		$args['order'] = 'ID';
	}
	if (!isset($args['tipoOrden']) || empty($args['tipoOrden'])) {
		$args['order'].= ' desc';
	} else {
		$args['order'].= ' '.$args['tipoOrden'];
	}
	if (!is_numeric($args['startnum']) ||
		!is_numeric($args['numitems'])) {
		return LogUtil::registerArgsError();
	}
	//Construir la clausula WHERE
	if (isset($args['Prioridad']) && $args['Prioridad'] != ''){
		$queryargs[] = "`Prioridad` = '".$args['Prioridad']."'";
	}
	if (isset($args['Asignado_A']) && $args['Asignado_A'] != ''){
		if ($args['Asignado_A'] == 'sin asignacion'){
			$queryargs[] = "`Asignado_A` IS NULL OR `Asignado_a` = ''";
		}else{
			$queryargs[] = "`Asignado_A` = '".$args['Asignado_A']."'";
		}
	}
	if (isset($args['Estado']) && $args['Estado'] != ''){
		$queryargs[] = "`Estado` = '".$args['Estado']."'";
	}

	if (count($queryargs) > 0) {
		$where = ' WHERE ' . implode(' AND ', $queryargs);
	}

	// Extrae el array de elementos de la BD (paginado)
	$objArray = DBUtil::selectObjectArray ('Tareas', $where, $args['order'], $args['startnum']-1, $args['numitems']);

	// Validamos que el elemento existe en la BD
	if ($objArray === false) {
		$dom = ZLanguage::getModuleDomain('Tareas');
		return LogUtil::registerError(__('Error! Do not found Web Work.', $dom));
	}

	// Retorna el objeto
	return $objArray;
}

/**
* Obtener tarea
* @param $args['ID'] ID de tarea
* @return Registro Tarea
*/ 
function Tareas_adminapi_get($args)
{
	// Valida los parámetros requeridos
	if (!isset($args['ID']) || !is_numeric($args['ID'])) {
		return LogUtil::registerArgsError();
	}
	// Security check
    if (!SecurityUtil::checkPermission('Tareas::', '::', ACCESS_OVERVIEW)) {
        return $items;
    }
	// Extrae el elemento de la BD con el ID
	$tarea = DBUtil::selectObjectByID('Tareas', $args['ID'], 'ID');
	
	// Retorna el objeto
	return $tarea;
}

/**
* Insertar una tarea
* @param $args['id'] ID 
* @return bool true on success, false on failure
*/
function Tareas_adminapi_insert($args)
{
	
	if (!isset($args) || empty($args)) {
		return LogUtil::registerArgsError();
	}
	extract($args);
	unset($args);

	//Campos obligatorios en la inserción
	if (!isset($Titulo) ||
		!isset($Creado_Por) ||
		!isset($Prioridad) ||
		!isset($Estado) ||
		!isset($Creacion) ){
		
		$dom = ZLanguage::getModuleDomain('Tareas');
		return LogUtil::registerError(__('Error! Empty field to try insert a record in DB.', $dom));
			
	}
	$args['Titulo'] 	  = pnVarPrepForStore($Titulo);
	$args['Creado_Por']   = pnVarPrepForStore($Creado_Por);
	$args['Prioridad']	  = pnVarPrepForStore($Prioridad);
	$args['Asignado_A']	  = pnVarPrepForStore($Asignado_A);
	$args['Estado'] 	  = pnVarPrepForStore($Estado);
	$args['Descripcion']  = pnVarPrepForStore($Descripcion);
	$args['Motivo']  	  = pnVarPrepForStore($Motivo);
	$args['Creacion']	  = pnVarPrepForStore($Creacion);
	$args['Modificacion'] = pnVarPrepForStore($Modificacion);

	return DBUtil::insertObject($args, 'Tareas','ID', false, true);
	
}

/**
 * Actualizar una tarea
 * @param $args['id'] ID de la tarea
 * @return bool true on success, false on failure
 */
function Tareas_adminapi_update($args)
{
	//Mandatory
	if (!isset($args['ID']) || empty($args['ID'])) {
		return LogUtil::registerArgsError();
	}
	$ID = $args['ID'];
	extract ($args);

	if ($Titulo != ''){
		$cadena.= $mas."`Titulo` = '".$Titulo."'";
		$mas = ",";
	}
	if ($Prioridad != ''){
		$cadena.= $mas."`Prioridad` = '".$Prioridad."'";
		$mas = ",";
	}
	if ($Asignado_A != ''){
		$cadena.= $mas."`Asignado_A` = '".$Asignado_A."'";
		$mas = ",";
	}else{
		$cadena.= $mas."`Asignado_A` = NULL";
	}
	if ($Estado != ''){
		$cadena.= $mas."`Estado` = '".$Estado."'";
		$mas = ",";
	}
	if ($Descripcion != ''){
		$cadena.= $mas."`Descripcion` = '".$Descripcion."'";
		$mas = ",";
	}else{
		$cadena.= $mas."`Descripcion` = NULL";
	}
	if ($Motivo != ''){
		$cadena.= $mas."`Motivo` = '".$Motivo."'";
		$mas = ",";
	}else{
		$cadena.= $mas."`Motivo` = NULL";
	}
	if ($Modificacion != ''){		
		$cadena.= $mas."`Modificacion` = '".$Modificacion."'";
		$mas = ",";
	}
	
	//Recuperar el nombre de la tabla completa
	$pntable = &pnDBGetTables();	
	$table  = $pntable['Tareas'];
	
	$sql = "UPDATE $table SET ".$cadena." WHERE `ID` = ".$ID;
	
	return DBUtil::executeSQL($sql);
	
}

/**
 * Borrar una Tarea
 * @param $args['id'] ID de la Tarea
 * @return bool true on success, false on failure
 */
function Tareas_adminapi_delete($args)
{
	// Argument check
	if (!isset($args['id'])) {
		return LogUtil::registerArgsError();
	}
	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Tareas');

	//Confirmamos que el registro que queremos borrar, existe.
	$item = pnModAPIFunc('Tareas', 'admin', 'get', array('ID' => $args['id']));

	if ($item === false) {
		return LogUtil::registerError(__('No such item found.', $dom));
	}

	if (!DBUtil::deleteObjectByID('Tareas', $args['id'], 'ID')) {
		return LogUtil::registerError(__('Error! Deletion attempt failed.', $dom));
	}

	// The item has been modified, so we clear all cached pages of this item.
	$render = & pnRender::getInstance('Tareas');
	$render->clear_cache(null, $args['id']);
	$render->clear_cache('Tareas_admin_view.htm');

	return true;
}

/**
 * Comprobar que el usuario existe en el sistema
 * @param $args['usuario'] Usuario
 * @return bool true on success, false on failure
 */
function Tareas_adminapi_chkUser($args)
{
	
	// Valida los parámetros requeridos
	if (!isset($args['user']) ) {
		return LogUtil::registerArgsError();
	}
	$where = "WHERE `pn_uname` LIKE '".$args['user']."'";
	// Extrae el array de elementos de la BD (paginado)
	$objCount = DBUtil::selectObjectCount ('users', $where);
	if ($objCount > 0){
		return true;
	}else{
		return false;
	}
	
}

/**
 * Contar los registros por una condicion
 * @param $args['usuario'] Usuario
 * @return Número de registros encontrados
 */
function Tareas_adminapi_countitems($args)
{
	
	$queryargs = array();

	if (isset($args['Prioridad']) && $args['Prioridad'] != ''){
		$queryargs[] = "`Prioridad` = '".$args['Prioridad']."'";
	}
	if (isset($args['Asignado_A']) && $args['Asignado_A'] != ''){
		$queryargs[] = "`Asignado_A` = '".$args['Asignado_A']."'";
	}
	if (isset($args['Estado']) && $args['Estado'] != ''){
		$queryargs[] = "`Estado` = '".$args['Estado']."'";
	}
	$where = '';
	if (count($queryargs) > 0) {
		$where = ' WHERE ' . implode(' AND ', $queryargs);
	}

	return DBUtil::selectObjectCount ('Tareas', $where, 'ID', false, '');

}

/**
 * sendEmail
 * Enviar un email de notificacion
 *
 *@param titulo string con el nombre de la tarea
 *@param asignado_por string con el usuario que te ha asignado la tarea
 *@param prioridad string con la prioridad de la tarea
 *@param asignado_a string con el destinatario del correo
 *@returns boolean
 */
 function Tareas_userapi_sendEmail($args)
{
	
	//Mandatory
	if (!isset($args['asignado_a']) || empty($args['asignado_a'])) {
		return LogUtil::registerArgsError();
	}

	$asignado_a = $args['asignado_a'];

	if(pnModAvailable('Mailer')) {

		//Obtener la direccion de mail de destino
		$detUser = pnModAPIFunc('User', 'user', 'get', array('uname' => $asignado_a));
		$emailDest = $detUser['email'];
		
		$sitename  = pnConfigGetVar('sitename');
		$toname    = $detUser['uname'];
		// Obtener la variable de email
		$toaddress = $emailDest;
		$subject   = 'Tarea asignada';
		$hoy = date("d-m-Y H:i");
		$body = 'El usuario '.$args['asignado_por'].' te ha asignado una tarea. Visita el
							<a href="http://www.heroesofmightandmagic.es/index.php?module=Tareas&type=admin&func=view">
							Gestor de Tareas</a> para revisarla. <br>'.
							'Tarea: '.$args['titulo'].'<br>'.
							'Prioridad: '.$args['prioridad'].'<br>'.
							'Fecha: '.$hoy;
		$res = pnModAPIFunc('Mailer', 'user', 'sendmessage',
                             array(	'toname'	=> $toname,
									'toaddress' => $toaddress,
									//'cc'		=> $emailCoa,
									'html'		=> true,
									'subject'   => $sitename." - ".$subject,
									'body'      => $body)
							);	
		return $res;
	}
	// no mailer module - error!
	return $false;
}