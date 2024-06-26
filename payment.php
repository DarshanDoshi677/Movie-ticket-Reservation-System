<?php
session_start();


if (isset($_SESSION['form_data'])) {
    $formData = $_SESSION['form_data'];

    $c_id = $formData['c_id'];
    $title = $formData['movie_title'];
    $show_time = $formData['show_time'];
    $date = $formData['date'];
    $seats = $formData['quantity'];
    $price = $formData['price'];

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Form</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;300;400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            border: none;
            outline: none;
            font-family: 'Poppins', sans-serif;
            text-transform: capitalize;
            transition: all 0.2s linear;
        
        }
        body{
            background-image: url('background8.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
        }

        .container {
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 25px;
            background-image: url("background8.png");
            background-size: cover;
            object-fit: cover;
            margin-left: 300px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0); 
        }

        .container form {
            display: flex;
            justify-content: space-between;
            width: 1200px;
            padding: 20px;
            background: #fff;
            box-shadow: 5px 5px 30px rgba(0, 0, 0, 0.2);
            background-image: url("background2.jpg");
        }

        .container form .col {
            flex: 1 1 500px;
        }

        .col .title {
            font-size: 20px;
            color: #5e8f94;
            padding-bottom: 5px;
        }

        .col .inputBox {
            margin: 15px 0;
        }

        .col .inputBox label {
            margin-bottom: 10px;
            display: block;
        }

        .col .inputBox input,
        .col .inputBox select {
            width: 100%;
            border: 1px solid #ccc;
            padding: 10px 15px;
            font-size: 15px;
        }

        .col .inputBox input:focus,
        .col .inputBox select:focus {
            border: 1px solid #000;
        }

        .col .flex {
            display: flex;
            gap: 15px;
        }

        .col .flex .inputBox {
            flex: 1 1;
            margin-top: 5px;
        }

        .col .inputBox img {
            height: 34px;
            margin-top: 5px;
            filter: drop-shadow(0 0 1px #000);
        }

        .container form .submit_btn {
            width: 100%;
            padding: 12px;
            font-size: 17px;
            background: #5e8f94;
            color: #fff;
            margin-top: 5px;
            cursor: pointer;
            letter-spacing: 1px;
          
        }

        .container form .submit_btn:hover {
            background: #73B5BD;
        }

        input::-webkit-inner-spin-button,
        input::-webkit-outer-spin-button {
            display: none;
        }

        h1 {
            text-align: center;
        }
    </style>
</head>
<body>
<h1>PAYMENT FORM</h1> 
<div class="container">

    <form action="book.php" method="post">
        <div class="col">
        <h3 class="title">Order Details</h3>
    <div class="inputBox">
        <label for="customerID">Customer ID:</label>
        <input type="text" id="customerID" name="c_id" value="<?php echo isset($c_id) ? htmlspecialchars($c_id) : ''; ?>" placeholder="Enter customer ID" required>
    </div>
    <div class="inputBox">
        <label for="movieTitle">Movie Title:</label>
        <input type="text" id="movieTitle" name="movie_title" value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" placeholder="Enter movie title" required>
    </div>
    <div class="inputBox">
        <label for="showTime">Show Time:</label>
        <input type="text" id="showTime" name="show_time" value="<?php echo isset($show_time) ? htmlspecialchars($show_time) : ''; ?>" placeholder="Enter show time" required>
    </div>
    <div class="inputBox">
        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" value="<?php echo isset($seats) ? htmlspecialchars($seats) : ''; ?>" placeholder="Enter quantity" required>
    </div>
    <div class="inputBox">
        <label for="date">Date:</label>
        <input type="text" id="Date" name="date" value="<?php echo isset($date) ? htmlspecialchars($date) : ''; ?>" placeholder="Enter date" required>
    </div>
    <div class="inputBox">
        <label for="price">Price:</label>
        <input type="text" id="price" name="price" value="<?php echo isset($price) ? htmlspecialchars($price) : ''; ?>" placeholder="Enter price" required>
    </div>

           
        </div>

        <div class="col">
            <div class="billing-col">
                <h3 class="title">Billing Address</h3>
                <div class="inputBox">
                    <label for="name">Card Accepted:</label>
                    <img src="https://i.ibb.co/X38b5PF/card-img.png" alt="">
                </div>
                <div class="inputBox">
    <label for="cardName">Name On Card:</label>
    <input type="text" id="cardName" name="card_name" pattern="[A-Za-z\s\-]{2,}" placeholder="Enter card name" required>
    <small>Enter a valid name on the card (at least 2 characters, only letters, spaces, and hyphens).</small>
</div>
                <div class="inputBox">
    <label for="cardNum">Credit/Debit Card Number:</label>
    <input type="text" id="cardNum" name="card_number" pattern="\d{4}[\s-]?\d{4}[\s-]?\d{4}[\s-]?\d{4}" maxlength="19" placeholder="1111-2222-3333-4444" required>
    <small>Enter a valid credit card number (16 digits, with optional spaces or hyphens).</small>
</div>
                <div class="inputBox">
                    <label for="">Exp Month:</label>
                    <select name="" id="">
                        <option value="">Choose month</option>
                        <option value="January">January</option>
                        <option value="February">February</option>
                        <option value="March">March</option>
                        <option value="April">April</option>
                        <option value="May">May</option>
                        <option value="June">June</option>
                        <option value="July">July</option>
                        <option value="August">August</option>
                        <option value="September">September</option>
                        <option value="October">October</option>
                        <option value="November">November</option>
                        <option value="December">December</option>
                    </select>
                </div>
                <div class="flex">
                    <div class="inputBox">
                        <label for="">Exp Year:</label>
                        <select name="" id="">
                            <option value="">Choose Year</option>
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>
                            <option value="2027">2027</option>
                        </select>
                    </div>
                    <div class="inputBox">
                        <label for="cvv">CVV</label>
                        <input type="number" id="cvv" name="cvv" pattern="\d{3}" placeholder="123" required>
                    </div>
            </div>
        </div>
        <input type="submit" value="Confirm Booking" class="submit_btn">
    </form>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        function formatCardNumber(input) {
            let cardNumber = input.value.replace(/\s/g, "");
            if (Number(cardNumber)) {
                cardNumber = cardNumber.match(/.{1,4}/g);
                cardNumber = cardNumber.join(" ");
                input.value = cardNumber;
            }
        }

        function isValidCardName(cardName) {
            return /^[A-Za-z\s\-]{2,}$/.test(cardName) && cardName.split(' ').length === 3;
        }

        var cardNumInput = document.getElementById('cardNum');
        cardNumInput.addEventListener('keyup', function () {
            formatCardNumber(cardNumInput);
        });

        var form = document.querySelector('form');
        form.addEventListener('submit', function (event) {
            var cardNameInput = document.getElementById('cardName');
            if (!isValidCardName(cardNameInput.value)) {
                alert('Invalid card name format. Please enter a valid name with exactly three words.');
                event.preventDefault();
                return;
            }

        var cardNumInput = document.getElementById('cardNum');
            if (!/^\d{4}[\s-]?\d{4}[\s-]?\d{4}[\s-]?\d{4}$/.test(cardNumInput.value.replace(/\s/g, ""))) {
                alert('Invalid card number format. Please enter a valid 16-digit number.');
                event.preventDefault();
                return;
            }

            var cvvInput = document.getElementById('cvv');
            if (!/^\d{3}$/.test(cvvInput.value)) {
                alert('Invalid CVV format. Please enter a valid 3-digit number.');
                event.preventDefault();
                return;
            }
        });
    });
</script>



</body>
</html>

