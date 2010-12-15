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
                {
                    $tmp .= "<span class='good'>&#9745; $driver</span><br />";
                }
                else
                {
                    $tmp .= "<span class='notyet'>&#9746; $driver</span><br />";
                }
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
        $table  = explode('_', $table);
        $table  = Inflector::singular(end($table));
        $dir    = $this->repository.__FUNCTION__.DIRECTORY_SEPARATOR.$this->module.'-'.$table.'-';
        $view   = new View(NULL, array('table' => $table, 'columns'=> $columns));

        foreach($this->settings['layout'] as $file => $flag)
        {
            if($flag === TRUE)
            {
                try
                {
                    $view->set_filename('codegen'.DIRECTORY_SEPARATOR.__FUNCTION__.DIRECTORY_SEPARATOR.$file);

                    $fp = fopen($dir.$file.'.php', 'w');
                    fwrite($fp, $view->render());
                    fclose($fp);
                }
                catch (Kohana_View_Exception $e)
                {
                    //
                }
            }
        }

        return TRUE;
    }

    protected function mustache($table, $columns)
    {
        $table  = explode('_', $table);
        $table  = Inflector::singular(end($table));

        $dir    = $this->repository.__FUNCTION__.DIRECTORY_SEPARATOR.$this->module.'-'.$table.'-';
        $view   = new View(NULL, array('table' => $table, 'columns'=> $columns));

        foreach($this->settings['layout'] as $file => $flag)
        {
            if($flag === TRUE)
            {
                try
                {
                    $view->set_filename('codegen'.DIRECTORY_SEPARATOR.__FUNCTION__.DIRECTORY_SEPARATOR.$file);
                    $fp = fopen($dir.$file.'.mustache', 'w');
                    fwrite($fp, $view->render());
                    fclose($fp);
                }
                catch (Kohana_View_Exception $e)
                {
                    //
                }
            }
        }

        return TRUE;
    }

    protected function twig($table, $columns)
    {
        $table  = explode('_', $table);
        $table  = Inflector::singular(end($table));

        $dir    = $this->repository.__FUNCTION__.DIRECTORY_SEPARATOR.$this->module.'-'.$table.'-';
        $view   = new View(NULL, array('table' => $table, 'columns'=> $columns));

        foreach($this->settings['layout'] as $file => $flag)
        {
            if($flag === TRUE)
            {
                try
                {
                    $view->set_filename('codegen'.DIRECTORY_SEPARATOR.__FUNCTION__.DIRECTORY_SEPARATOR.$file);

                    $fp = fopen($dir.$file.'.twig', 'w');
                    fwrite($fp, $view->render());
                    fclose($fp);
                }
                catch (Kohana_View_Exception $e)
                {
                    //
                }
            }
        }

        return TRUE;
    }

    protected function smarty($table, $columns)
    {
        //
    }

} // End Codegen_Theme
