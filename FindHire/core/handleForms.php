<?php
require_once 'dbConfig.php';
require_once 'models.php';


if (isset($_POST['registerApplicantBtn'])) {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    $firstName = sanitizeInput($_POST['firstname']);
    $lastName = sanitizeInput($_POST['lastname']);
    $age = (int)$_POST['age'];
    $currentAddress = sanitizeInput($_POST['currentAddress']);
    $emailAddress = sanitizeInput($_POST['emailAddress']);
    $phoneNumber = sanitizeInput($_POST['phoneNumber']);
    $gender = $_POST['gender'];

    if (!empty($username) && !empty($password) && !empty($firstName) && !empty($lastName) &&
        !empty($currentAddress) && !empty($emailAddress) && !empty($phoneNumber)) {

        if (validatePassword($password)) {
            $hashedPassword = sha1($password);
            $userID = insertNewUserAccountApplicant($pdo, $username, $hashedPassword);

            if ($userID) {
                $insertInfo = insertUserInformationApplicant($pdo, $userID, $firstName, $lastName, $age, $currentAddress, $emailAddress, $phoneNumber, $gender, "Applicant");

                if ($insertInfo) {
                    $_SESSION['message'] = "Applicant registered successfully!";
                    header("Location: ../login.php");
                } else {
                    $_SESSION['message'] = "Error saving Applicant information.";
                    header("Location: ../register.php");
                }
            } else {
                $_SESSION['message'] = "Error registering Applicant. Username might already exist.";
                header("Location: ../register.php");
            }
        } else {
            $_SESSION['message'] = "Password must be at least 8 characters, include a lowercase letter, an uppercase letter, and a number.";
            header("Location: ../register.php");
        }
    } else {
        $_SESSION['message'] = "All fields are required.";
        header("Location: ../register.php");
    }
}

if (isset($_POST['loginBtn'])) {
    $username = sanitizeInput($_POST['username']);
    $password = sha1($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $user = loginUser($pdo, $username, $password);

        if ($user) {
            $_SESSION['message'] = "Welcome! login successful!";
            header("Location: ../index.php");
        } else {
            $_SESSION['message'] = "Invalid username or password.";
            header("Location: ../login.php");
        }
    } else {
        $_SESSION['message'] = "All fields are required.";
        header("Location: ../login.php");
    }
}

if (isset($_GET['logoutBtn'])) {
    unset($_SESSION['username']);
    unset($_SESSION['userID']);
    unset($_SESSION['userStatus']);
    header("Location: ../loginMain.php");
}

$jobs = getAllJobPosts($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['applyJobBtn'])) {
        $jobDescID = (int) $_POST['jobDescID'];
        $selfIntro = htmlspecialchars($_POST['selfIntro']);
        $userID = $_SESSION['userID'];
        $attachment = null;

        if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = "../uploads/resumes/job_post_{$jobDescID}/";
            $fileName = basename($_FILES['resume']['name']);
            $fileTmpName = $_FILES['resume']['tmp_name'];
            $fileType = $_FILES['resume']['type'];
            $fileSize = $_FILES['resume']['size'];

            $allowedTypes = ['application/pdf'];

            if (!in_array($fileType, $allowedTypes)) {
                $_SESSION['message'] = "Only PDF files are allowed.";
                header("Location: ../applyJob.php?jobID=$jobDescID");
                exit;
            }

            if ($fileSize > 5 * 1024 * 1024) {
                $_SESSION['message'] = "File size exceeds 5 MB.";
                header("Location: ../applyJob.php?jobID=$jobDescID");
                exit;
            }

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $newFilePath = $uploadDir . $fileName;
            if (move_uploaded_file($fileTmpName, $newFilePath)) {
                $attachment = basename($newFilePath);
            } else {
                $_SESSION['message'] = "Error uploading the resume.";
                header("Location: ../applyJob.php?jobID=$jobDescID");
                exit;
            }
        }

        if (applyToJob($pdo, $userID, $jobDescID, $selfIntro, $attachment)) {
            $_SESSION['message'] = "Application submitted successfully.";
        } else {
            $_SESSION['message'] = "Failed to submit application.";
        }

        header("Location: ../applyJob.php?jobID=$jobDescID");
        exit;
    }
}

?>