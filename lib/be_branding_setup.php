<?php

/**
 * be_branding – gemeinsame Installations-/Update-Logik
 *
 * Wird von install.php und update.php eingebunden.
 * Idempotent: kann beliebig oft aufgerufen werden.
 */
function be_branding_setup(): void
{
    $sql    = rex_sql::factory();
    $prefix = rex::getTablePrefix();

    // ─────────────────────────────────────────────────────────────
    // Media-Manager-Typen anlegen (je nur wenn nicht vorhanden)
    // ─────────────────────────────────────────────────────────────
    $mediaTypes = [
        'be_branding_login_2100_webp' => ['desc' => 'Login-BG 2100w WebP (be_branding)',  'resize' => 2100, 'format' => 'webp'],
        'be_branding_login_3300_webp' => ['desc' => 'Login-BG 3300w WebP (be_branding)',  'resize' => 3300, 'format' => 'webp'],
        'be_branding_login_2100_jpg'  => ['desc' => 'Login-BG 2100w JPG (be_branding)',   'resize' => 2100, 'format' => 'jpg'],
        'be_branding_login_3300_jpg'  => ['desc' => 'Login-BG 3300w JPG (be_branding)',   'resize' => 3300, 'format' => 'jpg'],
        'be_branding_login_2100_avif' => ['desc' => 'Login-BG 2100w AVIF (be_branding)',  'resize' => 2100, 'format' => 'avif'],
        'be_branding_login_3300_avif' => ['desc' => 'Login-BG 3300w AVIF (be_branding)',  'resize' => 3300, 'format' => 'avif'],
    ];

    foreach ($mediaTypes as $name => $opts) {
        $sql->setQuery(
            "SELECT id FROM {$prefix}media_manager_type WHERE name = :name",
            ['name' => $name]
        );
        if ($sql->getRows() > 0) {
            continue; // bereits vorhanden
        }

        // Typ anlegen
        $sql->setQuery(
            "INSERT INTO {$prefix}media_manager_type (status, name, description) VALUES (0, :name, :desc)",
            ['name' => $name, 'desc' => $opts['desc']]
        );
        $typeId = (int) $sql->getLastId();

        // Effekt 1: resize
        $resizeParams = be_branding_effectParams('resize', $opts['resize']);
        $sql->setQuery(
            "INSERT INTO {$prefix}media_manager_type_effect
             (type_id, effect, parameters, priority, createdate, createuser)
             VALUES (:tid, 'resize', :params, 1, CURRENT_TIMESTAMP, 'be_branding')",
            ['tid' => $typeId, 'params' => $resizeParams]
        );

        // Effekt 2: image_format (konvertiert in gewünschtes Format)
        $formatParams = be_branding_effectParams('image_format', 0, $opts['format']);
        $sql->setQuery(
            "INSERT INTO {$prefix}media_manager_type_effect
             (type_id, effect, parameters, priority, createdate, createuser)
             VALUES (:tid, 'image_format', :params, 2, CURRENT_TIMESTAMP, 'be_branding')",
            ['tid' => $typeId, 'params' => $formatParams]
        );
    }

    // Template-Cache leeren
    rex_dir::delete(rex_path::addonCache('templates'), false);
}

/**
 * Erzeugt den JSON-Parameter-String für einen Media-Manager-Effekt.
 */
function be_branding_effectParams(string $effect, int $resizeWidth = 0, string $format = 'webp'): string
{
    $base = [
        'rex_effect_rounded_corners' => ['rex_effect_rounded_corners_topleft' => '', 'rex_effect_rounded_corners_topright' => '', 'rex_effect_rounded_corners_bottomleft' => '', 'rex_effect_rounded_corners_bottomright' => ''],
        'rex_effect_workspace'       => ['rex_effect_workspace_width' => '', 'rex_effect_workspace_height' => '', 'rex_effect_workspace_hpos' => 'left', 'rex_effect_workspace_vpos' => 'top', 'rex_effect_workspace_set_transparent' => 'colored', 'rex_effect_workspace_bg_r' => '', 'rex_effect_workspace_bg_g' => '', 'rex_effect_workspace_bg_b' => ''],
        'rex_effect_resize'          => ['rex_effect_resize_width' => $resizeWidth ?: '', 'rex_effect_resize_height' => '', 'rex_effect_resize_style' => $resizeWidth ? 'minimum' : 'maximum', 'rex_effect_resize_allow_enlarge' => 'enlarge'],
        'rex_effect_filter_blur'     => ['rex_effect_filter_blur_repeats' => '10', 'rex_effect_filter_blur_type' => 'gaussian', 'rex_effect_filter_blur_smoothit' => ''],
        'rex_effect_filter_sharpen'  => ['rex_effect_filter_sharpen_amount' => '80', 'rex_effect_filter_sharpen_radius' => '0.5', 'rex_effect_filter_sharpen_threshold' => '3'],
        'rex_effect_image_format'    => ['rex_effect_image_format_convert_to' => $format],
        'rex_effect_header'          => ['rex_effect_header_download' => 'open_media', 'rex_effect_header_cache' => 'no_cache', 'rex_effect_header_filename' => 'filename'],
    ];

    return json_encode($base);
}

/**
 * Räumt alle be_branding-Media-Manager-Typen beim Deinstallieren auf.
 */
function be_branding_teardown(): void
{
    $sql    = rex_sql::factory();
    $prefix = rex::getTablePrefix();

    $sql->setQuery(
        "SELECT id FROM {$prefix}media_manager_type WHERE name LIKE 'be_branding_%'"
    );
    foreach ($sql->getArray() as $row) {
        $tid = (int) $row['id'];
        $sql->setQuery("DELETE FROM {$prefix}media_manager_type_effect WHERE type_id = :tid", ['tid' => $tid]);
        $sql->setQuery("DELETE FROM {$prefix}media_manager_type WHERE id = :tid", ['tid' => $tid]);
    }
}
