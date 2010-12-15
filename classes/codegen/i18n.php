<?php defined('SYSPATH') or die('No direct script access.');

class Codegen_I18n extends Codegen {

    protected $i18n = '';

    public function __construct(array $config = NULL)
    {
        if($config === NULL) $config = parent::$config['i18n'];

        $repos = parent::$config['repository'].'i18n'.DIRECTORY_SEPARATOR;

        is_dir($repos) ? parent::empty_dir($repos) : mkdir($repos, 0755, TRUE);

        $this->repository   = $repos;

        $this->settings     = $config;
    }

    public function render($table, $columns)
    {
        if($this->settings['standalone'])
        {
            $this->standalone($table, $columns);
        }
        else
        {
            $this->i18n .= $this->compact($table, $columns);
        }

        return '<span class="good">&#8730;</span>';
    }

    protected function compact($table, $columns)
    {
        static $i18n;

        // Remove the auto increasement field
        array_shift($columns);

        if($i18n)
        {
            $columns = array_diff_assoc($columns, $i18n);
            $i18n += $columns;
        }
        else $i18n = $columns;

        if(empty($columns)) return '';

        $table      = explode('_', $table);
        $table      = Inflector::singular(end($table));

        $content    = "\n\n    // $table";
        foreach($columns as $key => $column)
        {
            $key = ucwords(Inflector::humanize($key));
            $content .= "\n    '".str_replace(' ','',$key)."'\t\t=> '".$key.'\',';

        }

        return $content;
    }

    protected function standalone($table, $columns)
    {
        // Remove the auto increasement field
        array_shift($columns);

        $table      = explode('_', $table);
        $table      = Inflector::singular(end($table));

        $content = "\n\n    // $table";
        foreach($columns as $key => $column)
        {
            $key = ucfirst(Inflector::humanize($key));
            $content .= "\n".'    \''.$key."'\t\t=> '".$key.'\',';
        }

        $this->write($content, $table.'.php');
    }

    public function __destruct()
    {
        if($this->i18n AND ! $this->settings['standalone'])
        {
            $this->write($this->i18n);
        }
    }

    protected function write($i18n, $file = 'en.php')
    {
        $i18n = '<?php defined(\'SYSPATH\') or die(\'No direct script access.\');'."\n\nreturn array (".$i18n."\n\n); // End en\n";
        $fp = fopen($this->repository.$file, 'w');
        fwrite($fp, $i18n);
        fclose($fp);
    }

} // End Codegen_I18n
