<?php
/**
 * @var rex_fragment $this
 * @psalm-scope-this rex_fragment
 */
?>
<?php if ('' != $this->navigation): ?>
    <div id="rex-js-nav-main" class="rex-nav-main navbar-default">
        <nav class="rex-nav-main-navigation" role="navigation">
            <div>
                <?php
                if (rex_addon::get('be_branding')->getConfig('file')) {
                    echo '<img src="' . be_branding::checkExtension(rex_addon::get('be_branding')->getConfig('file')) . '" class="img-responsive center-block" style="padding: 20px 10px 5px 10px; width: 100%;"/></a>';
                }
                echo $this->navigation;
                ?>
            </div>
        </nav>
    </div>
    <div id="rex-js-nav-main-backdrop" class="rex-nav-main-backdrop"></div>
<?php endif;

