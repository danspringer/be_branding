<?php

/** @var rex_addon $this */

// Daten wie Autor, Version, Subpages etc. sollten wenn möglich in der package.yml notiert werden.
// Sie können aber auch weiterhin hier gesetzt werden:
$this->setProperty('author', 'Daniel Springer, Medienfeuer');

// Die Datei sollte keine veränderbare Konfigurationen mehr enthalten, um die Updatefähigkeit zu erhalten.
// Stattdessen sollte dafür die rex_config verwendet werden (siehe install.php)

// Klassen und lang-Dateien müssen hier nicht mehr eingebunden werden, sie werden nun automatisch gefunden.

// Addonrechte (permissions) registieren
if (rex::isBackend() && is_object(rex::getUser())) {
    rex_perm::register('be_branding[branding]');
    rex_perm::register('be_branding[config]');
	rex_perm::register('be_branding[fe_favicon]');
}

if (!function_exists('hex2rgb')) {
        function hex2rgb($hex)
        {
            $hex = str_replace("#", "", $hex);
            
            if (strlen($hex) == 3) {
                $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
                $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
                $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
            } else {
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
            }
            $rgb = array(
                $r,
                $g,
                $b
            );
            //return implode(",", $rgb); // returns the rgb values separated by commas
            return $rgb; // returns an array with the rgb values
        }
    }
    
    if (!function_exists('rgb2hex')) {
        function rgb2hex($rgb)
        {
            
            $rgb = str_replace('rgba(', '', $rgb);
            $rgb = str_replace(')', '', $rgb);
            $rgb = str_replace(' ', '', $rgb);
            
            $rgbarr = explode(",", $rgb, 3);
            $hex    = '#' . sprintf("%02x%02x%02x", $rgbarr[0], $rgbarr[1], $rgbarr[2]);
            
            return $hex; // returns the hex value including the number sign (#)
        }
    }
    
    if (!function_exists('rgba2hex')) {
        function rgba2hex($rgba)
        {
            
            $rgba = str_replace('rgba(', '', $rgba);
            $rgba = str_replace(')', '', $rgba);
            $rgba = str_replace(' ', '', $rgba);
            
            $rgbarr = explode(",", $rgba, 3);
            $hex    = '#' . sprintf("%02x%02x%02x", $rgbarr[0], $rgbarr[1], $rgbarr[2]);
            
            return $hex; // returns the hex value including the number sign (#)
        }
    }
    
    if (!function_exists('makeFavIcon')) {
        function makeFavIcon($hexColor, $path)
        {
            
            $rgbColor        = hex2rgb($hexColor);
            $favIconOriginal = $path . 'favicon-original.png';
            $favIconNew      = rex_path::addonAssets('be_branding') . '/favicon/favicon-original-' . str_replace('#', '', $hexColor) . '.png';
            
            $im = imagecreatefrompng($favIconOriginal);
            imagealphablending($im, false);
            
            imagesavealpha($im, true);
            
            if ($im && imagefilter($im, IMG_FILTER_COLORIZE, $rgbColor[0], $rgbColor[1], $rgbColor[2], 0)) {
                imagepng($im, $favIconNew);
                imagedestroy($im);
            }
        }
    }
	
