<?php
namespace App\Controller;

use App\Model\ItemLevelManager;
use App\Model\MissionCategoryManager;
use App\Model\ShopManager;
use App\Model\MissionsManager;

class HomeController extends AbstractController
{
    /**
     * Display home page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['KittenChoice'])) {
                $_SESSION['kittenadopt'] = true;
            }
        }

        return $this->twig->render('Home/index.html.twig', [
            'session' => $_SESSION,

        ]);
    }
}
