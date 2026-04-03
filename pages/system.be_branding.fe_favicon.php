<?php

/** @var rex_addon $this */

$content = '';
$buttons = '';

$domains = rex_sql::factory()->setDebug(false)->setQuery('SELECT * FROM rex_yrewrite_domain');

// ─────────────────────────────────────────────────────────────────────────────
// Einstellungen speichern
// ─────────────────────────────────────────────────────────────────────────────
if (rex_post('formsubmit', 'string') === '1') {

    foreach ($domains as $domain) {
        $did = $domain->getValue('id');
        $this->setConfig(rex_post('config', [
            ['fe_favicon_svg_' . $did,       'string'],
            ['fe_favicon_filename_' . $did,  'string'],
            ['fe_favicon_tilecolor_' . $did, 'string'],
        ]));
    }

    // Generierte PNG/ICO-Favicons invalidieren
    foreach (glob(rex_path::base('assets/addons/be_branding/fe_favicon/*')) ?: [] as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }

    // PNG/ICO neu generieren (nur wenn Imagick verfügbar)
    if (class_exists('Imagick')) {
        fe_favicon::generate();
    }

    echo rex_view::success('Einstellungen gespeichert.'
        . (class_exists('Imagick') ? ' PNG/ICO-Favicons wurden neu generiert.' : ''));
}

$classColorpicker = $this->getConfig('colorpicker') ? ' minicolors' : '';

// ─────────────────────────────────────────────────────────────────────────────
// Hinweis wenn keine Domains vorhanden
// ─────────────────────────────────────────────────────────────────────────────
if ($domains->getRows() === 0) {
    $content .= rex_view::warning('Bitte zuerst in YRewrite mindestens eine Domain anlegen.');
    $content .= '</fieldset>';

    $frag = new rex_fragment();
    $frag->setVar('class', 'edit');
    $frag->setVar('title', 'Frontend-Favicon-Generator');
    $frag->setVar('body', $content, false);
    echo $frag->parse('core/page/section.php');
    return;
}

// ─────────────────────────────────────────────────────────────────────────────
// Hilfsfunktion: Medienpool-Widget
// ─────────────────────────────────────────────────────────────────────────────
$mediaWidget = static function (string $label, string $name, string $value, string $mediaId): array {
    $e = static fn($v) => rex_escape($v);
    return [
        'label' => '<label>' . $label . '</label>',
        'field' => '
<div class="rex-js-widget rex-js-widget-media">
    <div class="input-group">
        <input class="form-control" type="text"
               name="' . $e($name) . '"
               value="' . $e($value) . '"
               id="REX_MEDIA_' . $e($mediaId) . '" readonly="readonly">
        <span class="input-group-btn">
            <a href="#" class="btn btn-popup" onclick="openREXMedia(\'' . $e($mediaId) . '\');return false;">
                <i class="rex-icon rex-icon-open-mediapool"></i>
            </a>
            <a href="#" class="btn btn-popup" onclick="addREXMedia(\'' . $e($mediaId) . '\');return false;">
                <i class="rex-icon rex-icon-add-media"></i>
            </a>
            <a href="#" class="btn btn-popup" onclick="deleteREXMedia(\'' . $e($mediaId) . '\');return false;">
                <i class="rex-icon rex-icon-delete-media"></i>
            </a>
            <a href="#" class="btn btn-popup" onclick="viewREXMedia(\'' . $e($mediaId) . '\');return false;">
                <i class="rex-icon rex-icon-view-media"></i>
            </a>
        </span>
    </div>
</div>',
    ];
};

// ─────────────────────────────────────────────────────────────────────────────
// Tab-Navigation
// ─────────────────────────────────────────────────────────────────────────────
$content .= '<div class="nav"><ul class="nav nav-tabs" role="tablist">';
$i = 0;
foreach ($domains as $domain) {
    $i++;
    $active = $i === 1 ? ' active' : '';
    $did    = (int) $domain->getValue('id');
    $content .= '<li role="presentation" class="' . $active . '">'
        . '<a href="#be_branding-fe-favicons-id--' . $did . '"'
        . ' role="tab" data-toggle="tab">'
        . rex_escape($domain->getValue('domain'))
        . '</a></li>';
}
$content .= '</ul></div>';

