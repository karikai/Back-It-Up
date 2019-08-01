<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\DatabaseConnector;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ProfileController extends AbstractController
{
    public function index(DatabaseConnector $db, LoggerInterface $logger)
    {
        session_start();

        $conn = $db->sqlConnection();

        if (isset($_POST['username'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            if ($username !== '' and $password !== '') {
                $sql = "SELECT * FROM users WHERE username = '$username'";
                $result = mysqli_query($conn, $sql);
                $numberOfRows = mysqli_num_rows($result);

                if ($numberOfRows === 1) {
                    $user = mysqli_fetch_assoc($result);
                    $passwordHash = hash('sha256', $password);
                    if ($passwordHash === $user['pwd']) {
                        $_SESSION["user_id"] = $user['id'];
                        $_SESSION["user_email"] = $user['email'];
                        $_SESSION["user_username"] = $user['username'];
                        $_SESSION["user_creation_date"] = $user['created_on'];
                        return $this->redirectToRoute('download');
                    } else {
                        return $this->redirectToRoute('index');
                    }
                } else {
                    // No Such User Exists
                    return $this->render('profile/index.html.twig');
                }
            }
        } else {
            return $this->render('profile/index.html.twig');
        }
    }

    public function createProfile(DatabaseConnector $db, LoggerInterface $logger)
    {
        session_start();

        if (isset($_SESSION['user_id'])) {
            return $this->redirectToRoute('download');
        } else {
            $conn = $db->sqlConnection();
            if (isset($_POST['new-username'])) {
                $username = $_POST['new-username'];
                $password = $_POST['new-password'];
                $confirm_password = $_POST['new-confirm-password'];
                $email = $_POST['new-email'];
                if ($username !== '' and $password !== '' and $confirm_password !== '' and $email !== '') {
                    $sql = "SELECT * FROM users WHERE username = '$username'";
                    $result = mysqli_query($conn, $sql);
                    $numberOfRows = mysqli_num_rows($result);

                    if ($numberOfRows === 0) {
                        $passwordHash = hash('sha256', $password);
                        $new_sql = "INSERT INTO users (email, username, pwd, created_on) VALUES ('$email', '$username', '$passwordHash', CURRENT_TIMESTAMP)";
                        $new_result = mysqli_query($conn, $new_sql);
                        
                        if($new_result) {
                            $logger->info($new_result);
                            $userdata_sql = "SELECT * FROM users WHERE username = '$username'";
                            $userdata_result = mysqli_query($conn, $userdata_sql);
                            $user = mysqli_fetch_assoc($userdata_result);
                            $_SESSION["user_id"] = $user['id'];
                            $_SESSION["user_email"] = $user['email'];
                            $_SESSION["user_username"] = $user['username'];
                            $_SESSION["user_creation_date"] = $user['created_on'];
                            return $this->redirectToRoute('download');
                        } else {
                            return $this->render('profile/sign-up.html.twig');
                        }
                        
                    } else {
                        $logger->info('Username already exist');
                        return $this->render('profile/index.html.twig');
                    }
                } else {
                    return $this->render('profile/sign-up.html.twig');
                }
            } else {
                return $this->render('profile/sign-up.html.twig');
            }
        }
    }

    public function logOut()
    {
        session_start();
        session_destroy();

        // $url = $this->generateUrl('', ['max' => 10]);
        return $this->redirectToRoute('index');
    }
}
