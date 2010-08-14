<?php defined('SYSPATH') or die('No direct script access.');

class Codegen_Model extends Codegen {

    public function __construct(array $config = NULL)
    {
        if($config === NULL) $config = parent::$config['model'];

        $repos = str_replace('_', DIRECTORY_SEPARATOR, $config['prefix']);
        $repos = parent::$config['repository'].'classes'.DIRECTORY_SEPARATOR.$repos.DIRECTORY_SEPARATOR;

        foreach($config['driver'] as $driver)
        {
            $driver = strtolower($driver);
            if(method_exists($this, $driver))
            {
                $driver = $repos.$driver;
                is_dir($driver) ? $this->empty_dir($driver) : mkdir($driver, 0755, TRUE);
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

    protected function model($table, $columns)
    {
        $table_old = $table;

        $key_id = key($columns);

        $table      = explode('_', $table);
        $table      = Inflector::singular(end($table));
        $uctalbe    = '_'.ucfirst($table);

        $content    = "<?php defined('SYSPATH') or die('No direct script access.');\n"
                        .strtr(parent::$config['license'], array(
                            '$package'  => $this->module,
                            '$year'     => date('Y'),
                            '$see'      => 'Model',
                        ))."\nclass {$this->settings['prefix']}{$uctalbe} extends Model {\n\n";

        $columns    = implode('\',\'', array_keys($columns));
        $content .= <<< CCC
    protected \$_id = '{$this->module}';

    public function get(\$$key_id = NULL)
    {
        return DB::select('$columns')
            ->from('$table_old')
            ->where('$key_id', '=', \$$key_id)
            ->execute(\$this->_db);
    }

    public function append(array \$params)
    {
        return DB::insert('$table_old', array_keys(\$params))
            ->set(array_values(\$params))
            ->execute(\$this->_db);
    }

    public function update(\$$key_id, array \$params)
    {
        return DB::update('$table_old')
            ->set(\$params)
            ->where('$key_id', '=', \$$key_id)
            ->execute(\$this->_db);
    }

    public function delete(\$$key_id)
    {
        return DB::delete('$table_old')
            ->where('$key_id', '=', \$$key_id)
            ->execute(\$this->_db);
    }

    public function lists(array \$params, \$page_from = 0, \$page_offset = 8, & \$total_rows = FALSE)
    {
        \$sql = 'FROM `$table_old` ';

        // Customize where from params
        //\$sql .= 'WHERE ... '

        // caculte the total rows
        if(\$total_rows === TRUE)
        {
            \$total_rows = \$this->_db->query(Database::SELECT,
                'SELECT COUNT(`$key_id`) num_rows '.\$sql
            )->get('num_rows');

            if(\$total_rows == 0)
                return array();
        }

        // Customize order by from params
        //\$sql .= 'ORDER BY ... '

        \$sql .= " LIMIT \$page_from, \$page_offset";

        return \$this->_db->query(Database::SELECT, 'SELECT * '.\$sql);
    }

} // END {$this->settings['prefix']}$uctalbe

CCC;
        $fp = fopen($this->repository.'model'.DIRECTORY_SEPARATOR.$table.'.php', 'w');
        fwrite($fp, $content);
        fclose($fp);
        
        return TRUE;
    }

    protected function hive($table, $columns)
    {
        //
    }

    protected function orm($table, $columns)
    {
        //
    }

    protected function jelly($table, $columns)
    {
        //
    }

    protected function sprig($table, $columns)
    {
        //
    }

} // End Codegen_Model
