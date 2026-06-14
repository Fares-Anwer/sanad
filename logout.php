<?php
session_start();
require_once __DIR__ . '/includes/auth.php';
logoutUser();
redirect('login.php?message=signed_out');
