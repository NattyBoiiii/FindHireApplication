<?php
require_once "core/models.php";
require_once "core/handleForms.php";
require_once 'core/dbConfig.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to FindHire</title>
    <link rel="stylesheet" href="styles/styles.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container">
        <h2>Welcome to FindHire</h2>
        <p>Please select your role and what you want to do:</p>
        <div class="buttons">
            <a href="HumanResources/registerHR.php" class="button">HR Register</a>
            <a href="login.php" class="button">FindHire Login</a>
            <a href="register.php" class="button">Applicant Register</a>
        </div>
    </div>
</body>
</html>