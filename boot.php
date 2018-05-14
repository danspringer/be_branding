<?php

/** @var rex_addon $this */

// Diese Datei ist keine Pflichdatei mehr.

// Daten wie Autor, Version, Subpages etc. sollten wenn möglich in der package.yml notiert werden.
// Sie können aber auch weiterhin hier gesetzt werden:
$this->setProperty('author', 'Daniel Springer, Medienfeuer');

// Die Datei sollte keine veränderbare Konfigurationen mehr enthalten, um die Updatefähigkeit zu erhalten.
// Stattdessen sollte dafür die rex_config verwendet werden (siehe install.php)

// Klassen und lang-Dateien müssen hier nicht mehr eingebunden werden, sie werden nun automatisch gefunden.

// Addonrechte (permissions) registieren
if (rex::isBackend() && is_object(rex::getUser())) {
    rex_perm::register('be_branding[]');
    rex_perm::register('be_branding[config]');
}

// Assets werden bei der Installation des Addons in den assets-Ordner kopiert und stehen damit
// öffentlich zur Verfügung. Sie müssen dann allerdings noch eingebunden werden:

// Assets im Backend einbinden
if (rex::isBackend()) {
	
		if ($this->getConfig('file')) {
		  rex_extension::register('OUTPUT_FILTER',function(rex_extension_point $ep){
			$suchmuster = array ('<h4 class="rex-nav-main-title">Hauptmenü</h4>');
			$ersetzen = array ('<a href="index.php?page=credits"><img src="index.php?rex_media_type=rex_mediapool_maximized&rex_media_file='.$this->getConfig('file').'" class="img-responsive" style="padding-top: 50px"/></a><h4 class="rex-nav-main-title">Hauptmenü</h4>');
			$ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
		  });
		  
		  // Wenn nciht eingeloggt und Backend Logo einbinden
		  if (rex::isBackend() && !rex::getUser()) {
			   rex_extension::register('OUTPUT_FILTER',function(rex_extension_point $ep){
				$suchmuster = array ('<section class="rex-page-main-inner" id="rex-js-page-main">');
				$ersetzen = array ('<img src="index.php?rex_media_type=rex_mediapool_maximized&rex_media_file='.$this->getConfig('file').'" class="img-responsive center-block" style="padding: 10px 0px 15px 0px; width: 370px;"/></a><section class="rex-page-main-inner" id="rex-js-page-main">');
				$ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
			  });
			  }
		  
		}
		
		// Footer Credit im BE
		if ($this->getConfig('agency')) {
		  rex_extension::register('OUTPUT_FILTER',function(rex_extension_point $ep){
			$suchmuster = array ('<li><a href="https://www.yakamara.de" target="_blank" rel="noreferrer noopener">yakamara.de</a></li>');
			$ersetzen = array ('<li><a href="index.php?page=credits">'.$this->getConfig('agency').'</a></li><li><a href="https://www.yakamara.de" target="_blank">yakamara.de</a></li>');
			$ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
		  });
		}
		
		// Text in den Credits
		if ($this->getConfig('textarea')) {
		  rex_extension::register('OUTPUT_FILTER',function(rex_extension_point $ep){
			$suchmuster = array ('<header class="rex-page-header">
    <div class="page-header">
        <h1>Credits                    </h1>
    </div>
    </header>');
			$ersetzen = array ('<header class="rex-page-header"><div class="page-header"><h1>Credits</h1></div></header>
			<section class="rex-page-section">
            <div class="panel panel-default">
        	<header class="panel-heading"><div class="panel-title">'.$this->getConfig('agency').'</div></header>
             <div class="panel-body">
                <div class="row">
					<div class="col-md-6">
					'.$this->getConfig('textarea').'
					</div>
					<div class="col-md-6">
					<p><img src="index.php?rex_media_type=rex_mediapool_maximized&rex_media_file='.$this->getConfig('file2').'" class="img-responsive" /></p>
					</div>
				</div>
				</div>
				</section>
			');
			$ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
		  });
		}
	
    // Die style.css überall im Backend einbinden
    // Es wird eine Versionsangabe angehängt, damit nach einem neuen Release des Addons die Datei nicht
    // aus dem Browsercache verwendet, sondern frisch geladen wird
    rex_view::addCssFile($this->getAssetsUrl('css/style.css?v=' . $this->getVersion()));

    // Die script.js nur auf der Unterseite »config« des Addons einbinden
    if (rex_be_controller::getCurrentPagePart(2) == 'config') {
        rex_view::addJsFile($this->getAssetsUrl('js/script.js?v=' . $this->getVersion()));
    }
	
// Border reinnehmen
if ($this->getConfig('showborder') == 1) {
 rex_extension::register('OUTPUT_FILTER',function(rex_extension_point $ep){
			$suchmuster = '<div id="rex-start-of-page" class="rex-page">';
			$ersetzen = '<div style="font-size: 12px; background-color: '.$this->getConfig('border_color').'; color: #fff; width: 100%; font-weight: bold; text-align: center; padding: 8px 0 6px 0;">'.$this->getConfig('border_text').'</div><div id="rex-start-of-page" class="rex-page">';
			$ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
		});

}
//Farben
if($this->getConfig('color1') && $this->getConfig('color2') ) {
	 rex_extension::register('OUTPUT_FILTER',function(rex_extension_point $ep){
				$suchmuster = '</head>';
				$ersetzen = '<style>.rex-nav-top{background-color: '.$this->getConfig('color1').' !important;} .rex-redaxo-logo path.rex-redaxo-logo-r, .rex-redaxo-logo path.rex-redaxo-logo-e, .rex-redaxo-logo path.rex-redaxo-logo-d, .rex-redaxo-logo path.rex-redaxo-logo-cms{fill: '.$this->getConfig('color2').' !important;} .rex-nav-meta .text-muted {color: '.$this->getConfig('color2').' !important;} .rex-redaxo-logo path.rex-redaxo-logo-a, .rex-redaxo-logo path.rex-redaxo-logo-x, .rex-redaxo-logo path.rex-redaxo-logo-o, .rex-redaxo-logo path.rex-redaxo-logo-reg{fill: #fff !important;}</style></head>';
				$ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
			});	
}	
		
	
	
}