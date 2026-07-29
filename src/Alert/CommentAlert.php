<?php
namespace App\Alert;

use App\Model\Entity\Comment;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;

class CommentAlert
{
    public static function send(Comment $comment): void
    {
        $alert = new Alert();
        $thoughtsTable = TableRegistry::getTableLocator()->get('Thoughts');
        $thought = $thoughtsTable->get($comment->thought_id);
        $alert->addLine('Someone commented about "' . $thought->word . '": ' . $thought->url);
        $alert->addLine('> ' . Text::truncate($comment->comment));
        $alert->send(Alert::TYPE_POSTS);
    }
}
