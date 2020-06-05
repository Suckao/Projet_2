<?php

namespace App\Controller;

use App\Model\UsersManager;
use App\Model\MissionsManager;
use App\Model\ShopManager;

/**
 * Class ItemController
 *
 */
class UsersController extends AbstractController
{
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['nickname']) && !empty($_POST['password']) && !empty($_POST['email'])) {
                $userManager = new UsersManager();
                $user = [
                    'nickname' => $_POST['nickname'],
                    'password' => $_POST['password'],
                    'email' => $_POST['email'],
                ];
                $userManager->insert($user);
                /* TODO : ICI AJOUTER REDIRECTION VERS PAGE LOGIN */
            }
        }

        return $this->twig->render('Users/register.html.twig');
    }

    public function contact()
    {
        if ($_SESSION['is_connected'] === true) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!empty($_POST['nickname']) && !empty($_POST['email']) && !empty($_POST['subject']) && !empty($_POST['message'])) {
                    $to = "sten.quidelleur36@gmail.com";
                    $from = "sten.test4php@gmail.com";

                    $email = $_POST['email'];
                    $message = $_POST['message'];

                    $_SESSION['name'] = $_POST['nickname'];

                    $subject = "New message from contact KittyExpress";
                    $content = "Message from the website KittyExpress: " . $message . " contact email: " . $email;
                    $headers = "from: " . $from;

                    mail($to, $subject, $content, $headers);

                    $this->twig->addGlobal("session", $_SESSION);
                    return $this->twig->render('Users/contactSucess.html.twig');
                }
            }
            return $this->twig->render('Users/contactForm.html.twig', [
                'session' => $_SESSION
            ]);
        }
    }

    public function profile(int $id)
    {
        $missionManager = new MissionsManager();

        if ($_SESSION['is_connected'] === true) {
            $userId = $_SESSION['id'];
            $anony = 'Anonymous_emblem.svg.png';
            $userManager = new UsersManager();
            if (!empty($_FILES['files']['name'][0])) {
                $avatar = [];
                $files = $_FILES['files'];

                $uploaded = array();
                $failed = array();

                $allowed = array('jpg','png','gif');
                foreach ($files['name'] as $position => $file_name) {
                    $file_tmp = $files['tmp_name'][$position];
                    $file_size = $files['size'][$position];
                    $file_error = $files['error'][$position];

                    $file_ext = explode('.', $file_name);
                    $file_ext = strtolower(end($file_ext));

                    if (in_array($file_ext, $allowed)) {
                        if ($file_error === 0) {
                            if ($file_size <= 8388608) {
                                $file_name_new = uniqid('', true) . '.' . $file_ext;
                                $file_destination = 'assets/images/avatar/' . $file_name_new;
                                if (empty($avatar)) {
                                    array_push($avatar, $file_name_new);
                                }
                                if (move_uploaded_file($file_tmp, $file_destination)) {
                                    $uploaded[$position] = $file_destination;
                                } else {
                                    $failed[$position] = "[{$file_name}] failed to upload.";
                                }
                            } else {
                                $failed[$position] = "[{$file_name}] is too large.";
                            }
                        } else {
                            $failed[$position] = "[{$file_name}] errored with code {$file_error}.";
                        }
                    } else {
                        $failed[$position] = "[{$file_name}] file extension '{$file_ext}' is not allowed.";
                    }
                }
                $picture = [
                    'id' => $_SESSION['id'],
                    'avatar' => $avatar[0]
                ];
                $userManager->updateAvatar($picture);
            }
            if (!empty($failed)) {
                print_r($failed);
            }
            $user = $userManager->selectOneById($_SESSION['id']);
          
            $missionManager = new MissionsManager();
            $missions = $missionManager->selectMissionsUser($id);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!empty($_POST['solution'])) {
                    $solution = [
                        'id' => $_POST['id'],
                        'solution' => $_POST['solution']
                    ];
                    $missionManager->insertSolution($solution);
                }
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['mission_id'])) {
                    $userMission = $_POST['mission_id'];
                    $missionManager->deleteUserMission($userMission);
                    header('Location: http://localhost:8000/users/profile/' .$userId);
                }
            }

            return $this->twig->render('Users/profile.html.twig', [
                'session' => $_SESSION,
                'user' => $user,
                'missions' => $missions,
                'anony' => $anony
            ]);
        }
    }
}
