<?php
$host = "localhost";
$port = "5432";
$dbname = "php";
$user = "postgres";
$password = "root";

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname;user=$user;password=$password");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

function insertMovie($m_id, $title, $genre, $releasedate, $image_url)
{
    global $pdo;

    $sqlCheckMId = "SELECT COUNT(*) FROM movie WHERE m_id = ?";
    $stmtCheckMId = $pdo->prepare($sqlCheckMId);
    $stmtCheckMId->execute([$m_id]);
    $countMId = $stmtCheckMId->fetchColumn();

    if ($countMId > 0) {
        echo "Error: Movie with the same ID already exists.";
    } else {
        $sqlCheckTitle = "SELECT COUNT(*) FROM movie WHERE title = ?";
        $stmtCheckTitle = $pdo->prepare($sqlCheckTitle);
        $stmtCheckTitle->execute([$title]);
        $countTitle = $stmtCheckTitle->fetchColumn();

        if ($countTitle > 0) {
            echo "Error: Movie with the same title already exists.";
        } else {
            $sqlInsert = "INSERT INTO movie (m_id, title, genre, releasedate, image_url) VALUES (?, ?, ?, ?, ?)";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->execute([$m_id, $title, $genre, $releasedate, $image_url]);
            echo "Record inserted successfully.";
        }
    }
}

function insertMovieShow($m_id, $title, $genre, $releasedate, $image_url, $selectedTime, $screenno, $price)
{
    global $pdo;

    $sqlCheckTitle = "SELECT COUNT(*) FROM movie WHERE title = ?";
    $stmtCheckTitle = $pdo->prepare($sqlCheckTitle);
    $stmtCheckTitle->execute([$title]);
    $countTitle = $stmtCheckTitle->fetchColumn();

    if ($countTitle > 0) {
        echo "Error: Movie with the same title already exists.";
    } else {
        $sqlGetShowDetails = "SELECT s_id, time, capacity FROM show WHERE time=?";
        $stmtGetShowDetails = $pdo->prepare($sqlGetShowDetails);
        $stmtGetShowDetails->execute([$selectedTime]);
        $showDetails = $stmtGetShowDetails->fetch(PDO::FETCH_ASSOC);

        if (!$showDetails) {
            echo "Error: Selected show time not found.";
        } else {
            $s_id = $showDetails['s_id'];
            $time = $showDetails['time'];
            $capacity = $showDetails['capacity'];

            $sqlCheckMovieShow = "SELECT COUNT(*) FROM movie_show WHERE m_id = ? AND s_id = ?";
            $stmtCheckMovieShow = $pdo->prepare($sqlCheckMovieShow);
            $stmtCheckMovieShow->execute([$m_id, $s_id]);
            $countMovieShow = $stmtCheckMovieShow->fetchColumn();

            if ($countMovieShow > 0) {
                echo "Error: Movie with the same ID and show time already exists.";
            } else {
                $sqlCheckExistingShow = "SELECT COUNT(*) FROM movie_show WHERE s_id = ?";
                $stmtCheckExistingShow = $pdo->prepare($sqlCheckExistingShow);
                $stmtCheckExistingShow->execute([$s_id]);
                $countExistingShow = $stmtCheckExistingShow->fetchColumn();

                if ($countExistingShow > 0) {
                    echo "Error: Show time already assigned to another movie.";
                } else {
                    insertMovie($m_id, $title, $genre, $releasedate, $image_url);

                    $ms_id = getRandomMsId();
                    $sqlMovieShow = "INSERT INTO movie_show (ms_id, m_id, s_id, seats, price, screenno) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmtMovieShow = $pdo->prepare($sqlMovieShow);
                    $stmtMovieShow->execute([$ms_id, $m_id, $s_id, $capacity, $price, $screenno]);
                    echo "Record inserted successfully.";
                }
            }
        }
    }
}

function updateMovie($m_id, $fieldToUpdate, $newValue)
{
    global $pdo;

    $allowedFields = ['title', 'genre', 'releasedate', 'image_url'];

    if (!in_array($fieldToUpdate, $allowedFields)) {
        echo "Error: Invalid field to update.";
    } else {
        $sql = "UPDATE movie SET $fieldToUpdate = ? WHERE m_id = ?";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$newValue, $m_id]);
            echo "Record updated successfully.";
                } catch (PDOException $e) {
            echo "Error updating record: ";
        }
    }
}

function deleteMovie($title)
{
    global $pdo;

    try {
        $pdo->beginTransaction();

        $sqlGetMovieId = "SELECT m_id FROM movie WHERE title=?";
        $stmtGetMovieId = $pdo->prepare($sqlGetMovieId);
        $stmtGetMovieId->execute([$title]);
        $m_id = $stmtGetMovieId->fetchColumn();

        if ($m_id) {
            $sqlDeleteMovieShow = "DELETE FROM movie_show WHERE m_id=?";
            $stmtDeleteMovieShow = $pdo->prepare($sqlDeleteMovieShow);
            $stmtDeleteMovieShow->execute([$m_id]);

            $sqlDeleteMovie = "DELETE FROM movie WHERE m_id=?";
            $stmtDeleteMovie = $pdo->prepare($sqlDeleteMovie);
            $stmtDeleteMovie->execute([$m_id]);

            $pdo->commit();
            echo"Record deleted successfully.";
        } else {
            echo "Record not found.";
        }

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error deleting record: ";
    }
}

