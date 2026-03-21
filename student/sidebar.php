<?php
// Get current page filename
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="fa-solid fa-graduation-cap"></i>
        <h2>Registrar SIS<span>Student Portal</span></h2>
    </div>
    <ul class="nav-list">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-table-cells-large"></i><span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="personal_info.php" class="nav-link <?php echo $current_page == 'personal_info.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-user"></i><span>My Profile</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="academic_history.php" class="nav-link <?php echo $current_page == 'academic_history.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-book-open"></i><span>Academic Records</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="health_record.php" class="nav-link <?php echo $current_page == 'health_record.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-truck-medical"></i><span>Health Records</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="qr_code.php" class="nav-link <?php echo $current_page == 'qr_code.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-qrcode"></i><span>My ID / QR</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="document_request.php" class="nav-link <?php echo $current_page == 'document_request.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-file-invoice"></i><span>Request Documents</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="status_tracker.php" class="nav-link <?php echo $current_page == 'status_tracker.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-chart-line"></i><span>Status Tracker</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="storage.php" class="nav-link <?php echo $current_page == 'storage.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-folder-tree"></i><span>Digital Files</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="../registrar/api/logout.php" class="nav-link" style="margin-top: 2rem; color: #fca5a5;">
                <i class="fa-solid fa-right-from-bracket"></i><span>Logout</span>
            </a>
        </li>
    </ul>
</aside>
