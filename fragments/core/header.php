<?php
/**
 * @var rex_fragment $this
 * @psalm-scope-this rex_fragment
 */

$isPopup = rex_be_controller::getCurrentPageObject()->isPopup();
$isLogin = ('login' === rex_be_controller::getCurrentPage());
$isSetup = ('setup' === rex_be_controller::getCurrentPage());
?>

<div id="rex-js-nav-top" class="rex-nav-top<?php if (!$isPopup && !$isSetup): ?> rex-nav-top-is-fixed<?php endif; ?>">
    <?php
    // Border reinnehmen
    if (rex_addon::get('be_branding')->getConfig('showborder') == 1 && rex_addon::get('be_branding')->getConfig('border_text') != "") {
        echo '<div style="font-size: 12px; background-color: ' . rex_addon::get('be_branding')->getConfig('border_color') . '; color: #fff; width: 100%; font-weight: bold; text-align: center; padding: 8px 0 6px 0;">' . rex_addon::get('be_branding')->getConfig('border_text') . '</div>';
        // Padding bei top-border korrigieren
        echo '<style>.rex-is-logged-in .rex-page-main { padding-top: 90px } .rex-is-logged-in .rex-nav-main { padding: 90px 0 0 0}</style>';
    } // EoF border
    ?>
    <nav class="navbar navbar-default">
        <div class="container-fluid">

            <?php if (!$isLogin && !$isPopup): ?>
                <button type="button" class="navbar-toggle" id="rex-js-nav-main-toggle">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bars">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </span>
                </button>
            <?php endif; ?>

            <div class="navbar-header">
                <?php if ($isPopup): ?>
                    <span class="navbar-brand"><?= rex_file::get(rex_path::coreAssets('redaxo-logo.svg')) ?></span>
                <?php else: ?>
                    <a class="navbar-brand" href="<?= rex_url::backendController() ?>"><?= rex_file::get(rex_path::coreAssets('redaxo-logo.svg')) ?></a>
                <?php endif; ?>
                <?php if (!$isPopup && rex::getUser() && rex::getUser()->isAdmin() && rex::isDebugMode()): ?>
                    <a class="rex-marker-debugmode" href="<?= rex_url::backendPage('system/settings') ?>" title="<?= rex_i18n::msg('debug_mode_marker') ?>">
                        <i class="rex-icon rex-icon-heartbeat rex-pulse"></i>
                    </a>
                <?php endif; ?>
            </div>

            <?= $this->meta_navigation ?>

        </div>
    </nav>

</div>
