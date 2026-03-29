<?php

require_once __DIR__ . '/lib/be_branding_setup.php';

// Neue AVIF-Mediatypen und etwaige neue Typen anlegen (idempotent)
be_branding_setup();

// Migration-Flag zurücksetzen, damit boot.php die Migration beim nächsten
// Backend-Aufruf erneut prüft und ggf. neue Keys anlegt
rex_addon::get('be_branding')->setConfig('_migrated_to_v2', false);
