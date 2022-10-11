<?php
/**
 * @var rex_fragment $this
 * @psalm-scope-this rex_fragment
 */
?>
<!doctype html>
<html lang="<?php echo rex_i18n::msg('htmllang'); ?>">
<head>
    <meta charset="utf-8" />

    <?php
    // PageTitle fuer be_branding anpassen, wenn yRewrite aktiv fuer Multidomain
    $pageTitle = $this->pageTitle;
    if(rex_addon::get('yrewrite')->isAvailable()) {
        $yrewrite = new rex_yrewrite;
        $domain = $yrewrite->getDomainById(be_branding::getCurrentBeDomainId(false));

        $activePageObj = rex_be_controller::getCurrentPageObject();
        if ($activePageObj->getTitle()) {
            $parts[] = $activePageObj->getTitle();
        }
        if (be_branding::getDomainById(be_branding::getCurrentBeDomainId(false))['domain']) {
            $parts[] = be_branding::getDomainById(be_branding::getCurrentBeDomainId(false))['domain'];
        }
        $parts[] = 'REDAXO CMS';

        $pageTitle =  implode(' · ', $parts);
    }
    ?>
    <title><?php echo $pageTitle ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?php
    $user = rex::getUser();

    $colorScheme = 'light dark'; // default: support both
    if (rex::getProperty('theme')) {
        // global theme from config.yml
        $colorScheme = rex_escape((string) rex::getProperty('theme'));
    } elseif ($user && $user->getValue('theme')) {
        // user selected theme
        $colorScheme = rex_escape($user->getValue('theme'));
    }
    echo "\n" . '    <meta name="color-scheme" content="' . $colorScheme . '">';
    echo "\n" . '    <style>:root { color-scheme: ' . $colorScheme . ' }</style>';

    $assetDir = rex_path::assets();

    foreach ($this->cssFiles as $media => $files) {
        foreach ($files as $file) {
            $file = (string) $file;
            $path = rex_path::frontend(rex_path::absolute($file));
            if (!rex::isDebugMode() && str_starts_with($path, $assetDir) && $mtime = @filemtime($path)) {
                $file = rex_url::backendController(['asset' => ltrim($file, '.'), 'buster' => $mtime]);
            } elseif ($mtime = @filemtime($path)) {
                $file .= '?buster='. $mtime;
            }
            echo "\n" . '    <link rel="stylesheet" type="text/css" media="' . $media . '" href="' . $file .'" />';
        }
    }
    echo "\n";
    echo "\n" . '    <script type="text/javascript">';
    echo "\n" . '    <!--';
    echo "\n" . '    var rex = '.$this->jsProperties.';';
    echo "\n" . '    //-->';
    echo "\n" . '    </script>';
    foreach ($this->jsFiles as $file) {
        if (is_string($file)) {
            // BC Case
            $options = [];
        } else {
            [$file, $options] = $file;
        }

        $file = (string) $file;
        $path = rex_path::frontend(rex_path::absolute($file));
        if (array_key_exists(rex_view::JS_IMMUTABLE, $options) && $options[rex_view::JS_IMMUTABLE]) {
            if (!rex::isDebugMode() && str_starts_with($path, $assetDir) && $mtime = @filemtime($path)) {
                $file = rex_url::backendController(['asset' => ltrim($file, '.'), 'buster' => $mtime]);
            }
        } elseif ($mtime = @filemtime($path)) {
            $file .= '?buster='. $mtime;
        }

        $attributes = [];
        if (array_key_exists(rex_view::JS_ASYNC, $options) && $options[rex_view::JS_ASYNC]) {
            $attributes[] = 'async="async"';
        }
        if (array_key_exists(rex_view::JS_DEFERED, $options) && $options[rex_view::JS_DEFERED]) {
            $attributes[] = 'defer="defer"';
        }

        echo "\n" . '    <script type="text/javascript" src="' . $file .'" '. implode(' ', $attributes) .'></script>';
    }
    ?>
    <?php
    // BE-Favicon nur färben, wenn Imagemagick verfügbar ist
    if(class_exists('Imagick') === true) {
        if(rex_addon::get('be_branding')->getConfig('coloricon') == 1) {

            $addon = rex_addon::get('be_branding');
            // Initiale Farbe für R setzen und als neues png abspeichern
            be_branding::makeFavIcon(be_branding::rgba2hex($addon->getConfig('color1'. be_branding::getCurrentBeDomainId(true))), rex_path::addon('be_branding') . 'vendor/favicon/');
            // aus dem png dann die Favicons generieren
            //https://github.com/dmamontov/favicon reinholen
            require rex_path::addon('be_branding') . 'vendor/favicon/src/BE_FaviconGenerator.php';
            $fav = new BE_FaviconGenerator(rex_path::addonAssets('be_branding') . 'favicon/favicon-original-' . str_replace('#', '', be_branding::rgba2hex($addon->getConfig('color1'. be_branding::getCurrentBeDomainId(true)))) . '.png');

            $fav->setCompression(BE_FaviconGenerator::COMPRESSION_VERYHIGH);

            $fav->setConfig(array(
                'apple-background' => substr($addon->getConfig('color1'. be_branding::getCurrentBeDomainId(true)), 1, 6),
                'apple-margin' => 0,
                'android-background' => substr($addon->getConfig('color1'. be_branding::getCurrentBeDomainId(true)), 1, 6),
                'android-margin' => 0,
                'android-name' => rex::getServerName(),
                'android-url' => rex::getServer(),
                'android-orientation' => BE_FaviconGenerator::ANDROID_PORTRAIT,
                'ms-background' => substr($addon->getConfig('color1'), 1, 6)
            ));

            // Erst die BE-Branding Favicons ausgeben
            echo $fav->createAllAndGetHtml(be_branding::rgba2hex($addon->getConfig('color1'. be_branding::getCurrentBeDomainId(true))));
            // Jetzt die Redaxo-Favicons löschen, aber die Scripts im pageHeader beibehalten
            echo be_favicon::removeRexCoreFavicons($this->pageHeader,"link","rel","icon");
        } else {
            echo $this->pageHeader;
        }
    }
    ?>

</head>
<body<?php echo $this->bodyAttr; ?>>

<div class="rex-ajax-loader" id="rex-js-ajax-loader">
    <div class="rex-ajax-loader-element"></div>
    <div class="rex-ajax-loader-backdrop"></div>
</div>

<div id="rex-start-of-page" class="rex-page">
