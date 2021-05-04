<?php

$content = '';
$buttons = '';

// Einstellungen speichern
if (rex_post('formsubmit', 'string') == '1') {
    $this->setConfig(rex_post('config', [
        ['agency', 'string'],
        ['file', 'string'],
		['file2', 'string'],
		['textarea', 'string'],
		['border_text', 'string'],
		['border_color', 'string'],
		['color1', 'string'],
		['color2', 'string'],
		['login_bg', 'string'],
		['login_bg_setting', 'string'],
    ]));
	
	// Generierte Favicons löschen wenn gespeichert wurde, damit Sie frisch generiert werden können
	$files = glob(rex_path::base('assets/addons/be_branding/favicon/*')); // get all file names
	foreach($files as $file){ // iterate files
	  if(is_file($file))
		unlink($file); // delete file
	}
	
    echo rex_view::success('Einstellungen gespeichert');
}

if($this->getConfig('colorpicker')) {
	$class_colorpicker = ' minicolors';
	} else $class_colorpicker = '';


if($this->getConfig('showborder')) {
$content .= '<fieldset><legend>Top-Border</legend>';


	// Einfaches Textfeld
	$formElements = [];
	$n = [];
	$n['label'] = '<label for="be_branding-config-border-text">Border-Text<p><small>Erscheint oberhalb der Seite im Backend</small></p></label>';
	$n['field'] = '<input class="form-control" type="text" id="be_branding-config-border-text" name="config[border_text]" value="' . $this->getConfig('border_text') . '"/>';
	$formElements[] = $n;
	
	$fragment = new rex_fragment();
	$fragment->setVar('elements', $formElements, false);
	$content .= $fragment->parse('core/form/container.php');
	
	// Einfaches Textfeld
	$formElements = [];
	$n = [];
	$n['label'] = '<label for="be_branding-config-border-color">Border-Farbe<p><small>Hintergrundfarbe des Balken oben</small></p></label>';
	$n['field'] = '<input class="form-control'.$class_colorpicker.'" type="text" id="be_branding-config-border-color" name="config[border_color]" value="' . $this->getConfig('border_color') . '" placeholder="z.B. rgba(255, 100, 0, 1)"/><p class="help-block rex-note">Beliebige RGBa-Farbangabe (z.B. <code>rgba(255, 100, 0, 1)</code>)</p>';
	$formElements[] = $n;
	
	$fragment = new rex_fragment();
	$fragment->setVar('elements', $formElements, false);
	$content .= $fragment->parse('core/form/container.php');
	
	
	$content .= '</fieldset>';
}


$content .= '<fieldset><legend>Farbschema</legend>';

// Einfaches Textfeld
$formElements = [];
$n = [];
$n['label'] = '<label for="be_branding-config-color1">Prim&auml;rfarbe<p><small>z.B. HG-Farbe des Redaxo-Headers</small></p></label>';
$n['field'] = '<input class="form-control'.$class_colorpicker.'" type="text" id="be_branding-config-color1" name="config[color1]" value="' . $this->getConfig('color1') . '" placeholder="z.B. rgba(255, 100, 0, 1)"/><p class="help-block rex-note">Beliebige RGBa-Farbangabe (z.B. <code>rgba(255, 100, 0, 0.5)</code>)</p>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');


// Einfaches Textfeld
$formElements = [];
$n = [];
$n['label'] = '<label for="be_branding-config-color2">Sekund&auml;rfarbe<p><small>Farbe f&uuml;r das Redaxo-Logo</small></p></label>';
$n['field'] = '<input class="form-control'.$class_colorpicker.'" type="text" id="be_branding-config-color2" name="config[color2]" value="' . $this->getConfig('color2') . '" placeholder="z.B. rgba(255, 100, 0, 1)"/><p class="help-block rex-note">Beliebige RGBa-Farbangabe (z.B. <code>rgba(255, 100, 0, 0.5)</code>)</p>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');

$content .= '</fieldset>';


$content .= '<fieldset><legend>Projektbranding</legend>';

// Dateiauswahl Medienpool-Widget
$formElements = [];
$n = [];
$n['label'] = '<label for="REX_MEDIA_1">Logo des Projekts <p><small>Erscheint &uuml;ber der Loginbox und in der Navi des BE<br />Am Besten als trans. PNG o. SVG mit weissen Elementen</small></p></label>';

$n['field'] = '
<div class="rex-js-widget rex-js-widget-media">
	<div class="input-group">
		<input class="form-control" type="text" name="config[file]" value="' . $this->getConfig('file') . '" id="REX_MEDIA_1" readonly="readonly">
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


