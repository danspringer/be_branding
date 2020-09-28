<?php
class fe_favicon {
	
	static function generate(){
		 /* Normales .ICO-File */
	 	 $ico = new PHP_ICO( rex_path::media().rex_addon::get('be_branding')->getConfig('fe_favicon_filename'), array(array(144,144)) );
		 $ico->save_ico( rex_path::addonAssets('be_branding','fe_favicon/favicon.ico') ); // Zum Verlinken in den Assets-Ordner
		 if(file_exists(rex_path::frontend('favicon.ico'))) {
		 	unlink(rex_path::frontend('favicon.ico'));
		 }
		 $ico->save_ico( rex_path::frontend('favicon.ico') ); // Zur Sicherheit noch ins Root
		 $ico_code = '<link rel="shortcut icon" type="image/x-icon" href="'.substr(rex::getServer(), 0, -1).rex_url::addonAssets('be_branding','fe_favicon/favicon.ico').'">';
		 /* Ende normales .ICO-File */
		 
		 /* Alle anderen*/
		 require rex_path::addon('be_branding') . 'vendor/favicon/src/FE_FaviconGenerator.php';
         $fav = new FE_FaviconGenerator(rex_path::media().rex_addon::get('be_branding')->getConfig('fe_favicon_filename'));
                
         $fav->setCompression(FE_FaviconGenerator::COMPRESSION_VERYHIGH);
                
         $fav->setConfig(array(
            'apple-background' => rgba2hex(rex_addon::get('be_branding')->getConfig('fe_favicon_tilecolor')),
            'apple-margin' => 0,
            'android-background' => rgba2hex(rex_addon::get('be_branding')->getConfig('fe_favicon_tilecolor')),
            'android-margin' => 0,
            'android-name' => rex::getServerName(),
            'android-url' => rex::getServer(),
            'android-orientation' => FE_FaviconGenerator::ANDROID_PORTRAIT,
            'ms-background' => rgba2hex(rex_addon::get('be_branding')->getConfig('fe_favicon_tilecolor'))
         ));
		 
		 $fav->createAll(rgba2hex(rex_addon::get('be_branding')->getConfig('fe_favicon_tilecolor')));
		 /* Ende alle anderen*/
		} // EoF generate()
		
		
	static function getHtml($hex_name){	
		$html = '';
		$addon = rex_addon::get('be_branding');
		$hex_name = substr($hex_name,1,6);
		
        foreach (array('16x16', '32x32', '96x96', '128x128') as $size) {
            if (file_exists(rex_path::addonAssets('be_branding','fe_favicon/favicon-'.$size.'-'.$hex_name.'.png'))) {
                $html .= "<link rel=\"icon\" type=\"image/png\" href=\"../assets/addons/be_branding/fe_favicon/favicon-{$size}-{$hex_name}.png\" sizes=\"{$size}\">\n";
            }
        }

        foreach (
            array('57x57', '60x60', '72x72', '76x76', '114x114', '120x120', '144x144', '152x152', '180x180')
            as $size
        ) {
			if (file_exists(rex_path::addonAssets('be_branding','fe_favicon/apple-touch-icon-'.$size.'-'.$hex_name.'.png'))) {
                $html .= "<link rel=\"apple-touch-icon\" sizes=\"{$size}\" href=\"../assets/addons/be_branding/fe_favicon/apple-touch-icon-{$size}-{$hex_name}.png\">\n";
            }
        }
		
		if (file_exists(rex_path::addonAssets('be_branding','fe_favicon/android-chrome-192x192-'.$hex_name.'.png'))) {
            $html .= "<link rel=\"icon\" type=\"image/png\" href=\"../assets/addons/be_branding/fe_favicon/android-chrome-192x192-{$hex_name}.png\" sizes=\"192x192\">\n";
        }
		if (file_exists(rex_path::addonAssets('be_branding','fe_favicon/manifest-'.$hex_name.'.png'))) {
            $html .= "<link rel=\"manifest\" href=\"../assets/addons/be_branding/fe_favicon/manifest-{$hex_name}.json\">\n";
        }
		if (file_exists(rex_path::addonAssets('be_branding','fe_favicon/mstile-144x144-'.$hex_name.'.png'))) {
            $html .= "<meta name=\"msapplication-TileImage\" content=\"../assets/addons/be_branding/fe_favicon/mstile-144x144-{$hex_name}.png\">\n";
        }
        if ($addon->getConfig('fe_favicon_tilecolor')) {
            $html .= "<meta name=\"msapplication-TileColor\" content=\"#{$hex_name}\">\n";
            $html .= "<meta name=\"theme-color\" content=\"#{$hex_name}\">\n";
        }

       return strlen($html) > 0 ? $html : false;
	} // EoF getHtml()
	
}
?>