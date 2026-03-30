<?php

/** @var rex_addon $this */

$this->setProperty('author', 'Daniel Springer, Medienfeuer');

// Permissions registrieren
if (rex::isBackend() && is_object(rex::getUser())) {
    rex_perm::register('be_branding[branding]');
    rex_perm::register('be_branding[config]');
    rex_perm::register('be_branding[fe_favicon]');
}

if (!rex::isBackend()) {
    return;
}

// Asset einbinden
if (rex_be_controller::getCurrentPage() == 'system/be_branding/branding') {
    rex_view::addJsFile($this->getAssetsUrl('js/be_branding.js?v=' . $this->getVersion()));
}

// ─────────────────────────────────────────────────────────────────────────────
// Logo im Login-Screen (vor dem Login gibt es kein Header-Fragment)
// ─────────────────────────────────────────────────────────────────────────────
if ($this->getConfig('file') && !rex::getUser()) {
    rex_extension::register('OUTPUT_FILTER', static function (rex_extension_point $ep) {
        $logoSrc = be_branding::checkExtension(
            rex_addon::get('be_branding')->getConfig('file' . be_branding::getCurrentBeDomainId(true))
        );
        if (!$logoSrc) {
            return;
        }
        $logoHtml = '<img src="' . rex_escape($logoSrc) . '" class="img-responsive center-block"'
            . ' style="padding: 10px 0 15px 0; width: 370px;" alt=""/>';

        $ep->setSubject(str_replace(
            '<section class="rex-page-main-inner" id="rex-js-page-main">',
            $logoHtml . '<section class="rex-page-main-inner" id="rex-js-page-main">',
            $ep->getSubject()
        ));
    }, rex_extension::LATE);
}

// ─────────────────────────────────────────────────────────────────────────────
// Agentur-Block in den Credits
// ─────────────────────────────────────────────────────────────────────────────
if ($this->getConfig('agency') && rex_be_controller::getCurrentPage() === 'credits'
    && rex_get('license', 'string', 'NONE') === 'NONE') {

    rex_extension::register('PAGE_TITLE_SHOWN', static function (rex_extension_point $ep) {
        $addon = rex_addon::get('be_branding');

        $agencyName = rex_escape($addon->getConfig('agency' . be_branding::getCurrentBeDomainId(true)));
        $text_col   = '';
        $img_col    = '';

        $file2 = $addon->getConfig('file2' . be_branding::getCurrentBeDomainId(true));
        $imgSrc = $file2 ? be_branding::checkExtension($file2) : '';

        $rawText = (string) $addon->getConfig('textarea' . be_branding::getCurrentBeDomainId(true));

        // Text durch konfigurierten Editor rendern
        $html_text = $rawText;
        switch ($addon->getConfig('editor')) {
            case 'markitup markdown':
                $html_text = markitup::parseOutput('markdown', $rawText);
                break;
            case 'markitup textile':
                $html_text = markitup::parseOutput('textile', $rawText);
                break;
        }

        if ($html_text !== '' && $imgSrc !== '') {
            $text_col = '<div class="col-md-6">' . $html_text . '</div>';
            $img_col  = '<div class="col-md-6"><p><img src="' . rex_escape($imgSrc) . '" class="img-responsive" /></p></div>';
        } elseif ($html_text !== '') {
            $text_col = '<div class="col-md-12">' . $html_text . '</div>';
        } elseif ($imgSrc !== '') {
            $img_col  = '<div class="col-md-12"><p><img src="' . rex_escape($imgSrc) . '" class="img-responsive" /></p></div>';
        }

        $html_append = '
<section class="rex-page-section">
  <div class="panel panel-default">
    <header class="panel-heading"><div class="panel-title">' . $agencyName . '</div></header>
    <div class="panel-body">
      <div class="row">
        ' . $text_col . '
        ' . $img_col . '
      </div>
    </div>
  </div>
</section>';

        $ep->setSubject($html_append);
    }, rex_extension::LATE);
}

// ─────────────────────────────────────────────────────────────────────────────
// Colorpicker einbinden
// ─────────────────────────────────────────────────────────────────────────────
$currentSubPage = rex_be_controller::getCurrentPagePart(2);
if ($this->getConfig('colorpicker')
    && !rex_addon::get('ui_tools')->getPlugin('jquery-minicolors')->isAvailable()
    && in_array($currentSubPage, ['be_branding'], true)) {

    rex_view::addCssFile($this->getAssetsUrl('jquery-minicolors/jquery.minicolors.css?v=' . $this->getVersion()));
    rex_view::addJsFile($this->getAssetsUrl('jquery-minicolors/jquery.minicolors.min.js?v=' . $this->getVersion()));
    rex_view::addJsFile($this->getAssetsUrl('jquery-minicolors/jquery-minicolors.js?v=' . $this->getVersion()));
}

// ─────────────────────────────────────────────────────────────────────────────
// Haupt-CSS (Header-Farben, Login-Screen, Dark Mode, Custom CSS)
// Delegiert an be_branding::buildHeaderCss()
// ─────────────────────────────────────────────────────────────────────────────
if ($this->getConfig('color1' . be_branding::getCurrentBeDomainId(true))
    || $this->getConfig('color2' . be_branding::getCurrentBeDomainId(true))) {

    rex_extension::register('OUTPUT_FILTER', static function (rex_extension_point $ep) {
        $cssBlock = be_branding::buildHeaderCss();
        if ($cssBlock !== '') {
            $ep->setSubject(str_replace('</head>', $cssBlock, $ep->getSubject()));
        }
    }, rex_extension::LATE);
}

// ─────────────────────────────────────────────────────────────────────────────
// Customizer: Domain-Link im Header anpassen
// ─────────────────────────────────────────────────────────────────────────────
if (rex_plugin::get('be_style', 'customizer')->getConfig('showlink')
    && $this->getConfig('domainprofiles_enabled')) {

    $domain = be_branding::getDomainById(be_branding::getCurrentBeDomainId(false));
    if ($domain) {
        $domainUrl    = rex_escape($domain['domain']);
        rex_view::setJsProperty(
            'customizer_showlink',
            '<h1 class="be-style-customizer-title">'
            . '<a href="' . $domainUrl . '" target="_blank" rel="noreferrer noopener">'
            . '<span class="be-style-customizer-title-name">' . $domainUrl . '</span>'
            . '<i class="fa fa-external-link"></i></a></h1>'
        );
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Migration v1 → v2 (einmalig beim ersten Start nach Update)
// ─────────────────────────────────────────────────────────────────────────────
if (!$this->getConfig('_migrated_to_v2') && rex::getUser()?->isAdmin()) {
    $migrationLog = be_branding::runMigrationIfNeeded();
    if (!empty($migrationLog)) {
        // Log für das Backend speichern
        $this->setConfig('_migration_log', implode("\n", $migrationLog));
    }
}
