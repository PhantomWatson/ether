<?php
namespace App\Alert;

class ErrorAlert
{
    public static function send($message): void
    {
        $alert = new Alert();
        $alert->addLine($message);
        $alert->addLine(print_r(debug_backtrace(limit: 1), true));
        $alert->send(Alert::TYPE_ERRORS);
    }
}
