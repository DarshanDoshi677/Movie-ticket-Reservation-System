<?php
session_start();

$host = "localhost";
$port = "5432";
$dbname = "php";
$user = "postgres";
$password = "root";


try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password");

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['c_id'])) {
            $customerId = $_POST['c_id'];
            $checkCustomerQuery = "SELECT * FROM customer WHERE c_id = :customerId";
            $checkCustomerStatement = $conn->prepare($checkCustomerQuery);
            $checkCustomerStatement->bindParam(':customerId', $customerId, PDO::PARAM_INT);
            $checkCustomerStatement->execute();
    
            if ($checkCustomerStatement->rowCount() == 0) {
                exit("Customer ID not found. Please enter a valid customer ID.");
            }
    
            $_SESSION['form_data'] = $_POST;
    
            header("Location: payment.php");
            exit; 
        }
    }
    


    $query = "SELECT MS.MS_id, M.Title AS movie_title, S.Time AS show_time, MS.Price AS movie_price, MS.MS_id AS movie_show_id, MS.Seats AS available_seats
    FROM Movie_Show MS
    JOIN Movie M ON MS.M_id = M.M_id
    JOIN Show S ON MS.S_id = S.S_id";

    $result = $conn->query($query);

    $dataList = $result->fetchAll(PDO::FETCH_ASSOC);

    $conn = null;

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>Movie Reservation System</title>
    <style>

 body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: white;
            margin: 0;
            padding: 0;
            background-image: url('background8.jpg');
            background-size: cover;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 800px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.7);
            border-radius: 15px;
            background-color: #FFE4ED;
            font-size: 25px;
            color: rgb(34, 34, 34);
            float: left;
            margin-left: 400px;
        }

        header {
            background-color: #3498db;
            padding: 15px;
            text-align: center;
            color: #fff;
            display: flex;
            align-items: center;
            animation: fadeInDown 1s ease-out;
        }

        #logo {
            margin-right: 10px;
            animation: fadeIn 1s ease-out;
        }

        #logo img {
            width: 100px;
            height: 100px;
        }

        h1 {
            margin: 0;
            margin-left: 20px;
            animation: fadeIn 1s ease-out;
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

        nav a {
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
            animation: fadeIn 1s ease-out;
            font-size: 20px;
        }

        nav a:hover {
            background-color: #27ae60;
        }

        .seats {
            width: 200px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            background-color: #FD61AF;
            font-size: 25px;
            color: rgb(34, 34, 34);
            margin-left: 20px;
            margin-bottom: 1000px;
            float: left;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.7);
        }

        @keyframes fadeInDown {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        input[type=date],
        input[type=number],
        input[type=number]:read-only,
        input[type=submit] {
            font-size: 28px;
font-family: 'Times New Roman', Times, serif;
        }
        label {
    display: block;
    margin-bottom: 12px;
    color: #555;
}

input, select {
    width: calc(100% - 16px);
    padding: 12px;
    margin-bottom: 20px;
    box-sizing: border-box;
    border: 1px solid #ddd;
    border-radius: 8px;
}
input#text{
    font-size: 20px;
    margin-right: 500px;
}

button {
    background-color: #3498db;
    color: #fff;
    padding: 12px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    width: 100%;
    display: inline-block;
    margin-top: 10px;
    font-size: 20px;
    font-family:'Times New Roman', Times, serif;
}

button:hover {
    background-color: #2980b9; 
}
.form-group {
    display: flex;
    justify-content: space-between;
}

.form-group label {
    width: 48%;
    color: rgb(34,34,34);
    font-family: 'Times New Roman', Times, serif;

}

.form-group select {
    width: 80%; 
    margin-right: 100px;
    font-size: 28px;
    font-family: 'Times New Roman', Times, serif;
}
input[type=text]{
font-size: 28px;
font-family: 'Times New Roman', Times, serif;
}
header {
    background-color: #3498db;
    padding: 15px;
    text-align: center;
    color: #fff;
}


