<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\DatabaseConnector;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    public function download(DatabaseConnector $db, LoggerInterface $logger)
    {
        $conn = $db->sqlConnection();
        $sql = "SELECT * FROM files";
        $result = mysqli_query($conn, $sql);

        $usersArray = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        return $this->render('dashboard/download.html.twig', [
            'dataArray' => $usersArray
        ]);
    }
    
    public function upload(DatabaseConnector $db, LoggerInterface $logger)
    {
        session_start();

        $conn = $db->sqlConnection();

        $statusMsg = '';
        
        if(isset($_POST["submit"]) && !empty($_FILES["file"]["name"])){
            // File upload path
            $targetDir = getcwd() . "/uploads/";
            $fileName = basename($_FILES["file"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            $logger->info($targetFilePath);
            $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);
            // Allow certain file formats
            $allowTypes = array('jpg','png','jpeg','gif','pdf');
            if(in_array($fileType, $allowTypes)){
                // Upload file to server
                if(move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)){
                    // Insert image file name into database
                    $sql = "INSERT into files (file_name, uploaded_on) VALUES ('".$fileName."', NOW())";
                    $result = mysqli_query($conn, $sql);
                    if($result){
                        $statusMsg = "The file ".$fileName. " has been uploaded successfully.";
                    }else{
                        $statusMsg = "File upload failed, please try again.";
                    } 
                }else{
                    $statusMsg = "Sorry, there was an error uploading your file.";
                }
            }else{
                $statusMsg = 'Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed to upload.';
            }
        }else{
            $statusMsg = 'Please select a file to upload.';
        }

        return $this->render('dashboard/upload.html.twig');
    }
}
