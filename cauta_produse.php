<?php
header('Content-Type: application/json');

$jsonFile = 'produse.json';
if (!file_exists($jsonFile)) {
    http_response_code(500);
    echo json_encode(['error' => 'Fișierul de produse nu există.']);
    exit;
}

$produseData = json_decode(file_get_contents($jsonFile), true);
if ($produseData === null) {
    http_response_code(500);
    echo json_encode(['error' => 'Eroare la citirea fișierului JSON.']);
    exit;
}

$searchTerm = isset($_GET['term']) ? strtolower(trim($_GET['term'])) : '';
$categorie = isset($_GET['categorie']) ? $_GET['categorie'] : '';

$rezultate = [];

if (!empty($categorie) && isset($produseData[$categorie])) {
    foreach ($produseData[$categorie] as $subcategorie => $produse) {
        $produseGasite = array_filter($produse, function($produs) use ($searchTerm) {
            return empty($searchTerm) || strpos(strtolower($produs['nume']), $searchTerm) !== false;
        });

        if (!empty($produseGasite)) {
            $rezultate[$subcategorie] = array_values($produseGasite);
        }
    }
}

echo json_encode($rezultate);
exit;