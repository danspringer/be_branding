<?php

/**
 * be_branding – Hauptklasse v2
 *
 * Änderungen gegenüber v1:
 * - getCurrentBeDomainId() statisch gecacht (kein DB-Hit pro Aufruf)
 * - rgb2hex() als Alias auf rgba2hex() vereinheitlicht
 * - getConfig() zentralisiert und mit rex_escape() gesichert
 * - buildLoginCss() / buildHeaderCss() aus boot.php ausgelagert
 * - Dark-Mode-Support: getConfigForScheme()
 * - Export/Import als JSON
 * - Custom-CSS-Feld
 * - checkExtension() ergänzt um WebP und AVIF
 */
class be_branding
{
    /** @var int|false|null Gecachte Domain-ID für den aktuellen Request */
    private static $cachedDomainId = null;

    // ─────────────────────────────────────────────────────────────
    // Farb-Hilfsmethoden
    // ─────────────────────────────────────────────────────────────

    public static function hex2rgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * rgba(r,g,b,a) oder rgb(r,g,b) → #rrggbb
     */
    public static function rgba2hex(string $rgba): string
    {
        if (!$rgba) {
            return '';
        }
        $rgba = preg_replace('/rgba?\(/', '', $rgba);
        $rgba = rtrim($rgba, ')');
        $parts = array_map('trim', explode(',', $rgba));
        return sprintf('#%02x%02x%02x', (int)$parts[0], (int)$parts[1], (int)$parts[2]);
    }

    /** BC-Alias */
    public static function rgb2hex(string $rgb): string
    {
        return self::rgba2hex($rgb);
    }

    /**
     * Hex-Farbe #rrggbb → rgba(r,g,b,1)
     */
    public static function hex2rgba(string $hex, float $alpha = 1.0): string
    {
        [$r, $g, $b] = self::hex2rgb($hex);
        return "rgba($r, $g, $b, $alpha)";
    }

    // ─────────────────────────────────────────────────────────────
    // Config-Zugriff (zentral + escaped)
    // ─────────────────────────────────────────────────────────────

    /**
     * Gibt einen Konfig-Wert zurück.
     * Mit $escape = true wird der Wert für die HTML-Ausgabe gesichert.
     */
    public static function getAddonConfig(string $key, bool $escape = false): string
    {
        $value = (string) rex_addon::get('be_branding')->getConfig($key, '');
        return $escape ? rex_escape($value) : $value;
    }

    /**
     * Gibt den Config-Key für die aktuelle Domain zurück.
     * Beispiel: getKey('color1') → 'color1--3' bei Domain-ID 3
     *           getKey('color1') → 'color1' wenn Domainprofile deaktiviert
     */
    public static function getKey(string $baseKey): string
    {
        $domainId = self::getCurrentBeDomainId(false);
        if ($domainId) {
            return $baseKey . '--' . $domainId;
        }
        return $baseKey;
    }

    /**
     * Gibt Konfig-Wert für die aktuelle Domain zurück, mit optionalem Fallback
     * auf die globale Einstellung (für Dark-Mode-Varianten sinnvoll).
     */
    public static function getDomainConfig(string $baseKey, bool $escape = false, bool $fallbackToGlobal = true): string
    {
        $key = self::getKey($baseKey);
        $value = (string) rex_addon::get('be_branding')->getConfig($key, '');

        // Fallback auf globale Einstellung, wenn Domainprofil-Wert leer
        if ($value === '' && $fallbackToGlobal) {
            $value = (string) rex_addon::get('be_branding')->getConfig($baseKey, '');
        }

        return $escape ? rex_escape($value) : $value;
    }

    // ─────────────────────────────────────────────────────────────
    // Dark Mode
    // ─────────────────────────────────────────────────────────────

    /**
     * Gibt den richtigen Config-Wert zurück, je nach aktivem Farbschema.
     * Dark-Mode-Variante wird unter dem Schlüssel "{key}_dark" gespeichert.
     * Fällt auf den normalen Wert zurück, wenn kein Dark-Wert gesetzt ist.
     */
    public static function getColorForScheme(string $baseKey, string $scheme = 'light'): string
    {
        if ($scheme === 'dark') {
            $darkKey = $baseKey . '_dark';
            $darkValue = self::getDomainConfig($darkKey);
            if ($darkValue !== '') {
                return $darkValue;
            }
        }
        return self::getDomainConfig($baseKey);
    }

