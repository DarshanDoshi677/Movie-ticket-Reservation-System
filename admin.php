<?php
include('dompdf/autoload.inc.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'src\Exception.php';
require 'src\PHPMailer.php';
require 'src\SMTP.php';


use Dompdf\Dompdf;

function generateBookingPDFContent($data) {
    $html = "<html><head><title>Booking History</title></head><body>";
    $html .= "<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        background-color: #FFA500;
        font-family: 'Times New Roman', Times, serif;
        font-size: 15px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }
    th {
        color: #fff; 
        background-color: #FF6347; 
        padding: 15px; 
        font-size: 20px; 
        text-align: center;
    }
    </style>";
    $html .= "<h1><center>Booking history</center></h1>";
    $html .= "<table>";
    $html .= "<tr><th>B_id</th><th>Date</th><th>Seats</th><th>ScreenNo</th><th>Price</th><th>TName</th><th>c_id</th><th>MS_id</th></tr>";

    foreach ($data as $row) {
        $html .= "<tr>";
        $html .= "<td>" . $row["b_id"] . "</td>";
        $html .= "<td>" . $row["date"] . "</td>";
        $html .= "<td>" . $row["seats"] . "</td>";
        $html .= "<td>" . $row["screenno"] . "</td>";
        $html .= "<td>" . $row["price"] . "</td>";
        $html .= "<td>" . $row["tname"] . "</td>";
        $html .= "<td>" . $row["c_id"] . "</td>";
        $html .= "<td>" . $row["ms_id"] . "</td>";
        $html .= "</tr>";
    }

    $html .= "</table></body></html>";

    return $html;
}

$host = 'localhost';
$dbname = 'php';
$dbuser = 'postgres';
$dbpass = 'root';

$bookingTableContent = ""; 
$dataPointsMovies = array();

try {
    
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $dbuser, $dbpass);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['action']) && $_GET['action'] === 'booking') {
        $bookingTableQuery = "SELECT * FROM Booking";
        $result = $conn->query($bookingTableQuery);

        if ($result->rowCount() > 0) {
            $bookingTableContent .= "<h2>Booking History</h2>";

            $bookingTableContent .= "<table>";
            $bookingTableContent .= "<tr><th>B_id</th><th>Date</th><th>Seats</th><th>ScreenNo</th><th>Price</th><th>TName</th><th>c_id</th><th>MS_id</th></tr>";

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
                $bookingTableContent .= "<tr>";
                $bookingTableContent .= "<td>" . $row["b_id"] . "</td>";
                $bookingTableContent .= "<td>" . $row["date"] . "</td>";
                $bookingTableContent .= "<td>" . $row["seats"] . "</td>";
                $bookingTableContent .= "<td>" . $row["screenno"] . "</td>";
                $bookingTableContent .= "<td>" . $row["price"] . "</td>";
                $bookingTableContent .= "<td>" . $row["tname"] . "</td>";
                $bookingTableContent .= "<td>" . $row["c_id"] . "</td>";
                $bookingTableContent .= "<td>" . $row["ms_id"] . "</td>";
                $bookingTableContent .= "</tr>";
                
            }

            $bookingTableContent .= "</table>";

            $bookingTableContent .= "<form method='post' action=''>";
            $bookingTableContent .= "<input type='hidden' name='pdfContent' value='" . json_encode($data, JSON_NUMERIC_CHECK) . "'>";
            $bookingTableContent .= "<button type='submit' name='printBtn'>Print</button>";
            $bookingTableContent .= "</form>";
        } else {
            $bookingTableContent .= "No bookings found.";
        }
    }
