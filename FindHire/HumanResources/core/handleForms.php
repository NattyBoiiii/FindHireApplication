<?php
require_once 'dbConfig.php';
require_once 'models.php';

if (isset($_POST['registerHRBtn'])) {
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
            $userID = insertNewUserAccountHR($pdo, $username, $hashedPassword, "HR");

            if ($userID) {
                $insertInfo = insertUserInformationHR($pdo, $userID, $firstName, $lastName, $age, $currentAddress, $emailAddress, $phoneNumber, $gender, "HR");

                if ($insertInfo) {
                    $_SESSION['message'] = "HR registered successfully!";
                    header("Location: /FindHire/loginMain.php");
                } else {
                    $_SESSION['message'] = "Error saving HR information.";
                    header("Location: ../registerHR.php");
                }
            } else {
                $_SESSION['message'] = "Error registering HR. Username might already exist.";
                header("Location: ../registerHR.php");
            }
        } else {
            $_SESSION['message'] = "Password must be at least 8 characters, include a lowercase letter, an uppercase letter, and a number.";
            header("Location: ../registerHR.php");
        }
    } else {
        $_SESSION['message'] = "All fields are required.";
        header("Location: ../registerHR.php");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['jobTitle'], $_POST['jobDesc'], $_POST['createJob'])) {
    $jobTitle = $_POST['jobTitle'];
    $jobDesc = $_POST['jobDesc'];
    $userID = $_SESSION['userID'];

    if (createJobPost($userID, $jobTitle, $jobDesc)) {
        $_SESSION['message'] = "Job post created successfully.";
    } else {
        $_SESSION['message'] = "Failed to create job post.";
    }
    header("Location: ../FindHire/index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['inquiryID'], $_POST['replyDescription'])) {
    $inquiryID = $_POST['inquiryID'];
    $replyDescription = $_POST['replyDescription'];
    $hrUserID = $_SESSION['userID'];

    if (replyToInquiry($inquiryID, $replyDescription, $hrUserID)) {
        $_SESSION['message'] = "Reply sent successfully.";
    } else {
        $_SESSION['message'] = "Failed to send reply.";
    }
    header("Location: ../HumanResources/replyMessages.php");
    exit;
}

if (isset($_POST['replyBtn'])) {
    $inquiryID = $_POST['inquiryID'];
    $replyDescription = $_POST['replyDescription'];
    $hrUserID = $_SESSION['userID'];

    if (replyToInquiry($inquiryID, $replyDescription, $hrUserID)) {
        echo "Reply sent successfully.";
    } else {
        echo "Failed to send reply.";
    }
}

if (isset($_POST['action']) && isset($_POST['applicationID'])) {
    $applicationID = (int)$_POST['applicationID'];
    $action = $_POST['action'];

    if ($action === 'accept') {
        $result = updateApplication($applicationID, 'Accepted');
        $message = $result ? "Application accepted successfully." : "Failed to accept application.";
    } elseif ($action === 'reject') {
        $result = updateApplication($applicationID, 'Rejected');
        $message = $result ? "Application rejected successfully." : "Failed to reject application.";
    }
    $_SESSION['message'] = $message;
    header("Location: manageApplications.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['inquiryID'], $_POST['replyDescription'])) {
    $inquiryID = (int)$_POST['inquiryID']; 
    $replyDescription = $_POST['replyDescription'];
    $userID = $_SESSION['userID']; 

    try {
        $query = "INSERT INTO replies (replyDescription, inquiryID, userID, date_added) 
                  VALUES (:replyDescription, :inquiryID, :userID, NOW())";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'replyDescription' => $replyDescription,
            'inquiryID' => $inquiryID,
            'userID' => $userID
        ]);

        $_SESSION['message'] = "Reply sent successfully.";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Failed to send reply: " . $e->getMessage();
    }

    header("Location: replyMessages.php");
    exit;
}

try {
    $query = "SELECT inquiries.inquiryID, inquiries.comDescription, userAccounts.username AS applicantName
              FROM inquiries
              LEFT JOIN replies ON inquiries.inquiryID = replies.inquiryID
              INNER JOIN userAccounts ON inquiries.userID = userAccounts.userID
              WHERE replies.inquiryID IS NULL
              ORDER BY inquiries.date_added DESC";
    $stmt = $pdo->query($query);
    $inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching inquiries: " . $e->getMessage());
}

if (isset($_POST['submitJob'])) {
    $jobTitle = sanitizeInput($_POST['jobTitle']);
    $jobDesc = sanitizeInput($_POST['jobDesc']);
    $userID = $_SESSION['userID'] ?? null; 

    if (!empty($jobTitle) && !empty($jobDesc) && $userID) {
        try {
            $query = "INSERT INTO jobDescriptions (userID, jobTitle, jobDesc, date_posted) 
                      VALUES ('$userID', '$jobTitle', '$jobDesc', NOW())";

            $result = $pdo->exec($query);

            if ($result) {
                $_SESSION['message'] = "Job post created successfully!";
                header("Location: /FindHire/index.php");
            } else {
                $_SESSION['message'] = "Error posting the job.";
                header("Location: /FindHire/index.php");
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = "Database error: " . $e->getMessage();
            header("Location: /FindHire/index.php");
        }
    } else {
        $_SESSION['message'] = "All fields are required, and you must be logged in.";
        header("Location: /FindHire/index.php");
    }
    exit;
}

?>