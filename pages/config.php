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
		['showborder', 'int'],
		['border_text', 'string'],
		['border_color', 'string'],
		['color1', 'string'],
		['color2', 'string'],
		
    ]));

    echo rex_view::success('Einstellungen gespeichert');
}


$content .= '<fieldset><legend>Top-Border</legend>';

$formElements = [];
$n = [];
$n['label'] = '<label for="be-branding-showborder">Top-Border anzeigen?</label>';
$n['field'] = '<input type="checkbox" id="be-branding-showborder" name="config[showborder]" value="1" ' . ($this->getConfig('showborder') ? 'checked="checked" ' : '') . ' />';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');


$content .= '<div id="show-border-area" style="display: none;">';

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
$n['field'] = '<input class="form-control minicolors" type="text" id="be_branding-config-border-color" name="config[border_color]" value="' . $this->getConfig('border_color') . '"/><p class="help-block rex-note">Beliebige valide CSS-Farbangabe (<code>#03f0ab</code>, <code>rgba(255, 100, 0, 0.5)</code> etc.)</p>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');

$content .= '</div>';

$content .= '</fieldset>';



$content .= '<fieldset><legend>Projektbranding</legend>';

// Dateiauswahl Medienpool-Widget
$formElements = [];
$n = [];
$n['label'] = '<label for="REX_MEDIA_1">Logo des Projekts <p><small>Erscheint &uuml;ber der Loginbox und in der Navi des BE<br />Am Besten als trans. PNG mit weissen Elementen</small></p></label>';

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

$content .= '</fieldset>';




$content .= '<fieldset><legend>Farbschema</legend>';

// Einfaches Textfeld
$formElements = [];
$n = [];
$n['label'] = '<label for="be_branding-config-color1">Prim&auml;rfarbe<p><small>z.B. HG-Farbe des Redaxo-Headers</small></p></label>';
$n['field'] = '<input class="form-control minicolors" type="text" id="be_branding-config-color1" name="config[color1]" value="' . $this->getConfig('color1') . '"/><p class="help-block rex-note">Beliebige valide CSS-Farbangabe (<code>#03f0ab</code>, <code>rgba(255, 100, 0, 0.5)</code> etc.)</p>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');


// Einfaches Textfeld
$formElements = [];
$n = [];
$n['label'] = '<label for="be_branding-config-color2">Sekund&auml;rfarbe<p><small>Farbe f&uuml;r das Redaxo-Logo</small></p></label>';
$n['field'] = '<input class="form-control minicolors" type="text" id="be_branding-config-color2" name="config[color2]" value="' . $this->getConfig('color2') . '"/><p class="help-block rex-note">Beliebige valide CSS-Farbangabe (<code>#03f0ab</code>, <code>rgba(255, 100, 0, 0.5)</code> etc.)</p>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');


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
$formElements = [];
$n = [];
$n['label'] = '<label for="be_branding-config-textarea">Adresse oder Zusatzinfo<p><small>Erscheint in den Credits</small></p></label>';
$n['field'] = '<textarea class="ckeditor" data-ckeditor-profile="lite" id="be_branding-config-textarea" name="config[textarea]" rows="8">' . $this->getConfig('textarea') . '</textarea>';
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
$fragment->setVar('title', $this->i18n('config'));
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


