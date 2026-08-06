<?php
namespace App\Alert;

use Cake\Core\Configure;
use Cake\Utility\Text;

class Alert {
    const string TYPE_ERRORS = 'errors';
    const string TYPE_POSTS = 'posts';

    public string $content = '';

    /**
     * Adds $line and a newline to the message being built
     *
     * @param string $line Line of text to add
     * @return void
     */
    public function addLine($line)
    {
        $this->content .= $line . "\n";
    }

    /**
     * Adds a string as a quote
     *
     * @param string $string
     * @return void
     */
    public function addQuote(string $string): void
    {
        $lines = explode("\n", $string);
        $this->addLine('> ' . implode("\n> ", $lines));
    }

    /**
     * Adds an unordered bulleted list to the message content
     *
     * @param array $list
     * @return void
     */
    public function addList(array $list)
    {
        foreach ($list as $item) {
            $this->addLine("• $item");
        }
    }

    public function send(string $alertType)
    {
        if (!Configure::read('enableAlerts', true)) {
            return;
        }

        // Don't send alerts when running tests
        if (defined('PHPUNIT_RUNNING') && constant('PHPUNIT_RUNNING')) {
            return;
        }

        // Send through Slack
        $slack = new Slack($alertType);
        $slack->content = $this->content;
        $slack->send();

        // Reset content
        $slack->content = '';
    }
}
