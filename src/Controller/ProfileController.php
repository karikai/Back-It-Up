<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\DatabaseConnector;
use Symfony\Component\Routing\Annotation\Route;
use mysqli;

class ProfileController extends AbstractController
{
    public function index(DatabaseConnector $db, LoggerInterface $logger)
    {
        session_start();

        $conn = $db->sqlConnection();
        
        if(isset($_POST['username'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $logger->info('username => ' . $username);
            $logger->info('password => ' . $password);
            if($username !== '' and $password !== '') {
                $sql = "SELECT * FROM users WHERE username = '$username'";
                $result = mysqli_query($conn, $sql);
                $numberOfRows = mysqli_num_rows($result);
                
                if($numberOfRows === 1) {
                    $user = mysqli_fetch_assoc($result);
                    $passwordHash = hash('sha256', $password);
                    $logger->info($passwordHash);
                    $logger->info($user['pwd']);
                    if ($passwordHash === $user['pwd']) {
                        $_SESSION["user_id"] = $user['id'];
                        $_SESSION["user_email"] = $user['email'];
                        $_SESSION["user_username"] = $user['username'];
                        $_SESSION["user_creation_date"] = $user['created_on'];
                        return $this->render('dashboard/download.html.twig');
                    } else {
                        return $this->render('profile/index.html.twig');
                    }
                } else {
                    // No Such User Exists
                }
            }
        } else {
            return $this->render('profile/index.html.twig', [
            ]);
        }

    }
    
    public function createProfile()
    {
        return $this->render('profile/sign-up.html.twig');
    }
}
