<?php
require_once 'dbConfig.php';
require_once 'handleForms.php';

function insertNewUserAccountApplicant($pdo, $username, $password) {
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
        $_SESSION['message'] = "Applicant User already exists.";
    }
    return false;
}

function insertUserInformationApplicant($pdo, $userID, $firstname, $lastname, $age, $email, $phone, $address) {
    $sql = "INSERT INTO userInformation (userID, firstName, lastName, age, emailAddress, phoneNumber, currentAddress, userStatus) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Applicant')";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$userID, $firstname, $lastname, $age, $email, $phone, $address])) {
        return true;
    } else {
        $_SESSION['message'] = "Error adding Applicant user information.";
    }
    return false;
}

function loginUser($pdo, $username, $password) {
    $sql = "SELECT userAccounts.userID as userID, 
            userAccounts.username as username,
            userAccounts.password as password,
            userInformation.userStatus as userStatus 
            FROM userAccounts JOIN userInformation ON userAccounts.userID = userInformation.userID
            WHERE userAccounts.username = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$username])) {
        $userInfoRow = $stmt->fetch();
        if ($userInfoRow['password'] == $password) {
            $_SESSION['userID'] = $userInfoRow['userID'];
            $_SESSION['username'] = $userInfoRow['username'];
            $_SESSION['userStatus'] = $userInfoRow['userStatus'];
            return true;
        }
    }
    $_SESSION['message'] = "Invalid Applicant username or password.";
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

function getApplicationByID($pdo, $userID) {
    $query = "SELECT * FROM applicationsInfo WHERE userID = ?";

    $statement = $pdo -> prepare($query);
    $executeQuery = $statement -> execute([$userID]);

    if($executeQuery) {
        $response = array(
            "statusCode" => "200",
            "querySet" => $statement -> fetch()
        );
    } else {
        $response = array(
            "statusCode" => "400",
            "message" => "Failed to get application #" . $userID . "!"
        );
    }
    return $response;
}

function createJobPost($userID, $jobTitle, $jobDesc) {
    global $conn;

    $jobTitle = mysqli_real_escape_string($conn, $jobTitle);
    $jobDesc = mysqli_real_escape_string($conn, $jobDesc);

    $query = "INSERT INTO jobDescriptions (userID, jobTitle, jobDesc, date_posted) 
              VALUES ('$userID', '$jobTitle', '$jobDesc', NOW())";

    return mysqli_query($conn, $query);
}

function getJobPosts($searchQuery = "") {
    global $pdo;

    $query = "SELECT jobDescID, jobTitle, jobDesc, date_posted FROM jobDescriptions";
    if (!empty($searchQuery)) {
        $query .= " WHERE jobTitle LIKE :searchQuery OR jobDesc LIKE :searchQuery";
    }
    $query .= " ORDER BY date_posted DESC";

    $stmt = $pdo->prepare($query);

    if (!empty($searchQuery)) {
        $stmt->bindValue(':searchQuery', "%$searchQuery%", PDO::PARAM_STR);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}

function updateApplication($applicationID, $status) {
    global $conn;

    $applicationID = (int)$applicationID;

    $query = "UPDATE applicationsInfo SET applicationState = ? WHERE applicationID = ?";

    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, 'si', $status, $applicationID);

        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            return true;
        } else {
            return false;
        }

        mysqli_stmt_close($stmt);
    } else {

        return false;
    }
}

function getApplicantMessages() {
    global $pdo;

    $query = "SELECT inquiries.inquiryID, inquiries.comDescription, inquiries.date_added, 
                     userInformation.firstName, userInformation.lastName 
              FROM inquiries 
              JOIN userInformation ON inquiries.userID = userInformation.userID
              ORDER BY inquiries.date_added DESC";

    $stmt = $pdo->query($query); 
    return $stmt->fetchAll(); 
}

function getAllJobPosts($pdo, $searchQuery = "") {
    $query = "SELECT jobDescID, jobTitle, jobDesc, date_posted FROM jobDescriptions";
    if (!empty($searchQuery)) {
        $query .= " WHERE jobTitle LIKE ? OR jobDesc LIKE ?";
        $stmt = $pdo->prepare($query);
        $searchTerm = "%$searchQuery%";
        $stmt->execute([$searchTerm, $searchTerm]);
    } else {
        $query .= " ORDER BY date_posted DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getApplicationAttachmentByID($pdo, $applicationID, $postID) {
    $query = "SELECT attachment FROM applicationsInfo WHERE applicationID = ? AND jobDescID = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$applicationID, $postID]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result ? $result['attachment'] : null;
}

function applyToJob($pdo, $userID, $jobDescID, $selfIntro, $attachment) {
    $query = "INSERT INTO applicationsInfo (userID, jobDescID, selfIntro, attachment, applicationState) 
              VALUES (:userID, :jobDescID, :selfIntro, :attachment, 'Evaluating')";
    $stmt = $pdo->prepare($query);
    return $stmt->execute([
        'userID' => $userID,
        'jobDescID' => $jobDescID,
        'selfIntro' => $selfIntro,
        'attachment' => $attachment
    ]);
}

function getApplicationStatuses($pdo, $userID) {
    $query = "
        SELECT 
            (SELECT username FROM userAccounts WHERE userAccounts.userID = jd.userID) AS username, 
            jd.jobTitle, 
            jd.jobDesc, 
            ai.applicationState 
        FROM applicationsInfo ai
        INNER JOIN jobDescriptions jd ON ai.jobDescID = jd.jobDescID
        WHERE ai.userID = :userID
        ORDER BY ai.date_established DESC
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['userID' => $userID]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>