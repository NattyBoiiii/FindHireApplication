<?php
require_once "core/dbConfig.php";
require_once "core/models.php";

if(!isset($_SESSION['username'])) {
    header("Location: /FindHire/loginMain.php");
}

if ($_SESSION['userStatus'] != "HR") {
    header("Location: ../index.php");
}

?>
<div class="greeting">
	<h1>Welcome to FindHire!, <span style="color: blue;"><?php echo $_SESSION['username']; ?></span></h1>
</div>
<nav>
    <ul>
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="createJob.php">Post Job</a></li>
        <li><a href="manageApplications.php">Manage Applications</a></li>
        <li><a href="replyMessages.php">Messages</a></li>
        <li><a href="../core/handleForms.php?logoutBtn=true">Logout</a></li>
    </ul>
</nav>