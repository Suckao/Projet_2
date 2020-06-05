<?php
namespace App\Controller;

use App\Model\ItemLevelManager;
use App\Model\MissionCategoryManager;
use App\Model\ShopManager;

class ShopController extends AbstractController
{
    /**
     * Display home page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function indexShop(int $id = null)
    {
        if ($_SESSION['is_connected'] === true) {
            $itemLevelManager = new ItemLevelManager();
            $levelItem = $itemLevelManager->selectAll();

            $missionCategManager = new MissionCategoryManager();
            $categoryMission = $missionCategManager->selectAll();

            $shopManager = new ShopManager();
            $items = $shopManager->selectAll();

            if ($id != null && $id < 10) {
                $missions = $shopManager->selectByLevel($id);
                $items = [];
                foreach ($missions as $mission) {
                    $item = $shopManager->selectOneById(intval($mission['item_id']));
                    array_push($items, $item);
                }
            } elseif ($id != null && $id > 20) {
                $missions = $shopManager->selectByCateg($id);
                $items = [];
                foreach ($missions as $mission) {
                    $item = $shopManager->selectOneById(intval($mission['item_id']));
                    array_push($items, $item);
                }
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST)) {
                    $item['search'] = $_POST['search'];
                    $items=$shopManager->search($item);
                }
            }

            return $this->twig->render('Shop/indexShop.html.twig', [
                'items' => $items,
                'levelItem' => $levelItem,
                'categoryMission' => $categoryMission,
                'session' => $_SESSION
            ]);
        } else {
            header('Location: /login/login');
        }
    }

    public function navbar()
    {
        if ($_SESSION['is_connected'] === true) {
            return $this->twig->render('layout.html.twig', [
                'session' => $_SESSION
            ]);
        }
    }

    public function shop(int $id)
    {
        if ($_SESSION['is_connected'] === true) {
            $shopManager = new ShopManager();
            $items = $shopManager->selectAll();

            $itemLevelManager = new ItemLevelManager();
            $levelItem = $itemLevelManager->selectAll();

            $missionCategoryManager = new MissionCategoryManager();
            $categoryMission = $missionCategoryManager->selectAll();

            $item = $shopManager->selectOneById($id);

            $_SESSION['item'] = $item['id'];
            $_SESSION['mission'] = $item['mission_id'];
            $_SESSION['missions'][] = $item['mission_id'];

            return $this->twig->render('Shop/indexShop.html.twig', [
                'item' => $item,
                'items' => $items,
                'levelItem' => $levelItem,
                'categoryMission' => $categoryMission,
                'session' => $_SESSION
            ]);
        }
    }
}
