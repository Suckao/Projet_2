<?php

namespace App\Controller;

use App\Model\MissionsManager;
use App\Model\ShopManager;
use App\Model\UsersManager;

class MissionsController extends AbstractController
{

    public function cart(int $id)
    {
        $missionsManager = new MissionsManager();
        if ($_SESSION['is_connected'] === true) {
            $arrayMissions = [];
            $arrayItems = [];
            if (isset($_SESSION['missions'])) {
                $missions = $_SESSION['missions'];
                foreach ($missions as $mission) {
                    $missionObject = $missionsManager->selectMissionsCart(intval($mission));
                    $shopManager = new ShopManager();
                    if (isset($missionObject) && !empty($missionObject)) {
                        $itemObject = $shopManager->selectOneById(intval($missionObject['item_id']));
                        array_push($arrayItems, $itemObject);
                    }
                    array_push($arrayMissions, $missionObject);
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (isset($_POST['missionId'])) {
                        $cart = $_SESSION['missions'];
                        $index = '';
                        for ($i = 0; $i < count($cart); $i++) {
                            if ($cart[$i] == $_POST['missionId']) {
                                $index = $i;
                            }
                        }
                        unset($_SESSION['missions'][$index]);
                        header('Location: http://localhost:8000/missions/cart/' . $id);
                    }
                }
            }
            return $this->twig->render('Missions/cart.html.twig', [
                'arrayItems' => $arrayItems,
                'arrayMissions' => $arrayMissions,
                'session' => $_SESSION
            ]);
        }
    }

    public function add(int $id)
    {
        $missionManager = new MissionsManager();
        if ($_SESSION['is_connected'] === true) {
            $items = $missionManager->selectMissionsCart($id);

            $userMission = $missionManager->selectUserMission($id);

            $categoryMission = $missionManager->selectCategMission($id);

            $_SESSION['enlisted'] = false;
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!empty($_POST['solution'])) {
                    $solution = [
                        'id' => $_POST['id'],
                        'solution' => $_POST['solution']
                    ];
                    $missionManager->insertSolution($solution);
                    $_SESSION['enlisted'] = true;
                }
            }

            $enlisted = $_SESSION['enlisted'];
            $successPost = [];
            if ($enlisted === true) {
                $item = $items['missionId'];
                $linkUsertoMission = [
                    'userId' => $_SESSION['id'],
                    'missionId' => intval($item)
                ];
                $missionManager->addUserMission($linkUsertoMission);
                $successPost = [
                    'text' => "Congrats, you successfully fed the kitten!",
                    'gif' => "https://thumbs.gfycat.com/EmptyMiniatureGalapagosdove-size_restricted.gif"
                ];
            }

            return $this->twig->render('Missions/add.html.twig', [
                'items' => $items,
                'categoryMission' => $categoryMission,
                'userMission' => $userMission,
                'success' => $successPost,
                'session' => $_SESSION
            ]);
        }
    }
}
