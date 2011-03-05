<?php defined('SYSPATH') or die('No direct script access.');

class Codegen_Api extends Codegen {

    public function __construct(array $config = NULL)
    {
        if($config === NULL) $config = parent::$config['api'];

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
        $uctalbe    = '_'.ucfirst($table);

        $content    = "<?php defined('SYSPATH') or die('No direct script access.');\n"
                        .strtr(parent::$config['license'], array(
                            '$package'  => $this->module,
                            '$year'     => date('Y'),
                            '$see'      => $this->settings['extends'],
                        ))."\nclass {$this->settings['directory']}{$uctalbe} extends {$this->settings['extends']} {\n\n";
        $content .= <<< CCC
    public function before()
    {
        parent::before();

        \$this->model = new Model$uctalbe;
    }

    public function action_get(\$params)
    {
        //
    }

    public function action_create(\$params)
    {
        //
    }

    public function action_update(\$params)
    {
        //
    }

    public function action_delete(\$params)
    {
        //
    }

    protected function lists(array \$params)
    {
        \$orderby = parent::get('sort', 0);

        switch(\$orderby)
        {
            case 0:
                \$params['orderby'] = 'priority DESC';
                break;
            case 1:
                \$params['orderby'] = 'status ASC';
                break;
            case 2:
                \$params['orderby'] = 'risk DESC';
                break;
            case 3:
                \$params['orderby'] = 'update_time DESC';
                break;
            default:
                \$params['orderby'] = 'priority DESC';
                break;
        }

        \$data = \$this->model->lists(\$params, \$page);

        \$data['orderby']       = \$orderby;
        \$data['pagination']    = \$page;

        return \$data;
    }

} // END {$this->settings['directory']}$uctalbe

CCC;
        $fp = fopen($this->repository.$table.'.php', 'w');
        fwrite($fp, $content);
        fclose($fp);

        return '<span class="good">&#8730;</span>';
    }

} // End Codegen_Controller
