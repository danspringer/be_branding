/**
 * be_branding v2 – Backend JavaScript
 *
 * - Live-Farbvorschau (Primär-/Sekundärfarbe, Dark Mode)
 * - Hex ↔ rgba Konvertierung im Colorpicker
 * - Favicon-Vorschau nach Farb-Eingabe (inkl. Invertierung)
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
        var color1         = normalizeColor($('#be_branding-config-color1').val());
        var color2         = normalizeColor($('#be_branding-config-color2').val());
        var topBorderColor = normalizeColor($('#be_branding-config-border-color').val());

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
        if (topBorderColor) {
            css += '#be_branding-top-border { background-color: ' + topBorderColor + ' !important; }\n';
        }

        getOrCreatePreviewStyle().text(css);
    }

    /**
     * Zeichnet die Favicon-Vorschau im Canvas.
     * Berücksichtigt die Invertierungs-Checkbox:
     *   normal    → transparenter Hintergrund, farbiges R
     *   invertiert → farbige Fläche, weißes R
     */
        // URL zum weißen R-Asset (relativ zu den Addon-Assets)
    var FAVICON_R_URL = (rex && rex.base_url ? rex.base_url : '/') + 'assets/addons/be_branding/img/favicon-r.svg';

    // Gecachtes Image-Objekt damit wir nicht bei jedem Update neu laden
    var _rImage      = null;
    var _rImageReady = false;

    function loadRImage(callback) {
        if (_rImageReady) {
            callback(_rImage);
            return;
        }
        var img = new Image();
        img.onload = function () {
            _rImage      = img;
            _rImageReady = true;
            callback(img);
        };
        img.onerror = function () {
            callback(null); // Fallback: kein R-Bild
        };
        img.src = FAVICON_R_URL;
    }

    /**
     * Rendert das Favicon-Canvas mit dem echten REDAXO-R aus den Assets,
     * setzt es als Preview-Bild und bindet es live als Tab-Favicon ein.
     */
    function updateFaviconPreview() {
        var $select  = $('[name^="config[be_favicon_setting"]').first();
        var setting  = $select.val();
        var inverted = $('[name^="config[be_favicon_invert"]').is(':checked');

        var colorRaw = '';
        if (setting === 'primary') {
            colorRaw = normalizeColor($('#be_branding-config-color1').val());
        } else if (setting === 'secondary') {
            colorRaw = normalizeColor($('#be_branding-config-color2').val());
        }

        loadRImage(function (rImg) {
            _renderFaviconCanvas(setting, colorRaw, inverted, rImg);
        });
    }

    function _renderFaviconCanvas(setting, colorRaw, inverted, rImg) {
        var size   = 64;
        var canvas = document.createElement('canvas');
        canvas.width = canvas.height = size;
        var ctx  = canvas.getContext('2d');
        var half = size / 2;

        if (colorRaw && setting) {
            var hex = /^#/.test(colorRaw) ? colorRaw : rgba2hex(colorRaw);

            if (inverted) {
                // Farbige Fläche mit abgerundeten Ecken
                var r = size * 0.18;
                ctx.beginPath();
                ctx.moveTo(r, 0);
                ctx.lineTo(size - r, 0);
                ctx.quadraticCurveTo(size, 0, size, r);
                ctx.lineTo(size, size - r);
                ctx.quadraticCurveTo(size, size, size - r, size);
                ctx.lineTo(r, size);
                ctx.quadraticCurveTo(0, size, 0, size - r);
                ctx.lineTo(0, r);
                ctx.quadraticCurveTo(0, 0, r, 0);
                ctx.closePath();
                ctx.fillStyle = hex;
                ctx.fill();

                // Weißes R-Asset drüberlegen (mit Padding)
                if (rImg) {
                    var pad = size * 0.14;
                    ctx.drawImage(rImg, pad, pad, size - pad * 2, size - pad * 2);
                }

            } else {
                // Schachbrett-Hintergrund (signalisiert Transparenz)
                ctx.fillStyle = '#f0f0f0';
                ctx.fillRect(0, 0, half, half);
                ctx.fillRect(half, half, half, half);
                ctx.fillStyle = '#e0e0e0';
                ctx.fillRect(half, 0, half, half);
                ctx.fillRect(0, half, half, half);

                // Farbiges R: R-Asset tonen via globalCompositeOperation
                if (rImg) {
                    // Temporäres Canvas: R erst in Zielfarbe einfärben
                    var tmp    = document.createElement('canvas');
                    tmp.width  = tmp.height = size;
                    var tctx   = tmp.getContext('2d');
                    tctx.drawImage(rImg, 0, 0, size, size);
                    tctx.globalCompositeOperation = 'source-in';
                    tctx.fillStyle = hex;
                    tctx.fillRect(0, 0, size, size);
                    ctx.drawImage(tmp, 0, 0);
                }
            }

        } else {
            // REDAXO-Standard: originales Core-Favicon als Preview setzen,
            // kein Canvas nötig
            var $preview = $('#be-branding-favicon-preview');
            if ($preview.length) {
                var coreFavicon = (rex && rex.base_url ? rex.base_url : '/') + 'assets/addons/be_style/plugins/redaxo/icons/favicon-32x32.png';
                $preview.attr('src', coreFavicon);
            }
            return; // früh raus, kein Tab-Favicon überschreiben
        }

        var dataUrl = canvas.toDataURL('image/png');

        // ── Preview-Bild aktualisieren ──
        var $preview = $('#be-branding-favicon-preview');
        if ($preview.length) {
            $preview.attr('src', dataUrl);
        }

        // ── Tab-Favicon live setzen ──
        if (colorRaw && setting) {
            if (inverted) {
                // Invertiert: serverseitig generiertes SVG bevorzugen (exaktes REDAXO-R)
                var hexNoHash = (/^#/.test(colorRaw) ? colorRaw : rgba2hex(colorRaw)).replace('#', '');
                var svgUrl    = (rex && rex.base_url ? rex.base_url : '/') + 'assets/addons/be_branding/favicon/favicon-inverted-' + hexNoHash + '.svg';
                var $link     = $('link[rel="icon"][type="image/svg+xml"]');
                if (!$link.length) {
                    $link = $('<link rel="icon" type="image/svg+xml">').appendTo('head');
                }
                $.ajax({ url: svgUrl, type: 'HEAD' })
                    .done(function () { $link.attr('href', svgUrl + '?t=' + Date.now()); })
                    .fail(function () {
                        $link.remove();
                        setFaviconDataUrl(dataUrl);
                    });
            } else {
                $('link[type="image/svg+xml"]').remove();
                setFaviconDataUrl(dataUrl);
            }
        }
    }

    function setFaviconDataUrl(dataUrl) {
        var $existing = $('link[rel~="icon"]:not([type="image/svg+xml"])');
        if ($existing.length) {
            $existing.first().attr('href', dataUrl);
        } else {
            $('<link rel="icon" type="image/png">').attr('href', dataUrl).appendTo('head');
        }
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

        if (!$select.length || !$setting.length) return;

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
            updateFaviconPreview();
        });

        // Einmalig beim Laden vorschauen
        applyLivePreview();
        updateFaviconPreview();

        // Favicon-Vorschau auch bei Änderung von Select und Invertierungs-Checkbox
        $(document).on('change', '[name^="config[be_favicon_setting"]', updateFaviconPreview);
        $(document).on('change', '[name^="config[be_favicon_invert"]',  updateFaviconPreview);

        // Hex-Eingabe normalisieren
        $colorInputs.each(function () {
            setupHexConversion($(this));
        });

        // Login-BG Toggle (einfaches Profil)
        setupLoginBgToggle('#be-branding-login-bg-option', '#be-branding-login-bg-setting');

        // Login-BG Toggle (Domainprofile – IDs werden per data-Attribut übergeben)
        $('[data-be-branding-domain-id]').each(function () {
            var id = $(this).data('be-branding-domain-id');
            setupLoginBgToggle(
                '#be-branding-login-bg-option--' + id,
                '#be-branding-login-bg-setting--' + id
            );
        });

        // Export-Button: JSON-Download auslösen
        $('#be-branding-export-btn').on('click', function (e) {
            e.preventDefault();
            var $form = $('#be-branding-export-form');
            $form.attr('action', $form.attr('action') + '&export=1').submit();
            $form.attr('action', $form.attr('action').replace('&export=1', ''));
        });

    });

}(jQuery));