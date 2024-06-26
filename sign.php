<?php
$signin = "";
$error_message = "";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'src\Exception.php';
require 'src\PHPMailer.php';
require 'src\SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $C_id = $_POST['customerId'];
    $Pass = $_POST['password'];
    $Cname = $_POST['customerName'];
    $Add = $_POST['address'];
    $contact = $_POST['Contact'];
    $email = $_POST['email'];

    try {
        $host = 'localhost';
        $dbname = 'php';
        $dbuser = 'postgres';
        $dbpass = 'root';

        $connec = new PDO("pgsql:host=$host;dbname=$dbname", $dbuser, $dbpass);
        $connec->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $checkQuery = "SELECT COUNT(*) FROM customer WHERE c_id = ? OR email = ? OR contact = ?";
        $checkStmt = $connec->prepare($checkQuery);
        $checkStmt->execute([$C_id, $email, $contact]);
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            $error_message = "Customer ID, Email, or Mobile Number already exists!";
        } else {
            $sql = "INSERT INTO customer VALUES (?, ?, ?, ?, ?,?)";
            $stmt = $connec->prepare($sql);

            if ($stmt->execute([$C_id, $Pass, $Cname, $Add, $contact, $email])) {
                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'darshandipakkumar2875@gmail.com';
                    $mail->Password   = 'tgcw mleejvnnhbpp';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->setFrom('darshandipakkumar2875@gmail.com', 'Housefull cinemas');
                    $mail->addAddress($_POST['email'], $_POST['customerName']);
                    $mail->addReplyTo('darshandipakkumar2875@gmail.com', 'Housefull cinemas');

                    $mail->isHTML(true);
                    $mail->Subject = 'Registration Confirmation';
                    $mail->Body    = "Hello $_POST[customerName],<br><br>
                                    Thank you for registering on our website!<br>
                                    Your login credentials:<br>
                                    <b>Username: $_POST[customerId]<b><br>
                                    <b>Password: $_POST[password]<b><br><br>
                                    Best regards,<br>
                                    Your Website Team";

                    $mail->send();
                } catch (Exception $e) {
                    $error_message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }

                header("Location: login.php");
                exit();
            } else {
                $error_message = "Error inserting data into the database";
            }
        }
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"> <!-- Include Font Awesome CSS -->
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      height: 100vh;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      background-image: url('background8.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      position: relative;
    }

    .form {
      max-width: 860px; /* Increased width */
      width: 100%;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 30px rgba(0, 0, 0, 0); /* Updated to a more transparent shadow */
      background: rgba(255, 255, 255, 0.5);
      transition: box-shadow 0.3s ease;
    }

    .form:hover {
      box-shadow: 0 0 40px rgba(0, 0, 0, 0.5); 
    }

    header {
  margin-top: 10px;
  font-size: 35px;
  font-weight: 600;
  color: #232836;
  text-align: center;
  border-bottom: 2px solid #0171d3; 
  padding-bottom: 10px; 
  border-radius: 10px 10px 0 0; 
  background-color: #DE66DA;
}


    form {
      margin-top: 30px;
    }

    .field {
      position: relative;
      height: 50px;
      margin-top: 20px;
      border-radius: 6px;
      font-size: 30px;
      font-family: 'Times New Roman', Times, serif;
    }

    .field input,
    .field button {
      height: 100%;
      width: 100%;
      border: none;
      font-size: 25px;
      border-radius: 6px;
      transition: border 0.3s ease;
      font-family: 'Times New Roman', Times, serif;
    }

    .field input {
      outline: none;
      padding: 0 15px;
      border: 1px solid #CACACA;
      font-family: 'Times New Roman', Times, serif;
    }

    .field input:focus {
      border-bottom-width: 2px;
      border-color: #0171d3;
    }

    .eye-icon {
      position: absolute;
      top: 50%;
      right: 10px;
      transform: translateY(-50%);
      font-size: 18px;
      color: #8b8b8b;
      cursor: pointer;
      padding: 5px;
    }

    .field button {
      color: #fff;
      background-color: #0171d3;
      transition: all 0.3s ease;
      cursor: pointer;
      border: 1px solid #0171d3;
      font-size: 30px;
      font-family: 'Times New Roman', Times, serif;
    }

    .field button:hover {
      background-color: #016dcb;
      border-color: #016dcb;
      transform: scale(1.05);
    }

    .form-link {
      text-align: center;
      margin-top: 10px;
    }

    .form-link span,
    .form-link a {
      font-size: 35px;
      color: #232836;
      font-family: 'Times New Roman', Times, serif;
    }

    .form a {
      color: #0171d3;
      text-decoration: none;
    }

    .form-content a:hover {
      text-decoration: underline;
    }

    .line {
      position: relative;
      height: 1px;
      width: 100%;
      margin: 36px 0;
      background-color: #d4d4d4;
    }

    .line::before {
      content: 'Or';
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: #FFF;
      color: #8b8b8b;
      padding: 0 15px;
    }

   
    .social-icons {
      text-align: center;
      margin-top: 20px;
    }

    .social-icons i {
      font-size: 60px; 
      margin: 0 20px; 
      color: #0171d3;
      cursor: pointer;
      transition: color 0.3s ease;
    }

    .social-icons i:hover {
      color: #016dcb;
    }
    .field {
      position: relative;
      height: 50px;
      margin-top: 20px;
      border-radius: 6px;
      font-size: 30px;
      font-family: 'Times New Roman', Times, serif;
    }

    .field input,
    .field button {
      height: 100%;
      width: 100%;
      border: none;
      font-size: 40px;
      border-radius: 6px;
      transition: border 0.3s ease;
      font-family: 'Times New Roman', Times, serif;
      padding-left: 40px; 
    }

    .field .icon {
      position: absolute;
      top: 30%;
      left: 8px;
      font-size: 25px;
      color: #8b8b8b;
      margin-bottom: 0px;
    }
  </style>
</head>
<body>
  <div class="form">
    <header>Sign Up</header>
    <form method="post" action="">
      <div class="field">
        <i class="fas fa-id-card icon"></i>
        <input type="text" name="customerId" placeholder="Customer ID" required>
      </div><br><br>
      <div class="field">
      <i class="fas fa-lock icon"></i>
        <input type="password" name="password" placeholder="Password" required>
      </div><br><br>
      <div class="field">
        <i class="fas fa-user icon"></i>
        <input type="text" name="customerName" placeholder="Customer Name" required>
      </div><br><br>
      <div class="field">
        <i class="fas fa-map-marker-alt icon"></i>
        <input type="text" name="address" placeholder="Address" required>
      </div><br><br>
      <div class="field">
        <i class="fas fa-phone icon"></i>
        <input type="tel" name="Contact" pattern="[0-9]{10}" placeholder="Mobile No" required>
      </div><br><br>
      
      
      <div class="field">
        <i class="fas fa-envelope icon"></i>
        <input type="email" name="email" placeholder="Email" required>
      </div><br><br>
      <div class="field">
        <button type="submit">Sign Up</button>
      </div><br><br>
    </form>
    <div class="error-message">
            <?php
            if (!empty($error_message)) {
                echo '<p>' . $error_message . '</p>';
            }
            ?>
    <div class="form-link">
      <span>Already have an account? <a href="login.php">Login</a></span>
    </div>
    <div class="social-icons">
      <i class="fab fa-facebook"></i>
      <i class="fab fa-twitter"></i>
      <i class="fab fa-google"></i>
    </div>
  </div>
  <script>
    <?php if (!empty($error_message)) : ?>
      alert('<?php echo $error_message; ?>');
    <?php endif; ?>
  </script>
</body>
</html>
