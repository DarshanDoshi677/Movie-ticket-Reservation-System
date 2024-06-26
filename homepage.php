<?php
$host = "localhost";
$port = "5432";
$dbname = "php";
$user = "postgres";
$password = "root";

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname;user=$user;password=$password");

    $sql = "SELECT * FROM movie";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Ticket Reservation System</title>
    <style>
        .modal {
        display: none;
        position: fixed;
        z-index: 1;
        padding-top: 50px;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.9);
    }

    .modal-content {
        margin: auto;
        display: block;
        max-width: 80%; 
        max-height: 80%;
    }
        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            font-size: 30px;
            color: #fff;
            cursor: pointer;
        }

        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            overflow-x: hidden; 
    overflow-y: auto
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

        
        section {
            padding: 20px;
            background-image: url('background2.jpg');
        }

        .movie-container {
    display: flex;
    flex-wrap: wrap; 
    justify-content: space-between;
    box-sizing: border-box;
    overflow-x: auto;
    width: 100%;
    white-space: nowrap;
}

.movie {
    margin: 10px 10px 10px 0; 
    text-align: center;
    animation: fadeIn 0.8s ease-out;
    display: inline-block;
    vertical-align: top;
    white-space: normal;
    width: calc(20% - 20px); 
    box-sizing: border-box;
}

.movie-container {
    max-width: 100%; 
}

.movie img:hover {
    transform: scale(1.1);
}


        
.movie img {
    width: 100%; 
    height: 500px;
    object-fit: cover;
    border: 1px solid #ddd;
    border-radius: 5px;
    opacity: 0;
    transition: opacity 0.8s ease-out;
    margin-bottom: 10px;
}


.movie-info-container {
    text-align: left;
    padding: 15px;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-top: 15px;
    box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
    min-height: 450px; 
}


        .movie img.animate {
            opacity: 1;
        }

        .movie-info h3 {
            margin-bottom: 10px;
            color: #333;
            font-family: 'Times New Roman', Times, serif;
            font-size: 30px;
        }

        .movie-info p {
            margin: 10px 0;
            color: #666;
            font-size: 30px;
            font-family: 'Times New Roman', Times, serif;
        }

        .background-section {
            background-image: url('background.jpg');
            background-size: cover;
            background-position: center;
            padding: 20px 0;
            text-align: center;
            color: #fff;
        }

        footer {
            background-color: #F8E4F0;
            color: black;
            text-align: center;
            padding: 8px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
            box-shadow: 0px -5px 10px rgba(0, 0, 0, 0.2);
            display: flex;
            justify-content: space-around;
            align-items: center;
        }

        footer div {
            max-width: 300px;
        }

        footer h3 {
         
            margin-bottom: 10px;
        }

        footer p {
            margin: 8px 0;
            font-size: 18px;
        }

        .theater-logo {
            width: 10px;
            height: auto;
            margin-top: 10px;
            margin-right: 10px;
        }

        .book-ticket-button {
            background-color: #ffd700;
            color: #333;
            padding: 10px 20px;
            text-decoration: none;
            border: 2px solid #ffd700;
            border-radius: 25px;
            display: inline-block;
            margin-top: 15px;
            font-size: 30px;
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
            cursor: pointer;
            font-family: 'Times New Roman', Times, serif;
        }

        .book-ticket-button:hover {
            background-color: #333;
            color: #fff;
            border-color: #333;
        }

        .book-ticket-button {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease, transform 0.3s ease;
        }

        .book-ticket-button:hover {
            transform: scale(1.1);
        }

        .movie img:hover {
            transform: scale(1.1);
        }
        .page-container {
            max-height: 8000px; 
            overflow-y: auto;
            box-sizing: border-box; 
            padding-bottom: 300px; 
        }
     
    </style>
</head>
<body><div class="page-container">

        <header>
            <div id="logo">
                <img src="logo.jpg" alt="Logo">
            </div>
            <h1>Welcome to Housefull Cinemas</h1>
        
    
        <nav>
            <a href="login.php">Login</a>
            <a href="Booking.php">Booking</a>
            <a href="History.php">History</a>
        </nav>
    </header>
    
    <div class="movie-container">
        <?php foreach ($movies as $movie): ?>
            <div class="movie">
                <div class="movie-info-container">
                    <img src="<?php echo $movie['image_url']; ?>" alt="<?php echo $movie['title']; ?>" class="animate">
                    <div class="movie-info">
    <h3><?php echo $movie['title']; ?></h3>
    <h3><p>Genre: <?php echo $movie['genre']; ?></p></h3>
    <h3><p>Release Date: <?php echo $movie['releasedate']; ?></p></h3>
    <a href="Booking.php" class="book-ticket-button">Book Ticket</a>
</div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    </section>
        </div>
    
    <footer>
        <div>
           <h1> <p>&copy; 2024 Movie Ticket Reservation System</p></h1>
        </div>
        <div>
            <h1>Contact Us</h1>
            <h1><p>Email: info@housefullcinemas.com</p></h1>
           <h1> <p>Phone: +1 (123) 456-7890</p></h1>
           <h1> <p>Address: 123 Movie Street, Cityville, Country</p></h1>
        </div>
    </footer>
    <div id="myModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="fullImage">
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        var movieImages = document.querySelectorAll('.movie img');
        var modal = document.getElementById("myModal");
        var modalImg = document.getElementById("fullImage");
        var closeBtn = document.getElementsByClassName("close")[0];

        function isInViewport(element) {
            var rect = element.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        }

        function handleScroll() {
            movieImages.forEach(function (img) {
                if (isInViewport(img) && !img.classList.contains('animate')) {
                    img.classList.add('animate');
                }
            });
        }

        function openModal(imgSrc) {
            modal.style.display = "block";
            modalImg.src = imgSrc;
        }

        handleScroll();

        window.addEventListener('scroll', handleScroll);

        movieImages.forEach(function (img) {
            img.addEventListener('click', function () {
                openModal(img.src);
            });
        });

        closeBtn.addEventListener('click', function () {
            modal.style.display = "none";
        });

        window.addEventListener('click', function (event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    });
</script>


</body>
</html>