function getRandomMsId()
{
    return mt_rand(10000, 99999);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add"])) {
        $m_id = $_POST["m_id"];
        $title = $_POST["title"];
        $genre = $_POST["genre"];
        $releasedate = $_POST["releasedate"];
        $image_url = $_POST["image_url"];
        $selectedTime = $_POST["selected_time"];
        $screenno = $_POST["screenno"];
        $price = $_POST["price"];

        insertMovieShow($m_id, $title, $genre, $releasedate, $image_url, $selectedTime, $screenno, $price);
    } elseif (isset($_POST["update"])) {
        $update_m_id = $_POST["update_m_id"];
        
        if (isset($_POST["field_to_update"]) && isset($_POST["new_field_value"])) {
            $fieldToUpdate = $_POST["field_to_update"];
            $newFieldValue = $_POST["new_field_value"];

            updateMovie($update_m_id, $fieldToUpdate, $newFieldValue);
        } else {
            echo "Error: Please fill in all required fields for the update.";
        }
    } elseif (isset($_POST["delete"])) {
        $delete_title = $_POST["delete_title"];
        deleteMovie($delete_title);
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie CRUD Form</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <style>
        body{
             font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            padding: 20px;
            display: flex;
            flex-direction: row;
            justify-content: space-around;
            background: url("background10.jpg");
            background-size: cover;
            font-size: 30px;
        }

        h2 {
            color: orange;
            margin-top: 20px;
        }

        .forms-container {
            display: flex;
            justify-content: space-around;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);

        }

        form {
            flex: 0 1 30%;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-right: 20px;
            background: rgba(255, 255, 255, 0.8);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #1a8cff;
            font-size: 25px;
            font-family: 'Times New Roman', Times, serif;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 25px; 
        }

        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 25px; 
        }

        button {
            background-color: skyblue;
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

        button:hover {
            background-color: #45a049;
        }

        body, label, input, button {
            color: #333;
            font-size: ;
        }px

        @media screen and (max-width: 600px) {
            .forms-container {
                flex-direction: column;
            }

            form {
                width: 100%;
                margin-bottom: 20px;
                margin-right: 0;
            }
        }
        .small-movies-btn {
    background-color: skyblue;
    color: #fff;
    width: 20%;
    padding: 20px;
    border-radius: 8px;
    cursor: pointer;
    font-size: larger;
    transition: background-color 0.3s ease;
    font-family: 'Times New Roman', Times, serif;
    animation: scaleIn 0.5s ease;
    height: 60px;
    margin-top: 200px; 
    margin-right: 90px; 
}

.small-movies-btn:hover,
button:hover {
    background-color: #45a049;
}
h1{
            color: red;
            margin-top: 150px;
            margin-left: 50px;
        
        }


    </style>
</head>
<body>
    <div id="Add">
        <h2>Add Movie</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="m_id">Movie ID:</label>
            <input type="text" id="m_id" name="m_id" required>

            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>

            <label for="genre">Genre:</label>
            <input type="text" id="genre" name="genre" required>

            <label for="releasedate">Release Date:</label>
            <input type="date" id="releasedate" name="releasedate" required>

            <label for="image_url">Image URL:</label>
            <input type="text" id="image_url" name="image_url" required>

            <label for="selected_time">Show Time:</label>
            <select id="selected_time" name="selected_time" required>
                <?php
                $sqlShowTimes = "SELECT time FROM show";
                $stmtShowTimes = $pdo->query($sqlShowTimes);
                while ($row = $stmtShowTimes->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $row['time'] . '">' . $row['time'] . '</option>';
                }
                ?>
            </select>

            <label for="screenno">Screen Number:</label>
            <input type="number" id="screenno" name="screenno" required>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" required>

            <button type="submit" name="add">Add Movie</button>
        </form>
    </div>

    <div id="update">
        <h2>Update Movie</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="update_m_id">Movie ID to Update:</label>
            <input type="text" id="update_m_id" name="update_m_id" required>

            <label for="field_to_update">Field to Update:</label>
            <select id="field_to_update" name="field_to_update">
                <option value="title">Title</option>
                <option value="genre">Genre</option>
                <option value="releasedate">Release Date</option>
                <option value="image_url">Image URL</option>
            </select>

            <label for="new_field_value">New Value:</label>
            <input type="text" id="new_field_value" name="new_field_value">

            <button type="submit" name="update">Update Movie</button>
        </form>
    </div>

    <div id="delete"> 
        <h2>Delete Movie</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="delete_title">Title to Delete:</label>
            <input type="text" id="delete_title" name="delete_title" required>

            <button type="submit" name="delete">Delete Movie</button>
        </form>
    </div>
    <h1>Movie Details:-</h1>
    <button type="button" class="btn btn-primary btn-sm small-movies-btn" data-toggle="modal" data-target="#moviesModal" style="font-size: smaller;">
    Movies
</button>




<div class="modal fade" id="moviesModal" tabindex="-1" role="dialog" aria-labelledby="moviesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moviesModalLabel">Movies Table</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Movie ID</th>
                            <th>Title</th>
                            <th>Release Date</th>
                            <th>Image URL</th>
                            <th>Show Time</th>
                            <th>Capacity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sqlMovies = "SELECT m.m_id, m.title, m.releasedate, m.image_url, s.time, s.capacity
                                      FROM movie m
                                      JOIN movie_show ms ON m.m_id = ms.m_id
                                      JOIN show s ON ms.s_id = s.s_id";
                        $stmtMovies = $pdo->query($sqlMovies);
                        while ($row = $stmtMovies->fetch(PDO::FETCH_ASSOC)) {
                            echo '<tr>';
                            echo '<td>' . $row['m_id'] . '</td>';
                            echo '<td>' . $row['title'] . '</td>';
                            echo '<td>' . $row['releasedate'] . '</td>';
                            echo '<td>' . $row['image_url'] . '</td>';
                            echo '<td>' . $row['time'] . '</td>';
                            echo '<td>' . $row['capacity'] . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


</body>
</html>
