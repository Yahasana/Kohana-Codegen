<?php defined('SYSPATH') or die('No direct script access.');

return array(

    // Directory for source code files
    'repository'    => DOCROOT.'temp',

    // database config group name
    'module'        => 'default',

    // Controller
    'controller' => array(
        'on'        => TRUE,            # To generate controller classes or not
        'directory' => 'controller',    # The directory you want to put your model classes
        'extends'   => 'Controller',    # All controller classes will extends from this class
        'before'    => FALSE,
        'after'     => FALSE,
    ),

    // Model
    'model' => array(
        'on'        => TRUE,            # To generate model classes or not
        'directory' => 'model',         # The directory you want to put your model classes
        'extends'   => 'Kohana_Model',  # All model classes will extends from this class
        't_prefix'  => 't_',            # Remove the table prefix for more clean classes name
        'model'     => array(
            'excludes'  => array('insert_time','insert_by','update_time','update_by','remark'),
        ),
        'orm'       => array(
            'excludes'  => array('insert_time','insert_by','update_time','update_by','remark'),
            'validate'  => array(
                'rules'   => TRUE,      # TRUE - generate valid rules
                'requires'=> FALSE      # TRUE - valid all fields, FALSE - valid ONLY not null fields
            ),
        ),
        'driver'    => array('Model','ORM','Jelly','Sprig','Hive'),
    ),

    // View
    'view' => array(
        'on'        => TRUE,    # To generate view classes or not
        'directory' => 'view',  # The directory you want to put your view classes
        'extends'   => 'Page',  # All view classes will extends from this class
        'driver'    => array('php', 'mustache', 'twig', 'smarty')
    ),

    // Theme layout
    'theme' => array(
        'on'        => TRUE,    # To generate theme layout or not
        'layout'    => array(
            'edit'  => TRUE,    # To generate edit layout or not
            'view'  => TRUE,    # To generate view layout or not
            'list'  => TRUE     # To generate list layout or not
        ),
        'driver'    => array('php', 'mustache', 'twig', 'smarty')
    ),

    // I18n
    'i18n' => array(
        'on'        => TRUE,    # To generate i18n or not
        'standalone'=> FALSE     # TRUE - one file for all tables, else each table has own file.
    ),

    // I18n
    'api' => array(
        'on'        => TRUE,    # To generate API or not
        'directory' => 'controller_api',   # The directory you want to put your view classes
        'extends'   => 'Oauth_Controller',    # All controller classes will extends from this class
    ),

    'license' => <<<CCC
/**
 * description...
 *
 * @author		sumh <oalite@gmail.com>
 * @package		\$package
 * @copyright	(c) \$year OALite team, All rights reserved.
 * @license		http://www.oalite.com/license.txt
 * @link		http://www.oalite.com
 * @see			\$see
 * *
 */
CCC
);
