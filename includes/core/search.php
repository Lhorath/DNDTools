<?php
/*
    Dack's DND Tools - includes/core/search.php
    ===========================================
    This file serves as the central API endpoint for all database queries.
    It has been refactored to use complex subqueries to build complete data
    objects from the new, normalized database schema for REV 012 and beyond.
*/

// SECTION 1: INITIALIZATION
// =========================
// Set the content type to application/json and include the core configuration
// to establish a database connection.
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

// SECTION 2: GET & SANITIZE PARAMETERS
// ======================================
// Retrieve and sanitize all possible URL parameters for security and consistency.
$category = isset($_GET['category']) ? $_GET['category'] : '';
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$index = isset($_GET['index']) ? trim($_GET['index']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
$offset = ($page - 1) * $limit;

// SECTION 3: SECURITY & VALIDATION
// ==================================
// A whitelist of allowed table names is crucial to prevent SQL injection on table names.
$allowed_tables = ['spells', 'monsters', 'equipment', 'magic_items', 'classes', 'races', 'subclasses', 'subraces', 'skills', 'traits', 'proficiencies', 'languages', 'features', 'equipment_categories', 'damage_types', 'conditions', 'ability_scores', 'backgrounds', 'feats', 'alignments'];
if (empty($category) || !in_array($category, $allowed_tables)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or missing category specified.']);
    exit;
}

// SECTION 4: DYNAMIC QUERY BUILDER
// ================================
// This section dynamically constructs the appropriate SQL query. When fetching a single
// detailed item, it uses subqueries to efficiently gather all related data.

$select_sql = "SELECT t1.*";
$from_sql = " FROM `$category` t1";
$group_by_sql = " GROUP BY t1.id";
$order_by_sql = " ORDER BY t1.name ASC";

if (!empty($index)) {
    // Use subqueries for better performance and to handle multiple one-to-many relationships.
    switch ($category) {
        case 'races':
            $select_sql .= ", 
                (SELECT JSON_ARRAYAGG(JSON_OBJECT('name', l.name)) FROM race_languages rl JOIN languages l ON rl.language_index = l.index WHERE rl.race_index = t1.index) as languages,
                (SELECT JSON_ARRAYAGG(JSON_OBJECT('name', p.name)) FROM race_proficiencies rp JOIN proficiencies p ON rp.proficiency_index = p.index WHERE rp.race_index = t1.index) as proficiencies,
                (SELECT JSON_ARRAYAGG(JSON_OBJECT('name', tr.name, 'description', tr.description)) FROM race_traits r_tr JOIN traits tr ON r_tr.trait_index = tr.index WHERE r_tr.race_index = t1.index) as traits,
                (SELECT JSON_ARRAYAGG(JSON_OBJECT('name', sr.name, 'index', sr.index)) FROM subraces sr WHERE sr.race_index = t1.index) as subraces";
            break;
        case 'classes':
            $select_sql .= ", 
                (SELECT JSON_ARRAYAGG(JSON_OBJECT('name', sc.name, 'index', sc.index)) FROM subclasses sc WHERE sc.class_index = t1.index) as subclasses,
                (SELECT JSON_ARRAYAGG(JSON_OBJECT('name', p.name)) FROM class_proficiencies cp JOIN proficiencies p ON cp.proficiency_index = p.index WHERE cp.class_index = t1.index) as proficiencies,
                (SELECT JSON_ARRAYAGG(JSON_OBJECT('name', st.name, 'index', st.index)) FROM class_saving_throws cst JOIN ability_scores st ON cst.ability_score_index = st.index WHERE cst.class_index = t1.index) as saving_throws,
                (SELECT JSON_ARRAYAGG(JSON_OBJECT('desc', pc.description, 'choose', pc.choose, 'type', pc.type, 'options', pc.options)) FROM class_proficiency_choices pc WHERE pc.class_index = t1.index) as proficiency_choices,
                (SELECT JSON_ARRAYAGG(JSON_OBJECT('equipment', e.name, 'quantity', se.quantity)) FROM class_starting_equipment se JOIN equipment e ON se.equipment_index = e.index WHERE se.class_index = t1.index) as starting_equipment,
                (SELECT JSON_ARRAYAGG(JSON_OBJECT('choose', seo.choose, 'desc', seo.description, 'options', seo.options)) FROM class_starting_equipment_options seo WHERE seo.class_index = t1.index) as starting_equipment_options";
            break;
        case 'monsters':
            $select_sql .= ",
                (SELECT JSON_ARRAYAGG(JSON_OBJECT('name', p.name, 'value', mp.value)) FROM monster_proficiencies mp JOIN proficiencies p ON mp.proficiency_index = p.index WHERE mp.monster_index = t1.index) as proficiencies,
                (SELECT JSON_ARRAYAGG(JSON_OBJECT('name', c.name)) FROM monster_condition_immunities mci JOIN conditions c ON mci.condition_index = c.index WHERE mci.monster_index = t1.index) as condition_immunities";
            break;
        case 'spells':
             $select_sql .= ", 
                (SELECT JSON_ARRAYAGG(JSON_OBJECT('name', c.name)) FROM spell_classes scl JOIN classes c ON scl.class_index = c.index WHERE scl.spell_index = t1.index) as classes,
                (SELECT JSON_ARRAYAGG(JSON_OBJECT('name', sc.name)) FROM spell_subclasses s_sc JOIN subclasses sc ON s_sc.subclass_index = sc.index WHERE s_sc.spell_index = t1.index) as subclasses";
             break;
        case 'backgrounds':
            $select_sql .= ",
                (SELECT JSON_ARRAYAGG(JSON_OBJECT('name', p.name)) FROM background_proficiencies bp JOIN proficiencies p ON bp.proficiency_index = p.index WHERE bp.background_index = t1.index) as starting_proficiencies,
                (SELECT JSON_ARRAYAGG(JSON_OBJECT('equipment', e.name, 'quantity', be.quantity)) FROM background_starting_equipment be JOIN equipment e ON be.equipment_index = e.index WHERE be.background_index = t1.index) as starting_equipment";
            break;
    }
}

// SECTION 5: WHERE CLAUSE & PAGINATION COUNT
$params = [];
$types = '';
$whereClauses = [];
if (!empty($index)) {
    $whereClauses[] = "t1.index = ?";
    $params[] = $index;
    $types .= 's';
} else if (!empty($query)) {
    $whereClauses[] = "LOWER(t1.name) LIKE ?";
    $likeQuery = "%" . strtolower($query) . "%";
    $params[] = $likeQuery;
    $types .= 's';
}
$whereSql = !empty($whereClauses) ? " WHERE " . implode(' AND ', $whereClauses) : '';
$total_records = 0;
$total_pages = 1;
if (empty($index)) {
    $count_sql = "SELECT COUNT(DISTINCT t1.id) as total FROM `$category` t1" . $whereSql;
    $stmt_count = $conn->prepare($count_sql);
    if ($stmt_count && !empty($params)) { $stmt_count->bind_param($types, ...$params); }
    if ($stmt_count) {
        $stmt_count->execute();
        $total_records = $stmt_count->get_result()->fetch_assoc()['total'];
        $total_pages = ceil($total_records / $limit);
        $stmt_count->close();
    }
}

// SECTION 6: EXECUTE QUERY & PROCESS RESULTS
$sql = $select_sql . $from_sql . $whereSql . $group_by_sql . $order_by_sql;
if (empty($index)) {
    $sql .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
}

$stmt = $conn->prepare($sql);
$data = [];
if ($stmt) {
    if (!empty($params)) { $stmt->bind_param($types, ...$params); }
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
        foreach ($row as $key => $value) {
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                if (is_array($decoded) && isset($decoded[0]) && is_array($decoded[0]) && isset($decoded[0]['name']) && $decoded[0]['name'] === null) {
                    $row[$key] = [];
                } else if (json_last_error() === JSON_ERROR_NONE) {
                    $row[$key] = $decoded;
                }
            }
        }
        $data[] = $row;
    }
    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare the main data query.', 'sql_error' => $conn->error]);
    exit;
}

// SECTION 7: SEND JSON RESPONSE
$conn->close();
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
