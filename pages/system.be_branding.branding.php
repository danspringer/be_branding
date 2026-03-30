<?php

/** @var rex_addon $this */

$content = '';
$buttons = '';

// ─────────────────────────────────────────────────────────────────────────────
// Migration-Protokoll anzeigen (einmalig nach Update)
// ─────────────────────────────────────────────────────────────────────────────
$migrationLog = $this->getConfig('_migration_log');
if ($migrationLog) {
    echo rex_view::info('<strong>be_branding wurde auf v2 migriert:</strong><br><pre style="margin:8px 0 0;font-size:12px">'
        . rex_escape($migrationLog) . '</pre>');
    $this->setConfig('_migration_log', null);
}

// ─────────────────────────────────────────────────────────────────────────────
// Export-Download verarbeiten
// ─────────────────────────────────────────────────────────────────────────────
if (rex_get('export', 'int') === 1 && rex::getUser()->hasPerm('be_branding[config]')) {
    $json     = be_branding::exportConfig();
    $filename = 'be_branding_config_' . date('Y-m-d') . '.json';
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($json));
    echo $json;
    exit;
}

// ─────────────────────────────────────────────────────────────────────────────
// Import verarbeiten
// ─────────────────────────────────────────────────────────────────────────────
if (rex_post('import_submit', 'int') === 1 && rex::getUser()->hasPerm('be_branding[config]')) {
    $uploadedFile = $_FILES['import_json'] ?? null;
    if ($uploadedFile && $uploadedFile['error'] === UPLOAD_ERR_OK) {
        $json   = file_get_contents($uploadedFile['tmp_name']);
        $result = be_branding::importConfig($json);
        echo $result['success']
            ? rex_view::success($result['message'])
            : rex_view::error($result['message']);
    } else {
        echo rex_view::error('Keine Datei hochgeladen oder Fehler beim Upload.');
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Einstellungen speichern
// ─────────────────────────────────────────────────────────────────────────────
$domains = rex_sql::factory()->setDebug(0)->setQuery('SELECT * FROM rex_yrewrite_domain');

if (rex_post('formsubmit', 'string') === '1') {

    if ($this->getConfig('domainprofiles_enabled')) {
        $domainprofiles = [];
        foreach ($domains as $domain) {
            $did = $domain->getValue('id');
            foreach (['agency', 'file', 'file2', 'textarea', 'border_text', 'border_color',
                      'color1', 'color2', 'color1_dark', 'color2_dark',
                      'login_bg', 'login_bg_setting', 'custom_css',
                      'be_favicon_setting', 'be_favicon_invert'] as $key) {
                $domainprofiles[] = [$key . '--' . $did, 'string'];
            }
        }
        $this->setConfig(rex_post('config', $domainprofiles));
    } else {
        $this->setConfig(rex_post('config', [
            ['agency', 'string'],
            ['file', 'string'],
            ['file2', 'string'],
            ['textarea', 'string'],
            ['border_text', 'string'],
            ['border_color', 'string'],
            ['color1', 'string'],
            ['color2', 'string'],
            ['color1_dark', 'string'],
            ['color2_dark', 'string'],
            ['login_bg', 'string'],
            ['login_bg_setting', 'string'],
            ['custom_css', 'string'],
            ['be_favicon_setting', 'string'],
            ['be_favicon_invert', 'int'],
        ]));
    }

    // Generierte Favicons invalidieren
    foreach (glob(rex_path::base('assets/addons/be_branding/favicon/*')) ?: [] as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }

    // Invertiertes Favicon generieren (pro Domain / global)
    if ($this->getConfig('domainprofiles_enabled')) {
        foreach ($domains as $domain) {
            $did    = $domain->getValue('id');
            $suffix = '--' . $did;
            if ($this->getConfig('be_favicon_invert' . $suffix)) {
                $setting = $this->getConfig('be_favicon_setting' . $suffix);
                $colorKey = $setting === 'secondary' ? 'color2' : 'color1';
                $hex = be_branding::rgba2hex((string) $this->getConfig($colorKey . $suffix));
                if ($hex) {
                    be_branding::generateInvertedFavicon($hex, $suffix);
                }
            }
        }
    } else {
        if ($this->getConfig('be_favicon_invert')) {
            $setting  = $this->getConfig('be_favicon_setting');
            $colorKey = $setting === 'secondary' ? 'color2' : 'color1';
            $hex = be_branding::rgba2hex((string) $this->getConfig($colorKey));
            if ($hex) {
                be_branding::generateInvertedFavicon($hex);
            }
        }
    }

    echo rex_view::success('Einstellungen gespeichert.');
}

// ─────────────────────────────────────────────────────────────────────────────
// Colorpicker-Klasse
// ─────────────────────────────────────────────────────────────────────────────
$classColorpicker = $this->getConfig('colorpicker') ? ' minicolors' : '';

// ─────────────────────────────────────────────────────────────────────────────
// Hilfsfunktion: Ein Farbfeld rendern
// ─────────────────────────────────────────────────────────────────────────────
$buildColorField = static function (
    string $id,
    string $name,
    string $label,
    string $hint,
    string $value,
    string $classColorpicker
): array {
    return [
        'label' => '<label for="' . rex_escape($id) . '">' . $label
            . '<p><small>' . $hint . '</small></p></label>',
        'field' => '<input class="form-control be-branding-color-input' . $classColorpicker . '"'
            . ' type="text"'
            . ' id="' . rex_escape($id) . '"'
            . ' name="' . rex_escape($name) . '"'
            . ' value="' . rex_escape($value) . '"'
            . ' placeholder="z.B. rgba(255, 100, 0, 1) oder #ff6400"/>'
            . '<p class="help-block rex-note">RGBa oder Hex (z.B. <code>rgba(255, 100, 0, 0.5)</code> oder <code>#ff6400</code>)</p>',
    ];
};

// ─────────────────────────────────────────────────────────────────────────────
// Hilfsfunktion: Medienpool-Widget
// ─────────────────────────────────────────────────────────────────────────────
$buildMediaWidget = static function (
    string $label,
    string $inputName,
    string $inputValue,
    string $mediaId
): array {
    $esc = static fn($v) => rex_escape($v);
    return [
        'label' => '<label>' . $label . '</label>',
        'field' => '
<div class="rex-js-widget rex-js-widget-media">
    <div class="input-group">
        <input class="form-control" type="text"
               name="' . $esc($inputName) . '"
               value="' . $esc($inputValue) . '"
               id="REX_MEDIA_' . $esc($mediaId) . '" readonly="readonly">
        <span class="input-group-btn">
            <a href="#" class="btn btn-popup" onclick="openREXMedia(\'' . $esc($mediaId) . '\');return false;">
                <i class="rex-icon rex-icon-open-mediapool"></i>
            </a>
            <a href="#" class="btn btn-popup" onclick="addREXMedia(\'' . $esc($mediaId) . '\');return false;">
                <i class="rex-icon rex-icon-add-media"></i>
            </a>
            <a href="#" class="btn btn-popup" onclick="deleteREXMedia(\'' . $esc($mediaId) . '\');return false;">
                <i class="rex-icon rex-icon-delete-media"></i>
            </a>
            <a href="#" class="btn btn-popup" onclick="viewREXMedia(\'' . $esc($mediaId) . '\');return false;">
                <i class="rex-icon rex-icon-view-media"></i>
            </a>
        </span>
    </div>
</div>',
    ];
};

// ─────────────────────────────────────────────────────────────────────────────
// Hilfsfunktion: Editor-Params ermitteln
// ─────────────────────────────────────────────────────────────────────────────
$getEditorParams = static function (string $editor): string {
    return match ($editor) {
        'ckeditor'          => 'class="form-control ckeditor" data-ckeditor-profile="lite"',
        'cke5'              => 'class="form-control cke5-editor" data-profile="default" data-lang="'
                               . rex_escape(\Cke5\Utils\Cke5Lang::getUserLang()) . '"',
        'markitup markdown' => 'class="form-control markitupEditor-markdown_full"',
        'markitup textile'  => 'class="form-control markitupEditor-textile_full"',
        'redactor2'         => 'class="form-control redactorEditor2-full"',
        'tinymce4'          => 'class="form-control tinyMCEEditor"',
        default             => 'class="form-control"',
    };
};

// ─────────────────────────────────────────────────────────────────────────────
// Haupt-Formular-Inhalt aufbauen
// Unterscheidet zwischen Einzel-Profil und Domainprofilen (Tab-Ansicht)
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Erzeugt den Formular-Inhalt für ein Profil (domain-unabhängig oder pro Domain).
 * $prefix: '' für Einzelprofil, '--{id}' für Domainprofil
 */
$buildProfileContent = function (
    string $prefix,
    string $domainLabel = ''
) use ($buildColorField, $buildMediaWidget, $getEditorParams, $classColorpicker): string {

    $addon = rex_addon::get('be_branding');
    $cfg   = static fn($key) => (string) $addon->getConfig($key . $prefix, '');
    $esc   = static fn($v) => rex_escape($v);

    $c = '';

    if ($domainLabel !== '') {
        $c .= '<h3>' . $esc($domainLabel) . '</h3>';
    }

    // ── Top-Border (nur wenn aktiviert)
    if ($addon->getConfig('showborder')) {
        $c .= '<fieldset><legend>Top-Border</legend>';

        $formElements = [['label' => '<label>Border-Text<p><small>Erscheint oben im Backend</small></p></label>',
            'field' => '<input class="form-control" type="text" name="config[border_text' . $esc($prefix) . ']"'
                . ' value="' . $esc($cfg('border_text')) . '"/>']];
        $frag = new rex_fragment();
        $frag->setVar('elements', $formElements, false);
        $c .= $frag->parse('core/form/container.php');

        $formElements = [$buildColorField(
            'be_branding-config-border-color',
            'config[border_color' . $prefix . ']',
            'Border-Farbe',
            'Hintergrundfarbe des Balkens oben',
            $cfg('border_color'),
            $classColorpicker
        )];
        $frag = new rex_fragment();
        $frag->setVar('elements', $formElements, false);
        $c .= $frag->parse('core/form/container.php');

        $c .= '</fieldset>';
    }

    // ── Farbschema
    $c .= '<fieldset><legend>Farbschema</legend>';

    // Primärfarbe
    $formElements = [$buildColorField(
        'be_branding-config-color1',
        'config[color1' . $prefix . ']',
        'Primärfarbe',
        'Hintergrundfarbe des REDAXO-Headers',
        $cfg('color1'),
        $classColorpicker
    )];
    $frag = new rex_fragment();
    $frag->setVar('elements', $formElements, false);
    $c .= $frag->parse('core/form/container.php');

    // Sekundärfarbe
    $formElements = [$buildColorField(
        'be_branding-config-color2',
        'config[color2' . $prefix . ']',
        'Sekundärfarbe',
        'Farbe für das REDAXO-Logo',
        $cfg('color2'),
        $classColorpicker
    )];
    $frag = new rex_fragment();
    $frag->setVar('elements', $formElements, false);
    $c .= $frag->parse('core/form/container.php');

    // Dark Mode
    $c .= '<div class="well" style="margin-top:8px;background:rgba(0,0,0,.04);border-radius:4px">';
    $c .= '<strong><i class="fa fa-moon-o"></i> Dark Mode-Farben</strong>';
    $c .= '<p class="text-muted" style="margin:4px 0 8px;font-size:12px">'
        . 'Werden verwendet, wenn der Browser/Nutzer Dark Mode aktiviert hat. '
        . 'Leer lassen = gleiche Farben wie oben.</p>';

    $formElements = [$buildColorField(
        'be_branding-config-color1-dark',
        'config[color1_dark' . $prefix . ']',
        'Primärfarbe (Dark Mode)',
        'Optionale alternative Header-Farbe im Dark Mode',
        $cfg('color1_dark'),
        $classColorpicker
    )];
    $frag = new rex_fragment();
    $frag->setVar('elements', $formElements, false);
    $c .= $frag->parse('core/form/container.php');

    $formElements = [$buildColorField(
        'be_branding-config-color2-dark',
        'config[color2_dark' . $prefix . ']',
        'Sekundärfarbe (Dark Mode)',
        'Optionale alternative Logo-Farbe im Dark Mode',
        $cfg('color2_dark'),
        $classColorpicker
    )];
    $frag = new rex_fragment();
    $frag->setVar('elements', $formElements, false);
    $c .= $frag->parse('core/form/container.php');

    $c .= '</div>'; // end well dark mode

    $c .= '</fieldset>';

    // ── Backend-Favicon
    if (class_exists('Imagick')) {
        $c .= '<fieldset><legend>Backend-Favicon</legend>';

        // Farbauswahl
        $beFaviconSelectId = 'be-branding-be-favicon-option' . ($prefix ?: '');
        $beFaviconSelect   = new rex_select;
        $beFaviconSelect->setId($beFaviconSelectId);
        $beFaviconSelect->setSize(1);
        $beFaviconSelect->setAttribute('class', 'form-control');
        $beFaviconSelect->setName('config[be_favicon_setting' . $prefix . ']');
        $beFaviconSelect->addOption('REDAXO-Standard (nicht färben, nicht invertieren)', '');
        $beFaviconSelect->addOption('Primärfarbe', 'primary');
        $beFaviconSelect->addOption('Sekundärfarbe', 'secondary');
        $beFaviconSelect->setSelected($cfg('be_favicon_setting'));

        $formElements = [['label' => '<label for="' . $esc($beFaviconSelectId) . '">Favicon-Farbe</label>',
            'field' => $beFaviconSelect->get()]];
        $frag = new rex_fragment();
        $frag->setVar('elements', $formElements, false);
        $c .= $frag->parse('core/form/form.php');

        // Invertieren-Option
        $beFaviconInvertId = 'be-branding-be-favicon-invert' . ($prefix ?: '');
        $formElements = [[
            'label' => '<label for="' . $esc($beFaviconInvertId) . '">Favicon invertieren?'
                . '<p><small>Erzeugt ein Favicon mit farbiger Hintergrundfläche und weißem Logo &ndash;'
                . ' besser sichtbar im Dark Mode und auf farbigen Browser-Tabs.</small></p></label>',
            'field' => '<input type="checkbox" id="' . $esc($beFaviconInvertId) . '"'
                . ' name="config[be_favicon_invert' . $esc($prefix) . ']" value="1"'
                . ($cfg('be_favicon_invert') ? ' checked="checked"' : '') . ' />',
        ]];
        $frag = new rex_fragment();
        $frag->setVar('elements', $formElements, false);
        $c .= $frag->parse('core/form/form.php');

        // Live-Vorschau (Canvas via JS) + zuletzt generierte Icons
        $c .= '<div style="margin-top:12px;display:flex;gap:16px;align-items:flex-start;flex-wrap:wrap">';

        // Canvas-Vorschau (wird per JS befüllt)
        $c .= '<div style="text-align:center">'
            . '<img id="be-branding-favicon-preview" src="" alt="Vorschau"'
            . ' style="width:48px;height:48px;border-radius:6px;border:1px solid #ddd;display:block;margin:0 auto 4px"/>'
            . '<small style="font-size:11px;color:#888">Vorschau</small>'
            . '</div>';

        // Bereits generierte PNGs (Imagick)
        $faviconDir   = rex_path::base('assets/addons/be_branding/favicon/');
        $faviconFiles = glob($faviconDir . 'favicon-original-*.png') ?: [];
        foreach (array_slice($faviconFiles, 0, 5) as $file) {
            $url = rex_url::base('assets/addons/be_branding/favicon/' . basename($file));
            $hex = '#' . str_replace(['favicon-original-', '.png'], '', basename($file));
            $c .= '<div style="text-align:center">'
                . '<img src="' . $esc($url) . '?v=' . filemtime($file) . '"'
                . ' alt="' . $esc($hex) . '"'
                . ' style="width:32px;height:32px;border:1px solid #ddd;border-radius:4px;display:block;margin:0 auto 4px"/>'
                . '<small style="font-size:10px;color:#888">' . $esc($hex) . '</small>'
                . '</div>';
        }

        $c .= '</div>'; // end flex

        $c .= '</fieldset>';
    }

    // ── Projektbranding
    $c .= '<fieldset><legend>Projektbranding</legend>';

    $mediaId = $prefix ? 'proj' . ltrim($prefix, '-') : 'proj1';
    $formElements = [$buildMediaWidget(
        'Logo des Projekts <p><small>Erscheint über der Loginbox und in der Navigation.<br>'
        . 'Am besten als transparentes PNG oder SVG mit weißen Elementen.</small></p>',
        'config[file' . $prefix . ']',
        $cfg('file'),
        $mediaId
    )];
    $frag = new rex_fragment();
    $frag->setVar('elements', $formElements, false);
    $c .= $frag->parse('core/form/container.php');

    // Login-Hintergrund
    $loginSelect = new rex_select;
    $loginSelectId = 'be-branding-login-bg-option' . ($prefix ? $prefix : '');
    $loginSelect->setId($loginSelectId);
    $loginSelect->setSize(1);
    $loginSelect->setAttribute('class', 'form-control');
    $loginSelect->setName('config[login_bg_setting' . $prefix . ']');
    $loginSelect->addOption('Eigenes Hintergrundbild', 'own_bg');
    $loginSelect->addOption('Primärfarbe aus Farbschema', 'primary_bg');
    $loginSelect->addOption('Sekundärfarbe aus Farbschema', 'secondary_bg');
    $loginSelect->addOption('Farbverlauf (Primär → Sekundär)', 'gradient_bg');
    $loginSelect->addOption('REDAXO-Standard-Hintergrundbild', 'redaxo_standard_bg');
    $loginSelect->setSelected($cfg('login_bg_setting'));

    $formElements = [['label' => '<label for="' . $esc($loginSelectId) . '">Hintergrund Login-Screen</label>',
        'field' => $loginSelect->get()]];
    $frag = new rex_fragment();
    $frag->setVar('elements', $formElements, false);
    $c .= $frag->parse('core/form/form.php');

    // Domain-ID als data-Attribut für JS (nur bei Domainprofilen)
    $domainDataAttr = $prefix ? ' data-be-branding-domain-id="' . $esc(ltrim($prefix, '-')) . '"' : '';
    $settingDivId   = 'be-branding-login-bg-setting' . ($prefix ? $prefix : '');

    $c .= '<div id="' . $esc($settingDivId) . '"' . $domainDataAttr . '>';

    $bgMediaId = $prefix ? 'bg' . ltrim($prefix, '-') : 'bg1';
    $formElements = [$buildMediaWidget(
        'Hintergrundbild Login-Screen',
        'config[login_bg' . $prefix . ']',
        $cfg('login_bg'),
        $bgMediaId
    )];
    $frag = new rex_fragment();
    $frag->setVar('elements', $formElements, false);
    $c .= $frag->parse('core/form/container.php');
    $c .= '</div>';

    $c .= '</fieldset>';

    // ── Agenturbranding
    $c .= '<fieldset><legend>Agenturbranding</legend>';

    $formElements = [['label' => '<label>Name der Agentur<p><small>Erscheint in den Credits und im Footer</small></p></label>',
        'field' => '<input class="form-control" type="text" name="config[agency' . $esc($prefix) . ']"'
            . ' value="' . $esc($cfg('agency')) . '"/>']];
    $frag = new rex_fragment();
    $frag->setVar('elements', $formElements, false);
    $c .= $frag->parse('core/form/container.php');

    $agencyMediaId = $prefix ? 'agency' . ltrim($prefix, '-') : 'agency1';
    $formElements = [$buildMediaWidget(
        'Logo der Agentur <p><small>Erscheint in den Credits</small></p>',
        'config[file2' . $prefix . ']',
        $cfg('file2'),
        $agencyMediaId
    )];
    $frag = new rex_fragment();
    $frag->setVar('elements', $formElements, false);
    $c .= $frag->parse('core/form/container.php');

    $editorParams = $getEditorParams(rex_addon::get('be_branding')->getConfig('editor'));
    $formElements = [['label' => '<label>Adresse oder Zusatzinfo<p><small>Erscheint in den Credits</small></p></label>',
        'field' => '<textarea ' . $editorParams . ' name="config[textarea' . $esc($prefix) . ']"'
            . ' rows="8" style="width:100%">' . $esc($cfg('textarea')) . '</textarea>']];
    $frag = new rex_fragment();
    $frag->setVar('elements', $formElements, false);
    $c .= $frag->parse('core/form/container.php');

    $c .= '</fieldset>';

    // ── Custom CSS
    $c .= '<fieldset><legend>Custom CSS</legend>';
    $c .= '<p class="help-block">Wird direkt in den Backend-Output eingebettet. '
        . 'Nur für Anpassungen, die über Farbe/Logo hinausgehen.<br>Eingabe ohne umschließendes <code>'.htmlentities('<style></style>').'</code>-Tag</p>';
    $formElements = [['label' => '<label>Freies CSS für das Backend</label>',
        'field' => '<textarea class="form-control" name="config[custom_css' . $esc($prefix) . ']"'
            . ' rows="6" style="width:100%;font-family:monospace;font-size:12px">'
            . $esc($cfg('custom_css')) . '</textarea>']];
    $frag->setVar('elements', $formElements, false);
    $c .= $frag->parse('core/form/container.php');
    $c .= '</fieldset>';

    return $c;
};

// ─────────────────────────────────────────────────────────────────────────────
// Formular-Inhalt zusammensetzen
// ─────────────────────────────────────────────────────────────────────────────
if ($this->getConfig('domainprofiles_enabled')) {
    $content .= '<fieldset><legend>Domainprofile</legend>';

    // Tab-Navigation
    $content .= '<div class="nav"><ul class="nav nav-tabs" role="tablist">';
    $i = 0;
    foreach ($domains as $domain) {
        $i++;
        $did    = $domain->getValue('id');
        $active = $i === 1 ? ' active' : '';
        $content .= '<li role="presentation" class="' . $active . '">'
            . '<a href="#be_branding-domain--' . (int)$did . '"'
            . ' aria-controls="#be_branding-domain--' . (int)$did . '"'
            . ' role="tab" data-toggle="tab">'
            . rex_escape($domain->getValue('domain'))
            . '</a></li>';
    }
    $content .= '</ul></div>';

    // Tab-Inhalt
    $content .= '<div class="tab-content">';
    $i = 0;
    foreach ($domains as $domain) {
        $i++;
        $did    = $domain->getValue('id');
        $active = $i === 1 ? ' active' : '';
        $content .= '<div role="tabpanel" class="tab-pane' . $active . '"'
            . ' id="be_branding-domain--' . (int)$did . '">';
        $content .= $buildProfileContent('--' . $did, $domain->getValue('domain'));
        $content .= '</div>';
    }
    $content .= '</div>';
    $content .= '</fieldset>';
} else {
    $content .= $buildProfileContent('');
}

// ─────────────────────────────────────────────────────────────────────────────
// Speichern-Button
// ─────────────────────────────────────────────────────────────────────────────
$formElements = [['field' => '<button class="btn btn-save rex-form-aligned" type="submit" name="save">Speichern</button>']];
$frag = new rex_fragment();
$frag->setVar('elements', $formElements, false);
$buttons = '<fieldset class="rex-form-action">' . $frag->parse('core/form/submit.php') . '</fieldset>';

// ─────────────────────────────────────────────────────────────────────────────
// Formular ausgeben
// ─────────────────────────────────────────────────────────────────────────────
$frag = new rex_fragment();
$frag->setVar('class', 'edit');
$frag->setVar('title', 'Einstellungen');
$frag->setVar('body', $content, false);
$frag->setVar('buttons', $buttons, false);
$output = $frag->parse('core/page/section.php');

echo '<form action="' . rex_url::currentBackendPage() . '" method="post" id="be-branding-export-form">'
    . '<input type="hidden" name="formsubmit" value="1">'
    . $output
    . '</form>';

// ─────────────────────────────────────────────────────────────────────────────
// Export / Import-Block
// ─────────────────────────────────────────────────────────────────────────────
$ioContent = '
<div class="row">
<div class="col-md-6">
    <h4>Export</h4>
    <p class="text-muted" style="font-size:13px">
        Speichert die gesamte Branding-Konfiguration als JSON-Datei,
        die auf einem anderen System (z.B. Live) importiert werden kann.
    </p>
    <a href="' . rex_url::currentBackendPage(['export' => 1]) . '"
       class="btn btn-default">
        <i class="fa fa-download"></i> Konfiguration exportieren
    </a>
</div>
<div class="col-md-6">
    <h4>Import</h4>
    <form method="post" enctype="multipart/form-data" action="' . rex_url::currentBackendPage() . '">
        <input type="hidden" name="import_submit" value="1">
        <div class="input-group">
            <input type="file" name="import_json" accept=".json" class="form-control" style="padding:4px">
            <span class="input-group-btn">
                <button class="btn btn-default" type="submit">
                    <i class="fa fa-upload"></i> Importieren
                </button>
            </span>
        </div>
        <p class="text-muted" style="font-size:12px;margin-top:6px">
            Akzeptiert be_branding-Exporte aus v1 und v2.
        </p>
    </form>
</div>
</div>';

$frag = new rex_fragment();
$frag->setVar('class', 'edit');
$frag->setVar('title', 'Export / Import');
$frag->setVar('body', $ioContent, false);
#echo $frag->parse('core/page/section.php'); // Export/Import ausblenden