    // ─────────────────────────────────────────────────────────────
    // CSS-Ausgabe (aus boot.php ausgelagert)
    // ─────────────────────────────────────────────────────────────

    /**
     * Erzeugt das komplette CSS für Header, Login und Watson.
     * Gibt einen <style>…</style>-Block zurück, bereit für den Output-Filter.
     */
    public static function buildHeaderCss(): string
    {
        $addon = rex_addon::get('be_branding');
        $color1 = self::getDomainConfig('color1');
        $color2 = self::getDomainConfig('color2');

        if (!$color1 && !$color2) {
            return '';
        }

        $color1e = rex_escape($color1);
        $color2e = rex_escape($color2);

        $css = "
.rex-nav-top .navbar { background-color: {$color1e} !important; }

.rex-redaxo-logo path.rex-redaxo-logo-r,
.rex-redaxo-logo path.rex-redaxo-logo-e,
.rex-redaxo-logo path.rex-redaxo-logo-d,
.rex-redaxo-logo path.rex-redaxo-logo-cms { fill: {$color2e} !important; }

.rex-redaxo-logo path.rex-redaxo-logo-a,
.rex-redaxo-logo path.rex-redaxo-logo-x,
.rex-redaxo-logo path.rex-redaxo-logo-o,
.rex-redaxo-logo path.rex-redaxo-logo-reg { fill: #fff !important; }

.rex-nav-meta .text-muted { color: {$color2e} !important; }
";

        // Watson-Integration
        if (rex_addon::get('watson')->isAvailable()) {
            $css .= "
.watson-btn svg { color: {$color2e}; padding-top: 8px; }
";
        }

        // Dark Mode: alternative Farben per prefers-color-scheme
        $color1Dark = self::getColorForScheme('color1', 'dark');
        $color2Dark = self::getColorForScheme('color2', 'dark');
        if ($color1Dark !== $color1 || $color2Dark !== $color2) {
            $c1d = rex_escape($color1Dark);
            $c2d = rex_escape($color2Dark);
            $css .= "
@media (prefers-color-scheme: dark) {
    .rex-nav-top .navbar { background-color: {$c1d} !important; }
    .rex-redaxo-logo path.rex-redaxo-logo-r,
    .rex-redaxo-logo path.rex-redaxo-logo-e,
    .rex-redaxo-logo path.rex-redaxo-logo-d,
    .rex-redaxo-logo path.rex-redaxo-logo-cms { fill: {$c2d} !important; }
    .rex-nav-meta .text-muted { color: {$c2d} !important; }
}
";
        }

        $css .= self::buildLoginCss($color1, $color2);

        // Custom CSS
        $customCss = self::getDomainConfig('custom_css');
        if ($customCss !== '') {
            $css .= "\n/* be_branding custom CSS */\n" . $customCss . "\n";
        }

        return '<style>' . $css . '</style></head>';
    }

    /**
     * Erzeugt das Login-Screen-CSS.
     * Eigene Methode, damit buildHeaderCss() übersichtlich bleibt.
     */
    private static function buildLoginCss(string $color1, string $color2): string
    {
        $color1e = rex_escape($color1);
        $color2e = rex_escape($color2);

        $setting = self::getDomainConfig('login_bg_setting');

        // Panel-Hintergrund: leicht transparentes color1
        $panelBg = preg_replace('/,\s*[\d.]+\s*\)$/', ', 0.8)', $color1);
        $panelBge = rex_escape($panelBg);

        // Login-Hintergrundfarbe je nach Einstellung
        $loginBgCss = "#rex-page-login { background-color: {$color2e} !important; }";

        switch ($setting) {
            case 'primary_bg':
                $loginBgCss  = "#rex-page-login { background-color: {$color1e} !important; }";
                $panelBge    = rex_escape('rgba(255,255,255, 0.4)');
                break;

            case 'secondary_bg':
                $loginBgCss  = "#rex-page-login { background-color: {$color2e} !important; }";
                break;

            case 'gradient_bg':
                // Bugfix v1: fehlende Klammer beim gradient korrigiert
                $hex1 = rex_escape(self::rgba2hex($color1));
                $hex2 = rex_escape(self::rgba2hex($color2));
                $loginBgCss = "
#rex-page-login {
    background: {$color2e};
    background: -moz-linear-gradient(71deg, {$color1e} 0%, {$color2e} 100%);
    background: -webkit-linear-gradient(71deg, {$color1e} 0%, {$color2e} 100%);
    background: linear-gradient(71deg, {$color1e} 0%, {$color2e} 100%);
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\"{$hex1}\",endColorstr=\"{$hex2}\",GradientType=1);
}";
                break;

            case 'own_bg':
                $loginBgCss  = "#rex-page-login { background-color: {$color2e} !important; }";
                $loginBg     = self::getDomainConfig('login_bg');
                if ($loginBg) {
                    $bgUrl = rex_escape(rex_media_manager::getUrl('be_branding_login_2100_jpg', $loginBg));
                    $loginBgCss .= "
@media (max-width: 991px) {
    #rex-page-login {
        background-image: url(\"{$bgUrl}\");
        background-size: cover;
    }
}";
                }
                break;

            case 'redaxo_standard_bg':
            default:
                $loginBgCss  = "#rex-page-login { background-color: {$color2e} !important; }";
                break;
        }

        return "
{$loginBgCss}

#rex-form-login .rex-redaxo-logo path.rex-redaxo-logo-r,
#rex-form-login .rex-redaxo-logo path.rex-redaxo-logo-e,
#rex-form-login .rex-redaxo-logo path.rex-redaxo-logo-d,
#rex-form-login .rex-redaxo-logo path.rex-redaxo-logo-cms { fill: {$color2e} !important; }

#rex-page-login .panel-default {
    background-color: {$panelBge};
    border: 0;
    color: {$color2e};
    border-radius: 5px;
}

#rex-page-login .form-control {
    background-color: {$panelBge} !important;
    color: #fff;
    border: none !important;
    box-shadow: none;
}

