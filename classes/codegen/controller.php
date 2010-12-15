<?php defined('SYSPATH') or die('No direct script access.');

class Codegen_Controller extends Codegen {

    public function __construct(array $config = NULL)
    {
        if($config === NULL) $config = parent::$config['controller'];

        $repos = str_replace('_', DIRECTORY_SEPARATOR, $config['directory']);
        $repos = parent::$config['repository'].'classes'.DIRECTORY_SEPARATOR.$repos.DIRECTORY_SEPARATOR;

        $config['directory'] = str_replace(' ', '_', ucwords(str_replace('_', ' ', $config['directory'])));

        is_dir($repos) ? parent::empty_dir($repos) : mkdir($repos, 0755, TRUE);

        $this->repository   = $repos;

        $this->module       = parent::$config['module'];

        $this->settings     = $config;
    }

    public function render($table, $columns)
    {
        $key_id     = key($columns);
        $table      = explode('_', $table);
        $table      = Inflector::singular(end($table));
        $uctable    = '_'.ucfirst($table);

        $content    = "<?php defined('SYSPATH') or die('No direct script access.');\n"
                        .strtr(parent::$config['license'], array(
                            '$package'  => $this->module,
                            '$year'     => date('Y'),
                            '$see'      => $this->settings['extends'],
                        ))."\nclass {$this->settings['directory']}{$uctable} extends {$this->settings['extends']} {\n\n";
        $content .= <<< CCC
    public function before()
    {
        parent::before();

        \$this->model = new Model$uctable;
    }

    public function action_index(\$$key_id = NULL)
    {
        echo new View$uctable;
    }

    protected function lists(array \$params)
    {
        \$params['orderby'] = parent::get('sort', 'priority');

        \$data = \$this->model->lists(\$params);

        return \$data;
    }

} // END {$this->settings['directory']}$uctable

CCC;
        $fp = fopen($this->repository.$table.'.php', 'w');
        fwrite($fp, $content);
        fclose($fp);

        return '<span class="good">&#8730;</span>';
    }

} // End Codegen_Controller
