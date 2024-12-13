<?php 
require_once "core/dbConfig.php";
require_once "core/models.php";

if (!isset($_SESSION['userID'])) {
    header("Location: loginMain.php");
    exit;
}

$userRole = $_SESSION['userStatus'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindHire Dashboard</title>
    <link rel="stylesheet" href="styles/stylesIndex.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container">
        <?php if ($userRole == "HR") { ?>
            <?php include 'navbarHR1.php'; ?> 
            <h2>Welcome to the HR Dashboard</h2>
            <?php if (isset($_SESSION['message'])) { ?>
                <p class="notification"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
            <?php } ?>
            <div class="dashboard-options">
                <div class="option-card">
                    <a href="/FindHire/HumanResources/createJob.php">
                    <h3>Post a Job</h3>
                    <p>Create and share new job opportunities.</p>
                    </a>
                </div>
                <div class="option-card">
                    <a href="/FindHire/HumanResources/manageApplications.php">
                    <h3>Manage Applications</h3>
                    <p>Review and manage candidate applications.</p>
                    </a>
                </div>
                <div class="option-card">
                    <a href="/FindHire/HumanResources/replyMessages.php">
                    <h3>Messages</h3>
                    <p>Communicate with candidates and respond to inquiries.</p>
                    </a>
                </div>
                <div class="option-card">
                    <a href="/FindHire/core/handleForms.php?logoutBtn=true">
                    <h3>Logout</h3>
                    <p>Sign out from your account.</p>
                    </a>
                </div>
                </div>
            <h2>Listed Job Posts</h2>
            <div class="job-listings">
                <?php
                $jobs = getAllJobPosts($pdo);
                if (!empty($jobs)) {
                    foreach ($jobs as $job) {
                        echo "<div class='job-item'>";
                        echo "<h3>" . htmlspecialchars($job['jobTitle']) . "</h3>";
                        echo "<p>" . htmlspecialchars($job['jobDesc']) . "</p>";
                        echo "<p><strong>Posted on:</strong> " . date('F j, Y', strtotime($job['date_posted'])) . "</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No job posts have been made yet.</p>";
                }
                ?>
            </div>
            <h2>Recent Inquiries</h2>
            <div class="inquiries">
                <?php
                $messages = getApplicantMessages($pdo); 
                if (!empty($messages)) {
                    foreach ($messages as $message) {
                        echo "<div class='inquiry-item'>";
                        echo "<p><strong>From:</strong> " . htmlspecialchars($message['firstName']) . " " . htmlspecialchars($message['lastName']) . "</p>";
                        echo "<p><strong>Message:</strong> " . htmlspecialchars($message['comDescription']) . "</p>";
                        echo "<p><strong>Date:</strong> " . date('F j, Y', strtotime($message['date_added'])) . "</p>";
                        echo "<p><a href='/FindHire/HumanResources/replyMessages.php?inquiryID=" . $message['inquiryID'] . "'>Reply</a></p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No inquiries received yet.</p>";
                }
                ?>
            </div>
        <?php } elseif ($userRole == "Applicant") { ?>
            <?php include 'navbar.php'; ?> 
            <h2>Welcome to the Applicant Dashboard</h2>
            <?php if (isset($_SESSION['message'])) { ?>
                <p class="notification"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
            <?php } ?>
            <div class="dashboard-options">
                <div class="option-card">
                    <a href="/FindHire/core/handleForms.php?logoutBtn=true">
                    <h3>Logout</h3>
                    <p>Sign out from your account securely.</p>
                    </a>
                </div>
            </div>
            <h2>Listed Job Posts</h2>
            <div class="job-listings">
                <?php
                $jobs = getAllJobPosts($pdo, isset($_GET['searchQuery']) ? sanitizeInput($_GET['searchQuery']) : "");
                if (!empty($jobs)) {
                    foreach ($jobs as $job) {
                        echo "<div class='job-item'>";
                        echo "<h3>" . htmlspecialchars($job['jobTitle']) . "</h3>";
                        echo "<p>" . htmlspecialchars($job['jobDesc']) . "</p>";
                        echo "<p><strong>Posted on:</strong> " . date('F j, Y', strtotime($job['date_posted'])) . "</p>";
                        echo "<p><a href='applyJob.php?jobID=" . $job['jobDescID'] . "'>Apply Now</a></p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No job posts have been made yet.</p>";
                }
                ?>
            </div>
        <?php } else { ?>
            <p>You do not have permission to access this page. Please log in as either an HR or Applicant.</p>
        <?php } ?>
    </div>
    <footer>
        <p>&copy; 2024 FindHire. All rights reserved.</p>
    </footer>
</body>
</html>