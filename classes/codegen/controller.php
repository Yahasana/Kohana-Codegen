<?php defined('SYSPATH') or die('No direct script access.');

class Codegen_Controller extends Codegen {

    public function __construct(array $config = NULL)
    {
        if($config === NULL) $config = parent::$config['controller'];

        $repos = str_replace('_', DIRECTORY_SEPARATOR, $config['directory']);
        $repos = parent::$config['repository'].'classes'.DIRECTORY_SEPARATOR.$repos.DIRECTORY_SEPARATOR;

        $config['directory'] = str_replace(' ', '_', ucwords(str_replace('_', ' ', $config['directory'])));

        is_dir($repos) ? $this->empty_dir($repos) : mkdir($repos, 0755, TRUE);

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

    public function action_index(\$$key_id = NULL)
    {
        echo new View$uctalbe;
    }

    protected function lists(array \$params)
    {
        \$page = new Pagination;

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

        \$total_rows = TRUE;

        \$data = \$this->model->lists(\$params, \$page->offset, \$page->items_per_page, \$total_rows);

        \$page->total_items     = \$total_rows;
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
