<?php
require_once "core/dbConfig.php";
require_once "core/handleForms.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Registration for FindHire</title>
    <link rel="stylesheet" href="../styles/stylesLogReg.css?v=<?php echo time(); ?>">
</head>
<body>
    <h1>Register as HR for FindHire</h1>

    <?php if (isset($_SESSION['message'])) { ?>
        <p class="notification"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
        <?php unset($_SESSION['message']); ?>
    <?php } ?>

    <form action="core/handleForms.php" method="POST">
        <p>
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required>
        </p>
        <p>
            <label for="firstname">First Name</label>
            <input type="text" name="firstname" id="firstname" required>
        </p>
        <p>
            <label for="lastname">Last Name</label>
            <input type="text" name="lastname" id="lastname" required>
        </p>
        <p>
            <label for="age">Age</label>
            <input type="number" name="age" id="age" min="18" required>
        </p>
        <p>
            <label for="currentAddress">Current Address</label>
            <input type="text" name="currentAddress" id="currentAddress" required>
        </p>
        <p>
            <label for="emailAddress">Email Address</label>
            <input type="email" name="emailAddress" id="emailAddress" required>
        </p>
        <p>
            <label for="phoneNumber">Phone Number</label>
            <input type="text" name="phoneNumber" id="phoneNumber" required>
        </p>
        <p>
            <label for="gender">Gender</label>
            <select name="gender" id="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </p>
        <p>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </p>
        <p>
            <input type="hidden" name="userStatus" value="HR">
            <input type="submit" name="registerHRBtn" value="Register">
        </p>
    </form>
    <input type="submit" value="Return to Main Page" onclick="window.location.href='/FindHire/loginMain.php'">
</body>
</html>