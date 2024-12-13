<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindHire Login</title>
    <link rel="stylesheet" href="styles/stylesLogReg.css?v=<?php echo time(); ?>">
</head>
<body>
    <h1>FindHire Login</h1>
    <?php if (isset($_SESSION['message'])) { ?>
        <p class="notification"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
    <?php } ?>
    <form action=" core/handleForms.php" method="POST">
        <p>
            <label for="username">Username:</label>
            <input type="text" name="username" required>
        </p>
        <p>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
        </p>
        <p>
            <button type="submit" name="loginBtn">Login to FindHire</button>
        </p>
    </form>
    <input type="submit" value="Return to Main Page" onclick="window.location.href='loginMain.php'">
</body>
</html>