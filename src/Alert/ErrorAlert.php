<?php
namespace App\Alert;

class ErrorAlert
{
    public static function send($message): void
    {
        $alert = new Alert();
        $alert->addLine($message);
        $alert->send(Alert::TYPE_ERRORS);
    }
}
