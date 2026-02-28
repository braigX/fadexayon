<?php
/**
* Since 2013 Ovidiu Cimpean.
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright Since 2013 Ovidiu Cimpean
* @license   Do not edit, modify or copy this file
*
* @version   Release: 4
*/

if (!defined('_PS_VERSION_')) {
	exit;
}

class NewsletterProEmbedImages
{
    private $template;

    private $images;

    public function __construct($template)
    {
        $this->template = $template;
        $this->images = [];

        if ((bool) pqnp_config('SEND_EMBEDED_IMAGES')) {
            $this->template = preg_replace_callback('/(<img.*src=("|\'))(http.*?)(\2[^>]+>)/', [$this, 'replaceEmbedCallback'], $this->template);
        }
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function getImages()
    {
        return $this->images;
    }

    public function replaceEmbedCallback($matches)
    {
        $path = $matches[3];

        if (preg_match('/data-embed=("|\')0\1/', $matches[0])) {
            return $matches[0];
        }

        $swift_image = Swift_Image::fromPath($path);
        $this->images[] = $swift_image;

        return $matches[1].'cid:'.$swift_image->getId().$matches[4];
    }
}
