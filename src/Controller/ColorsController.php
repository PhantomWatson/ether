<?php
namespace App\Controller;

use App\Model\Table\UsersTable;
use Cake\ORM\TableRegistry;

class ColorsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow();
    }

    /**
     * Renders /color-names
     *
     * @return void
     */
    public function colorNames()
    {
        /** @var UsersTable $usersTable */
        $usersTable = TableRegistry::get('Users');
        $colors = $usersTable->getColorsWithThoughts();
        $Color = new \App\Color\Color();
        $hexCodes = [];
        foreach ($colors as $section => $sectionColors) {
            foreach ($sectionColors as $color => $count) {
                $hexCodes[] = $color;
            }
        }

        $this->set([
            'colors' => $Color->getClosestXkcdColors($hexCodes),
            'title_for_layout' => 'Color Names'
        ]);
    }
}
