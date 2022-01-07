<?php
class fe_favicon {
	
	static function generate(){
        require rex_path::addon('be_branding') . 'vendor/favicon/src/FE_FaviconGenerator.php';
        # Für jede Domain
        foreach (rex_yrewrite::getDomains() as $domain) {
            $domainId = $domain->getId();
            // Wenn ein Icon in den Einstellungen definiert ist
            if(rex_addon::get('be_branding')->getConfig('fe_favicon_filename_'.$domainId)) {
                /* Normales .ICO-File */
                $ico = new PHP_ICO(rex_path::media() . rex_addon::get('be_branding')->getConfig('fe_favicon_filename_' . $domainId), array(array(144, 144)));
                $ico->save_ico(rex_path::addonAssets('be_branding', 'fe_favicon/favicon--' . $domainId . '.ico')); // Zum Verlinken in den Assets-Ordner
                if (file_exists(rex_path::frontend('favicon--' . $domainId . '.ico'))) {
                    unlink(rex_path::frontend('favicon--' . $domainId . '.ico'));
                }
                $ico->save_ico(rex_path::frontend('favicon--' . $domainId . '.ico')); // Zur Sicherheit noch ins Root
                $ico_code = '<link rel="shortcut icon" type="image/x-icon" href="' . substr(rex::getServer(), 0, -1) . rex_url::addonAssets('be_branding', 'fe_favicon/favicon--' . $domainId . '.ico') . '">';
                /* Ende normales .ICO-File */

                /* Alle anderen*/
                $fav = new FE_FaviconGenerator(rex_path::media() . rex_addon::get('be_branding')->getConfig('fe_favicon_filename_' . $domainId));

                $fav->setCompression(FE_FaviconGenerator::COMPRESSION_VERYHIGH);

                $fav->setConfig(array(
                    'apple-background' => be_branding::rgba2hex(rex_addon::get('be_branding')->getConfig('fe_favicon_tilecolor_' . $domainId)),
                    'apple-margin' => 0,
                    'android-background' => be_branding::rgba2hex(rex_addon::get('be_branding')->getConfig('fe_favicon_tilecolor_' . $domainId)),
                    'android-margin' => 0,
                    'android-name' => rex::getServerName(),
                    'android-url' => rex::getServer(),
                    'android-orientation' => FE_FaviconGenerator::ANDROID_PORTRAIT,
                    'ms-background' => be_branding::rgba2hex(rex_addon::get('be_branding')->getConfig('fe_favicon_tilecolor_' . $domainId))
                ));

                $fav->createAll(be_branding::rgba2hex(rex_addon::get('be_branding')->getConfig('fe_favicon_tilecolor_' . $domainId)), $domainId);
                /* Ende alle anderen*/
            } // Ende if
        } // EoF foreach
	} // EoF generate()
		
		
	static function getHtml($hex_name, $domainId){
            $html = '';
            $addon = rex_addon::get('be_branding');
            $hex_name = substr($hex_name, 1, 6);

            foreach (array('16x16', '32x32', '96x96', '128x128') as $size) {
                if (file_exists(rex_path::addonAssets('be_branding', 'fe_favicon/favicon-' . $size . '-' . $hex_name . '--'.$domainId.'.png'))) {
                    $html .= "<link rel=\"icon\" type=\"image/png\" href=\"" . rex_yrewrite::getCurrentDomain()->getUrl() . "assets/addons/be_branding/fe_favicon/favicon-{$size}-{$hex_name}--{$domainId}.png\" sizes=\"{$size}\">\n";
                }
            }

            foreach (
                array('57x57', '60x60', '72x72', '76x76', '114x114', '120x120', '144x144', '152x152', '180x180')
                as $size
            ) {
                if (file_exists(rex_path::addonAssets('be_branding', 'fe_favicon/apple-touch-icon-' . $size . '-' . $hex_name . '--'.$domainId.'.png'))) {
                    $html .= "<link rel=\"apple-touch-icon\" sizes=\"{$size}\" href=\"" . rex_yrewrite::getCurrentDomain()->getUrl() . "assets/addons/be_branding/fe_favicon/apple-touch-icon-{$size}-{$hex_name}--{$domainId}.png\">\n";
                }
            }
            if (file_exists(rex_path::addonAssets('be_branding', 'fe_favicon/favicon--'.$domainId.'.ico'))) {
                $html .= '<link rel="shortcut icon" type="image/x-icon" href="' . substr(rex_yrewrite::getCurrentDomain()->getUrl(), 0, -1) . rex_url::addonAssets('be_branding', 'fe_favicon/favicon--'.$domainId.'.ico') . '">';
            }
            if (file_exists(rex_path::addonAssets('be_branding', 'fe_favicon/android-chrome-192x192-' . $hex_name . '--'.$domainId.'.png'))) {
                $html .= "<link rel=\"icon\" type=\"image/png\" href=\"" . rex_yrewrite::getCurrentDomain()->getUrl() . "assets/addons/be_branding/fe_favicon/android-chrome-192x192-{$hex_name}--{$domainId}.png\" sizes=\"192x192\">\n";
            }
            if (file_exists(rex_path::addonAssets('be_branding', 'fe_favicon/manifest-' . $hex_name . '--'.$domainId.'.png'))) {
                $html .= "<link rel=\"manifest\" href=\"" . rex_yrewrite::getCurrentDomain()->getUrl() . "assets/addons/be_branding/fe_favicon/manifest-{$hex_name}--{$domainId}}.json\">\n";
            }
            if (file_exists(rex_path::addonAssets('be_branding', 'fe_favicon/mstile-144x144-' . $hex_name . '--'.$domainId.'.png'))) {
                $html .= "<meta name=\"msapplication-TileImage\" content=\"" . rex_yrewrite::getCurrentDomain()->getUrl() . "assets/addons/be_branding/fe_favicon/mstile-144x144-{$hex_name}--{$domainId}.png\">\n";
            }
            if ($addon->getConfig('fe_favicon_tilecolor_'.$domainId)) {
                $html .= "<meta name=\"msapplication-TileColor\" content=\"#{$hex_name}\">\n";
                $html .= "<meta name=\"theme-color\" content=\"#{$hex_name}\">\n";
            }

       return strlen($html) > 0 ? $html : false;
	} // EoF getHtml()
	
}
?>