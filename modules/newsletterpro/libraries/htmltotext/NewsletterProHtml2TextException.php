<?php

class NewsletterProHtml2TextException extends Exception
{
    public $more_info;

    public function __construct($message = "", $more_info = "")
    {
        parent::__construct($message);
        $this->more_info = $more_info;
    }
}
