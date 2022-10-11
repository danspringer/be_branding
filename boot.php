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


// Im Backend
if (rex::isBackend()) {
    

    if ($this->getConfig('file')) {
        // Wenn nicht eingeloggt und Backend Logo einbinden
        // Login-Screen hat kein Fragment für < R5.12, deshalb per Output-Filter
        if (!rex::getUser()) {
            rex_extension::register('OUTPUT_FILTER', function (rex_extension_point $ep) {
                $suchmuster = array('<section class="rex-page-main-inner" id="rex-js-page-main">');
                $ersetzen = array('<img src="' . be_branding::checkExtension($this->getConfig('file'. be_branding::getCurrentBeDomainId(true) )) . '" class="img-responsive center-block" style="padding: 10px 0px 15px 0px; width: 370px;"/></a><section class="rex-page-main-inner" id="rex-js-page-main">');
                $ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
            }, rex_extension::LATE);
        } // EoF if rex::isBackend() && !rex::getUser()
    } // EoF getConfig()


    // Text in den Credits per Output-Filter, da kein Fragment
    if ($this->getConfig('agency') && rex_be_controller::getCurrentPage() == 'credits' && rex_get('license', 'string', 'NONE') == 'NONE') {
        rex_extension::register('PAGE_TITLE_SHOWN', function (rex_extension_point $ep) {

            // Ausgabevariablen initialisieren
            $text_col = '';
            $img_col = '';

            // SVGs abfangen
            if ($this->getConfig('file2')) {
                $imgSrc = be_branding::checkExtension($this->getConfig('file2'. be_branding::getCurrentBeDomainId(true)));
            }

            // Text und Bild vorhanden
            if ($this->getConfig('textarea') != "" && $this->getConfig('file2'. be_branding::getCurrentBeDomainId(true)) != "") {
                $html_text = $this->getConfig('textarea'. be_branding::getCurrentBeDomainId(true));
                if ($this->getConfig('editor') == 'markitup markdown') {
                    $html_text = markitup::parseOutput('markdown', $html_text);
                } elseif ($this->getConfig('editor') == 'markitup textile') {
                    $html_text = markitup::parseOutput('textile', $html_text);
                }
                $text_col = '<div class="col-md-6">' . $html_text . '</div>';
                $img_col = '<div class="col-md-6"><p><img src="' . $imgSrc . '" class="img-responsive" /></p></div>';
            }
            // Nur Text, kein Bild vorhanden
            if ($this->getConfig('textarea') != "" && $this->getConfig('file2'. be_branding::getCurrentBeDomainId(true)) == "") {
                $html_text = $this->getConfig('textarea');
                if ($this->getConfig('editor') == 'markitup') {
                    $html_text = markitup::parseOutput('markdown', $html_text);
                }
                $text_col = '<div class="col-md-12">' . $html_text . '</div>';
                $img_col = '';
            }
            // Nur Bild, kein Text vorhanden
            if ($this->getConfig('textarea') == "" && $this->getConfig('file2'. be_branding::getCurrentBeDomainId(true)) != "") {
                $text_col = '';
                $img_col = '<div class="col-md-12"><p><img src="' . $imgSrc . '" class="img-responsive" /></p></div>';
            }

            $html_append = '
				<section class="rex-page-section">
				  <div class="panel panel-default">
					<header class="panel-heading"><div class="panel-title">' . $this->getConfig('agency'. be_branding::getCurrentBeDomainId(true)) . '</div></header>
					<div class="panel-body">
					<div class="row">
					  ' . $text_col . '
					  ' . $img_col . '
					</div>
				  </div>
				</section>';
            $ep->setSubject($html_append);
        }, rex_extension::LATE);
    }

    // Wenn colorpicker aktiviert ist, checken ob ui_tools/jquery-minicolors bereits aktiviert ist, ansonsten selbst auf branding-Seite einbinden
    if (($this->getConfig('colorpicker') && !rex_addon::get('ui_tools')->getPlugin('jquery-minicolors')->isAvailable()) && rex_be_controller::getCurrentPagePart(2) == 'branding' || rex_be_controller::getCurrentPagePart(2) == 'fe_favicon') {
        rex_view::addCssFile($this->getAssetsUrl('jquery-minicolors/jquery.minicolors.css?v=' . $this->getVersion()));
        rex_view::addJsFile($this->getAssetsUrl('jquery-minicolors/jquery.minicolors.min.js?v=' . $this->getVersion()));
        rex_view::addJsFile($this->getAssetsUrl('jquery-minicolors/jquery-minicolors.js?v=' . $this->getVersion()));
    }

    //Farben für das REDAXO-Logo anpassen
    if ($this->getConfig('color1'. be_branding::getCurrentBeDomainId(true)) && $this->getConfig('color2'. be_branding::getCurrentBeDomainId(true))) {
        rex_extension::register('OUTPUT_FILTER', function (rex_extension_point $ep) {
            $suchmuster = '</head>';
            $ersetzen = '<style>
				.rex-nav-top .navbar{
				    background-color: ' . $this->getConfig('color1'. be_branding::getCurrentBeDomainId(true)) . ' !important;
				}
				
				.rex-redaxo-logo path.rex-redaxo-logo-r,
				.rex-redaxo-logo path.rex-redaxo-logo-e,
				.rex-redaxo-logo path.rex-redaxo-logo-d,
				.rex-redaxo-logo path.rex-redaxo-logo-cms{fill: ' . $this->getConfig('color2'. be_branding::getCurrentBeDomainId(true)) . ' !important;}
				
				.rex-nav-meta .text-muted {color: ' . $this->getConfig('color2'. be_branding::getCurrentBeDomainId(true)) . ' !important;}
				
				.rex-redaxo-logo path.rex-redaxo-logo-a,
				.rex-redaxo-logo path.rex-redaxo-logo-x,
				.rex-redaxo-logo path.rex-redaxo-logo-o,
				.rex-redaxo-logo path.rex-redaxo-logo-reg{
				    fill: #fff !important;
				}';
            // Wenn Watson verfügbar ist, Icon einfärben und noch ein bisschen gerade rücken
            if(rex_addon::get('watson')->isAvailable()){
                $ersetzen .= '
                .watson-btn svg {
				    color: ' . $this->getConfig('color2'. be_branding::getCurrentBeDomainId(true)) . ';
				    padding-top: 8px;
				}';
            }
            # Bissle Farbe im Login Screen ab REX 5.12
            if (rex_string::versionCompare(rex::getVersion(), '5.12', '>=')) {

                $panel_bg = rex_addon::get('be_branding')->getConfig('color1'. be_branding::getCurrentBeDomainId(true));
                if (rex_addon::get('be_branding')->getConfig('color1'. be_branding::getCurrentBeDomainId(true))) {
                    $panel_bg = str_replace(', 1)', ', 0.8)', $panel_bg);
                }

                $rex_page_login = '
						#rex-page-login {
								background-color: ' . $this->getConfig('color2'. be_branding::getCurrentBeDomainId(true)) . ' !important;
						}
						';

                // Wenn eigenes BG-Bild
                if (rex_addon::get('be_branding')->getConfig('login_bg'. be_branding::getCurrentBeDomainId(true)) && rex_addon::get('be_branding')->getConfig('login_bg_setting'. be_branding::getCurrentBeDomainId(true)) == "own_bg") {
                    $rex_page_login = '
						#rex-page-login {
								background-color: ' . $this->getConfig('color2'. be_branding::getCurrentBeDomainId(true)) . ' !important;
						}
						@media (max-width: 991px) {
							#rex-page-login {
								background-image: url("' . rex_media_manager::getUrl('be_branding_login_2100_jpg', rex_addon::get('be_branding')->getConfig('login_bg'. be_branding::getCurrentBeDomainId(true))) . '");
								background-size: cover;
							}
						}';
                }

                // Wenn REDAXO-Standard
                if (rex_addon::get('be_branding')->getConfig('login_bg_setting'. be_branding::getCurrentBeDomainId(true)) == "redaxo_standard_bg") {
                    $rex_page_login = '
						#rex-page-login {
								background-color: ' . $this->getConfig('color2'. be_branding::getCurrentBeDomainId(true)) . ' !important;
						}
						';
                }

                // Wenn Primärfarbe
                if (rex_addon::get('be_branding')->getConfig('login_bg_setting'. be_branding::getCurrentBeDomainId(true)) == "primary_bg") {
                    $rex_page_login = '
						#rex-page-login {
								background-color: ' . $this->getConfig('color1'. be_branding::getCurrentBeDomainId(true)) . ' !important;
						}
						';
                    // Panel in transp. wess, damit man es noch sieht
                    $panel_bg = '
							rgba(255,255,255, 0.4)
						';
                }

                // Wenn Sekundärfarbe
                if (rex_addon::get('be_branding')->getConfig('login_bg_setting'. be_branding::getCurrentBeDomainId(true)) == "secondary_bg") {
                    $rex_page_login = '
						#rex-page-login {
								background-color: ' . $this->getConfig('color2'. be_branding::getCurrentBeDomainId(true)) . ' !important;
						}
						';
                }

                // Wenn Verlauf
                if (rex_addon::get('be_branding')->getConfig('login_bg_setting'. be_branding::getCurrentBeDomainId(true)) == "gradient_bg") {
                    $rex_page_login = '
						#rex-page-login {
								background: ' . $this->getConfig('color2'. be_branding::getCurrentBeDomainId(true)) . ';
								background: -moz-linear-gradient(71deg, ' . $this->getConfig('color1'. be_branding::getCurrentBeDomainId(true)) . ') 0%, ' . $this->getConfig('color2') . ' 100%);
								background: -webkit-linear-gradient(71deg, ' . $this->getConfig('color1'. be_branding::getCurrentBeDomainId(true)) . ' 0%, ' . $this->getConfig('color2') . ' 100%);
								background: linear-gradient(71deg, ' . $this->getConfig('color1'. be_branding::getCurrentBeDomainId(true)) . ' 0%, ' . $this->getConfig('color2'. be_branding::getCurrentBeDomainId(true)) . ' 100%);
								filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="' . be_branding::rgba2hex($this->getConfig('color1'. be_branding::getCurrentBeDomainId(true))) . '",endColorstr="' . be_branding::rgba2hex($this->getConfig('color2'. be_branding::getCurrentBeDomainId(true))) . '",GradientType=1);
						}
						';
                }


                $ersetzen .= $rex_page_login;

                $ersetzen .= '
					
					#rex-form-login .rex-redaxo-logo path.rex-redaxo-logo-r,
					#rex-form-login .rex-redaxo-logo path.rex-redaxo-logo-e,
					#rex-form-login .rex-redaxo-logo path.rex-redaxo-logo-d,
					#rex-form-login .rex-redaxo-logo path.rex-redaxo-logo-cms{fill: ' . $this->getConfig('color2'. be_branding::getCurrentBeDomainId(true)) . ' !important;}
					
					#rex-page-login .panel-default {
					background-color: ' . $panel_bg . ';
					border: 0;
					color: ' . $this->getConfig('color2'. be_branding::getCurrentBeDomainId(true)) . ';
					border-radius: 5px;}
					
					#rex-page-login .form-control {
						background-color: ' . $panel_bg . ' !important;
						color: #fff;
						border: none !important;
						box-shadow: none;
					}
					
					#rex-page-login .input-group-addon,
					#rex-page-login .input-group-btn .btn-view {
						background-color: ' . $panel_bg . ' !important;
						color: ' . $this->getConfig('color2'. be_branding::getCurrentBeDomainId(true)) . ' !important;
						border: none !important;
					}
					
					#rex-page-login .btn-primary {
						background-color: ' . $this->getConfig('color2'. be_branding::getCurrentBeDomainId(true)) . ' !important;
						color: ' . $this->getConfig('color1'. be_branding::getCurrentBeDomainId(true)) . ' !important;
						border-color:' . $this->getConfig('color1'. be_branding::getCurrentBeDomainId(true)) . ' !important;
					}
					';
            } // End Rex 5.12

            $ersetzen .= '#rex-page-login .rex-page-main:before{border-color: ' . $this->getConfig('color1'. be_branding::getCurrentBeDomainId(true)) . ' transparent transparent transparent !important; top: -1px !important;}
				</style></head>';
            $ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
        }, rex_extension::LATE);
    } // EoF Farben

    // Fuer den Customizer den PageName und Link im Header auf die aktuelle Domain ändern
    if (rex_plugin::get('be_style','customizer')->getConfig('showlink') && $this->getConfig('domainprofiles_enabled')) {
        rex_view::setJsProperty(
            'customizer_showlink',
            '<h1 class="be-style-customizer-title"><a href="'. be_branding::getDomainById(be_branding::getCurrentBeDomainId(false))['domain'] .'" target="_blank" rel="noreferrer noopener"><span class="be-style-customizer-title-name">' . be_branding::getDomainById(be_branding::getCurrentBeDomainId(false))['domain'] . '</span><i class="fa fa-external-link"></i></a></h1>'
        );
    }

} // EoF if rex Backend