// ─────────────────────────────────────────────────────────────────────────────
// Tab-Inhalt pro Domain
// ─────────────────────────────────────────────────────────────────────────────
$content .= '<div class="tab-content">';
$i = 0;
foreach ($domains as $domain) {
    $i++;
    $active = $i === 1 ? ' active' : '';
    $did    = (int) $domain->getValue('id');

    $content .= '<div role="tabpanel" class="tab-pane' . $active . '" id="be_branding-fe-favicons-id--' . $did . '">';
    $content .= '<h3>' . rex_escape($domain->getValue('domain')) . '</h3>';

    // ── SVG-Favicon ──────────────────────────────────────────────
    $content .= '<fieldset><legend><i class="fa fa-star"></i> SVG-Favicon <small style="font-weight:normal;margin-left:6px">Modern · kein Imagick nötig · Dark-Mode-fähig</small></legend>';

    $svgValue = (string) $this->getConfig('fe_favicon_svg_' . $did);
    $formElements = [$mediaWidget(
        'SVG-Favicon'
        . '<p><small>Wird als <code>image/svg+xml</code> eingebunden – skaliert perfekt auf jede Auflösung.'
        . '<br>Tipp: Dark-Mode-Farben direkt in die SVG per <code>@media (prefers-color-scheme: dark)</code> einbauen.</small></p>',
        'config[fe_favicon_svg_' . $did . ']',
        $svgValue,
        'svg' . $did
    )];

    // SVG-Vorschau wenn bereits gewählt
    if ($svgValue !== '') {
        $svgUrl = rex_url::frontend('media/' . $svgValue);
        $formElements[0]['field'] .= '
<div style="margin-top:8px;display:flex;align-items:center;gap:10px">
        <div style="color-scheme: light;">
            <img src="' . rex_escape($svgUrl) . '" alt="SVG-Vorschau hell"
         style="width:64px;height:64px;border:1px solid #ddd;border-radius:4px;background:#f5f5f5;padding:2px">
         </div>
         <div style="color-scheme: dark;">
            <img src="' . rex_escape($svgUrl) . '" alt="SVG-Vorschau dark-Mode"
                 style="width:64px;height:64px;border:1px solid #ddd;border-radius:4px;background:#222;padding:4px">
         </div>
    <span class="text-muted" style="font-size:12px">Vorschau hell &amp; dunkel</span>
</div>';
    }

    $frag = new rex_fragment();
    $frag->setVar('elements', $formElements, false);
    $content .= $frag->parse('core/form/container.php');
    $content .= '</fieldset>';

    // ── PNG/ICO-Favicons (Imagick) ───────────────────────────────
    if (class_exists('Imagick')) {
        $content .= '<fieldset><legend>PNG/ICO-Favicons <small style="font-weight:normal;margin-left:6px">Fallback · Imagick verfügbar</small></legend>';

        $pngValue = (string) $this->getConfig('fe_favicon_filename_' . $did);
        $formElements = [$mediaWidget(
            'Quelldatei für PNG/ICO-Generierung'
            . '<p><small>Transparentes PNG, mindestens 310×310 Pixel empfohlen.'
            . '<br>Wird als Fallback für Browser ohne SVG-Favicon-Support verwendet.</small></p>',
            'config[fe_favicon_filename_' . $did . ']',
            $pngValue,
            'png' . $did
        )];
        $frag = new rex_fragment();
        $frag->setVar('elements', $formElements, false);
        $content .= $frag->parse('core/form/container.php');

        $tileValue = (string) $this->getConfig('fe_favicon_tilecolor_' . $did);
        $formElements = [[
            'label' => '<label>Favicon-Farbschema (Tile Color)'
                . '<p><small>Betrifft nicht das Favicon selbst, sondern z.B. die'
                . ' <a href="https://css-tricks.com/favicon-quiz/" target="_blank">Tile Color</a>'
                . ' in Windows und die Farbe des Browserfensters in Android.</small></p></label>',
            'field' => '<input class="form-control' . rex_escape($classColorpicker) . '"'
                . ' type="text"'
                . ' name="config[fe_favicon_tilecolor_' . $did . ']"'
                . ' value="' . rex_escape($tileValue) . '"'
                . ' placeholder="z.B. rgba(255, 100, 0, 1)"/>'
                . '<p class="help-block rex-note">RGBa oder Hex (z.B. <code>rgba(255, 100, 0, 1)</code> oder <code>#ff6400</code>)</p>',
        ]];
        $frag = new rex_fragment();
        $frag->setVar('elements', $formElements, false);
        $content .= $frag->parse('core/form/container.php');

        // Vorschau bereits generierter PNGs
        $hex = ltrim(be_branding::rgba2hex($tileValue ?: 'rgba(0,0,0,1)'), '#');
        $generatedFiles = [];
        foreach (['16x16', '32x32', '96x96', '128x128'] as $size) {
            $path = rex_path::addonAssets('be_branding', 'fe_favicon/favicon-' . $size . '-' . $hex . '--' . $did . '.png');
            if (file_exists($path)) {
                $generatedFiles[$size] = rex_url::base('assets/addons/be_branding/fe_favicon/favicon-' . $size . '-' . $hex . '--' . $did . '.png');
            }
        }
        if (!empty($generatedFiles)) {
            $content .= '<div style="margin:8px 0 4px"><p class="text-muted" style="font-size:12px">Generierte Icons:</p>'
                . '<div style="display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap">';
            foreach ($generatedFiles as $size => $url) {
                [$w] = explode('x', $size);
                $displaySize = $w;
                $content .= '<div style="text-align:center">'
                    . '<img src="' . rex_escape($url) . '?v=' . time() . '"'
                    . ' style="width:' . $displaySize . 'px;height:' . $displaySize . 'px;border:1px solid #ddd;border-radius:3px;display:block">'
                    . '<small style="font-size:10px;color:#888">' . $size . '</small>'
                    . '</div>';
            }
            $content .= '</div></div>';
        }

        $content .= '</fieldset>';

    } else {
        $content .= '<div class="alert alert-info" style="margin-top:8px">'
            . '<i class="fa fa-info-circle"></i> '
            . '<strong>Imagick nicht verfügbar</strong> – PNG/ICO-Favicons können nicht generiert werden.'
            . ' Das SVG-Favicon funktioniert ohne Imagick.'
            . '</div>';
    }

    $content .= '</div>'; // Ende .tab-pane
}
$content .= '</div>'; // Ende .tab-content

