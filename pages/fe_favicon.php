<?php

$content = '';
$buttons = '';

// Einstellungen speichern
if (rex_post('formsubmit', 'string') == '1') {
    $this->setConfig(rex_post('config', [
        ['fe_favicon_filename', 'string'],
		['fe_favicon_tilecolor', 'string'],
    ]));
	
	// Generierte FE-Favicons löschen wenn gespeichert wurde, damit Sie frisch generiert werden können
	$files = glob(rex_path::base('assets/addons/be_branding/fe_favicon/*')); // get all file names
	foreach($files as $file){ // iterate files
	  if(is_file($file))
		unlink($file); // delete file
	}
	
    echo rex_view::success('Einstellungen gespeichert');
}

if($this->getConfig('colorpicker')) {
	$class_colorpicker = ' minicolors';
	} else $class_colorpicker = '';
	

$content .= '<fieldset><legend>Frontend-Favicon-Generator</legend>';

// Dateiauswahl Medienpool-Widget
$formElements = [];
$n = [];
$n['label'] = '<label for="REX_MEDIA_1">Quelldatei: Favicon-Datei <p><small>Als transparentes PNG mit 310x310 Pixeln</small></p></label>';

$n['field'] = '
<div class="rex-js-widget rex-js-widget-media">
	<div class="input-group">
		<input class="form-control" type="text" name="config[fe_favicon_filename]" value="' . $this->getConfig('fe_favicon_filename') . '" id="REX_MEDIA_1" readonly="readonly">
		<span class="input-group-btn">
        <a href="#" class="btn btn-popup" onclick="openREXMedia(1);return false;" title="ÖFFNEN">
        	<i class="rex-icon rex-icon-open-mediapool"></i>
        </a>
        <a href="#" class="btn btn-popup" onclick="addREXMedia(1);return false;" title="NEU">
        	<i class="rex-icon rex-icon-add-media"></i>
        </a>
        <a href="#" class="btn btn-popup" onclick="deleteREXMedia(1);return false;" title="REMOVE">
        	<i class="rex-icon rex-icon-delete-media"></i>
        </a>
        <a href="#" class="btn btn-popup" onclick="viewREXMedia(1);return false;" title="ANSEHEN">
        	<i class="rex-icon rex-icon-view-media"></i>
        </a>
        </span>
	</div>
 </div>
';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');


// Einfaches Textfeld
$formElements = [];
$n = [];
$n['label'] = '<label for="be_branding-fe_favicon_tilecolor">Favicon-Farbschema<p><small>Betrifft nicht das Favicon selbst, sondern z.B. die <a href="https://css-tricks.com/favicon-quiz/" target="_blank">Tile-Color</a> in Windows oder <a href="https://developers.google.com/web/updates/2014/11/Support-for-theme-color-in-Chrome-39-for-Android" target="_blank">Farbe des Browserfensters in Android</a>.</small></p></label>';
$n['field'] = '<input class="form-control'.$class_colorpicker.'" type="text" id="be_branding-fe_favicon_tilecolor" name="config[fe_favicon_tilecolor]" value="' . $this->getConfig('fe_favicon_tilecolor') . '" placeholder="z.B. rgba(255, 100, 0, 1)"/><p class="help-block rex-note">Beliebige RGBa-Farbangabe (z.B. <code>rgba(255, 100, 0, 0.5)</code>)</p>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');


$content .= '</fieldset>';





// Save-Button
$formElements = [];
$n = [];
$n['field'] = '<button class="btn btn-save rex-form-aligned" type="submit" name="save" value="Speichern">Speichern</button>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$buttons = $fragment->parse('core/form/submit.php');
$buttons = '
<fieldset class="rex-form-action">
    ' . $buttons . '
</fieldset>
';

// Ausgabe Formular
$fragment = new rex_fragment();
$fragment->setVar('class', 'edit');
$fragment->setVar('title', 'Einstellungen');
$fragment->setVar('body', $content, false);
$fragment->setVar('buttons', $buttons, false);
$output = $fragment->parse('core/page/section.php');

$output = '
<form action="' . rex_url::currentBackendPage() . '" method="post">
<input type="hidden" name="formsubmit" value="1" />
    ' . $output . '
</form>
';

echo $output;




$content = '<h2>Erklärung</h2>
<p>Das Addon generiert aus der Quell-Datei alle möglichen Favicon-Dateien, die dann einfach per Snippet im Frontend im <code>&lt;head&gt;</code>-Bereich eingefügt werden können.</p><hr />

<h2>Snippet zum Einbinden im Frontend</h2>
<p>Das untenstehende Snippet einfach im &lt;head&gt;-Bereich des Templates einfügen.</p>
<code>';
if($this->getConfig('fe_favicon_filename')) {
	$content .= 'REX_BE_BRANDING[type=fe_favicon]';
} else {
	$content .= 'Bitte zuerst ein Favicon auswählen.';
	}
$content .= '</code>';
$fragment = new rex_fragment();
$fragment->setVar('title', "Anwendung");
$fragment->setVar('body', $content, false);

echo $fragment->parse('core/page/section.php');






