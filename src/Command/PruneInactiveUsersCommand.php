<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\TableRegistry;

/**
 * PruneInactiveUsers command.
 */
class PruneInactiveUsersCommand extends Command
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
        $io->out('Fetching inactive accounts older than six months...');
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $users = $usersTable->getInactiveUsersToPrune();
        $count = $users->count();
        $io->out($count . __n(' user', ' users', $count) . ' found');

        if (!$count) {
            return;
        }

        $choice = $io->askChoice(
            'Are you sure you want to delete these users? A backup before this is suggested.',
            ['y', 'n'],
            'n'
        );
        if ($choice != 'y') {
            return;
        }

        $progress = $io->helper('Progress');
        $progress->init([
            'total' => $count,
            'width' => 20,
        ]);
        foreach ($users as $user) {
            if (!$usersTable->delete($user)) {
                $io->error(sprintf(
                    'Error deleting user #%s. Details:',
                    $user->id
                ));
                $io->out(print_r($user->getErrors(), true));
                return;
            }
            $progress->increment(1);
            $progress->draw();
        }

        $io->success('Done');
    }
}
