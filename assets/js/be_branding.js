/**
 * be_branding v2 – Backend JavaScript
 *
 * - Live-Farbvorschau (Primär-/Sekundärfarbe, Dark Mode)
 * - Hex ↔ rgba Konvertierung im Colorpicker
 * - Favicon-Vorschau nach Farb-Eingabe
 * - Login-Hintergrund-Auswahl (show/hide)
 */

(function ($) {
    'use strict';

    // ─────────────────────────────────────────────────────────────
    // Farb-Hilfsfunktionen
    // ─────────────────────────────────────────────────────────────

    /**
     * rgba(r,g,b,a) → #rrggbb
     */
    function rgba2hex(rgba) {
        var match = rgba.match(/rgba?\(\s*([\d.]+)\s*,\s*([\d.]+)\s*,\s*([\d.]+)/i);
        if (!match) return rgba;
        return '#' + [match[1], match[2], match[3]].map(function (v) {
            return ('0' + parseInt(v, 10).toString(16)).slice(-2);
        }).join('');
    }

    /**
     * #rrggbb oder #rgb → rgba(r,g,b,1)
     */
    function hex2rgba(hex, alpha) {
        alpha = alpha !== undefined ? alpha : 1;
        hex = hex.replace('#', '');
        if (hex.length === 3) {
            hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
        }
        var r = parseInt(hex.substring(0, 2), 16);
        var g = parseInt(hex.substring(2, 4), 16);
        var b = parseInt(hex.substring(4, 6), 16);
        return 'rgba(' + r + ', ' + g + ', ' + b + ', ' + alpha + ')';
    }

    /**
     * Normalisiert eine Farbeingabe auf rgba(…).
     * Akzeptiert: rgba(…), rgb(…), #rrggbb, #rgb
     */
    function normalizeColor(input) {
        input = $.trim(input);
        if (!input) return '';
        if (/^rgba?\(/.test(input)) return input;
        if (/^#[0-9a-fA-F]{3,6}$/.test(input)) return hex2rgba(input);
        return input;
    }

    // ─────────────────────────────────────────────────────────────
    // Live-Vorschau
    // ─────────────────────────────────────────────────────────────

    var $previewStyle = null;

    function getOrCreatePreviewStyle() {
        if (!$previewStyle || $previewStyle.length === 0) {
            $previewStyle = $('<style id="be-branding-preview-css"></style>').appendTo('head');
        }
        return $previewStyle;
    }

    function applyLivePreview() {
        var color1 = normalizeColor($('#be_branding-config-color1').val());
        var color2 = normalizeColor($('#be_branding-config-color2').val());

        if (!color1 && !color2) {
            getOrCreatePreviewStyle().text('');
            return;
        }

        var css = '';
        if (color1) {
            css += '.rex-nav-top .navbar { background-color: ' + color1 + ' !important; }\n';
        }
        if (color2) {
            css += '.rex-redaxo-logo path.rex-redaxo-logo-r,'
                 + '.rex-redaxo-logo path.rex-redaxo-logo-e,'
                 + '.rex-redaxo-logo path.rex-redaxo-logo-d,'
                 + '.rex-redaxo-logo path.rex-redaxo-logo-cms { fill: ' + color2 + ' !important; }\n';
            css += '.rex-nav-meta .text-muted { color: ' + color2 + ' !important; }\n';
        }

        getOrCreatePreviewStyle().text(css);
    }

    function updateFaviconPreview(color) {
        var $preview = $('#be-branding-favicon-preview');
        if (!$preview.length) return;

        var hex = /^#/.test(color) ? color : rgba2hex(color);
        // Zeichnet ein minimales 16x16-Favicon-Preview im Canvas
        var canvas = document.createElement('canvas');
        canvas.width = canvas.height = 32;
        var ctx = canvas.getContext('2d');
        ctx.fillStyle = hex || '#cccccc';
        ctx.fillRect(0, 0, 32, 32);
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 20px sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('R', 16, 17);
        $preview.attr('src', canvas.toDataURL());
    }

    // ─────────────────────────────────────────────────────────────
    // Hex-Eingabe normalisieren
    // ─────────────────────────────────────────────────────────────

    function setupHexConversion($input) {
        $input.on('blur', function () {
            var val = $.trim($(this).val());
            if (/^#[0-9a-fA-F]{3,6}$/.test(val)) {
                $(this).val(hex2rgba(val));
            }
        });
    }

    // ─────────────────────────────────────────────────────────────
    // Login-BG Toggle (show/hide Medienpool-Auswahl)
    // ─────────────────────────────────────────────────────────────

    function setupLoginBgToggle(selectId, settingId) {
        var $select  = $(selectId);
        var $setting = $(settingId);

        function update(animate) {
            if ($select.val() === 'own_bg') {
                animate ? $setting.show('fast') : $setting.show();
            } else {
                animate ? $setting.hide('fast') : $setting.hide();
            }
        }

        update(false);
        $select.on('change', function () { update(true); });
    }

    // ─────────────────────────────────────────────────────────────
    // Initialisierung
    // ─────────────────────────────────────────────────────────────

    $(document).on('rex:ready', function () {

        // Live-Vorschau an alle Farbfelder binden
        var $colorInputs = $('.be-branding-color-input');
        $colorInputs.on('input change', function () {
            applyLivePreview();
            var color1 = normalizeColor($('#be_branding-config-color1').val());
            if (color1) updateFaviconPreview(color1);
        });

        // Einmalig beim Laden vorschauen
        applyLivePreview();

        // Hex-Eingabe normalisieren
        $colorInputs.each(function () {
            setupHexConversion($(this));
        });

        // Login-BG Toggle (einfaches Profil)
        if ($('#be-branding-login-bg-option').length) {
            setupLoginBgToggle('#be-branding-login-bg-option', '#be-branding-login-bg-setting');
        }

        // Login-BG Toggle (Domainprofile – IDs werden per data-Attribut übergeben)
        $('[data-be-branding-domain-id]').each(function () {
            var id = $(this).data('be-branding-domain-id');
            setupLoginBgToggle(
                '#be-branding-login-bg-option--' + id,
                '#be-branding-login-bg-setting--' + id
            );
        });

        // Initialen Favicon-Preview
        var color1 = normalizeColor($('#be_branding-config-color1').val());
        if (color1) updateFaviconPreview(color1);

        // Export-Button: JSON-Download auslösen
        $('#be-branding-export-btn').on('click', function (e) {
            e.preventDefault();
            var $form = $('#be-branding-export-form');
            $form.attr('action', $form.attr('action') + '&export=1').submit();
            $form.attr('action', $form.attr('action').replace('&export=1', ''));
        });

    });

}(jQuery));
