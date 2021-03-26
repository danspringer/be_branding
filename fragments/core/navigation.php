
        <?php if ('' != $this->navigation): ?>
        <nav id="rex-js-nav-main" class="rex-nav-main navbar-default" role="navigation">
            <div class="navbar-collapse collapse">
            	<?php
				if(rex_addon::get('be_branding')->getConfig('file')) {
					echo '<img src="'.checkExtension(rex_addon::get('be_branding')->getConfig('file')).'" class="img-responsive center-block" style="padding: 20px 10px 5px 10px; width: 100%;"/></a>';
					}
					
				echo $this->navigation;
				 ?>
            </div>
        </nav>
        <?php endif;
