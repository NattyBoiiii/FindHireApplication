<?php 
require_once "core/dbConfig.php";
require_once "core/models.php";
require_once "core/handleForms.php";

if(!isset($_SESSION['username'])) {
    header("Location: loginMain.php");
}

if($_SESSION['userStatus'] !== "Applicant") {
    header("Location: index.php");
}

$userID = $_SESSION['userID'];
$applicationStatuses = getApplicationStatuses($pdo, $userID);
$jobs = getAllJobPosts($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for a Job</title>
    <link rel="stylesheet" href="styles/stylesApplicant.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h1>Available Jobs</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <p class="notification"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" action="core/handleForms.php">
            <label for="jobDescID">Select Job:</label>
            <select name="jobDescID" id="jobDescID" required>
                <?php foreach ($jobs as $job): ?>
                    <option value="<?php echo $job['jobDescID']; ?>">
                        <?php echo htmlspecialchars($job['jobTitle']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="selfIntro">Self Introduction:</label>
            <textarea name="selfIntro" id="selfIntro" required></textarea>

            <label for="resume">Upload Resume (PDF only):</label>
            <input type="file" name="resume" id="resume" accept=".pdf" required>

            <button type="submit" name="applyJobBtn">Apply</button>
        </form>
    </div>
    <div class="container">
    <h2>Your Application Status</h2>
    <?php if (!empty($applicationStatuses)): ?>
        <table>
            <thead>
                <tr>
                    <th>HR Username</th>
                    <th>Job Title</th>
                    <th>Job Description</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applicationStatuses as $status): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($status['username']); ?></td>
                        <td><?php echo htmlspecialchars($status['jobTitle']); ?></td>
                        <td><?php echo htmlspecialchars($status['jobDesc']); ?></td>
                        <td>
                            <?php 
                            if ($status['applicationState'] === 'Accepted') {
                                echo "<span style='color: green;'>Accepted</span>";
                            } elseif ($status['applicationState'] === 'Rejected') {
                                echo "<span style='color: red;'>Rejected</span>";
                            } else {
                                echo "<span style='color: orange;'>Evaluating</span>";
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You have not applied for any jobs yet.</p>
    <?php endif; ?>
</div>
</body>
</html>