.icon {
    margin-right: 5px;
}
h3{
    color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
            animation: fadeIn 1s ease-out;
            font-size: 40px;
}
header {
            background-image: url('images.jpg');
            font-family: 'Times New Roman', Times, serif;
            background-color: #3498db;
            color: #fff;
            padding: 15px;
            display: flex;
            align-items: center;
            animation: fadeInDown 1s ease-out;
        }


</style>
</head>
<body>
    <header>
            <div id="logo">
                <img src="logo.jpg" alt="Logo">
            </div>
            <h1>Booking Page</h1>
        
    
        <nav>
            <a href="Homepage.php"><h3>Home</h3></a>
          
        </nav>
    </header><br>
    
    <div class="container">
    
        <h2>Movie Reservation System</h2>
      
        <form method="post" action="" id="reservationform">
            <div class="form-group">
                <label for="c_id">Customer ID:</label>
                <input type="text" id="c_id" name="c_id" required>
            </div>

            <div class="form-group">
                <label for="movie_title">Select Movie:</label>
                <select id="movie_title" name="movie_title" onchange="updateShowTimes()" required>
                    <?php
                    foreach ($dataList as $row) {
                        echo "<option value=\"{$row['movie_title']}\" 
                            data-show-time=\"{$row['show_time']}\" 
                            data-movie-price=\"{$row['movie_price']}\" 
                            data-available-seats=\"{$row['available_seats']}\">{$row['movie_title']}</option>";
                    }
                    
                    
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="show_time">Select Show:</label>
                <select id="show_time" name="show_time" required>
                </select>
            </div>
			
            <div class="form-group">
                <label for="screen_no">Select Screen:</label>
                
                <select id="screen_no" name="screen_no" required>
                    <option value="1">Screen 1</option>
                </select>
            </div>

            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required>
            </div>

            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity"
name="quantity" min="1" required>
</div>
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" min="0" readonly required>
        </div>
       
                

        <button type="button" onclick="calculateTotalPrice()">Calculate Total Price</button>
            <button type="submit">Make Reservation</button>
                </div>
        </div>

        <div class="seats">
        <h3>Available Seats</h3>
        <input type="text" id="seats" name="seats" readonly>
    </div>
                

                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; 
    var yyyy = today.getFullYear();

    if (dd < 10) {
        dd = '0' + dd;
    }

    if (mm < 10) {
        mm = '0' + mm;
    }

    today = yyyy + '-' + mm + '-' + dd;

    document.getElementById('date').min = today;

    updateShowTimes();
});

  
  function calculateTotalPrice() {
    var selectedMovie = document.getElementById('movie_title');
    var selectedOption = selectedMovie.options[selectedMovie.selectedIndex];
    var availableSeats = parseInt(selectedOption.getAttribute('data-available-seats'));

    var quantityInput = document.getElementById('quantity');
    var quantity = parseInt(quantityInput.value);

    if (isNaN(quantity) || quantity <= 0) {
        alert('Please enter a valid quantity.');
        return;
    }

    if (quantity > availableSeats) {
        alert('Quantity cannot exceed available seats (' + availableSeats + ').');
        quantityInput.value = availableSeats; 
        return;
    }

    if (availableSeats - quantity < 0) {
        alert('Not enough available seats for the selected quantity.');
        return;
    }

    var moviePrice = parseFloat(selectedOption.getAttribute('data-movie-price'));
    var totalPrice = moviePrice * quantity;

    document.getElementById('price').value = totalPrice.toFixed(2);
    updateAvailableSeats();
    showPaymentForm();
}


    
    function updateShowTimes() {
        var selectedMovie = document.getElementById('movie_title');
        var selectedOption = selectedMovie.options[selectedMovie.selectedIndex];
        var showTime = selectedOption.getAttribute('data-show-time');
        var availableSeats = selectedOption.getAttribute('data-available-seats');

        var showDropdown = document.getElementById('show_time');
        showDropdown.innerHTML = '';

        var option = document.createElement('option');
        option.value = showTime;
        option.text = showTime;
        showDropdown.add(option);

        document.getElementById('seats').value = availableSeats;

        
    }

    updateShowTimes();
    
</script>




</body></html>