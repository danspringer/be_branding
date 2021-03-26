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


if (!function_exists('checkExtension')) {
	function checkExtension($filename){
		
			$img_file_parts = pathinfo($filename);
			//print_r($img_file_parts);
			$be_logo = '/media/'.$filename;
			
			$ext = $img_file_parts['extension'];
			//echo $ext;
			
			if ($ext == "jpg" || $ext == "jpeg" || $ext == "png") {
				$be_logo = 'index.php?rex_media_type=rex_mediapool_maximized&rex_media_file=' . $filename;
				return $be_logo;
				}
			if ($ext === "svg" ) {
				$be_logo = 'index.php?rex_media_type=ORIGINAL_' . hash("md5", $filename) . '&rex_media_file=' . $filename;
				return $be_logo;
				}
		}// EoF
}

	
// Im Backend
if (rex::isBackend()) {    
    
    if ($this->getConfig('file')) {
        // Wenn nicht eingeloggt und Backend Logo einbinden
		// Login-Screen hat kein Fragment, deshalb per Output-Filter
        if (rex::isBackend() && !rex::getUser()) {
            rex_extension::register('OUTPUT_FILTER', function(rex_extension_point $ep)
            {
				$suchmuster = array('<section class="rex-page-main-inner" id="rex-js-page-main">');
				$ersetzen   = array('<img src="'.checkExtension($this->getConfig('file')).'" class="img-responsive center-block" style="padding: 10px 0px 15px 0px; width: 370px;"/></a><section class="rex-page-main-inner" id="rex-js-page-main">');
				$ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
				}); 
			} // EoF if rex::isBackend() && !rex::getUser()
		} // EoF getConfig()
    
    
    
		// Text in den Credits per Output-Filter, da kein Fragment
		  if ($this->getConfig('agency') && rex_be_controller::getCurrentPage() == 'credits' && rex_get('license', 'string', 'NONE') == 'NONE') {
			rex_extension::register('PAGE_TITLE_SHOWN', function (rex_extension_point $ep) {
			  // Ausgabevariablen initialisieren
			  $text_col = '';
			  $img_col = '';
			  // Text und Bild vorhanden
			  if ($this->getConfig('textarea') != "" && $this->getConfig('file2') != "") {
				$html_text = $this->getConfig('textarea');
				if ($this->getConfig('editor') == 'markitup markdown') {
				  $html_text = markitup::parseOutput('markdown', $html_text);
				} elseif ($this->getConfig('editor') == 'markitup textile') {
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
		
			  $html_append = '
				<section class="rex-page-section">
				  <div class="panel panel-default">
					<header class="panel-heading"><div class="panel-title">' . $this->getConfig('agency') . '</div></header>
					<div class="panel-body">
					<div class="row">
					  ' . $text_col . '
					  ' . $img_col . '
					</div>
				  </div>
				</section>';
			  $ep->setSubject($html_append);
			}, rex_extension::EARLY);
		  }
		
		// Wenn colorpicker aktiviert ist, checken ob ui_tools/jquery-minicolors bereits aktiviert ist, ansonsten selbst auf branding-Seite einbinden
		if (($this->getConfig('colorpicker') && !rex_addon::get('ui_tools')->getPlugin('jquery-minicolors')->isAvailable()) && rex_be_controller::getCurrentPagePart(2) == 'branding' || rex_be_controller::getCurrentPagePart(2) == 'fe_favicon') {
			rex_view::addCssFile($this->getAssetsUrl('jquery-minicolors/jquery.minicolors.css?v=' . $this->getVersion()));
			rex_view::addJsFile($this->getAssetsUrl('jquery-minicolors/jquery.minicolors.min.js?v=' . $this->getVersion()));
			rex_view::addJsFile($this->getAssetsUrl('jquery-minicolors/jquery-minicolors.js?v=' . $this->getVersion()));
		}
		
		
		// BE-Favicon nur färben wenn Imagemagick verfügbar ist
		if ($this->getConfig('coloricon') == 1 && class_exists('Imagick') === true ) {
			
			rex_extension::register('OUTPUT_FILTER', function(rex_extension_point $ep)
			{
				// Initiale Farbe für R setzen und als neues png abspeichern        
				makeFavIcon(rgba2hex($this->getConfig('color1')), rex_path::addon('be_branding') . 'vendor/favicon/');
				
					// aus dem png dann die Favicons generieren
					//https://github.com/dmamontov/favicon reinholen
					require rex_path::addon('be_branding') . 'vendor/favicon/src/BE_FaviconGenerator.php';
					$fav = new BE_FaviconGenerator(rex_path::addonAssets('be_branding') . 'favicon/favicon-original-' . str_replace('#', '', rgba2hex($this->getConfig('color1'))) . '.png');
					
					$fav->setCompression(BE_FaviconGenerator::COMPRESSION_VERYHIGH);
					
					$fav->setConfig(array(
						'apple-background' => substr($this->getConfig('color1'), 1, 6),
						'apple-margin' => 0,
						'android-background' => substr($this->getConfig('color1'), 1, 6),
						'android-margin' => 0,
						'android-name' => rex::getServerName(),
						'android-url' => rex::getServer(),
						'android-orientation' => BE_FaviconGenerator::ANDROID_PORTRAIT,
						'ms-background' => substr($this->getConfig('color1'), 1, 6)
					));
					
					$ersetzen = $fav->createAllAndGetHtml(rgba2hex($this->getConfig('color1')));
				
			}); // EoF rex_extension::register
			
		} // EoF if coloricon == 1
	  
	  
		//Farben
		if ($this->getConfig('color1') && $this->getConfig('color2')) {
			rex_extension::register('OUTPUT_FILTER', function(rex_extension_point $ep)
			{
				$suchmuster = '</head>';
				$ersetzen   = '<style>
				.rex-nav-top{background-color: ' . $this->getConfig('color1') . ' !important;}
				
				.rex-redaxo-logo path.rex-redaxo-logo-r,
				.rex-redaxo-logo path.rex-redaxo-logo-e,
				.rex-redaxo-logo path.rex-redaxo-logo-d,
				.rex-redaxo-logo path.rex-redaxo-logo-cms{fill: ' . $this->getConfig('color2') . ' !important;}
				
				.rex-nav-meta .text-muted {color: ' . $this->getConfig('color2') . ' !important;}
				
				.rex-redaxo-logo path.rex-redaxo-logo-a,
				.rex-redaxo-logo path.rex-redaxo-logo-x,
				.rex-redaxo-logo path.rex-redaxo-logo-o,
				.rex-redaxo-logo path.rex-redaxo-logo-reg{fill: #fff !important;}';
				
				# Bissle Farbe im Login Screen ab REX 5.12
				if(rex_string::versionCompare(rex::getVersion(), '5.12', '>=')) {
					
					$panel_bg = rex_addon::get('be_branding')->getConfig('color1');
					if(rex_addon::get('be_branding')->getConfig('color1')) {
						$panel_bg = str_replace(', 1)', ', 0.8)', $panel_bg);
						}
						
					$ersetzen .= '
					#rex-page-login {
						background: ' . $this->getConfig('color1') . ' !important;
					}

					#rex-form-login .rex-redaxo-logo path.rex-redaxo-logo-r,
					#rex-form-login .rex-redaxo-logo path.rex-redaxo-logo-e,
					#rex-form-login .rex-redaxo-logo path.rex-redaxo-logo-d,
					#rex-form-login .rex-redaxo-logo path.rex-redaxo-logo-cms{fill: ' . $this->getConfig('color2') . ' !important;}
					
					#rex-page-login .panel-default {
					background-color: ' . $panel_bg . ';
					border: 0;
					color: ' . $this->getConfig('color2') . ';
					border-radius: 5px;}
					
					#rex-page-login .form-control {
						background-color: '.$panel_bg.' !important;
						color: #fff;
						border: none !important;
						box-shadow: none;
					}
					
					#rex-page-login .input-group-addon,
					#rex-page-login .input-group-btn .btn-view {
						background-color: '.$panel_bg.' !important;
						color: ' . $this->getConfig('color2') . ' !important;
						border: none !important;
					}
					
					#rex-page-login .btn-primary {
						background-color: ' . $this->getConfig('color2') . ' !important;
						color: ' . $this->getConfig('color1') . ' !important;
						border-color:' . $this->getConfig('color1') . ' !important;
					}
					';
					} // End Rex 5.12
				
				$ersetzen .= '#rex-page-login .rex-page-main:before{border-color: ' . $this->getConfig('color1') . ' transparent transparent transparent !important; top: -1px !important;}
				</style></head>';
				$ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
			});
		} // EoF Farben    
    
    
} // EoF if rex Backend


//Frontend Favicon / fe_favicon 
if (class_exists('Imagick') === true && $this->getConfig('fe_favicon_filename') && !rex::isBackend()) { 
	rex_extension::register('OUTPUT_FILTER', function(rex_extension_point $ep)
     {	
	 	// Nur neu generieren, wenn nicht existent
	 	 /*if(!file_exists(rex_path::addonAssets('be_branding','fe_favicon/favicon-16x16-'.substr(rgba2hex($this->getConfig('fe_favicon_tilecolor')),1,6).'.png'))) {
         	fe_favicon::generate();
	 		}*/
		 $suchmuster = 'REX_BE_BRANDING[type=fe_favicon]';
		 $ersetzen	= fe_favicon::getHtml(rgba2hex($this->getConfig('fe_favicon_tilecolor')));
         $ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
     });
} // EoF fe_favicon && !rex::isBackend()
else {
		// Var REX_BE_BRANDING durch nichts ersetzen, wenn Imageick nicht verfügbar oder Backend
		rex_extension::register('OUTPUT_FILTER', function(rex_extension_point $ep)
		{
			if(!rex::isBackend()) {
				$suchmuster = 'REX_BE_BRANDING[type=fe_favicon]';
				$ersetzen   = '';
				$ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
			}
		});
	}