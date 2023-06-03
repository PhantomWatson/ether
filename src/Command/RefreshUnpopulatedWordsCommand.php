<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\TableRegistry;

/**
 * RefreshUnpopulatedWords command.
 */
class RefreshUnpopulatedWordsCommand extends Command
{
    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/3.0/en/console-and-shells/commands.html#defining-arguments-and-options
     *
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);

        return $parser;
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return void
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $io->out('Refreshing cache of unpopulated words...');

        /** @var \App\Model\Table\ThoughtsTable $thoughtsTable */
        $thoughtsTable = TableRegistry::getTableLocator()->get('Thoughts');
        $unpopulatedWords = $thoughtsTable->getUnpopulatedWords();
        $io->out('Done');
        $count = count($unpopulatedWords);
        $io->out(sprintf(
            '%s unpopulated %s found',
            number_format($count),
            __n('word', 'words', $count)
        ));
    }
}
