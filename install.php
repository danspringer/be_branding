<?php

require_once __DIR__ . '/lib/be_branding_setup.php';

// Alte v1-Einstellungsdatei aufräumen (fe_favicon)
if (file_exists(rex_path::addonAssets('be_branding', 'fe_favicon/.settings'))) {
    unlink(rex_path::addonAssets('be_branding', 'fe_favicon/.settings'));
}

if (is_dir(rex_path::frontend('favicon'))) {
    rex_dir::delete(rex_path::frontend('favicon'));
}

be_branding_setup();
