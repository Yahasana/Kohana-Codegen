<?php defined('SYSPATH') or die('No direct script access.');

class Codegen_Theme extends Codegen {

    public function __construct(array $config = NULL)
    {
        if($config === NULL) $config = parent::$config['theme'];

        $repos = parent::$config['repository'].'theme'.DIRECTORY_SEPARATOR;

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

    protected function php($table, $columns)
    {
        //
    }

    protected function mustache($table, $columns)
    {
        $key_id     = key($columns);
        $table      = explode('_', $table);
        $table      = Inflector::singular(end($table));

        $content    = '{{#I18N}}';
        foreach($columns as $key => $column)
        {
            if($key == $key_id)
            {
                $content .= "\n".'<input type="hidden" name="'.$key.'" id="'.$key.'" value="{{'.$key.'}}" />';
                continue;
            }
            switch($column['data_type'])
            {
                case 'int':
                case 'int unsigned':
                case 'tinyint':
                case 'tinyint unsigned':
                    $content .= "\n".'<label for="'.$key.'">{{'.ucfirst(Inflector::humanize($key)).'}}</label><input type="text" name="'.$key.'" id="'.$key.'" value="{{'.$key.'}}" />';
                    break;
                case 'varchar':
                    if($column['character_maximum_length'] < 256)
                        $content .= "\n".'<label for="'.$key.'">{{'.ucfirst(Inflector::humanize($key)).'}}</label><input type="text" name="'.$key.'" id="'.$key.'" value="{{'.$key.'}}" />';
                    else
                        $content .= "\n".'<label for="'.$key.'">{{'.ucfirst(Inflector::humanize($key)).'}}</label><textarea name="'.$key.'" id="'.$key.'">{{'.$key.'}}</textarea>';
                    break;
                case 'text':
                    $content .= "\n".'<label for="'.$key.'">{{'.ucfirst(Inflector::humanize($key)).'}}</label><textarea name="'.$key.'" id="'.$key.'">{{'.$key.'}}</textarea>';
                    break;
                default:
                    $content .= "\n".'<label for="'.$key.'">{{'.ucfirst(Inflector::humanize($key)).'}}</label><input type="text" name="'.$key.'" id="'.$key.'" value="{{'.$key.'}}" />';
                    break;
            }
        }
        $fp = fopen($this->repository.'mustache'.DIRECTORY_SEPARATOR.$table.'.mustache', 'w');
        fwrite($fp, $content."\n{{/I18N}}");
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

} // End Codegen_Theme
