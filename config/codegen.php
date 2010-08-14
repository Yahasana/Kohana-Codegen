<?php defined('SYSPATH') or die('No direct script access.');

return array(

    // Directory for source code files
    'repository'    => DOCROOT.'temp',

    // database config group name
    'module'        => 'default',

    // Controller
    'controller' => array(
        'on'        => TRUE,
        'prefix'    => 'controller',
        'extends'   => 'Controller',
        'before'    => FALSE,
        'after'     => FALSE,
    ),

    // Model
    'model' => array(
        'on'        => TRUE,
        'prefix'    => 'model',
        'orm'       => array(
            'excludes'  => array('insert_time','insert_by','update_time','update_by'),
            'validate'  => array(
                'rules'   => TRUE,      # TRUE - generate valid rules
                'requires'=> FALSE      # TRUE - valid all fields, FALSE - valid ONLY not null fields
            ),
        ),
        'driver'    => array('Model','ORM','Jelly','Sprig','Hive'),
    ),

    // View
    'view' => array(
        'on'        => TRUE,
        'prefix'    => 'view',
        'extends'   => 'Page',
        'driver'    => array('php', 'mustache', 'twig', 'smarty')
    ),

    // Theme layout
    'theme' => array(
        'on'        => TRUE,
        'layout'      => array(
            'edit'  => TRUE,
            'view'  => TRUE,
            'list'  => TRUE
        ),
        'driver'    => array('php', 'mustache', 'twig', 'smarty')
    ),


    // I18n
    'i18n' => array(
        'on'        => TRUE,
        'standalone'=> FALSE
    ),

    'license'=> <<<'CCC'
/**
 * description...
 *
 * @author		example <example@example.com>
 * @package		$package
 * @copyright	(c) $year example team, All rights reserved.
 * @license		http://www.example.com/license.txt
 * @link		http://www.example.com
 * @see			$see
 * *
 */
CCC
);
