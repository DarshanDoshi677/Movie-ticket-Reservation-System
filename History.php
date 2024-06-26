<?php
error_reporting(E_ALL);
require 'db.php';
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$customerId = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['customerId'])) {
    $customerId = htmlspecialchars($_GET['customerId'], ENT_QUOTES, 'UTF-8');

    try {
        $stmt = $conn->prepare("
            SELECT 
                b.Date AS bookingDate,
                b.Seats,
                b.ScreenNo,
                b.Price,
                s.Time AS showTime,
                m.Title AS movieTitle
            FROM Booking b
            INNER JOIN Movie_Show ms ON b.MS_id = ms.MS_id
            INNER JOIN Show s ON ms.S_id = s.S_id
            INNER JOIN Movie m ON ms.M_id = m.M_id
            WHERE b.c_id = :customerId
        ");

        $stmt->bindParam(':customerId', $customerId, PDO::PARAM_STR);
        $stmt->execute();

       
        $bookingDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tableHTML'])) {
    $tableHTML = $_POST['tableHTML'];

    $additionalStyles = '
        <style>
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }

            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }

            th {
                background-color: #f2f2f2;
            }
        </style>
    ';

    $tableHTMLWithStyles = $additionalStyles . $tableHTML;

    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($tableHTMLWithStyles);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    header('Content-type: application/pdf');
    header('Content-Disposition: attachment; filename="booking_details.pdf"');
    echo $dompdf->output();
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    
    <style>
         body {
    font-family: 'Arial', sans-serif;
    background-color: #f8f9fa;
    margin: 0;
    padding: 0;
    background-image: url('background8.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    height: 100vh; 
    position: relative;
}


        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
           box-shadow: 0 0 30px rgba(0, 0, 0, 0);
      background: rgba(255, 255, 255, 0.5);
        }

        h1, h2 {
            color: #007bff;
            text-align: center;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            font-size: 18px;
            color: #343a40;
        }

        .search-container {
        position: relative;
        background-color: #e2e6ea;
        margin-bottom: 20px;
        border-radius: 5px;
        overflow: hidden;
        margin-left: 20px;
        display: flex; 
        align-items: center; 
    }

    .search-icon {
        font-size: 24px;
        margin-right: 10px; 
        color: #495057;
    }

    input[type="text"] {
        flex: 1;
        padding: 12px;
        box-sizing: border-box;
        border: 1px solid #ced4da;
        border-radius: 5px;
        outline: none;
        font-size: 16px;
        transition: border-color 0.3s;
        background-color: #e2e6ea;
    }

        input[type="text"]:focus {
            border-color: #007bff;
        }

        button {
            background-color: #007bff;
            color: #ffffff;
            padding: 12px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-left: 500px;
            margin-top: 1px;
        }

        button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            border-spacing: 0;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: #ffffff;
        }

        tbody tr:hover {
            background-color: #f8f9fa;
        }

        th, td {
            width: 20%;
        }
        h1, h2 {
        color: #333;
        margin-bottom: 20px;
    }

    p {
        font-size: 16px;
        line-height: 1.6;
        color: #555;
    }

    .btn-primary {
        background-color: #3498db;
        color: #fff;
        padding: 12px 20px;
        font-size: 18px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-primary:hover {
        background-color: #2980b9;
    }

    .btn-secondary {
        background-color: #2ecc71;
        color: #fff;
        padding: 12px 20px;
        font-size: 18px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-secondary:hover {
        background-color: #27ae60;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
    }

    input[type="text"], input[type="password"], textarea {
        width: 100%;
        padding: 12px;
        box-sizing: border-box;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 16px;
        font-size: 16px;
    }

    select {
        width: 100%;
        padding: 12px;
        box-sizing: border-box;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 16px;
        font-size: 16px;
        background-color: #fff;
        color: #555;
    }

    @media only screen and (max-width: 768px) {
        .container {
            margin: 30px;
        }

        input[type="text"] {
            width: 100%;
            margin-left: 0;
        }

        button {
            margin-left: 0;
            margin-right: 50px;
        }
    }
    </style>
    <title>Booking Details</title>
</head>
<body>
    <div class="container">
        <h1>Booking Details</h1>
        <form method="GET" action="">
            <label for="customerId">Enter Customer ID:</label>
            <div class="search-container">
                <i class="material-icons search-icon">search</i>
                <input type="text" id="customerId" name="customerId" placeholder="Enter Customer ID" value="<?= $customerId ?>">
            </div>
            <button type="submit">Search</button>
        </form>

        <?php if (!empty($bookingDetails)) : ?>
            <h2>Search Results:</h2>
            <button id="generatePdf">Generate PDF</button>
            <table id="bookingTable">
                <thead>
                    <tr>
                        <th>Booking Date</th>
                        <th>Seats</th>
                        <th>Screen No</th>
                        <th>Price</th>
                        <th>Show Time</th>
                        <th>Movie Title</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookingDetails as $booking) : ?>
                        <tr>
                            <td><?= isset($booking['bookingdate']) ? $booking['bookingdate'] : '' ?></td>
                            <td><?= isset($booking['seats']) ? $booking['seats'] : '' ?></td>
                            <td><?= isset($booking['screenno']) ? $booking['screenno'] : '' ?></td>
                            <td><?= isset($booking['price']) ? $booking['price'] : '' ?></td>
                            <td><?= isset($booking['showtime']) ? $booking['showtime'] : '' ?></td>
                            <td><?= isset($booking['movietitle']) ? $booking['movietitle'] : '' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
    <script>
        document.getElementById('generatePdf').addEventListener('click', function() {
            const tableHTML = document.getElementById('bookingTable').outerHTML;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo $_SERVER['PHP_SELF']; ?>');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.responseType = 'blob';
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        const blob = new Blob([xhr.response], { type: 'application/pdf' });
                        const link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = 'booking_details.pdf';
                        link.click();
                    } else {
                        console.error('Failed to generate PDF');
                    }
                }
            };
            xhr.send('tableHTML=' + encodeURIComponent(tableHTML));
        });
    </script>
</body>
</html>
