<?php
namespace App\Alert;

use App\Model\Entity\Thought;
use Cake\Utility\Text;

class CommentAlert
{
    public static function send(Thought $thought): void
    {
        $alert = new Alert();

        $alert->addLine('Someone commented about "' . $thought->word . '": ' . $thought->url);
        $alert->addLine('> ' . Text::truncate($thought->thought));
        $alert->send(Alert::TYPE_POSTS);
    }
}
