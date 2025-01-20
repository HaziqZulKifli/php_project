<?php
function calculatePowerAndCost($voltage, $current, $rate, $day) {
    // Calculate power in kW
    $power = ($voltage * $current) / 1000;

    // Convert rate to RM/kWh = current rate
    $rateInRM = $rate / 100;



    // Prepare hourly calculations
    //prepare an array to store calculation for each hour (1 to 24)
    $hourlyData = [];

    for ($hour = 1; $hour <= 24; $hour++) {
        // Energy in kWh
        $energy = $power * $hour; 

        // Total cost in RM
        $totalCost = $energy * $rateInRM;
        
        //Store the results in the array with formatted values
        $hourlyData[] = [
            // Current hour
            'hour' => $hour,

            // Energy consumption in kWh (formatted to 5 decimal places)
            'energy' => number_format($energy, 5),

            // Total cost in RM (formatted to 2 decimal places)
            'totalCost' => number_format($totalCost, 2),
        ];
    }

    $totalCostdays = ($power *(24*$day)) * $rateInRM;
    

    // Return an associative array containing the power, rate in RM, and hourly data
    return [
        // Power in kW
        'power' => $power,

        // Electricity rate in RM/kWh
        'rateInRM' => $rateInRM,

        // Array of hourly calculations
        'hourlyData' => $hourlyData,

        'totalCostdays' => $totalCostdays, 
    ];
}

// Check if the form has been submitted (via POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Retrieve the input values from the form which the user key in
    $voltage = $_POST['voltage'];
    $current = $_POST['current'];
    $rate = $_POST['rate'];
    $day = $_POST['day'] ?? null;

    if ($day !== null && !is_numeric($day)) {
        $day = 1; // Set default value to 1 if day is not numeric
    } elseif ($day === null) {
        $day = 1; // Set default value to 1 if day is empty
    }


    // Call the function to perform calculations and store the results
    $calculationResults = calculatePowerAndCost($voltage, $current, $rate,$day);
    $power = $calculationResults['power'];
    $rateInRM = $calculationResults['rateInRM'];
    $hourlyData = $calculationResults['hourlyData'];
    $totalCostdays = $calculationResults['totalCostdays'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electricity Bill Calculator</title>

     <!-- Include Bootstrap CSS for styling -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Electricity Bill Calculator</h1>

        <!-- Form to collect user inputs for voltage, current, and rate -->
        <form action="" method="post">
            <div class="form-group">
                <label for="voltage">Voltage (V)</label>
                <input type="number" step ="any" class="form-control" id="voltage" value="<?= $voltage ?>" name="voltage" required>
            </div>
            <div class="form-group">
                <label for="current">Current (A)</label>
                <input type="number" step ="any" class="form-control" id="current" value="<?= $current ?>" name="current" required>
            </div>
            <div class="form-group">
                <label for="rate">Current Rate (sen/kWh)</label>
                <input type="number" step ="any" class="form-control" id="rate" value="<?= $rate ?>" name="rate" required>
            </div>

            <div class="form-group">
                <label for="day">Enter days of total (*optional)</label>
                <input type="number" step ="any" class="form-control" id="day" value="<?= $day ?>" name="day">
            </div>
            <div class="form-group">

            <button type="submit" class="btn btn-primary" name="calculate">Calculate</button>
        </form>
        <?php if (isset($power)) : ?>
        <!-- Display the calculated power and rate -->
        <div class="mt-4 p-3 border rounded">
            <p><strong>POWER:</strong> <?= number_format($power, 5) ?> kW</p>
            <p><strong>RATE:</strong> <?= number_format($rateInRM, 3) ?> RM</p>
            <p><strong>Total cost for <?= $day ?> day:</strong> <?= number_format($totalCostdays, 2) ?> RM</p>
        </div>

         <!-- Table to display hourly energy consumption and costs -->
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Hour</th>
                    <th>Energy (kWh)</th>
                    <th>Total (RM)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hourlyData as $row): ?>
                    <tr>
                        <td><b><?= $row['hour'] ?></b></td>
                        <td><?= $row['hour'] ?></td>
                        <td><?= $row['energy'] ?></td>
                        <td><?= $row['totalCost'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</body>
</html>