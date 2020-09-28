<?php
if(file_exists(rex_path::addonAssets('be_branding','fe_favicon/.settings'))) {
	unlink(rex_path::addonAssets('be_branding','fe_favicon/.settings'));
}

if (is_dir(rex_path::frontend('favicon'))) {
	rmdir(rex_path::frontend('favicon'));
}