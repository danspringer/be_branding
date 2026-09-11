<?php
/**
 * @var rex_fragment $this
 * @psalm-scope-this rex_fragment
 *
 * v2: AVIF als zusätzliches Format (neben WebP/JPG)
 */

$addon   = rex_addon::get('be_branding');
$setting = (string) $addon->getConfig('login_bg_setting' . be_branding::getCurrentBeDomainId(true));
$loginBg = (string) $addon->getConfig('login_bg' . be_branding::getCurrentBeDomainId(true));

// Standard-REDAXO-Bilder (AVIF-basiert ab v2)
$useOwn  = $setting === 'own_bg' && $loginBg !== '';
$useStd  = $setting === 'redaxo_standard_bg' || (!$setting && !$useOwn);

// Breiten-Deskriptoren für srcset (eigene Bilder: 2100/3300, neuer Core-Standard: 2400/3500)
$wSmall = 2100;
$wLarge = 3300;

if ($useOwn) {
    // Eigene Bilder: WebP + JPG (AVIF über Media Manager nur wenn Server das unterstützt)
    $src2100webp  = rex_url::frontend(rex_media_manager::getUrl('be_branding_login_2100_webp', $loginBg));
    $src3300webp  = rex_url::frontend(rex_media_manager::getUrl('be_branding_login_3300_webp', $loginBg));
    $src2100avif  = rex_url::frontend(rex_media_manager::getUrl('be_branding_login_2100_avif', $loginBg));
    $src3300avif  = rex_url::frontend(rex_media_manager::getUrl('be_branding_login_3300_avif', $loginBg));
    $src2100jpg   = rex_url::frontend(rex_media_manager::getUrl('be_branding_login_2100_jpg',  $loginBg));
    $src3300jpg   = rex_url::frontend(rex_media_manager::getUrl('be_branding_login_3300_jpg',  $loginBg));
    $showBg = true;
} elseif ($useStd) {
    // REDAXO-Standard: neuere Cores liefern jr-korpa-UFx4NcvTRBE-unsplash
    // (-2400/-3500, AVIF+WebP), ältere jr-korpa-9XngoIpxcEo-unsplash
    // (_2100/_3300, AVIF+JPG). Existenz-Check statt Versionsvergleich (#36)
    $stdNew = 'images/jr-korpa-UFx4NcvTRBE-unsplash';
    $stdOld = 'images/jr-korpa-9XngoIpxcEo-unsplash';
    $src2100webp = ''; $src3300webp = '';
    if (file_exists(rex_path::pluginAssets('be_style', 'redaxo', $stdNew . '-2400.avif'))) {
        $wSmall = 2400;
        $wLarge = 3500;
        $src2100avif = rex_url::pluginAssets('be_style', 'redaxo', $stdNew . '-2400.avif');
        $src3300avif = rex_url::pluginAssets('be_style', 'redaxo', $stdNew . '-3500.avif');
        // kein JPG im neuen Bundle — WebP als <img>-Fallback (wie im Core-Fragment)
        $src2100jpg  = rex_url::pluginAssets('be_style', 'redaxo', $stdNew . '-2400.webp');
        $src3300jpg  = rex_url::pluginAssets('be_style', 'redaxo', $stdNew . '-3500.webp');
    } else {
        $src2100avif = rex_url::pluginAssets('be_style', 'redaxo', $stdOld . '_2100.avif');
        $src3300avif = rex_url::pluginAssets('be_style', 'redaxo', $stdOld . '_3300.avif');
        $src2100jpg  = rex_url::pluginAssets('be_style', 'redaxo', $stdOld . '_2100.jpg');
        $src3300jpg  = rex_url::pluginAssets('be_style', 'redaxo', $stdOld . '_3300.jpg');
    }
    $showBg = true;
} else {
    $showBg = false;
}

if ($showBg):
?>
<picture class="rex-background">
    <?php if ($src2100avif): ?>
    <source
        srcset="<?= rex_escape($src2100avif) ?> <?= $wSmall ?>w, <?= rex_escape($src3300avif) ?> <?= $wLarge ?>w"
        sizes="100vw"
        type="image/avif"
    />
    <?php endif; ?>
    <?php if ($useOwn && $src2100webp): ?>
    <source
        srcset="<?= rex_escape($src2100webp) ?> <?= $wSmall ?>w, <?= rex_escape($src3300webp) ?> <?= $wLarge ?>w"
        sizes="100vw"
        type="image/webp"
    />
    <?php endif; ?>
    <img
        alt=""
        src="<?= rex_escape($src2100jpg) ?>"
        srcset="<?= rex_escape($src2100jpg) ?> <?= $wSmall ?>w, <?= rex_escape($src3300jpg) ?> <?= $wLarge ?>w"
        sizes="100vw"
    />
</picture>
<script>
(function() {
    var pic = document.querySelector('.rex-background');
    if (!pic) return;
    pic.classList.add('rex-background--process');
    pic.querySelector('img').onload = function() {
        pic.classList.add('rex-background--ready');
    };
})();
</script>
<?php endif; ?>

<footer class="rex-global-footer">
    <nav class="rex-nav-footer">
        <ul class="list-inline">
            <?php
            $agency = (string) rex_addon::get('be_branding')->getConfig('agency');
            if ($agency):
            ?>
                <li><?= rex_escape($agency) ?></li>
            <?php endif; ?>
            <li><a href="https://www.yakamara.de" target="_blank" rel="noreferrer noopener">yakamara.de</a></li>
            <li><a href="https://www.redaxo.org" target="_blank" rel="noreferrer noopener">redaxo.org</a></li>
            <?php if ($useStd): ?>
                <li class="rex-background-credits">
                    <a href="https://unsplash.com/@jrkorpa" target="_blank" rel="noreferrer noopener">Photo by Jr Korpa on Unsplash</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</footer>
