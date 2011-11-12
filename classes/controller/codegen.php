<?php

class Controller_Codegen extends Kohana_Controller {

    public function action_index()
    {
        if( ! $modules = array_keys((array) Kohana::config('database')))
        {
            $this->response->body('No any database config can be found'); return;
        }

        if(isset($_GET['m']) AND in_array($_GET['m'], $modules))
            $module = $_GET['m'];
        else
            $module = current($modules);

        $database   = Database::instance($module);

        $controller = Codegen::factory('controller');
        $model      = Codegen::factory('model');
        $view       = Codegen::factory('view');
        $theme      = Codegen::factory('theme');
        $i18n       = Codegen::factory('i18n');
        $api        = Codegen::factory('api');

        $tables     = array();

        foreach($database->list_tables() as $table)
        {
            $columns = $database->list_columns($table);

            $tables[$table] = array(
                'controller'    => $controller ? $controller->render($table, $columns) : '&oplus;',
                'model'         => $model ? $model->render($table, $columns) : '&oplus;',
                'view'          => $view ? $view->render($table, $columns) : '&oplus;',
                'theme'         => $theme ? $theme->render($table, $columns) : '&oplus;',
                'i18n'          => $i18n ? $i18n->render($table, $columns) : '&oplus;',
                'api'           => $api ? $api->render($table, $columns) : '&oplus;',
            );
        }

        $view = new View('codegen', array('module' => $module, 'modules' => $modules, 'tables' => $tables));

        $this->response->body($view->render());
    }

} // END Controller_Codegen
