<?php

namespace Boyhagemann\Crud\Manager;

use Zend\Code\Reflection\FileReflection;

class Scanner
{
    public function scanForControllers()
    {
        $subclass = 'Boyhagemann\Crud\CrudController';
        $controllers = array();
        $files = $this->globFolders('*Controller.php', array('../app/controllers', '../workbench'));
        
        foreach($files as $filename) {
            
            require_once $filename;
            $file = new FileReflection($filename);
            $class = $file->getClass();
            
            if($class->isSubclassOf($subclass)) {
                $key = str_replace('\\', '/', $class->getName());
                $controllers[$key] = $class;
            }
        }
        
        return $controllers;
    }
    
    public function scanForJson()
    {        
        return $scanned;
    }
    
    /**
     * 
     * @param type $pattern
     * @param array $folders
     * @return array
     */
    public function globFolders($pattern, Array $folders)
    {
        $files = array();
        foreach($folders as $folder) {
            $files = array_merge($this->glob($pattern, $folder), $files);
        }
        return $files;
    }

    /**
     * 
     * @param type $pattern
     * @param type $folder
     * @return array
     */
    protected function glob($pattern, $folder)
    {        
        $files = \File::glob($folder . '/' . $pattern, GLOB_BRACE);
                
        foreach(\File::directories($folder) as $sub) {
            $files = array_merge($this->glob($pattern, $sub), $files);
        }
        
        return $files;
    }

}