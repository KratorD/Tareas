/**
 * PostNuke Application Framework
 *
 * @copyright (c) 2002, Zikula Development Team
 * @link http://www.postnuke.com
 * @version $Id: h6_artefactos.js 18677 2006-04-06 12:07:09Z markwest $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula_3rdParty_Modules
 * @subpackage h6_artefactos
*/

//Declaraciones
var contenido_textarea = "" ;
var num_caracteres_permitidos = 3000;

//Funcion para no permitir más de 150 caracteres en la casilla especial
function valida_longitud(control){ 
	
	num_caracteres = control.value.length;
	if (num_caracteres > num_caracteres_permitidos){ 
		control.value = contenido_textarea;
	}else{
		contenido_textarea = control.value;
	}

}

/**
 * Show Text Area for reason
 *
 *@params none;
 *@return none;
 *@author Krator
 */
 function crear_motivo(obj) {
  valor = document.fichaTarea.cmbEstado.options[document.fichaTarea.cmbEstado.selectedIndex].value;
  capa=document.getElementById('capa_motivo');
  capa.innerHTML="";

	if (valor == "Detenida" || valor == "Rechazada"){
		capa.innerHTML+="<strong>Motivo:</strong><br><textarea name=\"txtMotivo\" cols=\"75\" rows=\"2\" id=\"txtMotivo\" ></textarea>";
	}
	
}