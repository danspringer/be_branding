<?php

class be_branding
{

    public static function hex2rgb($hex)
    {
        if($hex) {
            $hex = str_replace("#", "", $hex);

            if (strlen($hex) == 3) {
                $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
                $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
                $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
            } else {
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
            }
            $rgb = array(
                $r,
                $g,
                $b
            );
            //return implode(",", $rgb); // returns the rgb values separated by commas
            return $rgb; // returns an array with the rgb values
        }
    }


    public static function rgb2hex($rgb)
    {
        if($rgb) {
            $rgb = str_replace('rgba(', '', $rgb);
            $rgb = str_replace(')', '', $rgb);
            $rgb = str_replace(' ', '', $rgb);

            $rgbarr = explode(",", $rgb, 3);
            $hex = '#' . sprintf("%02x%02x%02x", $rgbarr[0], $rgbarr[1], $rgbarr[2]);

            return $hex; // returns the hex value including the number sign (#)
        }
    }


    public static function rgba2hex($rgba)
    {
        if($rgba) {
            $rgba = str_replace('rgba(', '', $rgba);
            $rgba = str_replace(')', '', $rgba);
            $rgba = str_replace(' ', '', $rgba);

            $rgbarr = explode(",", $rgba, 3);
            $hex = '#' . sprintf("%02x%02x%02x", $rgbarr[0], $rgbarr[1], $rgbarr[2]);

            return $hex; // returns the hex value including the number sign (#)
        }
    }


    public static function makeFavIcon($hexColor, $path)
    {
        $rgbColor = be_branding::hex2rgb($hexColor);
        $favIconOriginal = $path . 'favicon-original.png';
        $favIconNew = rex_path::addonAssets('be_branding') . '/favicon/favicon-original-' . str_replace('#', '', $hexColor) . '.png';

        $im = imagecreatefrompng($favIconOriginal);
        imagealphablending($im, false);

        imagesavealpha($im, true);

        if ($im && imagefilter($im, IMG_FILTER_COLORIZE, $rgbColor[0], $rgbColor[1], $rgbColor[2], 0)) {
            imagepng($im, $favIconNew);
            imagedestroy($im);
        }
    }


    public static function checkExtension($filename)
    {
        $img_file_parts = pathinfo($filename);
        //print_r($img_file_parts);
        $be_logo = '/media/' . $filename;

        $ext = $img_file_parts['extension'];
        //echo $ext;
        if ($ext == "jpg" || $ext == "jpeg" || $ext == "png") {
            $be_logo = 'index.php?rex_media_type=rex_media_medium&rex_media_file=' . $filename;
            return $be_logo;
        }
        if ($ext === "svg") {
            $be_logo = '/media/' . $filename;
            return $be_logo;
        }
    }// EoF

    /**
     * Gibt den HTML-Code für die generierten Favicons für die angegebene Domain $domainId zurück
     *
     * @param $domainId
     * @return false|string
     */
    public static function getFrontendFavicons($domainId)
    {
        return fe_favicon::getHtml(be_branding::rgba2hex(rex_addon::get('be_branding')->getConfig('fe_favicon_tilecolor_' . $domainId)), $domainId);
    }

}

?>