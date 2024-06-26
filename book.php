<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Reservation System</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .success-container {
            color: #fff;
            padding: 80px;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
            text-align: center;
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

        .success-animation {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            animation: bounce 1s infinite;
        }

        h1 {
            color: #333;
        }

        h2 {
            color: #666;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-40px);
            }
            60% {
                transform: translateY(-20px);
            }
        }

        .thank-you-message {
            font-size: 40px;
            margin-top: 20px;
            color: #666;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .success-container button {
            margin-top: 20px;
            padding: 15px 20px;
            background-color: #FF6F61;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 20px;
            transition: background-color 0.3s;
        }

        .success-container button:hover {
            background-color: #E63946;
        }
    </style>
</head>

<body>
    <?php
     session_start();
    
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require 'src\Exception.php';
    require 'src\PHPMailer.php';
    require 'src\SMTP.php';

    
        $c_id = $_POST['c_id'];
        $title = $_POST['movie_title'];
        $show_time = $_POST['show_time'];
        $date = $_POST['date'];
        $seats = $_POST['quantity'];
        $price = $_POST['price'];
   
    

    

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $host = "localhost";
        $port = "5432";
        $dbname = "php";
        $user = "postgres";
        $password = "root";

       
        try {
            
            $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password");

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $queryCustomerValidation = "SELECT * FROM customer WHERE C_id=:username";
            $stmtCustomerValidation = $conn->prepare($queryCustomerValidation);
            $stmtCustomerValidation->bindParam(':username', $c_id, PDO::PARAM_STR);
            $stmtCustomerValidation->execute();

            $rowCount = $stmtCustomerValidation->rowCount();

            if ($rowCount > 0) {
                $customerData = $stmtCustomerValidation->fetch(PDO::FETCH_ASSOC);
                $cname = $customerData['cname'];
                $customerEmail = $customerData['email'];

                $B_id = uniqid('B');

                $conn->beginTransaction();

                try {
                    $queryBooking = "INSERT INTO Booking (B_id, Date, Seats, ScreenNo, Price, TName, c_id, MS_id)
                                  SELECT :B_id, :date, :seats, MS.ScreenNo, :price, 'Housefull Cinemas' AS TName, :c_id AS c_id, MS.MS_id
                                  FROM Movie_Show MS
                                  JOIN Movie M ON MS.M_id = M.M_id
                                  JOIN Show S ON MS.S_id = S.S_id
                                  WHERE M.Title = :title AND S.Time = :show_time";

                    $stmtBooking = $conn->prepare($queryBooking);
                    $stmtBooking->bindParam(':B_id', $B_id, PDO::PARAM_STR);
                    $stmtBooking->bindParam(':title', $title, PDO::PARAM_STR);
                    $stmtBooking->bindParam(':show_time', $show_time, PDO::PARAM_STR);
                    $stmtBooking->bindParam(':date', $date, PDO::PARAM_STR);
                    $stmtBooking->bindParam(':seats', $seats, PDO::PARAM_INT);
                    $stmtBooking->bindParam(':price', $price, PDO::PARAM_STR);
                    $stmtBooking->bindParam(':c_id', $c_id, PDO::PARAM_STR);

                    $stmtBooking->execute();

                    $updateSeatsQuery = "UPDATE Movie_Show SET Seats = Seats - :seats
                                     WHERE M_id IN (SELECT M_id FROM Movie WHERE Title = :title)
                                     AND S_id IN (SELECT S_id FROM Show WHERE Time = :show_time)";

                    $stmtUpdateSeats = $conn->prepare($updateSeatsQuery);
                    $stmtUpdateSeats->bindParam(':seats', $seats, PDO::PARAM_INT);
                    $stmtUpdateSeats->bindParam(':title', $title, PDO::PARAM_STR);
                    $stmtUpdateSeats->bindParam(':show_time', $show_time, PDO::PARAM_STR);

                    $stmtUpdateSeats->execute();

                    $conn->commit();

                    $mail = new PHPMailer(true);

                    try {
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'darshandipakkumar2875@gmail.com'; 
                        $mail->Password   = 'tgcwmleejvnnhbpp'; 
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;

                        $mail->setFrom('darshandipakkumar2875@gmail.com', 'Housefull Cinemas');
                        $mail->addAddress($customerEmail, 'recipient');
                        $mail->addReplyTo('darshandipakkumar2875@gmail.com', 'dd');

                        $mail->isHTML(true);
                        $mail->Subject = 'Movie Booking Confirmation';
                        $mail->Body    = "<div>
                            <img src='success.jpg' alt='success.jpg' style='max-width: 100%; height: auto; margin-top: 20px;'>    
                            <h1 style='font-size: 24px; text-shadow: 2px 2px 4px #000000; color: #DEDDF8;'>Booking successful, $cname!</h1>
                            <h2 style='font-size: 18px; text-shadow: 2px 2px 4px #000000; color: orange;'>Your Booking ID is: $B_id</h2>
                            <p style='font-size: 16px; color: #195D8C; text-shadow: 2px 2px 4px #000000;'>Thank you for choosing Housefull Cinemas!</p>
                            <p style='font-size: 16px; color: #888; text-shadow: 2px 2px 4px #000000;'>Movie: $title</p>
                            <p style='font-size: 16px; color: #888; text-shadow: 2px 2px 4px #000000;'>Show Time: $show_time</p>
                            <p style='font-size: 16px; color: #888; text-shadow: 2px 2px 4px #000000;'>Seats: $seats</p>
                            <p style='font-size: 16px; color: #888; text-shadow: 2px 2px 4px #000000;'>Price: $price</p>
                        </div>";
                        $mail->AltBody = "Thank you for booking with Housefull Cinemas! Your Booking ID is: $B_id";

                        $mail->send();
                    
                    } catch (Exception $e) {
                        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    }

                    echo "<div class='success-container'>";
                    echo "<img src='success.jpg' alt='Success Image' class='success-animation'>";
                    echo "<h1>Booking successful, $cname!</h1>";
                    echo "<h2>Your Booking ID is: $B_id</h2>";
                    echo "<p class='thank-you-message'>Thank you for choosing Housefull Cinemas!</p>";
                    echo "<button onclick='goToHome()'>Go to Home</button>";
                    echo "</div>";
                } catch (Exception $e) {
                    $conn->rollBack();
                    echo "Booking failed. Please try again later.";
                }
            }
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    } else {
        header("Location: Booking.php");
        exit();
    }
    ?>

    <script>
        function goToHome() {
            window.location.href = 'Homepage.php'; 
        }
    </script>
</body>

</html>
