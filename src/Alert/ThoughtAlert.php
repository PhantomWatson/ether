<?php
namespace App\Alert;

use App\Model\Entity\Thought;
use Cake\Utility\Text;

class ThoughtAlert
{
    public static function send(Thought $thought): void
    {
        $alert = new Alert();

        $alert->addLine('Someone thought about "' . $thought->word . '": ' . $thought->url);
        $alert->addQuote(Text::truncate($thought->thought));
        $alert->send(Alert::TYPE_POSTS);
    }
}
