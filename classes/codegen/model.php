<?php defined('SYSPATH') or die('No direct script access.');

class Codegen_Model extends Codegen {

    public function __construct(array $config = NULL)
    {
        if($config === NULL) $config = parent::$config['model'];

        $repos = str_replace('_', DIRECTORY_SEPARATOR, $config['prefix']);
        $repos = parent::$config['repository'].'classes'.DIRECTORY_SEPARATOR.$repos.DIRECTORY_SEPARATOR;

        $config['prefix'] = str_replace(' ', '_', ucwords(str_replace('_', ' ', $config['prefix'])));

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
        $table_old = $table;

        $key_id = key($columns);

        $table      = explode('_', $table);
        $table      = Inflector::singular(end($table));
        $uctalbe    = '_'.ucfirst($table);

        $content    = "<?php defined('SYSPATH') or die('No direct script access.');\n"
                        .strtr(parent::$config['license'], array(
                            '$package'  => $this->module,
                            '$year'     => date('Y'),
                            '$see'      => 'ORM',
                        ))."\nclass {$this->settings['prefix']}{$uctalbe} extends ORM {\n\n";
        $rules = "protected \$_rules = array(\n";
        $labels = "protected \$_labels = array(\n";
        //var_export($columns);die;
        foreach($columns as $key => $column)
        {
            $rule = array();

            if( ! $column['is_nullable'])
                $rule[] = "'not_empty' => TRUE, ";

            switch($column['data_type'])
            {
                case 'int':
                case 'int unsigned':
                case 'tinyint':
                case 'tinyint unsigned':
                    $rule[] = "'range' => array(".$column['min'].", ".$column['max']."), ";
                    break;
                case 'varchar':
                case 'text':
                case 'string':
                    $rule[] = "'max_length' => array(".$column['character_maximum_length']."), ";
                    break;
                case 'enum':
                    break;
            }
            
            if($column['comment'])
                $rules .= "\t\t// $key - ".$column['comment']."\n";
                
            if($rule)
                $rules .= "\t\t'$key'\t=> array(".implode('', $rule)."),\n";

            $labels .= "\t\t'$key'\t=> '".ucfirst(Inflector::humanize($key))."',\n";
        }

        $rules .= "\t);\n";
        $labels .= "\t);\n";

        $content .= <<< CCC
    protected \$_db = '{$this->module}';

    protected \$_table_name = '$table_old';

    protected \$_primary_key = '$key_id';

    protected \$_filters = array(TRUE => array('trim' => NULL));

    $rules

    $labels

    public function lists(array \$params, \$page_from = 0, \$page_offset = 8, & \$total_rows = FALSE)
    {
        // Customize where from params
        //\$this->where('', '', );

        // caculte the total rows
        if(\$total_rows === TRUE)
        {
            \$total_rows = \$this->count_all();

            if(\$total_rows === 0)
                return array();
        }

        // Customize order by from params
        \$this->order_by('', 'ASC|DESC');

        return \$this->limit(\$page_from)->offset(\$page_offset)->find_all();
    }

} // END {$this->settings['prefix']}$uctalbe

CCC;
        $fp = fopen($this->repository.'orm'.DIRECTORY_SEPARATOR.$table.'.php', 'w');
        fwrite($fp, $content);
        fclose($fp);

        return TRUE;
    }

    protected function jelly($table, $columns)
    {
        //
    }

    protected function sprig($table, $columns)
    {
        //
    }

    protected function foreign_key($table)
    {
        $tables = array();
        $db = Database::instance($this->module);
        $query = $db->query(Database::SELECT, 'SELECT * FROM information_schema.key_column_usage WHERE table_name=\''.$table.'\' AND referenced_column_name IS NOT NULL');
        foreach($query as $row)
        {
            $tables[$row['REFERENCED_TABLE_NAME']][] = $row['REFERENCED_COLUMN_NAME'];
        }
        return $tables;
    }

} // End Codegen_Model
