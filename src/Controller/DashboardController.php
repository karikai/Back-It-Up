<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\DatabaseConnector;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends AbstractController
{
    public function download(DatabaseConnector $db, LoggerInterface $logger)
    {
        session_start();
        $user_id = $_SESSION['user_id'];
                
        $conn = $db->sqlConnection();
        $sql = "SELECT * FROM files WHERE uploaded_by = $user_id";
        $result = mysqli_query($conn, $sql);
        $logger->info(mysqli_num_rows($result));
        $usersArray = mysqli_fetch_all($result, MYSQLI_ASSOC);

        
        if (mysqli_num_rows($result) !== 0) {
            return $this->render('dashboard/download.html.twig', [
                'dataArray' => $usersArray,
                'user_id' => $_SESSION['user_id']
            ]);
        } else {
            return $this->render('dashboard/download-no-uploads.html.twig');
        }
    }
    
    public function upload(DatabaseConnector $db, LoggerInterface $logger)
    {
        session_start();
        $user_id = $_SESSION['user_id'];
        
        $conn = $db->sqlConnection();
        $statusMsg = '';
        if(isset($_POST["submit"]) && !empty($_FILES["file"]["name"])){
            // File upload path
            $timestamp = time(); 
            $targetDir = getcwd() . "/uploads/";
            $fileType = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
            $logger->info($fileType);
            $fileName = basename($_FILES["file"]["name"], ".$fileType");
            $logger->info($fileName);
            $fileName =  $fileName . '-' . $user_id . $timestamp . ".$fileType";
            $targetFilePath = $targetDir . $fileName;
            $logger->info($targetFilePath);
            $logger->info($_FILES["file"]["tmp_name"]);
            $logger->info($_FILES["file"]["name"]);
            $logger->info($fileName);
            // Upload file to server
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
                // Insert image file name into database
                $sql = "INSERT into files (file_name, uploaded_by, uploaded_on) VALUES ('" . $fileName . "', $user_id, NOW())";
                $result = mysqli_query($conn, $sql);
                if ($result) {
                    $statusMsg = "The file " . $fileName . " has been uploaded successfully.";
                    $logger->info($statusMsg);
                    return $this->redirectToRoute('download');
                } else {
                    $statusMsg = "File upload failed, please try again.";
                    $logger->info($statusMsg);
                }
            } else {
                $statusMsg = "Sorry, there was an error uploading your file.";
                $logger->info($statusMsg);
            }
        }else{
            $statusMsg = 'Please select a file to upload.';
            $logger->info($statusMsg);
        }
        
        return $this->render('dashboard/upload.html.twig');
    }
    
    public function downloadLink(DatabaseConnector $db, LoggerInterface $logger, $uid, $filename)
    {
        session_start();
        $user_id = $_SESSION['user_id'];
        $targetDir = getcwd() . "/uploads/";
        
        if ($user_id === $uid) {
            $response = new BinaryFileResponse($targetDir.$filename);
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
            return $response;
        } else {
            return new Response('File Not Found');
        }
            
    }
    
}
