<?php defined('SYSPATH') or die('No direct script access.');

abstract class Codegen {

	public static $config = NULL;

	public static function factory($type)
	{
		if ( ! Codegen::$config)
		{
			$config = Kohana::config('codegen');

			isset($_GET['m']) AND $config['module'] = $_GET['m'];

			$config['repository'] .= DIRECTORY_SEPARATOR.$config['module'].DIRECTORY_SEPARATOR;

            is_dir($config['repository']) OR mkdir($config['repository'], 755, TRUE);

			Codegen::$config = $config;
		}

        if(empty(Codegen::$config[$type]['on'])) return NULL;

        // Set the class name
        $class = 'Codegen_'.$type;

		return new $class(Codegen::$config[$type]);
	}

    public static function empty_dir($dir, $ext = EXT)
    {
        try
        {
            foreach(glob("*.$ext") as $file)
            {
                unlink($dir.DIRECTORY_SEPARATOR.$file);
            }
        }
        catch(Exception $e)
        {
            throw new Kohana_Exception('Can not empty the directory :dir', array(':dir' => $dir));
        }
    }

    abstract public function render($table, $columns);

} // End Codegen
