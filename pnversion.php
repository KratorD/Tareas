<?php
/**
 * Tareas  Module
 *
 * @package      Tareas
 * @version      $Id: pnversion.php 2011-05-18$
 * @author       Krator
 * @link         http://www.heroesofmightandmagic.es
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

$dom = ZLanguage::getModuleDomain('Tareas');
$modversion['name']           = 'Tareas';
$modversion['displayname']    = __('Tareas', $dom);
$modversion['url']            = __('Tareas', $dom);
$modversion['version']        = '1.6';
$modversion['description']    = __('Web Work', $dom);
$modversion['credits']        = 'pndocs/changelog.txt';
$modversion['help']           = 'pndocs/readme.txt';
$modversion['changelog']      = 'pndocs/changelog.txt';
$modversion['license']        = 'pndocs/license.txt';
$modversion['official']       = 0;
$modversion['author']         = 'Krator';
$modversion['contact']        = 'http://www.heroesofmightandmagic.es';
$modversion['admin']          = 1;
$modversion['user']           = 0;
$modversion['securityschema'] = array('Tareas::' => 'Tareas::');
