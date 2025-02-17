<?php
// Write to UIDContainer.php
$Write = "<?php $" . "UIDresult=''; " . "echo $" . "UIDresult;" . " ?>";
file_put_contents('UIDContainer.php', $Write);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/bootstrap.min.js"></script>
    <style>
        html {
            font-family: Arial;
            display: inline-block;
            margin: 0px auto;
            text-align: center;
        }

        ul.topnav {
            list-style-type: none;
            margin: auto;
            padding: 0;
            overflow: hidden;
            background-color: #4CAF50;
            width: 70%;
        }

        ul.topnav li {
            float: left;
        }

        ul.topnav li a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        ul.topnav li a:hover:not(.active) {
            background-color: #3e8e41;
        }

        ul.topnav li a.active {
            background-color: #333;
        }

        ul.topnav li.right {
            float: right;
        }

        @media screen and (max-width: 600px) {
            ul.topnav li.right,
            ul.topnav li {
                float: none;
            }
        }

        .table {
            margin: auto;
            width: 90%;
        }

        thead {
            color: #FFFFFF;
        }
    </style>

    <title>User Data : NodeMCU V3 ESP8266 / ESP12E with MYSQL Database</title>
</head>

<body>
    <h2>NodeMCU V3 ESP8266 / ESP12E with MYSQL Database</h2>
    <ul class="topnav">
        <li><a href="home.php">Home</a></li>
        <li><a class="active" href="user data.php">User Data</a></li>
        <li><a href="registration.php">Registration</a></li>
        <li><a href="read tag.php">Read Tag ID</a></li>
    </ul>
    <br>
    <div class="container">
        <div class="row">
            <h3>User Data Table</h3>
        </div>
        <div class="row">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr bgcolor="#10a0c5" color="#FFFFFF">
                        <th>Name</th>
                        <th>ID</th>
                        <th>Gender</th>
                        <th>Email</th>
                        <th>Mobile Number</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include 'database.php';

                    try {
                        // Connect to the database
                        $pdo = Database::connect();
                        $sql = 'SELECT * FROM table_nodemcu_rfidrc522_mysql ORDER BY name ASC';

                        // Iterate through query results
                        foreach ($pdo->query($sql) as $row) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($row['name'] ?? 'N/A') . '</td>';
                            echo '<td>' . htmlspecialchars($row['id'] ?? 'N/A') . '</td>';
                            echo '<td>' . htmlspecialchars($row['gender'] ?? 'N/A') . '</td>';
                            echo '<td>' . htmlspecialchars($row['email'] ?? 'N/A') . '</td>';
                            echo '<td>' . htmlspecialchars($row['mobile'] ?? 'N/A') . '</td>';
                            echo '<td>';
                            if (isset($row['id'])) {
                                echo '<a class="btn btn-success" href="user data edit page.php?id=' . urlencode($row['id']) . '">Edit</a> ';
                                echo '<a class="btn btn-danger" href="user data delete page.php?id=' . urlencode($row['id']) . '">Delete</a>';
                            } else {
                                echo 'N/A';
                            }
                            echo '</td>';
                            echo '</tr>';
                        }

                        Database::disconnect();
                    } catch (PDOException $e) {
                        echo '<tr><td colspan="6">Error fetching data: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div> <!-- /container -->
</body>

</html>
