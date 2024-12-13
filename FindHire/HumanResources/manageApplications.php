<?php
require_once "core/dbConfig.php";
require_once "core/models.php";

if(!isset($_SESSION['username'])) {
    header("Location: /FindHire/loginMain.php");
}

if ($_SESSION['userStatus'] != "HR") {
    header("Location: ../index.php");
}
$applications = getJobApplications();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Applications</title>
    <link rel="stylesheet" href="../styles/stylesHR.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include 'navbarHR1.php'; ?>
    <div class="container">
        <h1>Manage Applications</h1>
        <?php if (isset($_SESSION['message'])) { ?>
            <p class="notification"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
        <?php } ?>
        <table>
            <thead>
                <tr>
                    <th>Applicant Name</th>
                    <th>Job Title</th>
                    <th>Self-Introduction</th>
                    <th>Attachment</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($applications)) { ?>
                    <?php foreach ($applications as $app) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($app['firstName'] . ' ' . $app['lastName']); ?></td>
                            <td><?php echo htmlspecialchars($app['jobTitle']); ?></td>
                            <td><?php echo htmlspecialchars(substr($app['selfIntro'], 0, 100)) . '...'; ?></td>
                            <td>
                                <?php if (!empty($app['attachment'])) { ?>
                                    <a href="/FindHire/downloadResume.php?file=<?php echo urlencode($app['attachment']); ?>&applicationID=<?php echo $app['applicationID']; ?>&jobDescID=<?php echo $app['jobDescID']; ?>">Download</a>
                                <?php } else { ?>
                                    None
                                <?php } ?>
                            </td>
                            <td><?php echo htmlspecialchars($app['applicationState']); ?></td>
                            <td>
                                <?php if ($app['applicationState'] === 'Evaluating') { ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="applicationID" value="<?php echo $app['applicationID']; ?>">
                                        <button type="submit" name="action" value="accept">Accept</button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="applicationID" value="<?php echo $app['applicationID']; ?>">
                                        <button type="submit" name="action" value="reject">Reject</button>
                                    </form>
                                <?php } else { ?>
                                    No Actions Available
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td>No applications found.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <footer>
        <p>&copy; 2024 FindHire. All rights reserved.</p>
    </footer>
</body>
</html>