// Wenn REX 5.12 - erst ab hier gibt es den Login-Screen mit BG
if(rex_string::versionCompare(rex::getVersion(), '5.12', '>=')) {
	
	$formElements = array();
	$elements = array();
	$elements['label'] = '
	  <label for="mf_quicktests-ga-meldung">Hintergrund Login-Screen</label>';
	
	// create select
	$select = new rex_select;
	$select->setId('be-branding-login-bg-option');
	$select->setSize(1);
	$select->setAttribute('class', 'form-control');
	$select->setName('config[login_bg_setting]');
	// add options
	$select->addOption('Eigenes Hintergrundbild', 'own_bg');
	$select->addOption('Primärfarbe aus Farbschema', 'primary_bg');
	$select->addOption('Sekundärfarbe aus Farbschema', 'secondary_bg');
	$select->addOption('Farbverlauf aus Primär-und Sekundärfarbe aus Farbschema', 'gradient_bg');
	$select->addOption('REDAXO-Standard-Hintergrundbild', 'redaxo_standard_bg');
	$select->setSelected($this->getConfig('login_bg_setting'));
	$elements['field'] = $select->get();
	$formElements[] = $elements;
	// parse select element
	$fragment = new rex_fragment();
	$fragment->setVar('elements', $formElements, false);
	$content .= $fragment->parse('core/form/form.php');
	
	
	$content .= '<div id="be-branding-login-bg-setting">';
	$formElements = [];
	$n = [];
	$n['label'] = '<label for="REX_MEDIA_3">Hintergrundbild des Login-Screens</label>';
	
	$n['field'] = '
	<div class="rex-js-widget rex-js-widget-media">
		<div class="input-group">
			<input class="form-control" type="text" name="config[login_bg]" value="' . $this->getConfig('login_bg') . '" id="REX_MEDIA_3" readonly="readonly">
			<span class="input-group-btn">
			<a href="#" class="btn btn-popup" onclick="openREXMedia(3);return false;" title="ÖFFNEN">
				<i class="rex-icon rex-icon-open-mediapool"></i>
			</a>
			<a href="#" class="btn btn-popup" onclick="addREXMedia(3);return false;" title="NEU">
				<i class="rex-icon rex-icon-add-media"></i>
			</a>
			<a href="#" class="btn btn-popup" onclick="deleteREXMedia(3);return false;" title="REMOVE">
				<i class="rex-icon rex-icon-delete-media"></i>
			</a>
			<a href="#" class="btn btn-popup" onclick="viewREXMedia(3);return false;" title="ANSEHEN">
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
	$content .= '</div>'; // Div be-branding-login-bg-setting schliessen
	
	} // Eo REX 5.12

$content .= '</fieldset>';


$content .= '<fieldset><legend>Agenturbranding</legend>';

// Einfaches Textfeld
$formElements = [];
$n = [];
$n['label'] = '<label for="be_branding-config-agency">Name der Agentur<p><small>Erscheint in den Credits und im Footer</small></p></label>';
$n['field'] = '<input class="form-control" type="text" id="be_branding-config-agency" name="config[agency]" value="' . $this->getConfig('agency') . '"/>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');

// Dateiauswahl Medienpool-Widget
$formElements = [];
$n = [];
$n['label'] = '<label for="REX_MEDIA_2">Logo der Agentur<p><small>Erscheint in den Credits</small></p></label>';

$n['field'] = '
<div class="rex-js-widget rex-js-widget-media">
	<div class="input-group">
		<input class="form-control" type="text" name="config[file2]" value="' . $this->getConfig('file2') . '" id="REX_MEDIA_2" readonly="readonly">
		<span class="input-group-btn">
        <a href="#" class="btn btn-popup" onclick="openREXMedia(2);return false;" title="'.$this->i18n('var_media_open').'">
        	<i class="rex-icon rex-icon-open-mediapool"></i>
        </a>
        <a href="#" class="btn btn-popup" onclick="addREXMedia(2);return false;" title="'.$this->i18n('var_media_new').'">
        	<i class="rex-icon rex-icon-add-media"></i>
        </a>
        <a href="#" class="btn btn-popup" onclick="deleteREXMedia(2);return false;" title="'.$this->i18n('var_media_remove').'">
        	<i class="rex-icon rex-icon-delete-media"></i>
        </a>
        <a href="#" class="btn btn-popup" onclick="viewREXMedia(2);return false;" title="'.$this->i18n('var_media_view').'">
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

// Textarea
$editor_params = '';
switch ($this->getConfig('editor')) {
    case 'ckeditor':
        $editor_params = 'class="form-control ckeditor" data-ckeditor-profile="lite"';
        break;
   case 'cke5':
        $editor_params = 'class="form-control cke5-editor" data-profile="default" data-lang="'. \Cke5\Utils\Cke5Lang::getUserLang().'';
        break;
   case 'markitup markdown':
       $editor_params = 'class="form-control markitupEditor-markdown_full"';
       break;
   case 'markitup textile':
       $editor_params = 'class="form-control markitupEditor-textile_full"';
       break;	   
   case 'redactor2':
       $editor_params = 'class="form-control redactorEditor2-full"';
       break;
   case 'tinymce4':
        $editor_params = 'class="form-control tinyMCEEditor"';
        break;
}

$formElements = [];
$n = [];
$n['label'] = '<label for="be_branding-config-textarea">Adresse oder Zusatzinfo (HTML)<p><small>Erscheint in den Credits</small></p></label>';
$n['field'] = '<textarea '.$editor_params.' id="be_branding-config-textarea" name="config[textarea]" rows="8" style="width: 100%";>' . $this->getConfig('textarea') . '</textarea>';
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
?>

<script>
$(document).on('rex:ready', function() {
	
	if($("#be-branding-login-bg-option").val() == 'own_bg') {
		$('#be-branding-login-bg-setting').show();
		} else {
			$('#be-branding-login-bg-setting').hide();
			}
		
    $("#be-branding-login-bg-option").change(function() {
	  if(this.value != 'own_bg') {
		  $('#be-branding-login-bg-setting').hide('fast');
		  } else {
			  $('#be-branding-login-bg-setting').show('fast');
			  }
	});
	
});
</script>





