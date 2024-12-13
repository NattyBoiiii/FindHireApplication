<?php 
require_once "core/dbConfig.php";
require_once "core/models.php";

if (!isset($_SESSION['username'])) {
    header("Location: /FindHire/loginMain.php");
}

if ($_SESSION['userStatus'] !== "HR") {
    header("Location: ../index.php");
}

$stmt = $pdo->prepare("SELECT inquiries.inquiryID, inquiries.comDescription, inquiries.date_added, 
    CONCAT(userInformation.firstName, ' ', userInformation.lastName) AS applicantName
    FROM inquiries
    JOIN userInformation ON inquiries.userID = userInformation.userID
    WHERE userInformation.userStatus = 'Applicant'");
$stmt->execute();
$inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['replyDescription'], $_POST['inquiryID'])) {
    $replyDescription = trim($_POST['replyDescription']);
    $inquiryID = (int) $_POST['inquiryID'];
    $hrUserID = $_SESSION['userID']; 

    if (!empty($replyDescription)) {
        $stmt = $pdo->prepare("INSERT INTO replies (replyDescription, inquiryID, userID) VALUES (:replyDescription, :inquiryID, :userID)");
        $stmt->execute([
            ':replyDescription' => $replyDescription,
            ':inquiryID' => $inquiryID,
            ':userID' => $hrUserID,
        ]);
        $_SESSION['message'] = "Reply sent successfully.";
        header("Location: replyMessages.php");
        exit();
    } else {
        $_SESSION['error'] = "Reply cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply to Messages</title>
    <link rel="stylesheet" href="../styles/stylesHR.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include 'navbarHR1.php'; ?>
    <h1>Messages</h1>
    <?php if (isset($_SESSION['message'])) { ?>
        <p class="notification"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
    <?php } ?>
    <div class="messages">
        <?php if (!empty($inquiries)) {
            foreach ($inquiries as $row) { ?>
                <div class="message">
                    <p><strong>From:</strong> <?php echo htmlspecialchars($row['applicantName']); ?></p>
                    <p><strong>Message:</strong> <?php echo htmlspecialchars($row['comDescription']); ?></p>
                    <p><small>Sent on: <?php echo htmlspecialchars($row['date_added']); ?></small></p>
                    <form action="replyMessages.php" method="POST">
                        <input type="hidden" name="inquiryID" value="<?php echo $row['inquiryID']; ?>">
                        <textarea name="replyDescription" rows="4" required></textarea><br>
                        <button type="submit">Send Reply</button>
                    </form>
                </div>
                <hr>
            <?php }
        } else { ?>
            <p>No messages to reply to.</p>
        <?php } ?>
    </div>
</body>
</html>