// Im Backend einbinden
if (rex::isBackend()) {    
    
    if ($this->getConfig('file')) {
        rex_extension::register('OUTPUT_FILTER', function(rex_extension_point $ep)
        {
            $suchmuster = array(
                '<h4 class="rex-nav-main-title">Hauptmenü</h4>'
            );
            $ersetzen   = array(
                '<a href="index.php?page=credits"><img src="index.php?rex_media_type=rex_mediapool_maximized&rex_media_file=' . $this->getConfig('file') . '" class="img-responsive" style="padding-top: 50px"/></a><h4 class="rex-nav-main-title">Hauptmenü</h4>'
            );
            $ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
        });
        
        // Wenn nciht eingeloggt und Backend Logo einbinden
        if (rex::isBackend() && !rex::getUser()) {
            rex_extension::register('OUTPUT_FILTER', function(rex_extension_point $ep)
            {
                $suchmuster = array(
                    '<section class="rex-page-main-inner" id="rex-js-page-main">'
                );
                $ersetzen   = array(
                    '<img src="index.php?rex_media_type=rex_mediapool_maximized&rex_media_file=' . $this->getConfig('file') . '" class="img-responsive center-block" style="padding: 10px 0px 15px 0px; width: 370px;"/></a><section class="rex-page-main-inner" id="rex-js-page-main">'
                );
                $ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
            });
        }
        
    }
    
    // Footer Credit im BE
    if ($this->getConfig('agency')) {
        rex_extension::register('OUTPUT_FILTER', function(rex_extension_point $ep)
        {
            $suchmuster = array(
                '<li><a href="https://www.yakamara.de" target="_blank" rel="noreferrer noopener">yakamara.de</a></li>'
            );
            // Wenn Agenturlogo gesetzt ist, auch im Footer anzeigen
            /*if($this->getConfig('file2')) {
            $ersetzen = array ('<li><img src="index.php?rex_media_type=rex_mediapool_preview&rex_media_file='.$this->getConfig('file2').'"></li><li><a href="index.php?page=credits">'.$this->getConfig('agency').'</a></li><li><a href="https://www.yakamara.de" target="_blank">yakamara.de</a></li>');
            } else {
            $ersetzen = array ('<li><a href="index.php?page=credits">'.$this->getConfig('agency').'</a></li><li><a href="https://www.yakamara.de" target="_blank">yakamara.de</a></li>');
            }*/
            $ersetzen   = array(
                '<li><a href="index.php?page=credits">' . $this->getConfig('agency') . '</a></li><li><a href="https://www.yakamara.de" target="_blank">yakamara.de</a></li>'
            );
            $ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
        });
    }
    
    // Text in den Credits
    
    if ($this->getConfig('agency')) {
        rex_extension::register('OUTPUT_FILTER', function(rex_extension_point $ep)
        {
            
            // Ausgabevariablen initialisieren
            $text_col = '';
            $img_col = '';
            // Text und Bild vorhanden
            if ($this->getConfig('textarea') != "" && $this->getConfig('file2') != "") {
                $html_text = $this->getConfig('textarea');
                if ($this->getConfig('editor') == 'markitup markdown') {
                    $html_text = markitup::parseOutput('markdown', $html_text);
                }
                if ($this->getConfig('editor') == 'markitup textile') {
                    $html_text = markitup::parseOutput('textile', $html_text);
                }
                $text_col = '<div class="col-md-6">' . $html_text . '</div>';
                $img_col  = '<div class="col-md-6"><p><img src="index.php?rex_media_type=rex_mediapool_maximized&rex_media_file=' . $this->getConfig('file2') . '" class="img-responsive" /></p></div>';
            }
            // Nur Text, kein Bild vorhanden
            if ($this->getConfig('textarea') != "" && $this->getConfig('file2') == "") {
                $html_text = $this->getConfig('textarea');
                if ($this->getConfig('editor') == 'markitup') {
                    $html_text = markitup::parseOutput('markdown', $html_text);
                }
                $text_col = '<div class="col-md-12">' . $html_text . '</div>';
                $img_col  = '';
            }
            // Nur Bild, kein Text vorhanden
            if ($this->getConfig('textarea') == "" && $this->getConfig('file2') != "") {
                $text_col = '';
                $img_col  = '<div class="col-md-12"><p><img src="index.php?rex_media_type=rex_mediapool_maximized&rex_media_file=' . $this->getConfig('file2') . '" class="img-responsive" /></p></div>';
            }
            
            $suchmuster = array(
                'Credits                    </h1>
    </div>
    </header>'
            );
            $ersetzen   = array(
                'Credits</h1></div></header>
            <section class="rex-page-section">
            <div class="panel panel-default">
            <header class="panel-heading"><div class="panel-title">' . $this->getConfig('agency') . '</div></header>
             <div class="panel-body">
                <div class="row">
                    ' . $text_col . '
                    ' . $img_col . '
                </div>
                </div>
                </section>
            '
            );
            $ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
        });
    }
    
    // Die style.css überall im Backend einbinden
    // Es wird eine Versionsangabe angehängt, damit nach einem neuen Release des Addons die Datei nicht
    // aus dem Browsercache verwendet, sondern frisch geladen wird
    rex_view::addCssFile($this->getAssetsUrl('css/style.css?v=' . $this->getVersion()));
    
    // Die script.js nur auf der Unterseite »config« des Addons einbinden
    /* if (rex_be_controller::getCurrentPagePart(2) == 'config') {
    rex_view::addJsFile($this->getAssetsUrl('js/script.js?v=' . $this->getVersion()));
    }*/
    
    // Wenn colorpicker aktiviert ist, checken ob ui_tools/jquery-minicolors bereits aktiviert ist, ansonsten selbst auf branding-Seite einbinden
    if (($this->getConfig('colorpicker') && !rex_addon::get('ui_tools')->getPlugin('jquery-minicolors')->isAvailable()) && rex_be_controller::getCurrentPagePart(2) == 'branding' || rex_be_controller::getCurrentPagePart(2) == 'fe_favicon') {
        rex_view::addCssFile($this->getAssetsUrl('jquery-minicolors/jquery.minicolors.css?v=' . $this->getVersion()));
        rex_view::addJsFile($this->getAssetsUrl('jquery-minicolors/jquery.minicolors.min.js?v=' . $this->getVersion()));
        rex_view::addJsFile($this->getAssetsUrl('jquery-minicolors/jquery-minicolors.js?v=' . $this->getVersion()));
    }
    
    
    // Favicon färben nur wenn Imagemagick verfügbar ist
    if ($this->getConfig('coloricon') == 1) {
        
        rex_extension::register('OUTPUT_FILTER', function(rex_extension_point $ep)
        {
			// Suchmuster 5.6.5 und darunter
			if (!rex_string::versionCompare(rex::getVersion(), '5.6.5', '>')) {
				
            $suchmuster = '<link rel="apple-touch-icon-precomposed" sizes="57x57" href="../assets/addons/be_style/plugins/redaxo/images/apple-touch-icon-57x57.png" />
    <link rel="apple-touch-icon-precomposed" sizes="60x60" href="../assets/addons/be_style/plugins/redaxo/images/apple-touch-icon-60x60.png" />
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/addons/be_style/plugins/redaxo/images/apple-touch-icon-72x72.png" />
    <link rel="apple-touch-icon-precomposed" sizes="76x76" href="../assets/addons/be_style/plugins/redaxo/images/apple-touch-icon-76x76.png" />
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/addons/be_style/plugins/redaxo/images/apple-touch-icon-114x114.png" />
    <link rel="apple-touch-icon-precomposed" sizes="120x120" href="../assets/addons/be_style/plugins/redaxo/images/apple-touch-icon-120x120.png" />
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/addons/be_style/plugins/redaxo/images/apple-touch-icon-144x144.png" />
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="../assets/addons/be_style/plugins/redaxo/images/apple-touch-icon-152x152.png" />
    <link rel="icon" type="image/png" href="../assets/addons/be_style/plugins/redaxo/images/favicon-16x16.png" sizes="16x16" />
    <link rel="icon" type="image/png" href="../assets/addons/be_style/plugins/redaxo/images/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="../assets/addons/be_style/plugins/redaxo/images/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/png" href="../assets/addons/be_style/plugins/redaxo/images/favicon-128x128.png" sizes="128x128" />
    <link rel="icon" type="image/png" href="../assets/addons/be_style/plugins/redaxo/images/favicon-196x196.png" sizes="196x196" />
    <meta name="msapplication-TileColor" content="#FFFFFF" />
    <meta name="msapplication-TileImage" content="../assets/addons/be_style/plugins/redaxo/images/mstile-144x144.png" />
    <meta name="msapplication-square70x70logo" content="../assets/addons/be_style/plugins/redaxo/images/mstile-70x70.png" />
    <meta name="msapplication-square150x150logo" content="../assets/addons/be_style/plugins/redaxo/images/mstile-150x150.png" />
    <meta name="msapplication-square310x310logo" content="../assets/addons/be_style/plugins/redaxo/images/mstile-310x310.png" />
    <meta name="msapplication-wide310x150logo" content="../assets/addons/be_style/plugins/redaxo/images/mstile-310x150.png" />';
			}
			
			// Suchmuster ab 5.7.0 (Favicons haben sich mit dieser Version geändert)
			if (rex_string::versionCompare(rex::getVersion(), '5.6.5', '>')) {
				
				$suchmuster = '    <link rel="apple-touch-icon" sizes="180x180" href="../assets/addons/be_style/plugins/redaxo/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/addons/be_style/plugins/redaxo/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/addons/be_style/plugins/redaxo/icons/favicon-16x16.png">
    <link rel="manifest" href="../assets/addons/be_style/plugins/redaxo/icons/site.webmanifest">
    <link rel="mask-icon" href="../assets/addons/be_style/plugins/redaxo/icons/safari-pinned-tab.svg" color="#4d99d3">
    <meta name="msapplication-TileColor" content="#2d89ef">
    <meta name="theme-color" content="#4d99d3">';
			}
            
            // Initiale Farbe für R setzen und als neues png abspeichern        
            makeFavIcon(rgba2hex($this->getConfig('color1')), rex_path::addon('be_branding') . 'vendor/favicon/');
            
            // v 1.0.5: Wenn Imagemagick verfügbar ist, nutze es. Ansosnten TODO: Rex Media Manager nutzen    
            if (class_exists('Imagick') === true) {
                
                // aus dem png dann die Favicons generieren
                //https://github.com/dmamontov/favicon reinholen
                require rex_path::addon('be_branding') . 'vendor/favicon/src/FaviconGenerator.php';
                $fav = new FaviconGenerator(rex_path::addonAssets('be_branding') . 'favicon/favicon-original-' . str_replace('#', '', rgba2hex($this->getConfig('color1'))) . '.png');
                
                $fav->setCompression(FaviconGenerator::COMPRESSION_VERYHIGH);
                
                $fav->setConfig(array(
                    'apple-background' => substr($this->getConfig('color1'), 1, 6),
                    'apple-margin' => 0,
                    'android-background' => substr($this->getConfig('color1'), 1, 6),
                    'android-margin' => 0,
                    'android-name' => rex::getServerName(),
                    'android-url' => rex::getServer(),
                    'android-orientation' => FaviconGenerator::ANDROID_PORTRAIT,
                    'ms-background' => substr($this->getConfig('color1'), 1, 6)
                ));
                
                $ersetzen = $fav->createAllAndGetHtml(rgba2hex($this->getConfig('color1')));
                $ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
            } // Ende if Imagemagick verfügbar
            
            
            // v 1.0.5: Wenn Imagemagick NICHT verfügbar ist, nutze Rex Media Manager (TODO)    
            if (class_exists('Imagick') === false) {
                // to do: Media-Types in DB anlegen für entsprechende Favicon-Formate, das Ursprungs-Favicon färben und per REX MEDIA MANAGER verkleinern und einbinden
                //$ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
            } // Ende Imagemagick false, dann REX MEDIA MANAGER
            
            
            //$ep->setSubject(str_replace($ersetzen, 'hex2rgb: '.hex2rgb('#FEC42A').'<br />rgba2hex: '.rgba2hex($this->getConfig('color1')).'<br />rgb2hex: '.rgb2hex('254,203,47'), $ep->getSubject()));
            
        }); // EoF rex_extension::register
        
    } // EoF if coloricon == 1
    
    
    // Border reinnehmen
    if ($this->getConfig('showborder') == 1 && $this->getConfig('border_text') != "") {
        rex_extension::register('OUTPUT_FILTER', function(rex_extension_point $ep)
        {
            $suchmuster = '<div id="rex-start-of-page" class="rex-page">';
            $ersetzen   = '<div style="font-size: 12px; background-color: ' . $this->getConfig('border_color') . '; color: #fff; width: 100%; font-weight: bold; text-align: center; padding: 8px 0 6px 0;">' . $this->getConfig('border_text') . '</div><div id="rex-start-of-page" class="rex-page">';
            $ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
        });
    } // EoF border
    
    //Farben
    if ($this->getConfig('color1') && $this->getConfig('color2')) {
        rex_extension::register('OUTPUT_FILTER', function(rex_extension_point $ep)
        {
            $suchmuster = '</head>';
            $ersetzen   = '<style>.rex-nav-top{background-color: ' . $this->getConfig('color1') . ' !important;} .rex-redaxo-logo path.rex-redaxo-logo-r, .rex-redaxo-logo path.rex-redaxo-logo-e, .rex-redaxo-logo path.rex-redaxo-logo-d, .rex-redaxo-logo path.rex-redaxo-logo-cms{fill: ' . $this->getConfig('color2') . ' !important;} .rex-nav-meta .text-muted {color: ' . $this->getConfig('color2') . ' !important;} .rex-redaxo-logo path.rex-redaxo-logo-a, .rex-redaxo-logo path.rex-redaxo-logo-x, .rex-redaxo-logo path.rex-redaxo-logo-o, .rex-redaxo-logo path.rex-redaxo-logo-reg{fill: #fff !important;} #rex-page-login .rex-page-main:before{border-color: ' . $this->getConfig('color1') . ' transparent transparent transparent !important; top: -1px !important;}</style></head>';
            $ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
        });
    } // EoF Farben    
    
    
} // EoF if rex Backend


