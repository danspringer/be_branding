<?php
$editor_array = array();
array_push($editor_array, 'Keinen Editor nutzen');

if(rex_addon::get('ckeditor')->isAvailable()) {
	array_push($editor_array, 'ckeditor');
	}

if(rex_addon::get('cke5')->isAvailable()) {
	array_push($editor_array, 'cke5');
	}

if(rex_addon::get('markitup')->isAvailable()) {
	array_push($editor_array, 'markitup markdown');
	}

if(rex_addon::get('markitup')->isAvailable()) {
	array_push($editor_array, 'markitup textile');
	}	

if(rex_addon::get('redactor2')->isAvailable()) {
	array_push($editor_array, 'redactor2');
	}

if(rex_addon::get('tinymce4')->isAvailable()) {
	array_push($editor_array, 'tinymce4');
	}
						

	

$content = '';
$buttons = '';


// Einstellungen speichern
if (rex_post('formsubmit', 'string') == '1') {
    $this->setConfig(rex_post('baseconfig', [
        ['editor', 'string'],
		['showborder', 'int'],
		['coloricon', 'int'],
		['colorpicker', 'int'],
		
    ]));
	
	
	// Generierte Favicons löschen wenn gespeichert wurde, damit Sie frisch generiert werden können
	$files = glob(rex_path::base('assets/addons/be_branding/favicon/*')); // get all file names
	foreach($files as $file){ // iterate files
	  if(is_file($file))
		unlink($file); // delete file
	}
	
    echo rex_view::success('Grundeinstellungen des AddOns gespeichert');
}	
	


$content .= '<fieldset><legend>Colorpicker f&uuml;r Farbauswahlfelder</legend>';

$formElements = [];
$n = [];
$n['label'] = '<label for="be-branding-colorpicker">Colorpicker in Farbauswahl verwenden?</label>';
$n['field'] = '<input type="checkbox" id="be-branding-colorpicker" name="baseconfig[colorpicker]" value="1" ' . ($this->getConfig('colorpicker') ? 'checked="checked" ' : '') . ' />';
$formElements[] = $n;
	
$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');
	
$content .= '</fieldset>';



$content .= '<fieldset><legend>Text-Editor</legend>';

$formElements = array();
$elements = array();
$elements['label'] = '
  <label for="rex-mform-config-template">Editor f&uuml;r Text-Eingabe w&auml;hlen</label>
';

// create select
$select = new rex_select;
$select->setId('rex-be_branding-baseconfig-editor');
$select->setSize(1);
$select->setAttribute('class', 'form-control');
$select->setName('baseconfig[editor]');
// add options
foreach ($editor_array as $editor) {
    $select->addOption($editor,$editor);
}
$select->setSelected($this->getConfig('editor'));
$elements['field'] = $select->get();
$formElements[] = $elements;
// parse select element
$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');

$content .= '</fieldset>';




$content .= '<fieldset><legend>Backend-Favicon</legend>';

// Nur wenn Imagemagick verfügbar ist anzeigen
if (class_exists('Imagick') === true) {	
	$formElements = [];
	$n = [];
	$n['label'] = '<label for="be-branding-coloricon">Favicon im Backend f&auml;rben?</label>';
	$n['field'] = '<input type="checkbox" id="be-branding-coloricon" name="baseconfig[coloricon]" value="1" ' . ($this->getConfig('coloricon') ? 'checked="checked" ' : '') . ' />';
	$formElements[] = $n;
		
	$fragment = new rex_fragment();
	$fragment->setVar('elements', $formElements, false);
	$content .= $fragment->parse('core/form/form.php');
	}
	
if (class_exists('Imagick') === false) {	
	$content .= '<p><code>Imagemagick</code> ist nicht auf dem Server installiert, deshalb k&ouml;nnen keine Favicons generiert werden.</p>';
}
$content .= '</fieldset>';



	
$content .= '<fieldset><legend>Top-Border</legend>';
$formElements = [];
$n = [];
$n['label'] = '<label for="be-branding-showborder">Top-Border anzeigen?</label>';
$n['field'] = '<input type="checkbox" id="be-branding-showborder" name="baseconfig[showborder]" value="1" ' . ($this->getConfig('showborder') ? 'checked="checked" ' : '') . ' />';
$formElements[] = $n;
	
$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');
	
	
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
$fragment->setVar('title', 'Grundeinstellungen');
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
?>
