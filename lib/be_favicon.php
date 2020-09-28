<?php
class be_favicon {
	
	static function getHtml($hex_name){	
		$html = '';
		$addon = rex_addon::get('be_branding');
		$hex_name = substr($hex_name,1,6);
		
        foreach (array('16x16', '32x32', '96x96', '128x128') as $size) {
            if (file_exists(rex_path::addonAssets('be_branding','favicon/favicon-'.$size.'-'.$hex_name.'.png'))) {
                $html .= "<link rel=\"icon\" type=\"image/png\" href=\"../assets/addons/be_branding/favicon/favicon-{$size}-{$hex_name}.png\" sizes=\"{$size}\">\n";
            }
        }

        foreach (
            array('57x57', '60x60', '72x72', '76x76', '114x114', '120x120', '144x144', '152x152', '180x180')
            as $size
        ) {
			if (file_exists(rex_path::addonAssets('be_branding','favicon/apple-touch-icon-'.$size.'-'.$hex_name.'.png'))) {
                $html .= "<link rel=\"apple-touch-icon\" sizes=\"{$size}\" href=\"../assets/addons/be_branding/favicon/apple-touch-icon-{$size}-{$hex_name}.png\">\n";
            }
        }
		
		if (file_exists(rex_path::addonAssets('be_branding','favicon/android-chrome-192x192-'.$hex_name.'.png'))) {
            $html .= "<link rel=\"icon\" type=\"image/png\" href=\"../assets/addons/be_branding/favicon/android-chrome-192x192-{$hex_name}.png\" sizes=\"192x192\">\n";
        }
		if (file_exists(rex_path::addonAssets('be_branding','favicon/manifest-'.$hex_name.'.png'))) {
            $html .= "<link rel=\"manifest\" href=\"../assets/addons/be_branding/favicon/manifest-{$hex_name}.json\">\n";
        }
		if (file_exists(rex_path::addonAssets('be_branding','favicon/mstile-144x144-'.$hex_name.'.png'))) {
            $html .= "<meta name=\"msapplication-TileImage\" content=\"../assets/addons/be_branding/favicon/mstile-144x144-{$hex_name}.png\">\n";
        }
        if ($addon->getConfig('fe_favicon_tilecolor')) {
            $html .= "<meta name=\"msapplication-TileColor\" content=\"#{$hex_name}\">\n";
            $html .= "<meta name=\"theme-color\" content=\"#{$hex_name}\">\n";
        }

       return strlen($html) > 0 ? $html : false;
	} // EoF getHtml()
	
}
?>