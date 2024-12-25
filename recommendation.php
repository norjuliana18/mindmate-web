<?php
function getRecommendedTherapists($conn)
{
    $recommended_therapists = [];

    // Query to fetch recommended therapists based on appointments
    $sql = "SELECT DISTINCT t.theraID
            FROM appointment a
            JOIN therapist t ON a.theraID = t.theraID
            ORDER BY RAND() LIMIT 3";  // Adjust the limit as needed

    $result = mysqli_query($conn, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $recommended_therapists[] = $row['theraID'];
        }
    }

    return $recommended_therapists;
}
?>

