<?php defined('SYSPATH') or die('No direct script access.');

class Codegen_Model extends Codegen {

    public function __construct(array $config = NULL)
    {
        if($config === NULL) $config = parent::$config['model'];

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

    protected function model($table, $columns)
    {
        $data['database']  = $this->module;
        $data['table_old'] = $table;

        $data['key_id'] = $key_id = key($columns);

        $tables = explode('_', $table);
        $data['table'] = $table = Inflector::singular(end($tables));
        $file = $this->repository.'model'.DIRECTORY_SEPARATOR.$table.'.php';

        if(file_exists($file))
        {
            $file = $this->repository.'model'.DIRECTORY_SEPARATOR.prev($tables).$table.'.php';
        }

        $uctalbe    = '_'.ucfirst($table);

        $data['description'] = strtr(parent::$config['license'], array(
            '$package'  => $this->module,
            '$year'     => date('Y'),
            '$see'      => $this->settings['extends'],
        ));

        $data['class'] = $this->settings['directory'].$uctalbe;
        $data['extends'] = $this->settings['extends'];

        $data['datatime'] = '';

        $rule = $rule_insert = $rule_update = $comment = array();

        foreach($columns as $key => $column)
        {
            if(in_array($key, $this->settings['model']['excludes']) OR $key_id === $key) continue;

            if( ! $column['is_nullable'] AND ! isset($column['column_default']))
            {
                $rule[$key] = "->rule('$key', 'not_empty')";
                $rule_update[$key]['not_empty'] = NULL;
            }

            switch($column['data_type'])
            {
                case 'int':
                case 'int unsigned':
                case 'tinyint':
                case 'tinyint unsigned':
                    $rule_insert[$key]['range'] = $rule_update[$key]['range'] = array($column['min'], $column['max']);
                    if(preg_match('/\_date$/i', $key))
                    {
                        $data['datatime'] .= "
        if(isset(\$params['$key']) AND \$timetamp = strtotime(\$params['$key']))
            \$params['$key'] = \$timetamp;
        else
            unset(\$params['$key']);\n";
                    }
                    break;
                case 'decimal':
                case 'decimal unsigned':
                    $rule_insert[$key]['numeric'] = $rule_update[$key]['numeric'] = array($column['numeric_scale'], $column['numeric_precision']);
                    break;
                case 'double':
                case 'double unsigned':
                    $rule_insert[$key]['numeric'] = $rule_update[$key]['numeric'] = NULL;
                    break;
                case 'varchar':
                case 'text':
                case 'string':
                    $rule_insert[$key]['max_length'] = $rule_update[$key]['max_length'] = array($column['character_maximum_length']);
                    break;
                case 'enum':
                    $rule_insert[$key]['in_array'] = $rule_update[$key]['in_array'] = array($column['options']);
                    break;
                case 'timestamp':
                    break;
                default:
                    //$rule[$key] = "->rule('$key', '{$column['data_type']}', array())".print_r($column,true);
                    $rule[$key] = '';
                    break;
            }

            if($column['comment'])
                $comment[$key] = "$key: ".$column['comment'];
        }

        if($rule = array_filter($rule))
        {
            //$keys = array_keys($rule);
            //$last = end($keys);
            //if(strpos($rule[$last], '//'))
            //    $rule[$last] = str_replace("\t\t//", ";\t\t//", $rule[$last]);
            //else
            //    $rule[$last] .= ';';
        }

        $data['rules'] = $rule ? "\n\t\t\t".implode("\n\t\t\t", $rule) : '';

        $data['comment'] = $comment ? "\n\t *      ".implode("\n\t *      ", $comment)."\n\t *" : '';

        $data['columns'] = implode('\',\'', array_keys($columns));

        $data['rule_insert'] = '$rules = array_intersect_key('.preg_replace(
                array('#\n\s+array#m', '#\(\n\s+\'#', '#\(\n\s+\d\s=\>\s#', '#,\n\s+\d\s=\>\s#', '#,\n\s+\)#', '#\'(\d+)\'#', '#  #', '#\n#', '#,\),\)#'),
                array('array', '(\'', "(", ',', ",)", '$1', '    ', "\n        ", '))'),
                var_export($rule_insert, TRUE))
            .', $params);';

        $data['rule_update'] = '$rules = array_intersect_key('.preg_replace(
                array('#\n\s+array#m', '#\(\n\s+\'#', '#\(\n\s+\d\s=\>\s#', '#,\n\s+\d\s=\>\s#', '#,\n\s+\)#', '#\'(\d+)\'#', '#  #', '#\n#', '#,\),\)#', '#=\> NULL,\n\s+#'),
                array('array', '(\'', "(", ',', ",)", '$1', '    ', "\n        ", '))', '=> NULL, '),
                var_export($rule_update, TRUE))
            .', $params);';

        $fp = fopen($file, 'w');
        fwrite($fp, View::factory('codegen/model/template', $data)->render());
        fclose($fp);

        return TRUE;
    }

    protected function hive($table, $columns)
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
                            '$see'      => 'Hive',
                        ))."\nclass {$this->settings['directory']}{$uctalbe} extends Hive {\n\n";

        $rules = "\$meta->rules += array(\n";

        $fields = "\$meta->fields += array(\n";

        foreach($columns as $key => $column)
        {
            if(in_array($key, $this->settings['orm']['excludes'])) continue;

            $rule = array();

            if( ! $column['is_nullable'] AND ! isset($column['column_default']))
                $rule[] = "'not_empty' => TRUE, ";

            switch($column['data_type'])
            {
                case 'int':
                case 'int unsigned':
                    if($column['extra'] === 'auto_increment')
                        $fields .= "            '$key'\t=> new Hive_Field_Auto,\n";
                    else
                        $fields .= "            '$key'\t=> new Hive_Field_Integer,\n";

                    $rule[] = "'range' => array(".$column['min'].", ".$column['max']."), ";
                    break;
                case 'tinyint':
                case 'tinyint unsigned':
                    $fields .= "            '$key'\t=> new Hive_Field_Integer,\n";
                    $rule[] = "'range' => array(".$column['min'].", ".$column['max']."), ";
                    break;
                case 'varchar':
                case 'text':
                case 'string':
                    $rule[] = "'max_length' => array(".$column['character_maximum_length']."), ";
                    $fields .= "            '$key'\t=> new Hive_Field_String,\n";
                    break;
                case 'enum':
                    $rule[] = "'in_array' => array(array('".implode("', '", $column['options'])."')), ";
                    $fields .= "            '$key'\t=> new Hive_Field_Enum,\n";
                    break;
                case 'timestamp':
                    $fields .= "            '$key'\t=> new Hive_Field_Timestamp(array('auto_now_create' => TRUE,)),\n";
                    break;
            }

            if($rule)
            {
                if($column['comment'])
                    $rules .= "            // ".$column['comment']."\n";

                $rules .= "            '$key'\t=> array(".implode('', $rule)."),\n";
            }
        }

        $rules .= "        );";
        $fields .= "        );";

        $content .= <<< CCC
    public static function init()
    {
        \$meta = parent::init();

        // Name of the database to use
        \$meta->db = '{$this->module}';

        // Table name to use
        \$meta->table = '$table_old';

        {$fields}

        \$meta->sorting['id'] = 'ASC';

        {$rules}

        return \$meta;
    }

} // END {$this->settings['directory']}$uctalbe

