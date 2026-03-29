<?php

/** @var rex_addon $this */

$content = '';
$buttons = '';

// ─────────────────────────────────────────────────────────────────────────────
// Einstellungen speichern
// ─────────────────────────────────────────────────────────────────────────────
if (rex_post('formsubmit', 'string') === '1') {
    $this->setConfig(rex_post('baseconfig', [
        ['editor',                'string'],
        ['showborder',            'int'],
        ['coloricon',             'int'],
        ['colorpicker',           'int'],
        ['domainprofiles_enabled','int'],
    ]));

    // Generierte Favicons invalidieren
    foreach (glob(rex_path::base('assets/addons/be_branding/favicon/*')) ?: [] as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }

    echo rex_view::success('Grundeinstellungen gespeichert.');
}

// ─────────────────────────────────────────────────────────────────────────────
// Editor-Optionen ermitteln
// ─────────────────────────────────────────────────────────────────────────────
$editorOptions = ['Keinen Editor nutzen'];
$editorMap = [
    'ckeditor'  => 'ckeditor',
    'cke5'      => 'cke5',
    'markitup'  => ['markitup markdown', 'markitup textile'],
    'redactor2' => 'redactor2',
    'tinymce4'  => 'tinymce4',
];
foreach ($editorMap as $addon => $values) {
    if (rex_addon::get($addon)->isAvailable()) {
        foreach ((array)$values as $v) {
            $editorOptions[] = $v;
        }
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Domainprofile
// ─────────────────────────────────────────────────────────────────────────────
$content .= '<fieldset><legend>Domainprofile</legend>';
$content .= '<div class="well">Wenn aktiviert, hat jede YRewrite-Domain ein eigenes Branding-Profil.'
    . ' Das Backend sieht je nach Login-URL (<code>domain-a.de/redaxo</code> oder <code>domain-b.de/redaxo</code>)'
    . ' unterschiedlich aus.</div>';

$n = [
    'label' => '<label for="be-branding-domainprofiles_enabled">Domainprofile aktivieren?</label>',
    'field' => '<input type="checkbox" id="be-branding-domainprofiles_enabled"'
        . ' name="baseconfig[domainprofiles_enabled]" value="1"'
        . ($this->getConfig('domainprofiles_enabled') ? ' checked="checked"' : '') . ' />',
];
$frag = new rex_fragment();
$frag->setVar('elements', [$n], false);
$content .= $frag->parse('core/form/form.php');
$content .= '</fieldset>';

// ─────────────────────────────────────────────────────────────────────────────
// Colorpicker
// ─────────────────────────────────────────────────────────────────────────────
$content .= '<fieldset><legend>Colorpicker für Farbauswahl</legend>';
$n = [
    'label' => '<label for="be-branding-colorpicker">Colorpicker verwenden?</label>',
    'field' => '<input type="checkbox" id="be-branding-colorpicker"'
        . ' name="baseconfig[colorpicker]" value="1"'
        . ($this->getConfig('colorpicker') ? ' checked="checked"' : '') . ' />',
];
$frag = new rex_fragment();
$frag->setVar('elements', [$n], false);
$content .= $frag->parse('core/form/form.php');
$content .= '<p class="help-block rex-note" style="margin-top:4px">'
    . 'Tipp: Hex-Farbcodes (#ff6400) werden beim Verlassen des Feldes automatisch in rgba umgerechnet.</p>';
$content .= '</fieldset>';

// ─────────────────────────────────────────────────────────────────────────────
// Text-Editor
// ─────────────────────────────────────────────────────────────────────────────
$content .= '<fieldset><legend>Text-Editor</legend>';
$select = new rex_select;
$select->setId('rex-be_branding-baseconfig-editor');
$select->setSize(1);
$select->setAttribute('class', 'form-control');
$select->setName('baseconfig[editor]');
foreach ($editorOptions as $opt) {
    $select->addOption($opt, $opt);
}
$select->setSelected($this->getConfig('editor'));

$n = ['label' => '<label for="rex-be_branding-baseconfig-editor">Editor für Text-Eingabe</label>',
      'field' => $select->get()];
$frag = new rex_fragment();
$frag->setVar('elements', [$n], false);
$content .= $frag->parse('core/form/form.php');
$content .= '</fieldset>';

// ─────────────────────────────────────────────────────────────────────────────
// Backend-Favicon
// ─────────────────────────────────────────────────────────────────────────────
$content .= '<fieldset><legend>Backend-Favicon</legend>';

if (class_exists('Imagick')) {
    $n = [
        'label' => '<label for="be-branding-coloricon">Favicon im Backend einfärben?</label>',
        'field' => '<input type="checkbox" id="be-branding-coloricon"'
            . ' name="baseconfig[coloricon]" value="1"'
            . ($this->getConfig('coloricon') ? ' checked="checked"' : '') . ' />',
    ];
    $frag = new rex_fragment();
    $frag->setVar('elements', [$n], false);
    $content .= $frag->parse('core/form/form.php');

    // Favicon-Vorschau: bereits generierte Icons anzeigen
    $faviconDir = rex_path::base('assets/addons/be_branding/favicon/');
    $faviconFiles = glob($faviconDir . 'favicon-original-*.png') ?: [];
    if (!empty($faviconFiles)) {
        $content .= '<div style="margin-top:8px">';
        $content .= '<p class="text-muted" style="font-size:12px">Zuletzt generierte Favicons:</p>';
        $content .= '<div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">';
        foreach (array_slice($faviconFiles, 0, 6) as $file) {
            $url  = rex_url::base('assets/addons/be_branding/favicon/' . basename($file));
            $hex  = '#' . str_replace(['favicon-original-', '.png'], '', basename($file));
            $content .= '<div style="text-align:center">'
                . '<img src="' . rex_escape($url) . '?v=' . filemtime($file) . '"'
                . ' alt="Favicon ' . rex_escape($hex) . '"'
                . ' style="width:32px;height:32px;border:1px solid #ddd;border-radius:4px;display:block"/>'
                . '<small style="font-size:10px;color:#888">' . rex_escape($hex) . '</small>'
                . '</div>';
        }
        $content .= '</div></div>';
    }
} else {
    $content .= '<p><code>Imagick</code> ist auf diesem Server nicht verfügbar &ndash;'
        . ' Favicons können nicht automatisch eingefärbt werden.</p>';
}

$content .= '</fieldset>';

// ─────────────────────────────────────────────────────────────────────────────
// Top-Border
// ─────────────────────────────────────────────────────────────────────────────
$content .= '<fieldset><legend>Top-Border</legend>';
$n = [
    'label' => '<label for="be-branding-showborder">Top-Border anzeigen?</label>',
    'field' => '<input type="checkbox" id="be-branding-showborder"'
        . ' name="baseconfig[showborder]" value="1"'
        . ($this->getConfig('showborder') ? ' checked="checked"' : '') . ' />',
];
$frag = new rex_fragment();
$frag->setVar('elements', [$n], false);
$content .= $frag->parse('core/form/form.php');
$content .= '</fieldset>';

// ─────────────────────────────────────────────────────────────────────────────
// Speichern-Button
// ─────────────────────────────────────────────────────────────────────────────
$n = ['field' => '<button class="btn btn-save rex-form-aligned" type="submit" name="save">Speichern</button>'];
$frag = new rex_fragment();
$frag->setVar('elements', [$n], false);
$buttons = '<fieldset class="rex-form-action">' . $frag->parse('core/form/submit.php') . '</fieldset>';

// ─────────────────────────────────────────────────────────────────────────────
// Ausgabe
// ─────────────────────────────────────────────────────────────────────────────
$frag = new rex_fragment();
$frag->setVar('class', 'edit');
$frag->setVar('title', 'Grundeinstellungen');
$frag->setVar('body', $content, false);
$frag->setVar('buttons', $buttons, false);

echo '<form action="' . rex_url::currentBackendPage() . '" method="post">'
    . '<input type="hidden" name="formsubmit" value="1">'
    . $frag->parse('core/page/section.php')
    . '</form>';
