<?php
namespace App\Controller;

use App\Model\UsersManager;

/**
 * Class LoginController
 *
 */
class LoginController extends AbstractController
{
    public function login()
    {
        $error = '';
        $userManager = new UsersManager;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = [
            'email' => $_POST['email'],
            'password' => $_POST['password']
            ];
            $result = $userManager->checkUserConnection($login);

            if ($result === "User not found") {
                $error = "User not found";
            }

            if ($result === "Incorrect password") {
                $error = "Incorrect password";
            }

            if (isset($result['id'])) {
                $_SESSION['is_connected'] = true;
                $_SESSION['id'] = $result['id'];
                $_SESSION['nickname'] = $result['nickname'];
                $_SESSION['email'] = $result['email'];
                $_SESSION['role'] = $result['role_id'];
                header('Location: /shop/indexShop');
            }
        }
        return $this->twig->render('Login/login.html.twig', ['error' => $error]);
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: /login/login');
    }
}