CCC;
        $fp = fopen($this->repository.'hive'.DIRECTORY_SEPARATOR.$table.'.php', 'w');
        fwrite($fp, $content);
        fclose($fp);

        return TRUE;
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
                        ))."\nclass {$this->settings['directory']}{$uctalbe} extends ORM {\n\n";


        $foreign = $this->foreign_key($table_old);

        if(isset($foreign['belong_to']))
        {
            $belong_to = "protected \$_belongs_to = array(\n";
            foreach($foreign['belong_to'] as $t => $k)
            {
                $t      = explode('_', $t);
                $t      = Inflector::singular(end($t));
                $belong_to .= "        '$t' => array('foreign_key' => '$k'),\n";
            }
            $belong_to .= "    );\n\n";
        }
        else
        {
            $belong_to = '';
        }

        if(isset($foreign['has_many']))
        {
            if($belong_to)
                $has_many = "    protected \$_has_many = array(\n";
            else
                $has_many = "protected \$_has_many = array(\n";

            foreach($foreign['has_many'] as $t => $k)
            {
                $t      = explode('_', $t);
                $t      = Inflector::singular(end($t));
                $has_many .= "        '$t' => array('through' => '$k'),\n";
            }
            $has_many .= "    );\n\n";
        }
        else
        {
            $has_many = '';
        }

        if($belong_to OR $has_many)
            $rules = "    protected \$_rules = array(\n";
        else
            $rules = "protected \$_rules = array(\n";

        $labels = "protected \$_labels = array(\n";

        foreach($columns as $key => $column)
        {
            if(in_array($key, $this->settings['orm']['excludes'])) continue;

            $rule = array();

            if( ! $column['is_nullable'] AND ! isset($column['column_default']))
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

            if($rule)
            {
                if($column['comment'])
                    $rules .= "        // ".$column['comment']."\n";

                $rules .= "        '$key'\t=> array(".implode('', $rule)."),\n";
            }

            $labels .= "        '$key'\t=> '".ucfirst(Inflector::humanize($key))."',\n";
        }

        $rules .= "    );";
        $labels .= "    );";

        $content .= <<< CCC
    /**
     * Name of the database to use
     *
     * @access	protected
     * @var		string	\$_db default [default]
     */
    protected \$_db = '{$this->module}';

    /**
     * Table name to use
     *
     * @access	protected
     * @var		string	\$_table_name default [singular model name]
     */
    protected \$_table_name = '$table_old';

    /**
     * Column to use as primary key
     *
     * @access	protected
     * @var		string	\$_primary_key default [id]
     */
    protected \$_primary_key = '$key_id';

    protected \$_filters = array(TRUE => array('trim' => NULL));

    {$belong_to}{$has_many}{$rules}

    {$labels}

    public function lists(array \$params, & \$pagination = NULL, \$calc_total = TRUE)
    {
        \$pagination instanceOf Pagination OR \$pagination = new Pagination;

        // Customize where from params
        //\$this->where('', '', );

        // caculte the total rows
        if(\$calc_total === TRUE)
        {
            \$pagination->total_items = \$this->count_all();

            if(\$pagination->total_items === 0)
                return array();
        }

        // Customize order by from params
        if(isset(\$params['orderby']))
            \$this->order_by(key(\$params['orderby']), current(\$params['orderby']));

        return \$this->limit(\$pagination->items_per_page)
            ->offset(\$pagination->offset)
            ->find_all();
    }

} // END {$this->settings['directory']}$uctalbe

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
        $query = $db->query(Database::SELECT, 'SELECT * FROM information_schema.key_column_usage WHERE (TABLE_NAME=\''
            .$table.'\' OR REFERENCED_TABLE_NAME=\''.$table.'\') AND referenced_column_name IS NOT NULL');

        foreach($query as $row)
        {
            if($row['REFERENCED_TABLE_NAME'] === $table)
                $tables['has_many'][$row['TABLE_NAME']] = $row['REFERENCED_COLUMN_NAME'];
            else
                $tables['belong_to'][$row['REFERENCED_TABLE_NAME']] = $row['REFERENCED_COLUMN_NAME'];
        }
        return $tables;
    }

} // End Codegen_Model