#rex-page-login .input-group-addon,
#rex-page-login .input-group-btn .btn-view {
    background-color: {$panelBge} !important;
    color: {$color2e} !important;
    border: none !important;
}

#rex-page-login .btn-primary {
    background-color: {$color2e} !important;
    color: {$color1e} !important;
    border-color: {$color1e} !important;
}

#rex-page-login .rex-page-main:before {
    border-color: {$color1e} transparent transparent transparent !important;
    top: -1px !important;
}
";
    }

    // ─────────────────────────────────────────────────────────────
    // Domain-Handling (gecacht)
    // ─────────────────────────────────────────────────────────────

    /**
     * Gibt die Domain-ID für den aktuellen HTTP_HOST zurück.
     * Ergebnis wird für den Request gecacht (kein wiederholter DB-Hit).
     *
     * @param bool $withPostfix  true → '--3', false → 3, false (kein Profil) → false
     */
    public static function getCurrentBeDomainId(bool $withPostfix = false)
    {
        if (!rex_addon::get('be_branding')->getConfig('domainprofiles_enabled')) {
            return false;
        }

        if (self::$cachedDomainId === null) {
            // HTTP_HOST validieren (nur Hostnamen und IPs erlaubt)
            $host = $_SERVER['HTTP_HOST'] ?? '';
            if (!preg_match('/^[a-zA-Z0-9.\-:\[\]]+$/', $host)) {
                self::$cachedDomainId = false;
                return false;
            }

            $sql = rex_sql::factory();
            $sql->setDebug(false);
            $sql->setQuery(
                'SELECT id FROM rex_yrewrite_domain WHERE domain LIKE :domain LIMIT 1',
                ['domain' => '%' . $host . '%']
            );
            self::$cachedDomainId = $sql->getRows() ? (int) $sql->getValue('id') : false;
        }

        if (self::$cachedDomainId === false) {
            return false;
        }

        return $withPostfix ? '--' . self::$cachedDomainId : self::$cachedDomainId;
    }

    public static function getDomainById($id): array|false
    {
        if (!$id) {
            return false;
        }
        $sql = rex_sql::factory();
        $sql->setDebug(false);
        $sql->setQuery('SELECT * FROM rex_yrewrite_domain WHERE id = :id', ['id' => (int)$id]);
        $rows = $sql->getArray();
        return $rows[0] ?? false;
    }

    // ─────────────────────────────────────────────────────────────
    // Medien & Dateien
    // ─────────────────────────────────────────────────────────────

    /**
     * Gibt die korrekte URL für eine Mediendatei zurück.
     * Unterstützt: jpg, jpeg, png, gif, webp, avif, svg
     */
    public static function checkExtension(string $filename): string
    {
        if (!$filename) {
            return '';
        }
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $vectorTypes  = ['svg'];
        $rasterTypes  = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];

        if (in_array($ext, $rasterTypes, true)) {
            return rex_url::frontend('index.php?rex_media_type=rex_media_medium&rex_media_file=' . $filename);
        }
        if (in_array($ext, $vectorTypes, true)) {
            return rex_url::frontend('media/' . $filename);
        }
        // Fallback
        return rex_url::frontend('media/' . $filename);
    }

    // ─────────────────────────────────────────────────────────────
    // Favicon
    // ─────────────────────────────────────────────────────────────

    /**
     * Generiert ein invertiertes Backend-Favicon als SVG:
     * farbige quadratische Fläche mit dem REDAXO-R in Weiß.
     *
     * Kein Imagick, kein GD nötig – reines SVG.
     * Wird beim Speichern der Branding-Einstellungen aufgerufen.
     *
     * Gespeichert unter: assets/addons/be_branding/favicon/favicon-inverted-{hex}.svg
     *
     * @param  string $hex       Hintergrundfarbe als #rrggbb
     * @param  string $domainSuffix  z.B. '--3' oder ''
     * @return string|false      Pfad zur generierten Datei oder false bei Fehler
     */
    /**
     * Generiert ein invertiertes Backend-Favicon als SVG:
     * farbige quadratische Fläche mit dem REDAXO-R (weißes statisches Asset).
     *
     * Kein Imagick, kein GD, kein SVG-Parsing – das R kommt als fertiges
     * Asset aus assets/img/favicon-r.svg und wird via <image> eingebettet.
     *
     * Gespeichert unter: assets/addons/be_branding/favicon/favicon-inverted-{hex}{suffix}.svg
     *
     * @param  string $hex          Hintergrundfarbe als #rrggbb
     * @param  string $domainSuffix z.B. '--3' oder ''
     * @return string|false         Pfad zur generierten Datei oder false bei Fehler
     */
    public static function generateInvertedFavicon(string $hex, string $domainSuffix = ''): string|false
    {
        if (!$hex || !preg_match('/^#[0-9a-fA-F]{6}$/', $hex)) {
            return false;
        }

        $hexEsc = htmlspecialchars($hex, ENT_XML1);

        // Absoluter URL zum weißen R-Asset (muss vom Browser aufrufbar sein)
        $redaxoR = '<path fill="#ffffff" d="M47.928,0.575L7.693,0.592L0,77.033h19.478l2.332-23.125h18.772l10.255,23.125h21.2L60.487,50.99 c10.598-5.783,14.229-13.92,15.102-23.823C76.695,14.604,66.008,0.575,47.928,0.575z M46.456,34.529H23.762l1.477-14.653 c8.459,0.043,19.314,0.102,23.173,0.102c4.981,0,7.741,3.41,7.741,6.984C56.153,31.677,52.171,34.529,46.456,34.529z"/>';
        $svg = <<<SVG
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100">
                  <rect width="100" height="100" rx="16" fill="{$hexEsc}"/>
                      <g transform="translate(50,60) scale(0.95) translate(-35,-50)">
                        {$redaxoR}
                      </g>
                </svg>
                SVG;

        $filename  = 'favicon-inverted-' . ltrim($hex, '#') . ($domainSuffix ?: '') . '.svg';
        $targetDir = rex_path::addonAssets('be_branding', 'favicon/');

        if (!is_dir($targetDir)) {
            rex_dir::create($targetDir);
        }

        $targetPath = $targetDir . $filename;
        if (file_put_contents($targetPath, $svg) === false) {
            return false;
        }

        return $targetPath;
    }

    public static function makeFavIcon(string $hexColor, string $path): void
    {
        $rgbColor = self::hex2rgb($hexColor);
        $favIconOriginal = $path . 'favicon-original.png';
        $favIconNew = rex_path::addonAssets('be_branding') . 'favicon/favicon-original-' . ltrim($hexColor, '#') . '.png';

        $im = imagecreatefrompng($favIconOriginal);
        if (!$im) {
            return;
        }
        imagealphablending($im, false);
        imagesavealpha($im, true);

        if (imagefilter($im, IMG_FILTER_COLORIZE, $rgbColor[0], $rgbColor[1], $rgbColor[2], 0)) {
            imagepng($im, $favIconNew);
        }
        imagedestroy($im);
    }

    public static function getFrontendFavicons(int $domainId): string
    {
        return fe_favicon::getHtml(
            self::rgba2hex((string) rex_addon::get('be_branding')->getConfig('fe_favicon_tilecolor_' . $domainId)),
            $domainId
        );
    }

    // ─────────────────────────────────────────────────────────────
    // Export / Import
    // ─────────────────────────────────────────────────────────────

    /**
     * Exportiert die komplette Addon-Konfiguration als JSON-String.
     * Enthält Metadaten für die Migrations-Kompatibilitätsprüfung.
     */
    public static function exportConfig(): string
    {
        $addon = rex_addon::get('be_branding');
        return json_encode([
            '_export_version' => '2.0',
            '_export_date'    => date('Y-m-d H:i:s'),
            '_addon_version'  => $addon->getVersion(),
            'config'          => $addon->getConfig(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Importiert eine JSON-Konfiguration.
     * Gibt ein Array mit 'success' (bool) und 'message' (string) zurück.
     *
     * @param string $json  Roher JSON-String
     * @return array{success: bool, message: string}
     */
    public static function importConfig(string $json): array
    {
        $data = json_decode($json, true);
        if (!is_array($data)) {
            return ['success' => false, 'message' => 'Ungültiges JSON-Format.'];
        }
        if (!isset($data['config']) || !is_array($data['config'])) {
            return ['success' => false, 'message' => 'Kein gültiger "config"-Block gefunden.'];
        }

        $exportVersion = $data['_export_version'] ?? '1.0';
        $config = $data['config'];

        // Migration: v1-Configs haben keinen _export_version-Key
        if ($exportVersion === '1.0' || !isset($data['_export_version'])) {
            $config = self::migrateConfigV1toV2($config);
        }

        $addon = rex_addon::get('be_branding');
        foreach ($config as $key => $value) {
            $addon->setConfig($key, $value);
        }

        return [
            'success' => true,
            'message' => 'Konfiguration erfolgreich importiert'
                . ($exportVersion !== '2.0' ? ' (automatisch von v1 migriert)' : '') . '.',
        ];
    }

    // ─────────────────────────────────────────────────────────────
    // Migration v1 → v2
    // ─────────────────────────────────────────────────────────────

    /**
     * Migriert eine v1-Konfiguration auf das v2-Format.
     * v1 speicherte Domain-Postfix als '--{id}' direkt im Key.
     * Das bleibt in v2 kompatibel – diese Methode normalisiert nur.
     */
    public static function migrateConfigV1toV2(array $config): array
    {
        // In v2 gibt es zusätzlich dark-Mode-Keys, die v1 nicht hatte.
        // Die bestehenden Schlüssel bleiben identisch → keine Umbenennung nötig.
        // Diese Methode kann in Zukunft für tiefere Migrationen genutzt werden.
        return $config;
    }

    /**
     * Prüft, ob eine Migration von v1 auf v2 notwendig ist,
     * und führt sie automatisch aus. Gibt ein Log-Array zurück.
     *
     * @return string[]  Log-Einträge für das Migrations-Protokoll
     */
    public static function runMigrationIfNeeded(): array
    {
        $addon = rex_addon::get('be_branding');
        $log   = [];

        // Nur einmal ausführen
        if ($addon->getConfig('_migrated_to_v2')) {
            return [];
        }

        // Prüfe ob v1-Daten vorhanden (haben keinen _migrated_to_v2-Key)
        $existingConfig = $addon->getConfig();
        if (!is_array($existingConfig) || empty($existingConfig)) {
            // Frische Installation – keine Migration nötig
            $addon->setConfig('_migrated_to_v2', true);
            return [];
        }

        $log[] = date('Y-m-d H:i:s') . ' – Migration v1 → v2 gestartet.';

        // Sicherstellen dass neue Keys mit Defaultwerten existieren
        $newDefaults = [
            'color1_dark'       => '',
            'color2_dark'       => '',
            'custom_css'        => '',
            'be_favicon_setting'=> '',
            'be_favicon_invert' => 0,
        ];
        foreach ($newDefaults as $key => $default) {
            if ($addon->getConfig($key) === null) {
                $addon->setConfig($key, $default);
                $log[] = "  Key '{$key}' mit Standardwert angelegt.";
            }
        }

        $addon->setConfig('_migrated_to_v2', true);
        $log[] = date('Y-m-d H:i:s') . ' – Migration abgeschlossen.';

        return $log;
    }

} // EoC be_branding
