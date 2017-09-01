<?php

namespace TheMatrix;

class View
{
    const TEMPLATE_PATH = 'php/_templates/';
    
    /** @var string */
    private $template;
    
    /** @var array */
    private $variables;
    
    /**
     * @param string $template
     */
    public function __construct($template)
    {
        $this->template = $template;
    }
    
    /**
     * @param array $variables
     * @return string
     */
    public function render($variables)
    {
        ob_start();
        
        extract($variables);
        include($this->getTemplateFilename());
        $contents = ob_get_contents();
        
        ob_end_clean();
        
        return $contents;
    }
    
    private function getTemplateFilename()
    {
        return implode([
            GSPLUGINPATH,
            Plugin::ID,
            self::TEMPLATE_PATH,
            $this->template
        ], '/') . '.php';
    }
}