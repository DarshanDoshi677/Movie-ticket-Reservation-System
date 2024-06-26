<?php
$loginType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $loginType = $_POST['loginType'];

    try {
        $host = 'localhost';
        $dbname = 'php';
        $dbuser = 'postgres';
        $dbpass = 'root';

        $connec = new PDO("pgsql:host=$host;dbname=$dbname", $dbuser, $dbpass);

        $query = '';
        if ($loginType === 'customer') {
            $query = "SELECT * FROM customer WHERE C_id=:username AND Password=:password";
        } elseif ($loginType === 'theater') {
            $query = "SELECT * FROM Theatre WHERE username=:username AND pass=:password";
        }

        $stmt = $connec->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->execute();

        $rowCount = $stmt->rowCount();
        if ($rowCount > 0) {
            if ($loginType === 'customer') {
              
                header("Location: homepage.php");
                exit(); 
            } elseif ($loginType === 'theater') {
                header("Location: Admin.php");
                exit(); 
            }
        } else {
            echo "<p style='color: red;'>Invalid credentials. Please try again.</p>";
        }
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage(), 0);

        echo "<p style='color: red;'>An error occurred while processing your request. Please try again later.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
  <style>
    
.field {
  position: relative;
  margin-top: 20px;
  font-size: 30px;
  font-family: 'Times New Roman', Times, serif;
}

.field input,
.field button,
.field select {
  height: 50px;
  width: 100%;
  border: 1px solid #ccc;
  border-radius: 6px;
  padding-left: 50px; 
  box-sizing: border-box;
  font-size: 25px;
}

.field .icon {
  position: absolute;
  top: 40%;
  left: 10px; 
  transform: translateY(-50%);
  font-size: 30px;
  color: #8b8b8b;
}

.field select {
  padding-left: 10px; 
}

.field input[type="text"],
.field input[type="password"] {
  padding-left: 50px; 
}

#loginType::after {
  content: '\25BC';
  position: absolute;
  top: 50%;
  right: 10px;
  transform: translateY(-50%);
}

#loginType option {
  font-size: 90px;
  font-family: 'Times New Roman', Times, serif;
}
.form-link {
      text-align: center;
      margin-top: 10px;
      font-size: 30px;
    }
.social-icons {
  text-align: center;
  margin-top: 20px;
}

.social-icons i {
  font-size: 80px;
  margin: 0 20px; 
  color: #0171d3;
  cursor: pointer;
  transition: color 0.3s ease;
}

.social-icons i:hover {
  color: #016dcb;
}


    .form-link span,
    .form-link a {
      font-size: 30px;
      color: #232836;
      font-family: 'Times New Roman', Times, serif;
    }
body {
  background: url("background8.jpg");
  background-size: cover;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
  margin: 0;
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background-color: #f4f4f4;
}


.signup-form {
  background: rgba(255, 255, 255, 0.5);
  padding: 20px;
  border-radius: 15px; 
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  max-width: 800px; 
  width: 100%; 
  border: 2px solid #ddd; 
  font-size: medium;
  animation: fadeIn 0.5s ease;
}


h2 {
  font-size: xx-large;
  background-color: rgba(255, 0, 0, 0.5);
  padding: 15px;
  border-radius: 15px; 
  margin-bottom: 20px;
  text-align: center;
  color: #fff;
}

.form-group {
  margin-bottom: 20px;
  font-size: 30px;
  font-family: 'Times New Roman', Times, serif
}

label {
  display: block;
  margin-bottom: 10px; 
  color: #333;
}

input[type="text"],
input[type="password"] {
  width: 100%;
  padding: 15px;
  border-radius: 8px; 
  border: 1px solid #ccc;
  margin-bottom: 15px;
  box-sizing: border-box;
  font-size: 30px;
}


input[type="submit"] {
  background-color: #ff8c00;
  color: #fff;
  width: 100%;
  padding: 20px; 
  border-radius: 8px;
  cursor: pointer;
  font-size: larger;
  transition: background-color 0.3s ease;
  font-family: 'Times New Roman', Times, serif;
  animation: scaleIn 0.5s ease; 
}

input[type="submit"]:hover {
  background-color: #e07c00;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes scaleIn {
  from {
    transform: scale(0.8);
  }
  to {
    transform: scale(1);
  }
}
#loginType {
  width: 100%;
  padding: 15px;
  border-radius: 8px;
  border: 1px solid #ccc;
  margin-bottom: 15px;
  box-sizing: border-box;
  font-size: medium;
}
.field button {
      color: #fff;
      background-color: #0171d3;
      transition: all 0.3s ease;
      cursor: pointer;
      border: 1px solid #0171d3;
      font-size: 30px;
      font-family: 'Times New Roman', Times, serif;
      width: 100%;
      
    }
#loginType {
  width: 100%;
  padding: 15px;
  border-radius: 8px;
  border: 1px solid #ccc;
  margin-bottom: 15px;
  box-sizing: border-box;
  font-size: 30px; 
}

#loginType::after {
  content: '\25BC'; 
  position: absolute;
  top: 50%;
  right: 10px;
  transform: translateY(-50%);
}

#loginType option {
  font-size: 30px;
  font-family: 'Times New Roman', Times, serif; 
}

.form-link a {
  color: #0171d3; 
  text-decoration: none;
}

.form-link a:hover {
  color: #016dcb; 
  text-decoration: underline;
}




  </style>
</head>
<body>
  <div class="signup-form">
    <h2>Login</h2>

    <form action="" method="post">
      <div class="form-group">
        <label for="loginType"><i class="fas fa-user icon"></i> Login Type:</label>
        <select id="loginType" name="loginType" required>
          <option value="customer">Customer</option>
          <option value="theater">Theater</option>
        </select>
      </div>
      <div class="field">
        <i class="fas fa-id-card icon"></i>
        <input type="text" id="username" name="username" placeholder="Username" required>
      </div>
      <div class="field">
        <i class="fas fa-lock icon"></i>
        <input type="password" id="password" name="password" placeholder="Password" required>
      </div>
     
      <div class="field">
        <button type="submit"><i class="fas fa-sign-in-alt icon"></i> Login</button>
      </div>
  
    </form>
    <div class="form-link">
      <span>New User <a href="sign.php">Create Account</a></span>
    </div>
    <div class="social-icons">
      <i class="fab fa-facebook"></i>
      <i class="fab fa-twitter"></i>
      <i class="fab fa-google"></i>
    </div>
  </div>

</body>
</html>