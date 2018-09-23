<?php
namespace App\Libs\I18N;

class ArabicException extends Exception
{
    /**
     * Make sure everything is assigned properly
     *
     * @param string $message Exception message
     * @param int    $code    User defined exception code
     */
    public function __construct($message, $code=0)
    {
        parent::__construct($message, $code);
    }
}