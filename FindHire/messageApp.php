<?php 
require_once "core/dbConfig.php";
require_once "core/models.php";

if (!isset($_SESSION['username'])) {
    header("Location: loginMain.php");
    exit();
}

if ($_SESSION['userStatus'] !== "Applicant") {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    $userID = $_SESSION['userID'];

    if (!empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO inquiries (comDescription, userID) VALUES (:message, :userID)");
        $stmt->execute([
            ':message' => $message,
            ':userID' => $userID,
        ]);
        $_SESSION['message'] = "Message sent successfully.";
        header("Location: messageApp.php");
        exit();
    } else {
        $_SESSION['error'] = "Message cannot be empty.";
    }
}

$userID = $_SESSION['userID'];
$stmt = $pdo->prepare("
    SELECT 
        inquiries.inquiryID,
        inquiries.comDescription AS applicantMessage,
        inquiries.date_added AS messageDate,
        replies.replyDescription AS hrReply,
        replies.date_added AS replyDate
    FROM 
        inquiries
    LEFT JOIN 
        replies ON inquiries.inquiryID = replies.inquiryID
    WHERE 
        inquiries.userID = :userID
    ORDER BY 
        inquiries.date_added DESC
");
$stmt->execute([':userID' => $userID]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message HR</title>
    <link rel="stylesheet" href="styles/stylesApplicant.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <h1>Send a Message to HR</h1>
    <?php if (isset($_SESSION['message'])) { ?>
        <p class="notification"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
    <?php } ?>
    <form method="POST" action="">
        <textarea name="message" rows="5" cols="50" placeholder="Type your message here..." required></textarea><br>
        <button type="submit">Send Message</button>
    </form>
    <h2>Your Messages</h2>
    <div class="messages">
        <?php if (!empty($messages)) {
            foreach ($messages as $message) { ?>
                <div class="message">
                    <p><strong>Your Message (<?php echo date('F j, Y, g:i a', strtotime($message['messageDate'])); ?>):</strong></p>
                    <p><?php echo htmlspecialchars($message['applicantMessage']); ?></p>
                    <?php if ($message['hrReply']) { ?>
                        <p><strong>HR Reply (<?php echo date('F j, Y, g:i a', strtotime($message['replyDate'])); ?>):</strong></p>
                        <p><?php echo htmlspecialchars($message['hrReply']); ?></p>
                    <?php } else { ?>
                        <p><em>No reply from HR yet.</em></p>
                    <?php } ?>
                </div>
                <hr>
            <?php }
        } else { ?>
            <p>You have not sent any messages yet.</p>
        <?php } ?>
    </div>
</body>
</html>