<?php

// Define API URL
$apiUrl = "https://data.gov.bh/api/explore/v2.1/catalog/datasets/01-statistics-of-students-nationalities_updated/records?where=colleges%20like%20%22IT%22%20AND%20the_programs%20like%20%22bachelor%22&limit=100";

try {
    // Fetch data from the API
    $apiResponse = file_get_contents($apiUrl);

    // Check if the response is valid, throw an error if not
    if ($apiResponse === false) {
        throw new Exception("Unable to retrieve data from the API");
    }
    // Decode JSON response
    $data = json_decode($apiResponse, true);
   // Get the results array from the API data or set to an empty array if not available
    $studentsData = $data['results'] ?? []; 
} catch (Exception $error) {
    $errorNotification = $error->getMessage();// Catch any errors and save the error message to display it later
}
// Get the search query from the URL parameters
$searchQuery = $_GET['nationality'] ?? ''; 
// Filter the student data to match the nationality query
$filteredData = array_filter($studentsData, function ($student) use ($searchQuery) {
    $nationality = $student['nationality'] ?? '';
    return empty($searchQuery) || stripos($nationality, $searchQuery) !== false;// Return true if the query is empty or matches the nationality
});
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Nationalities</title>
    <meta name="description" content="Visualizing UOB student nationality data">
    <!--  CSS framework for styling -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2.0.6/css/pico.min.css">
</head>
<body>
<header>
    <h1>UOB Bachelor Student Nationality Data</h1> 
</header>

<main>
    <!-- Form for searching by nationality -->
    <form method="GET">
        <!-- Input for nationality search -->
        <input type="text" name="nationality" placeholder="Search by nationality" value="<?php echo htmlspecialchars($searchQuery); ?>">
        <button type="submit">Search</button>
        <a href="?" class="secondary">Reset</a>
    </form>

    <?php if (isset($errorNotification)): ?>
    <!-- Display an error message if something went wrong -->
        <article>
            <h4>Error:</h4>
            <p><?php echo htmlspecialchars($errorNotification); ?></p>
        </article>

    <?php elseif (!empty($filteredData)): ?>
    <!-- If the it's not empty display the data in a table  -->
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
                         <!-- Display the student data in table rows -->
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
     <!-- Display a message if no data matches the search -->
        <article>
            <h4>No data Found.</h4>
        </article>
    <?php endif; ?>
</main>
</body>
</html>
