<?php
require_once "core/dbConfig.php";

if(!isset($_SESSION['username'])) {
    header("Location: /FindHire/loginMain.php");
}

if ($_SESSION['userStatus'] != "HR") {
    header("Location: ../index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Job Post</title>
    <link rel="stylesheet" href="../styles/stylesHR.css?v=<?php echo time(); ?>">
</head>
<body>
<?php include 'navbarHR1.php'; ?>
    <h1>Create Job Post</h1>
    <?php if (isset($_SESSION['message'])) { ?>
        <p class="notification"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
    <?php } ?>
    <form action="core/handleForms.php" method="POST">
        <input type="hidden" name="formType" value="createJob">
        <label for="jobTitle">Job Title:</label>
        <input type="text" id="jobTitle" name="jobTitle" required>
        
        <label for="jobDesc">Job Description:</label>
        <textarea id="jobDesc" name="jobDesc" rows="5" required></textarea>
        
        <button type="submit" name="submitJob">Post Job</button>
    </form>
</body>
</html>