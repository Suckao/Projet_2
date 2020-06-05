<?php

namespace App\Controller;

use App\Model\ShopManager;
use App\Model\UsersManager;

class AdminController extends AbstractController
{
    public function addItem()
    {
        if (isset($_SESSION) && $_SESSION['is_connected'] === true && $_SESSION['role'] == 2) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST)) {
                    $shopManagerI = new ShopManager();
                    $item = [
                        'title' => $_POST['title'],
                        'picture' => $_POST['picture'],
                        'description' => $_POST['description'],
                        'item_category_id' => $_POST['item_category_id']
                    ];
                    $itemId = $shopManagerI->insertItem($item);

                    $shopManagerM = new ShopManager();
                    $mission = [
                        'name_mission' => $_POST['name_mission'],
                        'item_id' => $itemId,
                        'level_id' => $_POST['level_id'],
                        'description_mission' => $_POST['description_mission'],
                        'mission_category_id' => $_POST['mission_category_id']
                    ];
                    $shopManagerM->insertMission($mission);
                }
            }
            $selectCategI = new ShopManager();
            $categItem = $selectCategI->selectAllCategItem();

            $selectLevel = new ShopManager();
            $levels = $selectLevel->selectAllLevel();

            $selectCategM = new ShopManager();
            $categMission = $selectCategM->selectAllCategMission();

            return $this->twig->render('Admin/add.html.twig', [
                'categItem' => $categItem,
                'levels' => $levels,
                'categMission' => $categMission,
                'session' => $_SESSION
            ]);
        }
    }

    public function editItem(int $id): string
    {
        if (isset($_SESSION) && $_SESSION['is_connected'] === true && $_SESSION['role'] == 2) {
            $shopManager = new ShopManager();
            $item = $shopManager->selectOneById($id);

            $shopManager = new ShopManager();
            $mission = $shopManager->selectMissionById($id);

            $selectCategI = new ShopManager();
            $categItem = $selectCategI->selectAllCategItem();

            $selectLevel = new ShopManager();
            $levels = $selectLevel->selectAllLevel();

            $selectCategM = new ShopManager();
            $categMission = $selectCategM->selectAllCategMission();

            $selectCategM = new ShopManager();
            $categMissId = $selectCategM->selectCategMissId($id);

            $selectCategM = new ShopManager();
            $levelId = $selectCategM->selectLevelId($id);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST)) {
                    $item['title'] = $_POST['title'];
                    $item['picture'] = $_POST['picture'];
                    $item['description'] = $_POST['description'];
                    $item['item_category_id'] = $_POST['item_category_id'];
                    $shopManager->updateItem($item);

                    $item['name_mission'] = $_POST['name_mission'];
                    $item['level_id'] = $_POST['level_id'];
                    $item['description_mission'] = $_POST['description_mission'];
                    $item['mission_category_id'] = $_POST['mission_category_id'];
                    $shopManager->updateMission($item);
                }
                header('Location: /Shop/indexShop');
            }

            return $this->twig->render('Admin/edit.html.twig', [
                'item' => $item,
                'mission' => $mission,
                'categItem' => $categItem,
                'levels' => $levels,
                'levelId' => $levelId,
                'categMission' => $categMission,
                'categMissId' => $categMissId,
                'session' => $_SESSION
            ]);
        }
    }

    public function delete(int $id)
    {
        if (isset($_SESSION) && $_SESSION['is_connected'] === true && $_SESSION['role'] == 2) {
            $shopManager = new ShopManager();
            $shopManager->delete($id);
            header('Location:/Shop/indexShop');
        }
    }

    public function adminUsers()
    {
        if (isset($_SESSION) && $_SESSION['is_connected'] === true && $_SESSION['role'] == 2) {
            $userManager = new UsersManager();
            $users = $userManager->selectAll();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST)) {
                    $user = ['search' => $_POST['search']];
                    $users = $userManager->search($user);
                }
            }

            return $this->twig->render('Admin/users.html.twig', [
                'users' => $users,
                'session' => $_SESSION
            ]);
        }
    }

    public function deleteUser(int $id)
    {
        if (isset($_SESSION) && $_SESSION['is_connected'] === true && $_SESSION['role'] == 2) {
            $userManager = new UsersManager();
            $userManager->deleteUser($id);
            header('Location:/admin/adminUsers');
        }
    }

    public function validMission(int $id = null)
    {
        if (isset($_SESSION) && $_SESSION['is_connected'] === true && $_SESSION['role'] == 2) {
            $userManager = new UsersManager();
            $usersMiss = $userManager->selectUserMiss();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST)) {
                    $user = ['search' => $_POST['search']];
                    $usersMiss = $userManager->searchUserMiss($user);
                }
            }

            $shopManager = new ShopManager();
            if ($id != null) {
                $shopManager->delete($id);
                header('Location:/admin/validMission');
            }

            return $this->twig->render('Admin/missions.html.twig', [
                'usersMiss' => $usersMiss,
                'session' => $_SESSION
            ]);
        }
    }
}