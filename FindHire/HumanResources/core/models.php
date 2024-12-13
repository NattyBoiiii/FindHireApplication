<?php
require_once 'dbConfig.php';
require_once 'handleForms.php';

function insertNewUserAccountHR($pdo, $username, $password) {
    $checkUserSql = "SELECT * FROM userAccounts WHERE username = ?";
    $checkUserSqlStmt = $pdo->prepare($checkUserSql);
    $checkUserSqlStmt->execute([$username]);

    if ($checkUserSqlStmt->rowCount() == 0) {
        $sql = "INSERT INTO userAccounts (username, password) VALUES(?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$username, $password])) {
            return $pdo->lastInsertId(); 
        } else {
            $_SESSION['message'] = "Error during user account creation.";
        }
    } else {
        $_SESSION['message'] = "HR User already exists.";
    }
    return false;
}

function insertUserInformationHR($pdo, $userID, $firstname, $lastname, $age, $email, $phone, $address) {
    $sql = "INSERT INTO userInformation (userID, firstName, lastName, age, emailAddress, phoneNumber, currentAddress, userStatus) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'HR')";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$userID, $firstname, $lastname, $age, $email, $phone, $address])) {
        return true;
    } else {
        $_SESSION['message'] = "Error adding HR user information.";
    }
    return false;
}


function sanitizeInput($input) {
	$input = trim($input);
	$input = stripslashes($input);
	$input = htmlspecialchars($input);
	return $input;
}

function validatePassword($password) {
	if(strlen($password) >= 8) {
		$hasLower = false;
		$hasUpper = false;
		$hasNumber = false;

		for($i = 0; $i < strlen($password); $i++) {
			if(ctype_lower($password[$i])) {
				$hasLower = true;
			}
			if(ctype_upper($password[$i])) {
				$hasUpper = true;
			}
			if(ctype_digit($password[$i])) {
				$hasNumber = true;
			}

			if($hasLower && $hasUpper && $hasNumber) {
				return true;
			}
		}
	}
	return false;
}

function getJobApplications() {
    global $pdo; 

    $query = "SELECT applicationsInfo.applicationID, applicationsInfo.selfIntro, applicationsInfo.attachment, 
                     applicationsInfo.applicationState, userInformation.firstName, userInformation.lastName, 
                     jobDescriptions.jobTitle, applicationsInfo.jobDescID
              FROM applicationsInfo
              JOIN userInformation ON applicationsInfo.userID = userInformation.userID
              JOIN jobDescriptions ON applicationsInfo.jobDescID = jobDescriptions.jobDescID
              ORDER BY applicationsInfo.date_established DESC";

    $stmt = $pdo->query($query);

    $applications = [];
    if ($stmt) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $applications[] = $row;
        }
    }

    return $applications;
}

function replyToInquiry($inquiryID, $replyDescription, $hrUserID) {
    global $pdo;

    $inquiryID = (int)$inquiryID;
    $replyDescription = addslashes($replyDescription); 

    $query = "INSERT INTO replies (replyDescription, inquiryID, userID, date_added) 
              VALUES ('$replyDescription', $inquiryID, $hrUserID, NOW())";

    return $pdo->exec($query); 
}

function updateApplication($applicationID, $newStatus) {
    global $pdo; 

    $query = "UPDATE applicationsInfo SET applicationState = :newStatus WHERE applicationID = :applicationID";
    $stmt = $pdo->prepare($query);

    $stmt->bindParam(':newStatus', $newStatus, PDO::PARAM_STR);
    $stmt->bindParam(':applicationID', $applicationID, PDO::PARAM_INT);

    return $stmt->execute();
}

?>