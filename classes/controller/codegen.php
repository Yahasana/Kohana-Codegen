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

        $response = '';

        foreach($modules as $m)
        {
            if($m === $module) continue;
            $response .= '<a href="'.URL::site('codegen?m='.$m).'">'.$m.'</a>';
        }

        $response = '<style>table{border:1px solid #eee;border-width:0 1px}
            a{text-decoration:none}
            caption{background:#A52A2A;color:#00FFA9}
            caption a{margin:0 1em}
            caption i{color:#FFA07A;padding:5px;display:block}
            table td{border-bottom:1px solid #eee;padding:0 2em}
            thead th{border-bottom:1px solid #eee;text-align:center;background:#222;color:#eee}
            tbody th{border:1px solid #bbb;border-width:0 0 1px 0;text-align:left;background:#eee;padding-left:5px}
            tfoot th,tfoot td{background:#FFE4C4;padding:1em 5px}
            .good{color:green}.notyet{color:#DAA520}.noexist{color:red}</style>
            <table><caption><h3>Kohana Codegen generated for '.$module.'</h3>'.$response.
            '<i>&#9745 - generated successfully, &#9746 - driver not impletement, &#10008; - driver not exist</i></caption>
            <thead><tr><th>Data Tables</th><th>Controllers</th><th>Models</th><th>Views</th><th>Themes</th><th>I18n</th></tr></thead><tbody>';

        $database   = Database::instance($module);

        $controller = Codegen::factory('controller');
        $model      = Codegen::factory('model');
        $view       = Codegen::factory('view');
        $theme      = Codegen::factory('theme');
        $i18n       = Codegen::factory('i18n');

        foreach($database->list_tables() as $table)
        {
            $columns = $database->list_columns($table);

            $response .= '<tr><th>'.$table.'</th><td>'
                .$controller->render($table, $columns).'</td><td>'
                .$model->render($table, $columns).'</td><td>'
                .$view->render($table, $columns).'</td><td>'
                .$theme->render($table, $columns).'</td><td>'
                .$i18n->render($table, $columns).'</td></tr>';
        }

        $response  .= '</tbody><tfoot><tr><th>Powered By <a href="http://github.com/Yahasana/Kohana-Codegen">Kohana Codegen</a></th><td colspan="5">Copyleft (c) sumh &lt;oalite at gmail dot com&gt;, version 1.0 <sup style="color:#A52A2A">alpha</sup></td></tr></tfoot></table>';

        $this->request->response = $response;
    }

} // END Controller_Codegen
