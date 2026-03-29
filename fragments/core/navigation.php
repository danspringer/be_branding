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
                $addon  = rex_addon::get('be_branding');
                $file   = $addon->getConfig('file' . be_branding::getCurrentBeDomainId(true));
                $logoSrc = $file ? be_branding::checkExtension($file) : '';
                if ($logoSrc):
                ?>
                    <img src="<?= rex_escape($logoSrc) ?>"
                         class="img-responsive center-block"
                         style="padding:20px 10px 5px;width:100%;"
                         alt=""/>
                <?php endif; ?>
                <?= $this->navigation ?>
            </div>
        </nav>
    </div>
    <div id="rex-js-nav-main-backdrop" class="rex-nav-main-backdrop"></div>
<?php endif;
