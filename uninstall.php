<?php

require_once __DIR__ . '/lib/be_branding_setup.php';

// Media-Manager-Typen entfernen
be_branding_teardown();

// Generierte Favicons löschen
$faviconDir = rex_path::addonAssets('be_branding', 'favicon/');
foreach (glob($faviconDir . '*') ?: [] as $file) {
    if (is_file($file)) {
        unlink($file);
    }
}

// fe_favicon-Einstellungen löschen
$feSettings = rex_path::addonAssets('be_branding', 'fe_favicon/.settings');
if (file_exists($feSettings)) {
    unlink($feSettings);
}
