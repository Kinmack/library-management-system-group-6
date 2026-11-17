<?php
require_once __DIR__ . '/functions.php';

if (!isset($_SESSION['user_id'])) {
    set_flash('error', 'Please log in to access this page.');
    redirect('auth/login.php');
}