if (isset($_POST['sendCredentials']) && isset($_POST['email'])) {
    $email = $_POST['email'];

    $credentialsQuery = "SELECT username, pass FROM theatre";
    $stmt = $conn->query($credentialsQuery);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $username = $row['username'];
    $password = $row['pass'];

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'darshandipakkumar2875@gmail.com'; 
        $mail->Password   = 'gkuduporohsumitk'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('darshandipakkumar2875@gmail.com', 'Housefull Cinemas');
        $mail->addAddress($email, 'recipient');
        $mail->addReplyTo('darshandipakkumar2875@gmail.com', 'Housefull cinemas');

        $mail->isHTML(true);
        $mail->Subject = 'Your Theater Admin Credentials';
        $mail->Body    = "Username: $username<br>Password: $password";

        $mail->send();

        echo "Credentials sent successfully to $email.";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

    
    
    if (isset($_GET['action']) && $_GET['action'] === 'dashboard') {
        try {
            $movieDataQuery = "SELECT Movie.genre, COUNT(Booking.b_id) AS bookings_count
            FROM Booking
            JOIN movie_show ON Booking.ms_id = movie_show.ms_id
            JOIN Movie ON movie_show.m_id = Movie.m_id
            GROUP BY Movie.genre;
            ";
            $stmtMovieData = $conn->prepare($movieDataQuery);
            $stmtMovieData->execute();

            while ($rowMovie = $stmtMovieData->fetch(PDO::FETCH_ASSOC)) {
                $dataPointsMovies[] = array("label" => $rowMovie['genre'], "y" => $rowMovie['bookings_count']);
            }

            $movieShowDataQuery = "SELECT movie_show.m_id, movie.title, COUNT(booking.b_id) AS booking_count
                                FROM movie_show
                                JOIN movie ON movie_show.m_id = movie.m_id
                                LEFT JOIN booking ON movie_show.ms_id = booking.ms_id
                                GROUP BY movie_show.m_id, movie.title";
            $stmtMovieShowData = $conn->prepare($movieShowDataQuery);
            $stmtMovieShowData->execute();

            while ($rowMovieShow = $stmtMovieShowData->fetch(PDO::FETCH_ASSOC)) {
                $dataPointsMovieShows[] = array("label" => $rowMovieShow['title'], "y" => $rowMovieShow['booking_count']);
            }

            $totalBookingsQuery = "SELECT COUNT(*) AS total_bookings FROM Booking";
            $stmtTotalBookings = $conn->prepare($totalBookingsQuery);
            $stmtTotalBookings->execute();
            $totalBookingsResult = $stmtTotalBookings->fetch(PDO::FETCH_ASSOC);
            $totalBookings = $totalBookingsResult['total_bookings'];

            $totalMoviesQuery = "SELECT COUNT(*) AS total_movies FROM Movie";
            $stmtTotalMovies = $conn->prepare($totalMoviesQuery);
            $stmtTotalMovies->execute();
            $totalMoviesResult = $stmtTotalMovies->fetch(PDO::FETCH_ASSOC);
            $totalMovies = $totalMoviesResult['total_movies'];

            $totalCustomersQuery = "SELECT COUNT(DISTINCT c_id) AS total_customers FROM customer";
            $stmtTotalCustomers = $conn->prepare($totalCustomersQuery);
            $stmtTotalCustomers->execute();
            $totalCustomersResult = $stmtTotalCustomers->fetch(PDO::FETCH_ASSOC);
            $totalCustomers = $totalCustomersResult['total_customers'];

        } catch (PDOException $e) {
            echo "Error fetching data: " . $e->getMessage();
        }
    }


} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
} finally {
    $conn = null;
}

if (isset($_POST['printBtn']) && isset($_POST['pdfContent'])) {
    $pdfContent = json_decode($_POST['pdfContent'], true);

    try {
        $obj = new Dompdf();

        $htmlContent = generateBookingPDFContent($pdfContent);

        $obj->loadHtml($htmlContent);
        $obj->setPaper('A4', 'portrait'); 
        $obj->render();
        $obj->stream();
    } catch (Exception $e) {
        die("Dompdf Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theater Admin Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: rgba(52, 152, 219, 0.8);
            color: #fff;
            padding: 15px;
            display: flex;
            align-items: center;
            animation: fadeInDown 1s ease-out;
        }

        #logo img {
            width: 100px;
            height: 100px;
            margin-right: 10px;
        }

        nav {
            display: flex;
            justify-content: center;
            padding: 10px;
            font-family: 'Times New Roman', Times, serif;
            color: black;
            margin-left: auto;
            animation: fadeInUp 1s ease-out;
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        nav a {
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
            animation: fadeIn 1s ease-out;
            font-size: 40px;
        }

        nav a:hover {
            background-color: #27ae60;
        }

        .container {
            width: 100%;
            height: 95vh;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }

        .overlay {
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.7);
        }

        .content {
            margin: 20px;
            padding: 20px;
            z-index: 2;
        }

        h1 {
            text-align: center;
            color: black;
            margin-bottom: 20px;
        }

        table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
            font-size: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
        }

        th {
            background-color: #FF6347;
            color: white;
            font-size: 20px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 15px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            margin-top: 20px;
        }

        #chartContainer, #chartContainerGenres {
            background: transparent;
            background-color: orange;
            width: 60%;
            height: 370px;
            display: inline-block;
            margin: 0 2.5%;
            margin-top: 20px;
        }

        #statContainer1, #statContainer2, #statContainer3 {
            background-color: #3498db;
            width: 30%;
            height: 300px;
            display: inline-block;
            border-radius: 15px;
            overflow: hidden;
            margin-right: 2.5%;
            animation: fadeIn 1s ease-out;
            font-size: 30px;
        }

        #statContainer2 {
            background-color: #FDDFDF;
        }

        #statContainer3 {
            background-color: #01FFFF;
        }

        .stat-content {
            text-align: center;
            padding: 20px;
            font-size: 40px;
        }

        .stat-content input {
            width: 80%;
            height: 40px; 
            margin-top: 10px;
            padding: 10px;
            font-size: 30px; 
            border: none;
            border-radius: 5px;
            text-align: center;
            font-family: 'Times New Roman', Times, serif;
        }
        input[type=email] {
        width: 100%; 
        height: 40px; 
        margin-top: 10px;
        padding: 10px;
        font-size: 18px; 
        border: 1px solid #ccc; 
        border-radius: 5px;
        box-sizing: border-box; 
        }
    </style>