// ─────────────────────────────────────────────────────────────────────────────
// Speichern-Button
// ─────────────────────────────────────────────────────────────────────────────
$n    = ['field' => '<button class="btn btn-save rex-form-aligned" type="submit" name="save">Speichern</button>'];
$frag = new rex_fragment();
$frag->setVar('elements', [$n], false);
$buttons = '<fieldset class="rex-form-action">' . $frag->parse('core/form/submit.php') . '</fieldset>';

// ─────────────────────────────────────────────────────────────────────────────
// Formular ausgeben
// ─────────────────────────────────────────────────────────────────────────────
$frag = new rex_fragment();
$frag->setVar('class', 'edit');
$frag->setVar('title', 'Frontend-Favicons');
$frag->setVar('body', $content, false);
$frag->setVar('buttons', $buttons, false);

echo '<form action="' . rex_url::currentBackendPage() . '" method="post">'
    . '<input type="hidden" name="formsubmit" value="1">'
    . $frag->parse('core/page/section.php')
    . '</form>';

// ─────────────────────────────────────────────────────────────────────────────
// Anleitung
// ─────────────────────────────────────────────────────────────────────────────
$info = '
<h3>SVG-Favicon <span class="label label-success" style="font-size:12px;vertical-align:middle">Empfohlen</span></h3>
<p>SVG-Favicons werden von allen modernen Browsern unterstützt (Chrome, Firefox, Edge, Safari ab 2022)
und skalieren pixelgenau auf jede Auflösung. Dark Mode wird unterstützt, wenn die SVG-Datei eine
<code>@media (prefers-color-scheme: dark)</code>-Regel enthält:</p>
<pre style="font-size:12px;background:#f5f5f5;padding:10px;border-radius:4px">&lt;svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"&gt;
  &lt;style&gt;
    .icon { fill: #ff6400; }
    @media (prefers-color-scheme: dark) {
      .icon { fill: #ffffff; }
    }
  &lt;/style&gt;
  &lt;circle class="icon" cx="50" cy="50" r="50"/&gt;
&lt;/svg&gt;</pre>

<h3>PNG/ICO-Favicons <span class="label label-default" style="font-size:12px;vertical-align:middle">Fallback</span></h3>
<p>Werden als Fallback für ältere Browser und Apple-Geräte generiert (erfordert Imagick auf dem Server).
Browser mit SVG-Support ignorieren die PNG-Links automatisch.</p>

<h3>Einbindung im Template</h3>
<p>Folgenden Code einmalig im <code>&lt;head&gt;</code>-Bereich des Templates einfügen:</p>
<pre style="font-size:12px;background:#f5f5f5;padding:10px;border-radius:4px">' . htmlspecialchars('<?= be_branding::getFrontendFavicons(rex_yrewrite::getCurrentDomain()->getId()) ?>') . '</pre>
<p>Das gibt automatisch den SVG-Link zuerst aus, gefolgt von den PNG/ICO-Fallbacks.</p>
';

$frag = new rex_fragment();
$frag->setVar('title', 'Anleitung & Einbindung');
$frag->setVar('body', $info, false);
echo $frag->parse('core/page/section.php');
