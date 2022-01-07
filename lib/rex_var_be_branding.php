<?php
class rex_var_be_branding extends rex_var
{
   protected function getOutput()
   {
       if ($this->hasArg('type') && $this->getArg('type') && $this->hasArg('domainid') && $this->getArg('domainid')) {
           $domainId = $this->getArg('domainid');
           $sql = rex_sql::factory();
           $sql->setDebug(false);
           switch ($this->getArg('type')) {
               case 'fe_favicon':
                   #$content = fe_favicon::getHtml(be_branding::rgba2hex(rex_addon::get('be_branding')->getConfig('fe_favicon_tilecolor_'.$domainId)), $domainId);
                   $content = '';
                   break;

               default:
                   // keine Änderung
           }
       }
       return self::quote($content);
   }
}

?>