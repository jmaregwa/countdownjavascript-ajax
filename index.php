<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Digim Games</title>
<style>
    body {
        background-image: url('background.jpg');
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }
    .panel {
        border: 1px solid #ccc;
        padding: 20px;
        margin: 20px auto;
        width: 400px;
        text-align: center;
        background-color: #ffffff;
    }
    .hidden {
        display: none;
    }
    .navbar {
        overflow: hidden;
        background-color: #333;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
    }
    .navbar a {
        color: #f2f2f2;
        text-decoration: none;
        padding: 14px 16px;
    }
    .navbar a:hover {
        background-color: #ddd;
        color: black;
    }
    .logo img {
        max-height: 30px;
        max-width: 100px;
        vertical-align: middle;
    }
    .popup-form {
        position: fixed;
        top: 40%; /* Adjusted top position */
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #444;
        padding: 30px; /* Increased padding */
        border-radius: 10px;
        color: #fff;
        z-index: 9999;
        width: 300px; /* Increased width */
    }
    /* Adjust font size and add spacing */
    .panel h2 {
        font-size: 28px;
    }
    #countdown_<?php echo $panelId; ?> {
        font-size: 24px !important;
        font-weight: bold !important;
        margin-top: 10px;
    }
    .panel div {
        font-size: 15px;
        margin-top: 10px;
    }
    .panel form {
        margin-top: 20px;
    }
    .popup-form label {
        display: block;
        margin-bottom: 10px; /* Added space between label and input */
    }
    .popup-form input[type="text"] {
        width: calc(100% - 20px); /* Adjusted input width */
        margin-bottom: 10px; /* Added space between input and button */
    }
</style>
</head>
<body>

<div class="navbar">
    <div class="logo">
        <img src="logo.jpeg" alt="Logo">
    </div>
    <div class="menu">
        <a href="#">Home</a>
        <a href="winners.php">Winner's History</a>
        <a href="http://magicpot.digimtec.com/" style="font-size: 15px;">Magic Pot</a>
    </div>
</div>

<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "digimgames";

// Set the time zone to match the database
date_default_timezone_set('UTC');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch target stop time and amount from the database
function getPanelData($panelId, $conn) {
    $sql = "SELECT target_time, amount FROM panels WHERE panelid = '$panelId'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $targetTime = strtotime($row['target_time']);
        $amount = $row['amount'];
        if ($targetTime !== false && $targetTime > time()) {
            return ['targetTime' => $targetTime * 1000, 'amount' => $amount];
        }
    }
    return null;
}

// Define the startCountdown function before the loop
?>
<script>
// Move the startCountdown function definition outside the DOMContentLoaded event listener
function startCountdown(targetTime, panelId, amount) {
    var countdownElement = document.getElementById('countdown_' + panelId);

    if (countdownElement) {
        var countdownFunction = setInterval(function() {
            var now = new Date().getTime();
            var distance = targetTime - now;

            if (distance > 0) {
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                countdownElement.innerHTML = hours + "h " + minutes + "m " + seconds + "s ";
            } else {
                clearInterval(countdownFunction);
                countdownElement.innerHTML = "NOT ACTIVE RIGHT";
            }
        }, 1000);
    } else {
        console.error("Countdown element not found for panel ID: " + panelId);
    }
}
</script>

<?php
// Define the target panels
$targets = [
    ['name' => 'Daily Draw', 'id' => 'panel_1', 'additional_label' => 'KSH 50/= Per Stake', 'stake' => 50],
    ['name' => 'Daily Jack Pot', 'id' => 'panel_2', 'additional_label' => 'KSH 150/= Per Stake', 'stake' => 150],
    ['name' => 'Fast Cash 2 hours Count Down', 'id' => 'panel_3', 'additional_label' => 'KSH 70/= Per Stake', 'stake' => 100]  
];

// Output panels and start countdown automatically
foreach ($targets as $target) {
    $targetName = $target['name'];
    $panelId = strtolower(str_replace(' ', '_', $target['id']));
    $additionalLabel = $target['additional_label'];
    $stake = $target['stake'];
    $panelData = getPanelData($panelId, $conn);
    if ($panelData) {
        $targetTime = $panelData['targetTime'];
        $amount = $panelData['amount'];
        ?>
        <div class="panel" id="<?php echo $panelId; ?>">
            <h2><?php echo $targetName; ?></h2>
            <div id="countdown_<?php echo $panelId; ?>"></div>
            <div id="amount_<?php echo $panelId; ?>">Amount: <?php echo number_format($amount); ?></div>
            <!-- Display additional label directly on the panel -->
            <div><?php echo $additionalLabel; ?></div>
            <form id="form_<?php echo $panelId; ?>" class="hidden popup-form">
                <label for="msisdn<?php echo $panelId; ?>">Phone Number:</label>
                <input type="text" id="msisdn<?php echo $panelId; ?>" name="msisdn<?php echo $panelId; ?>" value='254' required>
                <button class="save-details-button" data-panel-id="<?php echo $panelId; ?>" data-stake="<?php echo $stake; ?>">SEND STK</button>
            </form>
            <button class="show-form-button" data-panel-id="<?php echo $panelId; ?>">Stake Now</button>
        </div>
        <script>
        // Start countdown for this panel
        var panelId = "<?php echo $panelId; ?>";
        var targetTime = parseInt(<?php echo $targetTime; ?>);
        var amount = <?php echo $amount; ?>;
        startCountdown(targetTime, '<?php echo $panelId; ?>', amount);
        </script>
        <?php
    }
}
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.show-form-button').forEach(button => {
        button.addEventListener('click', function() {
            var panelId = this.getAttribute('data-panel-id');
            document.getElementById('form_' + panelId).classList.remove('hidden');
        });
    });

    document.querySelectorAll('.save-details-button').forEach(button => {
        button.addEventListener('click', function() {
            var panelId = this.getAttribute('data-panel-id');
            var msisdn = document.getElementById('msisdn' + panelId).value;
            var stake = 50;
            // Send data to the server using AJAX
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'save_details.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    console.log(xhr.responseText);
                }
            };
            xhr.send('msisdn=' + encodeURIComponent(msisdn) + '&stake=' + encodeURIComponent(stake));
        });
    });
});
</script>

</body>
</html>
