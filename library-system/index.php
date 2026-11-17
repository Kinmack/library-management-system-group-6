<?php
require_once __DIR__ . '/config/database.php';

if (isset($_SESSION['user_id'])) {
    redirect('dashboard/index.php');
}

redirect('auth/login.php');