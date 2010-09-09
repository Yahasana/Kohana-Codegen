<?php

class Controller_Codegen extends Kohana_Controller {

    public function action_index()
    {
        if( ! $modules = array_keys((array) Kohana::config('database')))
        {
            $this->request->response = 'No any database config can be found'; return;
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

        $tables     = array();

        foreach($database->list_tables() as $table)
        {
            $columns = $database->list_columns($table);

            $tables[$table] = array(
                'controller'    => $controller->render($table, $columns),
                'model'         => $model->render($table, $columns),
                'view'          => $view->render($table, $columns),
                'theme'         => $theme->render($table, $columns),
                'i18n'          => $i18n->render($table, $columns),
            );
        }

        $view = new View('codegen', array('module' => $module, 'modules' => $modules, 'tables' => $tables));

        $this->request->response = $view->render();
    }

} // END Controller_Codegen
