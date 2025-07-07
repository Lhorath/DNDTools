<?php
/*
    Dack's DND Tools - includes/core/fill-pdf.php
    =============================================
    This script receives character sheet data, loads a PDF template, and writes
    the data at specific coordinates to generate a filled character sheet.
*/

// SECTION 1: INITIALIZATION & LIBRARY REQUIREMENT
// Ensure the FPDF and FPDI libraries are available.
require_once __DIR__ . '/../lib/fpdf/fpdf.php';
require_once __DIR__ . '/../lib/fpdi/src/autoload.php';

use setasign\Fpdi\Fpdi;

// Only process POST requests.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// Get the character data sent from the browser.
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    exit('Invalid JSON data provided.');
}

// SECTION 2: PDF FIELD COORDINATE MAPPING
// This array maps HTML form field IDs to their exact X and Y coordinates on the PDF page.
// Format: 'html_id' => ['x' => X_COORD, 'y' => Y_COORD, 'type' => 'text'/'check']
$fieldCoordinates = [
    // Character Info
    'char-name' => ['x' => 62, 'y' => 61, 'type' => 'text'],
    'class' => ['x' => 245, 'y' => 47, 'type' => 'text'],
    'level' => ['x' => 280, 'y' => 47, 'type' => 'text'],
    'background' => ['x' => 345, 'y' => 47, 'type' => 'text'],
    'player-name' => ['x' => 440, 'y' => 47, 'type' => 'text'],
    'race' => ['x' => 245, 'y' => 74, 'type' => 'text'],
    'alignment' => ['x' => 345, 'y' => 74, 'type' => 'text'],
    'exp' => ['x' => 440, 'y' => 74, 'type' => 'text'],

    // Ability Scores & Mods
    'strength' => ['x' => 70, 'y' => 140, 'type' => 'text', 'size' => 16],
    'strength-mod' => ['x' => 73, 'y' => 161, 'type' => 'text'],
    'dexterity' => ['x' => 70, 'y' => 200, 'type' => 'text', 'size' => 16],
    'dexterity-mod' => ['x' => 73, 'y' => 221, 'type' => 'text'],
    'constitution' => ['x' => 70, 'y' => 260, 'type' => 'text', 'size' => 16],
    'constitution-mod' => ['x' => 73, 'y' => 281, 'type' => 'text'],
    'intelligence' => ['x' => 70, 'y' => 320, 'type' => 'text', 'size' => 16],
    'intelligence-mod' => ['x' => 73, 'y' => 341, 'type' => 'text'],
    'wisdom' => ['x' => 70, 'y' => 380, 'type' => 'text', 'size' => 16],
    'wisdom-mod' => ['x' => 73, 'y' => 401, 'type' => 'text'],
    'charisma' => ['x' => 70, 'y' => 440, 'type' => 'text', 'size' => 16],
    'charisma-mod' => ['x' => 73, 'y' => 461, 'type' => 'text'],
    
    // Proficiency & Perception
    'proficiency-bonus' => ['x' => 118, 'y' => 147, 'type' => 'text'],
    'passive-perception' => ['x' => 60, 'y' => 497, 'type' => 'text'],

    // Saving Throws
    'strength-save' => ['x' => 170, 'y' => 201, 'type' => 'text'],
    'dexterity-save' => ['x' => 170, 'y' => 218, 'type' => 'text'],
    'constitution-save' => ['x' => 170, 'y' => 235, 'type' => 'text'],
    'intelligence-save' => ['x' => 170, 'y' => 252, 'type' => 'text'],
    'wisdom-save' => ['x' => 170, 'y' => 269, 'type' => 'text'],
    'charisma-save' => ['x' => 170, 'y' => 286, 'type' => 'text'],
    'strength-save-prof' => ['x' => 157, 'y' => 200, 'type' => 'check'],
    'dexterity-save-prof' => ['x' => 157, 'y' => 217, 'type' => 'check'],
    'constitution-save-prof' => ['x' => 157, 'y' => 234, 'type' => 'check'],
    'intelligence-save-prof' => ['x' => 157, 'y' => 251, 'type' => 'check'],
    'wisdom-save-prof' => ['x' => 157, 'y' => 268, 'type' => 'check'],
    'charisma-save-prof' => ['x' => 157, 'y' => 285, 'type' => 'check'],
    
    // ... (Add coordinates for all other fields as needed)
];


// SECTION 3: PDF GENERATION
// =========================
$pdf = new Fpdi();
$pdf->AddPage();
// The source file must be located where the script can access it.
$pdf->setSourceFile(__DIR__ . '/../../5E_CharacterSheet_Fillable.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0, 210); // Use A4 width

// Set the font and color for the text to be added.
$pdf->SetFont('Helvetica');
$pdf->SetTextColor(0, 0, 0);

// Loop through the coordinate map and write data onto the PDF.
foreach ($fieldCoordinates as $formId => $coords) {
    if (isset($data[$formId]) && $data[$formId] !== '') {
        $pdf->SetXY($coords['x'], $coords['y']);
        
        if ($coords['type'] === 'check' && $data[$formId] === true) {
            // Draw a filled circle for checkboxes
            $pdf->SetFillColor(0,0,0);
            $pdf->Cell(3, 3, '', 0, 1, 'C', true);
        } elseif ($coords['type'] === 'text') {
            $fontSize = $coords['size'] ?? 10;
            $pdf->SetFont('Helvetica', '', $fontSize);
            $pdf->Write(0, $data[$formId]);
        }
    }
}

// Output the filled PDF. 'D' forces a download dialog.
$pdf->Output('D', 'DND_Character_Sheet.pdf');

?>
