<?php
class rex_var_be_branding extends rex_var
{
   protected function getOutput()
   {
       $content = '';

       if ($this->hasArg('type') && $this->getArg('type')) {
           switch ($this->getArg('type')) {
              
               case 'fe_favicon':
                   $content = fe_favicon::getHtml(be_branding::rgba2hex(rex_addon::get('be_branding')->getConfig('fe_favicon_tilecolor')));
                   break;

               default:
                   // keine Änderung
           }
       }

       return self::quote($content);
   }
}

?>