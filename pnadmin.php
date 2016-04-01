<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2001, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: pnadmin.php 31 2008-12-23 20:55:41Z Guite $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

function Tareas_admin_main()
{
    if (!SecurityUtil::checkPermission('Tareas::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();
    }
	
	return Tareas_admin_view();
}

function Tareas_admin_view($args)
{
	if (!SecurityUtil::checkPermission('Tareas::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	//Obtener los parametros
	$page  = (int)FormUtil::getPassedValue('page', isset($args['page']) ? $args['page'] : 1, 'GET');
	$order = FormUtil::getPassedValue('order', isset($args['order']) ? $args['order'] : 'ID', 'GET');
	$asignada = FormUtil::getPassedValue('asignada', isset($args['asignada']) , 'GET');
	$filtroE = FormUtil::getPassedValue('filtroE', isset($_POST['cmbFiltroEstado']) ? $_POST['cmbFiltroEstado'] : 'Todos', 'POST');
	$filtroP = FormUtil::getPassedValue('filtroP', isset($_POST['cmbFiltroPrioridad']) ? $_POST['cmbFiltroPrioridad'] : 'Todos', 'POST');

	// Si el filtro viene por URL
	$estado = FormUtil::getPassedValue('estado', isset($args['estado']) , 'GET');
	if ($estado != ""){
		$filtroE = $estado;
	}
	
	// Obtener todas las variables del modulo
	$modvars = pnModGetVar('Tareas');
	
	$itemsperpage = $modvars['itemsperpage'];
	$estados 	 = explode("/", $modvars['estado']);
	$prioridades = explode("/", $modvars['prioridad']);

	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Tareas');
	
	// Primer elemento a obtener de la paginacion
	$startnum = (($page - 1) * $itemsperpage) + 1;
	
	if ($filtroE == 'Todos'){unset($filtroE);}
	if ($filtroP == 'Todos'){unset($filtroP);}
	// Procesamos los datos con los APIs necesarios
	$tareas = pnModAPIFunc('Tareas', 'admin', 'getAll', 
						array(	'Estado'    => $filtroE,
								'Prioridad' => $filtroP,
								'Asignado_A' => $asignada,
								'startnum'  => $startnum,
								'numitems'  => $itemsperpage,
								'order'     => $order));

	//Obtener tareas sin asignacion
	$sinAsignacion	= pnModAPIFunc('Tareas', 'admin', 'getAll', 
							array(	'Asignado_A' => 'sin asignacion',
									'numitems'  => $itemsperpage,
									'order'     => $order));

	//Obtener el usuario para ver sus tareas
	$user = pnUserGetVar('uname');

	// Construimos y devolvemos la Vista
	$render = & pnRender::getInstance('Tareas');

	//Enviarlas a la plantilla
	$render->assign('tareas', $tareas);
	$render->assign('sinAsignacion', $sinAsignacion);
	$render->assign('estados', $estados);
	$render->assign('prioridades', $prioridades);
	$render->assign('filtroE', $filtroE);
	$render->assign('filtroP', $filtroP);
	$render->assign('user', $user);

	// Asignar los valores al sistema de paginación
	$render->assign('pager', array(	'numitems' => pnModAPIFunc('Tareas', 'admin', 'countitems',
															array(	'Estado'    => $filtroE,
																	'Prioridad' => $filtroP,
																	'Asignado_A' => $asignada)),
									'itemsperpage' => $itemsperpage));

	return $render->fetch('Tareas_admin_view.htm');

}

//Funcion que presenta la plantilla para ver una tarea
function Tareas_admin_display($args)
{
	if (!SecurityUtil::checkPermission('Tareas::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	//Obtener los parametros
	$tid  = (int)FormUtil::getPassedValue('tid', isset($args['tid']) , 'GET');
	
  	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Tareas');
	
	//Recuperamos los datos del registro que queremos presentar.
	$tarea = pnModAPIFunc('Tareas', 'admin', 'get', array('ID' => $tid));
	
	// Construimos y devolvemos la Vista
	$render = & pnRender::getInstance('Tareas');
	
	$render->assign('tarea', $tarea);
	
	return $render->fetch('Tareas_admin_display.htm');
}

//Funcion que presenta la plantilla para crear una tarea
function Tareas_admin_new()
{
	if (!SecurityUtil::checkPermission('Tareas::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
  	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Tareas');
	
	// Obtener todas las variables del modulo
	$modvars = pnModGetVar('Tareas');
	
	$prioridades = explode("/", $modvars['prioridad']);
	$estados 	 = explode("/", $modvars['estado']);
	
	// Construimos y devolvemos la Vista
	$render = & pnRender::getInstance('Tareas');
	//Pasamos variables a plantilla
	$render->assign('prioridades', $prioridades);
	$render->assign('estados', $estados);

	return $render->fetch('Tareas_admin_new.htm');
  
}

function Tareas_admin_create($args)
{
	if (!SecurityUtil::checkPermission('Tareas::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	
	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Tareas');
	
	//Pasar a nuevas variables para ser más manejable el nombre
	extract($args);
	unset($args);
	
	list(	$txtNombre,
			$cmbPrioridad,			
			$txtAsignado,
			$cmbEstado,
			$txtDesc) = pnVarCleanFromInput('txtNombre', 'cmbPrioridad',  'txtAsignado', 'cmbEstado', 'txtDesc');
	
	// No html permitido para las casillas de texto
	list(	$txtNombre,
			$txtAsignado,
			$txtDesc
			) = pnVarPrepForDisplay($txtNombre, $txtAsignado, $txtDesc);

	//Check for input parameters (mandatory!!)
	if ($txtNombre == '' || $cmbPrioridad == '' || $cmbEstado == '' ){	
		// Construimos y devolvemos la Vista
		$render = & pnRender::getInstance('Tareas');
		$tipo_error = 1;  //Error por parametros de entrada vacios
		$render->assign('tipo_error', $tipo_error);
		return $render->fetch('Tareas_admin_error.htm');
		
	}	
	
	//Comprobar que el usuario asignado existe
	if ($txtAsignado != ''){
		$exito = pnModAPIFunc('Tareas', 'admin', 'chkUser', 
								array('user'    => $txtAsignado));
		if ($exito == false){
			// Construimos y devolvemos la Vista
			$render = & pnRender::getInstance('Tareas');
			$tipo_error = 2;  //Error por usuario inexistente
			$render->assign('tipo_error', $tipo_error);
			$render->assign('user', $txtAsignado);
			return $render->fetch('Tareas_admin_error.htm');
		}
	}
	
	//Comprobar que el estatus indicado es correcto
	if ($txtAsignado != '' && $cmbEstado != 'Asignada'){
		// Construimos y devolvemos la Vista
		$render = & pnRender::getInstance('Tareas');
		$tipo_error = 3;  //Error por status incorrecto
		$render->assign('tipo_error', $tipo_error);
		return $render->fetch('Tareas_admin_error.htm');
	}
	//Comprobar que el estatus indicado es correcto
	if ($txtAsignado == '' && $cmbEstado == 'Asignada'){
		// Construimos y devolvemos la Vista
		$render = & pnRender::getInstance('Tareas');
		$tipo_error = 3;  //Error por status incorrecto
		$render->assign('tipo_error', $tipo_error);
		return $render->fetch('Tareas_admin_error.htm');
	}
	
	//Obtener el usuario actual
	$curr_user = pnUserGetVar('uname');
	
	// Obtener todas las variables del modulo
	$modvars = pnModGetVar('Tareas');
	
	//Preparar los datos para añadir el registro
	$record['Titulo'] 	  = $txtNombre;
	$record['Creado_Por'] = $curr_user;
	$record['Prioridad']  = $cmbPrioridad;
	if ($txtAsignado != ''){
		$record['Asignado_A'] = $txtAsignado;
	}	
	$record['Estado'] 	   = $cmbEstado;
	$record['Descripcion'] = $txtDesc;
	$record['Creacion']    = date('Y-m-d');	

	//Añadir el registro a la base de datos
	if (pnModAPIFunc('Tareas', 'admin', 'insert', $record)) {
		// Success
		LogUtil::registerStatus (__('Work added sucessfully.', $dom));
		// Si el usuario ha asignado la tarea a otro usuario diferente, avisarle con un email
		if (pnModAvailable('Mailer')) 
		{
			pnModAPIFunc('Tareas', 'user', 'sendEmail', 
	  					array(	'titulo'		=> $record['Titulo'],
								'asignado_por'	=> $record['Creado_Por'],
    							'prioridad'		=> $record['Prioridad'],
								'asignado_a'	=> $record['Asignado_A'])); 
		}
	}else{
		// Error
		LogUtil::registerError (__('Error trying insert.', $dom));
	}
	return pnRedirect(pnModURL('Tareas', 'admin', 'view'));
  
}

//Funcion que presenta la plantilla para editar una tarea
function Tareas_admin_modify($args)
{
	if (!SecurityUtil::checkPermission('Tareas::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	//Obtener los parametros
	$tid  = (int)FormUtil::getPassedValue('tid', isset($args['tid']) , 'GET');
	
	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Tareas');
	
	// Obtener todas las variables del modulo
	$modvars = pnModGetVar('Tareas');
	
	$prioridades = explode("/", $modvars['prioridad']);
	$estados 	 = explode("/", $modvars['estado']);
	
	//Recuperamos los datos del registro que queremos modificar.
	$tarea = pnModAPIFunc('Tareas', 'admin', 'get', array('ID' => $tid));
	
	// Construimos y devolvemos la Vista
	$render = & pnRender::getInstance('Tareas');
	
	$render->assign('tarea', $tarea);
	$render->assign('prioridades', $prioridades);
	$render->assign('estados', $estados);

	return $render->fetch('Tareas_admin_modify.htm');
	
}

function Tareas_admin_update($args)
{
	if (!SecurityUtil::checkPermission('Tareas::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	//Obtener los parametros
	$tid  = (int)FormUtil::getPassedValue('tid', isset($args['tid']) , 'GET');
	
	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Tareas');
	
	// Obtener todas las variables del modulo
	$modvars = pnModGetVar('Tareas');
	
	$prioridades = explode("/", $modvars['prioridad']);
	$estados 	 = explode("/", $modvars['estado']);
	
	//Confirmamos que el registro que queremos actualizar, existe.
	$lista = pnModAPIFunc('Tareas', 'admin', 'get', array('ID' => $tid));
	
	if ($lista == false) {
		LogUtil::registerError(__('Error! The work do not exists.', $dom));
	}
	
	extract($args);
	unset($args);
	
	list(	$txtNombre,
			$cmbPrioridad,			
			$txtAsignado,
			$cmbEstado,
			$txtMotivo,
			$txtDesc) = pnVarCleanFromInput('txtNombre', 'cmbPrioridad',  'txtAsignado', 'cmbEstado', 'txtMotivo', 'txtDesc');
	
	// No html permitido para las casillas de texto
	list(	$txtNombre,
			$txtAsignado,
			$txtMotivo,
			$txtDesc
			) = pnVarPrepForDisplay($txtNombre, $txtAsignado, $txtMotivo, $txtDesc);

	//Check for input parameters (mandatory!!)
	if ($txtNombre == '' || $cmbPrioridad == '' || $cmbEstado == '' ){	
		// Construimos y devolvemos la Vista
		$render = & pnRender::getInstance('Tareas');
		$tipo_error = 1;  //Error por parametros de entrada vacios
		$render->assign('tipo_error', $tipo_error);
		return $render->fetch('Tareas_admin_error.htm');
		
	}	
	
	//Comprobar que el usuario asignado existe
	if ($txtAsignado != ''){
		$exito = pnModAPIFunc('Tareas', 'admin', 'chkUser', 
								array('user'    => $txtAsignado));
		if ($exito == false){
			// Construimos y devolvemos la Vista
			$render = & pnRender::getInstance('Tareas');
			$tipo_error = 2;  //Error por usuario inexistente
			$render->assign('tipo_error', $tipo_error);
			$render->assign('user', $txtAsignado);
			return $render->fetch('Tareas_admin_error.htm');
		}
	}
	
	//Comprobar que el estatus indicado es correcto
	if ($txtAsignado != '' && $cmbEstado == 'Creada'){
		// Construimos y devolvemos la Vista
		$render = & pnRender::getInstance('Tareas');
		$tipo_error = 3;  //Error por status incorrecto
		$render->assign('tipo_error', $tipo_error);
		return $render->fetch('Tareas_admin_error.htm');
	}
	if ($txtAsignado == '' && $cmbEstado != 'Creada'){
		// Construimos y devolvemos la Vista
		$render = & pnRender::getInstance('Tareas');
		$tipo_error = 3;  //Error por status incorrecto
		$render->assign('tipo_error', $tipo_error);
		return $render->fetch('Tareas_admin_error.htm');
	}
	
	$record['Titulo'] 	    = $txtNombre;
	//$record['Creado_Por']   = $lista['Creado_Por'];
	$record['Prioridad']    = $cmbPrioridad;
	$record['Asignado_A']   = $txtAsignado;
	$record['Estado'] 	    = $cmbEstado;
	$record['Motivo'] 	    = $txtMotivo;
	$record['Descripcion']  = $txtDesc;
	//$record['Creacion'] 	= $lista['Creacion'];
	$record['Modificacion'] = date('Y-m-d');

	//Actualizar la tarea
	$result = pnModAPIFunc('Tareas', 'admin', 'update',	
							array(	'ID' 	 	   => $tid,
									'Titulo' 	   => $txtNombre,
									'Prioridad'    => $cmbPrioridad,
									'Asignado_A'   => $txtAsignado,
									'Estado' 	   => $cmbEstado,
									'Motivo'  	   => $txtMotivo,
									'Descripcion'  => $txtDesc,
									'Modificacion' => $record['Modificacion']
							));
	
	if ($result == false){
		// Error
		LogUtil::registerError(__('Error! The work was not updated.', $dom));
	}else{
		// Success
		LogUtil::registerStatus (__('Work modify sucessfully.', $dom));
		// Si el usuario ha asignado la tarea a otro usuario diferente, avisarle con un email
		if (pnModAvailable('Mailer')) 
		{
			$asignado_por = pnUserGetVar('uname');
			pnModAPIFunc('Tareas', 'user', 'sendEmail', 
	  					array(	'titulo'		=> $record['Titulo'],
								'asignado_por'	=> $asignado_por,
    							'prioridad'		=> $record['Prioridad'],
								'asignado_a'	=> $record['Asignado_A'])); 
		}
	}
			
	return pnRedirect(pnModURL('Tareas', 'admin', 'view'));
  
}

//Funciona para borrar una tarea
function Tareas_admin_delete($args)
{
	if (!SecurityUtil::checkPermission('Tareas::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	$confirmation = FormUtil::getPassedValue('confirmation', null, 'POST');
	
	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Tareas');
	
	//Recuperar los parametros
	$tid = isset($args['tid']) ? $args['tid'] : FormUtil::getPassedValue('tid', null, 'REQUEST');

	// Check for confirmation.
	if (empty($confirmation)) {
  	// No confirmation yet
		// Construimos y devolvemos la Vista
		$render = & pnRender::getInstance('Tareas');
		$render->assign('tid', $tid);

		// Return the output that has been generated by this function
		return $render->fetch('Tareas_admin_delete.htm');
	}
	// Confirm authorisation code
	if (!SecurityUtil::confirmAuthKey()) {
		return LogUtil::registerAuthidError (pnModURL('Tareas', 'admin', 'view'));
	}

	//Confirmamos que el registro que queremos borrar, existe.
	$lista = pnModAPIFunc('Tareas', 'admin', 'get', array('ID' => $tid));
	
	if ($lista == false) {
		LogUtil::registerError(__('Error! The work do not exists.', $dom));
	}

	//Almacenar el registro en la papelera
	if (pnModAvailable('Papelera')){
		$usuario = pnUserGetVar('uname');
		$bin = pnModAPIFunc('Papelera', 'admin', 'store', array('Modulo' => 'Tareas',
																'Tabla' => 'Tareas',
																'Usuario' => $usuario,
																'Registro' => $tid));
	}

	if (pnModAPIFunc('Tareas', 'admin', 'delete', array('id' =>$tid))) {
		// Success
		LogUtil::registerStatus (__('Work deleted sucessfully.', $dom));
	}else{
		// Error
		LogUtil::registerError(__('Error! The work was not deleted.', $dom));
	}
	return pnRedirect(pnModURL('Tareas', 'admin', 'view'));
	
}

//Funcion para autoasignarse una tarea rapidamente
function Tareas_admin_assignme($args)
{
	if (!SecurityUtil::checkPermission('Tareas::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	//Obtener los parametros
	$tid  = (int)FormUtil::getPassedValue('tid', isset($args['tid']) , 'GET');

	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Tareas');

	// Obtener todas las variables del modulo
	$modvars = pnModGetVar('Tareas');

	$prioridades = explode("/", $modvars['prioridad']);
	$estados 	 = explode("/", $modvars['estado']);

	//Confirmamos que el registro que queremos actualizar, existe.
	$lista = pnModAPIFunc('Tareas', 'admin', 'get', array('ID' => $tid));

	if ($lista == false) {
		LogUtil::registerError(__('Error! The work do not exists.', $dom));
	}

	extract($args);
	unset($args);

	//Obtener el usuario activo
	$user = pnUserGetVar('uname');

	//Si el estado era Creada, pasarlo a Asignada
	if ($lista['Estado'] == "Creada"){
		$record['Estado'] = "Asignada";
	}else{
		$record['Estado'] = $lista['Estado'];
	}

	$record['Asignado_A']   = $user;
	$record['Modificacion'] = date('Y-m-d');

	//Actualizar la tarea
	$result = pnModAPIFunc('Tareas', 'admin', 'update',	
							array(	'ID' 	 	   => $tid,
									'Asignado_A'   => $record['Asignado_A'],
									'Estado' 	   => $record['Estado'],
									'Modificacion' => $record['Modificacion']
							));
	
	if ($result == false){
		// Error
		LogUtil::registerError(__('Error! The work was not updated.', $dom));
	}else{
		// Success
		LogUtil::registerStatus (__('Work modify sucessfully.', $dom));
	}
			
	return pnRedirect(pnModURL('Tareas', 'admin', 'view'));
  
}

//Funcion para mostrar la configuracion
function Tareas_admin_modifyconfig($args)
{
	if (!SecurityUtil::checkPermission('Tareas::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Tareas');
	
	// Obtener todas las variables del modulo
	$modvars = pnModGetVar('Tareas');
	
	$prioridad = $modvars['prioridad'];
	$estado    = $modvars['estado'];
	
	// Construimos y devolvemos la Vista
	$render = & pnRender::getInstance('Tareas');

	$render->assign('prioridad', $prioridad);
	$render->assign('estado', $estado);

	return $render->fetch('Tareas_admin_modifyconfig.htm');
	
}

//Funciona para actualizar la configuracion del modulo
function Tareas_admin_updateconfig($args)
{
	if (!SecurityUtil::checkPermission('Tareas::', '::', ACCESS_ADMIN)) {
		return LogUtil::registerPermissionError();
	}
	//Lenguaje
	$dom = ZLanguage::getModuleDomain('Tareas');
	
	//Valor de Prioridad
	$prioridad = FormUtil::getPassedValue('txtPrioridad', "Crítica/Alta/Media/Baja", 'POST');
	pnModSetVar('Tareas', 'prioridad', $prioridad);
	
	//Valor de Estado
	$estado = FormUtil::getPassedValue('txtEstado', "Creada/Asignada/En progreso/Detenida/Rechazada/Finalizada", 'POST');
	pnModSetVar('Tareas', 'estado', $estado);
	
	// the module configuration has been updated successfuly
    LogUtil::registerStatus (__('Done! Module configuration updated.', $dom));

    return pnRedirect(pnModURL('Tareas', 'admin', 'main'));
	
}