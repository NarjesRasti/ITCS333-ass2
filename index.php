<?php

$apiUrl = "https://data.gov.bh/api/explore/v2.1/catalog/datasets/01-statistics-of-students-nationalities_updated/records?where=colleges%20like%20%22IT%22%20AND%20the_programs%20like%20%22bachelor%22&limit=100";

try {
    $apiResponse = file_get_contents($apiUrl);

    if ($apiResponse === false) {
        throw new Exception("Unable to retrieve data from the API");
    }

    $data = json_decode($apiResponse, true);
    $studentsData = $data['results'] ?? []; 
} catch (Exception $error) {
    $errorNotification = $error->getMessage();
}

$searchQuery = $_GET['nationality'] ?? ''; 
$filteredData = array_filter($studentsData, function ($student) use ($searchQuery) {
    $nationality = $student['nationality'] ?? '';
    return empty($searchQuery) || stripos($nationality, $searchQuery) !== false;
});
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Nationalities</title>
    <meta name="description" content="Visualizing UOB student nationality data">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2.0.6/css/pico.min.css">
</head>
<body>
<header>
    <h1>UOB Bachelor Student Nationality Data</h1> 
</header>

<main>
    <form method="GET">
        <input type="text" name="nationality" placeholder="Search by nationality" value="<?php echo htmlspecialchars($searchQuery); ?>">
        <button type="submit">Search</button>
        <a href="?" class="secondary">Reset</a>
    </form>

    <?php if (isset($errorNotification)): ?>
        <article>
            <h4>Error:</h4>
            <p><?php echo htmlspecialchars($errorNotification); ?></p>
        </article>

    <?php elseif (!empty($filteredData)): ?>
        <div class="overflow-container">
            <table class="striped">
                <thead>
                    <tr>
                        <th>Year</th>
                        <th>Semester</th>
                        <th>Program</th>
                        <th>Nationality</th>
                        <th>College</th>
                        <th>Student Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filteredData as $student): ?>
                        <?php 
                            $year = $student['year'] ?? 'Unknown';
                            $semester = $student['semester'] ?? 'Unknown';
                            $program = $student['the_programs'] ?? 'Not specified';
                            $nationality = $student['nationality'] ?? 'Not provided';
                            $college = $student['colleges'] ?? 'Unknown';
                            $count = $student['number_of_students'] ?? 'N/A';
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($year); ?></td>
                            <td><?php echo htmlspecialchars($semester); ?></td>
                            <td><?php echo htmlspecialchars($program); ?></td>
                            <td><?php echo htmlspecialchars($nationality); ?></td>
                            <td><?php echo htmlspecialchars($college); ?></td>
                            <td><?php echo htmlspecialchars($count); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <article>
            <h4>No data Found.</h4>
        </article>
    <?php endif; ?>
</main>
</body>
</html>
