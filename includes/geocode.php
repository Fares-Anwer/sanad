<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

header('Content-Type: application/json');

if (!isset($_GET['q']) || trim($_GET['q']) === '') {
    jsonResponse(['error' => 'معامل البحث مطلوب'], 400);
}

$query = trim($_GET['q']);
$results = nominatimGeocode($query);

if ($results === null) {
    jsonResponse(['error' => 'فشل البحث عن الموقع'], 500);
}

jsonResponse(['results' => $results]);
