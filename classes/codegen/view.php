<?php defined('SYSPATH') or die('No direct script access.');

class Codegen_View extends Codegen {

    public function __construct(array $config = NULL)
    {
        if($config === NULL) $config = parent::$config['view'];

        $repos = str_replace('_', DIRECTORY_SEPARATOR, $config['directory']);
        $repos = parent::$config['repository'].'classes'.DIRECTORY_SEPARATOR.$repos.DIRECTORY_SEPARATOR;

        $config['directory'] = str_replace(' ', '_', ucwords(str_replace('_', ' ', $config['directory'])));

        foreach($config['driver'] as $driver)
        {
            $driver = strtolower($driver);
            if(method_exists($this, $driver))
            {
                $driver = $repos.$driver;
                is_dir($driver) ? parent::empty_dir($driver) : mkdir($driver, 0755, TRUE);
            }
        }

        $this->repository   = $repos;

        $this->module       = parent::$config['module'];

        $this->settings     = $config;
    }

    public function render($table, $columns)
    {
        $tmp = '';
        foreach($this->settings['driver'] as $driver)
        {
            $driver = strtolower($driver);
            if(method_exists($this, $driver))
            {
                if($this->$driver($table, $columns))
                    $tmp .= "<span class='good'>&#9745; $driver</span><br />";
                else
                    $tmp .= "<span class='notyet'>&#9746; $driver</span><br />";
            }
            else
            {
                $tmp .= "<span class='noexist'>&#10008; $driver</span><br />";
            }
        }
        return $tmp;
    }

    protected function php($table, $columns)
    {
        //
    }

    protected function mustache($table, $columns)
    {
        $table      = explode('_', $table);
        $table      = Inflector::singular(end($table));
        $uctalbe    = '_'.ucfirst($table);

        $content    = "<?php defined('SYSPATH') or die('No direct script access.');\n"
                        .strtr(parent::$config['license'], array(
                            '$package'  => $this->module,
                            '$year'     => date('Y'),
                            '$see'      => 'Model',
                        ))."\nclass {$this->settings['directory']}{$uctalbe} extends Mustache {\n\n";
        $content .= <<< CCC
    protected \$_template = '$table';

    public function __construct(\$template = NULL, \$view = NULL, \$partials = NULL)
    {
        \$this->I18N = I18n::load(I18n::\$lang);

        parent::__construct(\$template, \$view, \$partials);
    }

} // END {$this->settings['directory']}{$uctalbe}

CCC;
        $fp = fopen($this->repository.'mustache'.DIRECTORY_SEPARATOR.$table.'.php', 'w');
        fwrite($fp, $content);
        fclose($fp);

        return TRUE;
    }

    protected function twig($table, $columns)
    {
        //
    }

    protected function smarty($table, $columns)
    {
        //
    }

} // End Codegen_View