</head>
<body>
<div class="container" id="dashboardContainer" style="background-image: url('background7.jpg');">

    <header>
        <div id="logo">
            <img src="logo.jpg" alt="Logo">
        </div>
        <h1>Welcome to Housefull Cinemas</h1>
        <nav>
            <a href="?action=booking">Booking</a>
            <a href="?action=dashboard" id="dashboardLink">Dashboard</a>
            <a href="addMovie.php">Manage</a>
            <a href="?action=admin">Admin</a>

        </nav>
    </header>
    




    <div class="content">
        <?php
        if (isset($_GET['action']) && $_GET['action'] === 'booking') {
            echo isset($bookingTableContent) ? $bookingTableContent : "No content to display."; // Display booking table
        } elseif (isset($_GET['action']) && $_GET['action'] === 'dashboard') {
            ?>
            <div id="statContainer1">
                <div class="stat-content">
                    Total Bookings
                    <br>
                    <input type="text" value="<?php echo isset($totalBookings) ? $totalBookings : ''; ?>" readonly>
                </div>
            </div>
            <div id="statContainer2">
                <div class="stat-content">
                    Total Movies
                    <br>
                    <input type="text" value="<?php echo isset($totalMovies) ? $totalMovies : ''; ?>" readonly>
                </div>
            </div>
            <div id="statContainer3">
                <div class="stat-content">
                    Total Customers
                    <br>
                    <input type="text" value="<?php echo isset($totalCustomers) ? $totalCustomers : ''; ?>" readonly>
                </div>
            </div>

            <div id="chartContainer" style="height: 300px; width: 40%; display: inline-block;"></div>
            <div id="chartContainerGenres" style="height: 300px; width: 40%; display: inline-block;"></div>
            <?php
    } elseif (isset($_GET['action']) && $_GET['action'] === 'admin') {
        ?>
      <div id="emailSelectionForm">
            <h2>Send Credentials to New Admin Email</h2>
            <form method="post" action="">
                <label for="email">Enter Admin Email:</label><br>
                <input type="email" id="email" name="email"><br>
                <button type="submit" name="sendCredentials">Send Credentials</button>
            </form>
        </div>
        <?php
    }
    ?>

            <script>
                window.onload = function () {
                    <?php if (isset($_GET['action']) && $_GET['action'] === 'dashboard') { ?>
                    var chartScript = document.createElement('script');
                    chartScript.src = 'https://canvasjs.com/assets/script/canvasjs.min.js';
                    chartScript.onload = function () {
                        var chartContainer = document.getElementById("chartContainer");
                        var chartContainerGenres = document.getElementById("chartContainerGenres");

                        console.log("Data Points Movies: ", <?php echo json_encode($dataPointsMovies, JSON_NUMERIC_CHECK); ?>);
                        console.log("Data Points Movie Shows: ", <?php echo json_encode($dataPointsMovieShows, JSON_NUMERIC_CHECK); ?>);

                        var chart = new CanvasJS.Chart("chartContainer", {
                            animationEnabled: true,
                            title: {
                                text: "Booking Data for Movie Genre"
                            },
                            data: [{
                                type: "pie",
                                showInLegend: true,
                                legendText: "{label}",
                                toolTipContent: "{label}: <strong>{y}</strong> bookings",
                                indexLabel: "{y}",
                                dataPoints: <?php echo json_encode($dataPointsMovies, JSON_NUMERIC_CHECK); ?>
                            }]
                        });

                        var chartGenres = new CanvasJS.Chart("chartContainerGenres", {
                            animationEnabled: true,
                            title: {
                                text: "Booking Data for Movies"
                            },
                            data: [{
                                type: "pie",
                                showInLegend: true,
                                legendText: "{label}",
                                toolTipContent: "{label}: <strong>{y}</strong> bookings",
                                indexLabel: "{y}",
                                dataPoints: <?php echo json_encode($dataPointsMovieShows, JSON_NUMERIC_CHECK); ?>
                            }]
                        });

                        chart.render();
                        chartGenres.render();
                    };

                    document.head.appendChild(chartScript);
                    <?php } ?>
                }
            </script>

            <?php
        
        ?>
    </div>
   
</div>
</body>
</html>
