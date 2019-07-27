<?php
namespace App\Controller;

use App\Color\Color;
use App\Model\Table\UsersTable;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Exception;

class ColorsController extends AppController
{
    /** @var Color */
    private $Color;

    /**
     * Initialize method
     *
     * @return void
     * @throws Exception
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->Auth->allow();
        $this->Color = new Color();
    }

    /**
     * Renders /color-names
     *
     * @return void
     */
    public function colorNames()
    {
        /** @var UsersTable $usersTable */
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $colors = $usersTable->getColorsWithThoughts();
        $hexCodes = [];
        foreach ($colors as $section => $sectionColors) {
            foreach ($sectionColors as $color => $count) {
                $hexCodes[] = $color;
            }
        }

        $this->set([
            'colors' => $this->Color->getClosestXkcdColors($hexCodes),
            'title_for_layout' => 'Color Names'
        ]);
    }

    /**
     * Renders /colors/get-name
     *
     * @param string|null $color Six-character hex code for a color
     * @return void
     * @throws NotFoundException
     */
    public function getName($color = null)
    {
        $this->viewBuilder()->setClassName('Json');

        $name = $this->Color->getClosestXkcdColor($color);

        if (! $name) {
            $msg = 'Sorry, Ether can\'t figure out the name of the color with the hex code "' . $color . '"';
            throw new NotFoundException($msg);
        }

        $this->set([
            'name' => $name,
            '_serialize' => ['name']
        ]);
    }
}
