<?php
/*
    Dack's DND Tools - api/search.php
    =================================
    This file serves as the central API endpoint for all database queries.
    It handles fetching lists of data (e.g., all spells) with optional searching
    and pagination, as well as fetching single, specific items by their index.
*/

// --- SECTION 1: INITIALIZATION AND HEADERS ---
// Set the content type to application/json so browsers interpret the output correctly.
header('Content-Type: application/json');
// Include the database configuration file.
require_once '../functions/config.php';

// --- SECTION 2: GET PARAMETERS ---
// Retrieve and sanitize all possible URL parameters.
$category = isset($_GET['category']) ? $_GET['category'] : '';
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$index = isset($_GET['index']) ? trim($_GET['index']) : ''; // For fetching a single item
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// --- SECTION 3: VALIDATION ---
// A whitelist of allowed table names to prevent unauthorized database access.
$allowed_tables = ['spells', 'monsters', 'equipment', 'magic_items', 'classes', 'races', 'subclasses', 'subraces', 'skills', 'traits', 'proficiencies', 'languages', 'features', 'equipment_categories', 'damage_types', 'conditions', 'ability_scores'];

if (empty($category) || !in_array($category, $allowed_tables)) {
    // If the category is missing or not in the whitelist, send an error and exit.
    echo json_encode(['error' => 'Invalid or missing category specified.']);
    exit;
}

// --- SECTION 4: BUILD AND EXECUTE QUERY ---
$params = [];
$types = '';
$whereClauses = [];

// If an index is provided, prioritize fetching that single item.
if (!empty($index)) {
    $whereClauses[] = "`index` = ?";
    $params[] = $index;
    $types .= 's';
}
// Otherwise, if a search query is provided, perform a search.
else if (!empty($query)) {
    $whereClauses[] = "LOWER(`name`) LIKE ?";
    $likeQuery = "%" . strtolower($query) . "%";
    $params[] = $likeQuery;
    $types .= 's';
}

// Construct the final WHERE part of the SQL query.
$whereSql = '';
if (!empty($whereClauses)) {
    $whereSql = " WHERE " . implode(' AND ', $whereClauses);
}

// --- SECTION 5: PAGINATION COUNT (only if not fetching a single item) ---
$total_records = 0;
$total_pages = 1;

if (empty($index)) {
    // Prepare and execute a query to count the total number of matching records for pagination.
    $count_sql = "SELECT COUNT(*) as total FROM `$category`" . $whereSql;
    $stmt_count = $conn->prepare($count_sql);

    if ($stmt_count === false) {
        echo json_encode(['error' => 'Failed to prepare count statement: ' . $conn->error]);
        exit;
    }

    if (!empty($params)) {
        $stmt_count->bind_param($types, ...$params);
    }

    $stmt_count->execute();
    $count_result = $stmt_count->get_result()->fetch_assoc();
    $total_records = $count_result['total'];
    $total_pages = ceil($total_records / $limit);
    $stmt_count->close();
}

// --- SECTION 6: FETCH DATA ---
// Prepare the main data query.
$sql = "SELECT * FROM `$category`" . $whereSql;

// Add sorting and pagination clauses only if we are fetching a list.
if (empty($index)) {
    $sql .= " ORDER BY name ASC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
}

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(['error' => 'Failed to prepare statement: ' . $conn->error]);
    exit;
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Many columns in the database store JSON data as text.
        // This loop attempts to decode any string value into a JSON object.
        foreach ($row as $key => $value) {
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $row[$key] = $decoded;
                }
            }
        }
        $data[] = $row;
    }
}

$stmt->close();
$conn->close();

// --- SECTION 7: SEND JSON RESPONSE ---
// Assemble the final JSON response object.
echo json_encode([
    'results' => $data,
    'pagination' => [
        'total_records' => $total_records,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'limit' => $limit
    ]
]);
?>
