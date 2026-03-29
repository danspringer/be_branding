<?php

class fe_favicon
{
    /**
     * Generiert alle PNG/ICO-Favicons für jede YRewrite-Domain.
     * Wird beim Speichern der fe_favicon-Seite aufgerufen.
     * SVG-Favicons brauchen keine Generierung – sie kommen direkt aus dem Medienpool.
     */
    public static function generate(): void
    {
        require rex_path::addon('be_branding') . 'vendor/favicon/src/FE_FaviconGenerator.php';

        $addon = rex_addon::get('be_branding');

        foreach (rex_yrewrite::getDomains() as $domain) {
            $domainId = $domain->getId();
            $filename = (string) $addon->getConfig('fe_favicon_filename_' . $domainId);

            if ($filename === '') {
                continue;
            }

            $tileColor = be_branding::rgba2hex((string) $addon->getConfig('fe_favicon_tilecolor_' . $domainId));

            // ICO ins Root und in Assets
            $ico = new PHP_ICO(rex_path::media() . $filename, [[144, 144]]);
            $ico->save_ico(rex_path::addonAssets('be_branding', 'fe_favicon/favicon--' . $domainId . '.ico'));
            if (file_exists(rex_path::frontend('favicon--' . $domainId . '.ico'))) {
                unlink(rex_path::frontend('favicon--' . $domainId . '.ico'));
            }
            $ico->save_ico(rex_path::frontend('favicon--' . $domainId . '.ico'));

            // Alle anderen Formate (PNG, Apple Touch, Android, MS Tile…)
            $fav = new FE_FaviconGenerator(rex_path::media() . $filename);
            $fav->setCompression(FE_FaviconGenerator::COMPRESSION_VERYHIGH);
            $fav->setConfig([
                'apple-background'    => $tileColor,
                'apple-margin'        => 0,
                'android-background'  => $tileColor,
                'android-margin'      => 0,
                'android-name'        => rex::getServerName(),
                'android-url'         => rex::getServer(),
                'android-orientation' => FE_FaviconGenerator::ANDROID_PORTRAIT,
                'ms-background'       => $tileColor,
            ]);
            $fav->createAll($tileColor, $domainId);
        }
    }

    /**
     * Gibt den vollständigen HTML-Code für alle Favicon-<link>-Tags zurück.
     *
     * Reihenfolge (Browser nimmt die erste passende):
     *   1. SVG  – modern, skaliert perfekt, Dark-Mode-fähig (kein Imagick nötig)
     *   2. PNG  – Fallback für Browser ohne SVG-Favicon-Support
     *   3. ICO  – Legacy-Fallback (IE, sehr alte Browser)
     *
     * @param  string     $hex_name  Hex-Farbe für Tile/Theme-Color (mit oder ohne #)
     * @param  int        $domainId  YRewrite-Domain-ID
     * @return string|false          HTML-String oder false wenn nichts vorhanden
     */
    public static function getHtml(string $hex_name, int $domainId): string|false
    {
        $html  = '';
        $addon = rex_addon::get('be_branding');

        // ── 1. SVG-Favicon ───────────────────────────────────────────
        // Direkt aus dem Medienpool, kein Imagick nötig.
        // Unterstützt Dark Mode wenn die SVG eine @media-Regel enthält.
        $svgFile = (string) $addon->getConfig('fe_favicon_svg_' . $domainId);
        if ($svgFile !== '') {
            $domainUrl = rex_yrewrite::getCurrentDomain()->getUrl();
            $svgUrl    = $domainUrl . 'media/' . $svgFile;
            $html .= '<link rel="icon" type="image/svg+xml" href="' . rex_escape($svgUrl) . '">' . "\n";
        }

        // ── 2. PNG-Favicons (Standard + Apple Touch + Android) ───────
        $hex = substr(ltrim($hex_name, '#'), 0, 6);

        foreach (['16x16', '32x32', '96x96', '128x128'] as $size) {
            $file = 'fe_favicon/favicon-' . $size . '-' . $hex . '--' . $domainId . '.png';
            if (file_exists(rex_path::addonAssets('be_branding', $file))) {
                $url = rex_yrewrite::getCurrentDomain()->getUrl() . 'assets/addons/be_branding/' . $file;
                $html .= '<link rel="icon" type="image/png" sizes="' . $size . '" href="' . rex_escape($url) . '">' . "\n";
            }
        }

        foreach (['57x57', '60x60', '72x72', '76x76', '114x114', '120x120', '144x144', '152x152', '180x180'] as $size) {
            $file = 'fe_favicon/apple-touch-icon-' . $size . '-' . $hex . '--' . $domainId . '.png';
            if (file_exists(rex_path::addonAssets('be_branding', $file))) {
                $url = rex_yrewrite::getCurrentDomain()->getUrl() . 'assets/addons/be_branding/' . $file;
                $html .= '<link rel="apple-touch-icon" sizes="' . $size . '" href="' . rex_escape($url) . '">' . "\n";
            }
        }

        $androidFile = 'fe_favicon/android-chrome-192x192-' . $hex . '--' . $domainId . '.png';
        if (file_exists(rex_path::addonAssets('be_branding', $androidFile))) {
            $url = rex_yrewrite::getCurrentDomain()->getUrl() . 'assets/addons/be_branding/' . $androidFile;
            $html .= '<link rel="icon" type="image/png" sizes="192x192" href="' . rex_escape($url) . '">' . "\n";
        }

        $manifestFile = 'fe_favicon/manifest-' . $hex . '--' . $domainId . '.json';
        if (file_exists(rex_path::addonAssets('be_branding', $manifestFile))) {
            $url = rex_yrewrite::getCurrentDomain()->getUrl() . 'assets/addons/be_branding/' . $manifestFile;
            $html .= '<link rel="manifest" href="' . rex_escape($url) . '">' . "\n";
        }

        // ── 3. ICO-Fallback ──────────────────────────────────────────
        $icoFile = 'fe_favicon/favicon--' . $domainId . '.ico';
        if (file_exists(rex_path::addonAssets('be_branding', $icoFile))) {
            $url = rtrim(rex_yrewrite::getCurrentDomain()->getUrl(), '/') . rex_url::addonAssets('be_branding', $icoFile);
            $html .= '<link rel="shortcut icon" type="image/x-icon" href="' . rex_escape($url) . '">' . "\n";
        }

        // ── MS Tile / Theme Color ────────────────────────────────────
        $msTileFile = 'fe_favicon/mstile-144x144-' . $hex . '--' . $domainId . '.png';
        if (file_exists(rex_path::addonAssets('be_branding', $msTileFile))) {
            $url = rex_yrewrite::getCurrentDomain()->getUrl() . 'assets/addons/be_branding/' . $msTileFile;
            $html .= '<meta name="msapplication-TileImage" content="' . rex_escape($url) . '">' . "\n";
        }

        if ($addon->getConfig('fe_favicon_tilecolor_' . $domainId)) {
            $html .= '<meta name="msapplication-TileColor" content="#' . rex_escape($hex) . '">' . "\n";
            $html .= '<meta name="theme-color" content="#' . rex_escape($hex) . '">' . "\n";
        }

        return $html !== '' ? $html : false;
    }
}
