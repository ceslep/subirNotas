<?php
require_once("headers.php");
require_once("datos_conexion.php");
$mysqli = new mysqli($host, $user, $pass, $database);
$mysqli->query("SET NAMES utf8");
$mysqli->set_charset('utf8');
// Get input data (assuming POST request for parameters)
$input = json_decode(file_get_contents('php://input'), true);

$asignacion = $input['asignacion'] ?? '1';
$grado = $input['grado'] ?? '6-1';
$periodo = $input['periodo'] ?? 'NO';
$year = date("Y"); // Assuming current year for 'year' field

$stmt = null; // Initialize $stmt to null

// Check if parameters for filtered query are provided
if (!empty($asignacion) && !empty($grado) && !empty($periodo)) {
    // Prepare SQL query for filtered data
    $sql = "SELECT
                n.*,
                eg.asignacion,
                eg.grado,
                eg.periodo
            FROM
                notas n
            JOIN
                estugrupos eg ON n.estudiante = eg.estudiante AND n.year = eg.year
            WHERE
                eg.asignacion = ? AND
                eg.grado = ? AND
                eg.periodo = ? AND
                eg.year = ?";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die(json_encode(["error" => "Failed to prepare statement: " . $conn->error]));
    }
    $stmt->bind_param("sssi", $asignacion, $grado, $periodo, $year); // sssi for string, string, string, integer
} else {
    // Prepare SQL query to read the entire 'notas' table
    $sql = "SELECT * FROM notas";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die(json_encode(["error" => "Failed to prepare statement for all notes: " . $conn->error]));
    }
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$stmt->close();
$conn->close();

echo json_encode($data);

?>