//Frontend Favicon / fe_favicon 
if (class_exists('Imagick') === true && $this->getConfig('fe_favicon_filename') && !rex::isBackend()) { 
	rex_extension::register('OUTPUT_FILTER', function(rex_extension_point $ep)
     {	
         require rex_path::addon('be_branding') . 'vendor/favicon/src/FE_FaviconGenerator.php';
         $fav = new FaviconGenerator(rex_path::media().$this->getConfig('fe_favicon_filename'));
                
         $fav->setCompression(FaviconGenerator::COMPRESSION_VERYHIGH);
                
         $fav->setConfig(array(
            'apple-background' => substr($this->getConfig('fe_favicon_tilecolor'), 1, 6),
            'apple-margin' => 0,
            'android-background' => substr($this->getConfig('fe_favicon_tilecolor'), 1, 6),
            'android-margin' => 0,
            'android-name' => rex::getServerName(),
            'android-url' => rex::getServer(),
            'android-orientation' => FaviconGenerator::ANDROID_PORTRAIT,
            'ms-background' => substr($this->getConfig('fe_favicon_tilecolor'), 1, 6)
         ));
				
		 $suchmuster = 'REX_BE_BRANDING[type=fe_favicon]';
         $ersetzen   = $fav->createAllAndGetHtml(rgba2hex($this->getConfig('fe_favicon_tilecolor')));
         $ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
     });
} // EoF fe_favicon && !rex::isBackend()
else {
		rex_extension::register('OUTPUT_FILTER', function(rex_extension_point $ep)
		{
			if(rex::isFrontend()) {
				$suchmuster = 'REX_BE_BRANDING[type=fe_favicon]';
				$ersetzen   = '';
				$ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
			}
		});
	}