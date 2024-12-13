<?php
require_once "core/dbConfig.php";
require_once "core/models.php";
?>
<link rel="stylesheet" href="styles/stylesNavbar.css?v=<?php echo time(); ?>">
<div class="greeting">
	<h1>Welcome to FindHire!, <span style="color: blue;"><?php echo $_SESSION['username']; ?></span></h1>
</div>
<nav>
    <ul>
        <li><a href="/FindHire/index.php">Dashboard</a></li>
        <li><a href="/FindHire/HumanResources/createJob.php">Post Job</a></li>
        <li><a href="/FindHire/HumanResources/manageApplications.php">Manage Applications</a></li>
        <li><a href="/FindHire/HumanResources/replyMessages.php">Messages</a></li>
        <li><a href="/FindHire/core/handleForms.php?logoutBtn=true">Logout</a></li>
    </ul>
</nav>