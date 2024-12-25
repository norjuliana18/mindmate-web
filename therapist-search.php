<?php
// Include the database connection file
include 'config.php';

// Handle search query
if (isset($_GET['search'])) {
    $searchTerm = mysqli_real_escape_string($conn, $_GET['search']);

    // Fix the query to use correct column names
    $query = "SELECT * FROM therapist WHERE theraName LIKE '%$searchTerm%'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error: " . $query . "<br>" . mysqli_error($conn));
    }

    // Display search results
    echo "<div class='container'>";
    if ($result->num_rows > 0) {
        echo "<h2>Search Results</h2>";
        while ($row = $result->fetch_assoc()) {
            // Determine the image path or use a default image if empty
            $theraImage = !empty($row['theraImage']) ? 'uploads/' . $row['theraImage'] : 'img/default-profile.png';
            
            echo "<div class='therapist-menu-box'>";
            echo "<div class='therapist-menu-img'>";
            echo "<img src='" . htmlspecialchars($theraImage) . "' alt='" . htmlspecialchars($row['theraName']) . "' class='img-responsive img-curve'>";
            echo "</div>";
            echo "<div class='therapist-menu-desc'>";
            echo "<h4>" . htmlspecialchars($row['theraName']) . "</h4>";
            echo "<p class='therapist-add'>" . htmlspecialchars($row['theraAdd']) . "</p>";
            echo "<p class='therapist-detail'>" . nl2br(htmlspecialchars($row['theraDesc'])) . "</p>";
            echo "<a href='therapist_details.php?theraID=" . htmlspecialchars($row['theraID']) . "' class='btn btn-primary'>View Details</a>";
            echo "</div></div>";
        }
    } else {
        echo "<p>No results found.</p>";
    }
    echo "</div>";

    // Close the database connection
    mysqli_close($conn);
    exit();
}
?>
