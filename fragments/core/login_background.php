<?php
/**
 * @var rex_fragment $this
 * @psalm-scope-this rex_fragment
 */

$login_bg_2100_webp    = rex_url::pluginAssets('be_style', 'redaxo', 'images/peter-olexa-RYtiT3b7XW4-unsplash_2100.webp');
$login_bg_3300_webp    = rex_url::pluginAssets('be_style', 'redaxo', 'images/peter-olexa-RYtiT3b7XW4-unsplash_3300.webp'); 
$login_bg_2100_jpg     = rex_url::pluginAssets('be_style', 'redaxo', 'images/peter-olexa-RYtiT3b7XW4-unsplash_2100.jpg');
$login_bg_3300_webp    = rex_url::pluginAssets('be_style', 'redaxo', 'images/peter-olexa-RYtiT3b7XW4-unsplash_3300.jpg');

if(rex_addon::get('be_branding')->getConfig('login_bg') && rex_addon::get('be_branding')->getConfig('login_bg_setting') == "own_bg")  {
    $login_bg_2100_webp    = rex_media_manager::getUrl('be_branding_login_2100_webp', rex_addon::get('be_branding')->getConfig('login_bg'));
    $login_bg_3300_webp    = rex_media_manager::getUrl('be_branding_login_3300_webp', rex_addon::get('be_branding')->getConfig('login_bg')); 
    $login_bg_2100_jpg     = rex_media_manager::getUrl('be_branding_login_2100_jpg', rex_addon::get('be_branding')->getConfig('login_bg'));
    $login_bg_3300_webp    = rex_media_manager::getUrl('be_branding_login_3300_jpg', rex_addon::get('be_branding')->getConfig('login_bg'));
}
if(rex_addon::get('be_branding')->getConfig('login_bg_setting') == "own_bg" || rex_addon::get('be_branding')->getConfig('login_bg_setting') == "redaxo_standard_bg") {
?>
<picture class="rex-background">
    <source
        srcset="
            <?= $login_bg_2100_webp ?> 2100w,
            <?= $login_bg_3300_webp ?> 3300w"
        sizes="100vw"
        type="image/webp"
    />
    <img
        alt=""
        src="<?= $login_bg_2100_jpg ?>"
        srcset="
            <?= $login_bg_2100_jpg ?> 2100w,
            <?= $login_bg_3300_webp ?> 3300w"
        sizes="100vw"
    />
</picture>
<?php
} // Ende if own_bg
?>

<script>
    var picture = document.querySelector('.rex-background');
    picture.classList.add('rex-background--process');
    picture.querySelector('img').onload = function() {
        picture.classList.add('rex-background--ready');
    }
</script>

<footer class="rex-global-footer">
    <nav class="rex-nav-footer">
        <ul class="list-inline">
            <?php
            if( rex_addon::get('be_branding')->getConfig('agency') )			
                echo '<li>' . rex_addon::get('be_branding')->getConfig('agency') .'</li>';
			?>
            <li><a href="https://www.yakamara.de" target="_blank" rel="noreferrer noopener">yakamara.de</a></li>
            <li><a href="https://www.redaxo.org" target="_blank" rel="noreferrer noopener">redaxo.org</a></li>
            <?php
            if( rex_addon::get('be_branding')->getConfig('login_bg_setting') == "redaxo_standard_bg" )			
                echo '<li class="rex-background-credits"><a href="https://unsplash.com/@deeezyfree" target="_blank" rel="noreferrer noopener">Photo by Peter Olexa on Unsplash</a></li>';
			?>
        </ul>
    </nav>
</footer>
