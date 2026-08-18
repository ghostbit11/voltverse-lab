<?php
require_once __DIR__ . '/inc/core.php';
header('Location: ' . (pf_user() ? '/dashboard.php' : '/login.php'));
