<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="CSS/style1.css">
    <nav class="navbar navbar-fixed-top" id="top-nav">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">Campus Recruitment System</a>
            </div>
            <ul id="list1" class="nav navbar-nav">
                <li class="active"><a href="company_dash.php">Home</a></li>
                <li class="active"><a href="index1.html">Logout</a></li>
            </ul>
        </div>
    </nav>
</head>
<body>

<?php
session_start();
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['name'])) {
        die("Session not started or company name is missing.");
    }

    $job_title = $_POST['job_title'];
    $salary = $_POST['salary'];
    $deadline = $_POST['deadline'];
    $bond = $_POST['bond'];
    $year = $_POST['year'];
    $cpi = $_POST['cpi'];
    $twp = $_POST['12p'];
    $tenp = $_POST['10p'];
    $branch = $_POST['branch'];
    $age = $_POST['age'];
    $degree = $_POST['degree'];
    $location = $_POST['location'];

    $sql = "INSERT INTO vacancy (company_name, job_title, salary, location, deadline, bond, age_e, degree_e, cpi_e, year_e, 12p_e, 10p_e) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsisdsddds", $_SESSION['name'], $job_title, $salary, $location, $deadline, $bond, $age, $degree, $cpi, $year, $twp, $tenp);

    if ($stmt->execute()) {
        // Send notifications
        $vacancy_message = "A new vacancy has been posted for your branch ($branch). Check it out now!";
        
        // Fetch students in the selected branch
        $query = "SELECT email FROM students WHERE branch = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $branch);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $student_email = $row['email'];
            $insert_query = "INSERT INTO notifications (email, message) VALUES (?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("ss", $student_email, $vacancy_message);
            $insert_stmt->execute();
        }

        echo "<script type='text/javascript'> 
                alert('Vacancy Created Successfully and Notifications Sent!');
                window.location.replace('company_dash.php');
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<div class="container-fluid" id="dash">
    <h1>CREATE VACANCY</h1>
    <form action="vacancy.php" method="POST">
        <div class="container">
            <h2>JOB DETAILS</h2>
            <hr>
            <ol>
                <li><h4><label for="job_title"><b>JOB TITLE</b></label></h4>
                    <input type="text" name="job_title" required></li>

                <li><h4><label for="salary"><b>SALARY</b></label></h4>
                    <input type="number" step="0.01" placeholder="in LPA" name="salary" required></li>

                <li><h4><label for="location"><b>LOCATION</b></label></h4>
                    <input type="text" placeholder="Ex.Delhi" name="location"></li>

                <li><h4><label for="deadline"><b>DEADLINE</b></label></h4>
                    <input type="date" name="deadline"></li>

                <li><h4><label for="bond"><b>BOND</b></label></h4>
                    <input type="number" name="bond"></li>

                <li><h4><label for="10p"><b>10<sup>th</sup> PERCENTAGE</b></label></h4>
                    <input type="number" step="0.01" name="10p"></li>

                <li><h4><label for="12p"><b>12<sup>th</sup> PERCENTAGE</b></label></h4>
                    <input type="number" step="0.01" name="12p"></li>

                <li><h4><label for="year"><b>YEAR</b></label></h4>
                    <input type="number" name="year"></li>

                <li><h4><label for="cpi"><b>CGPA</b></label></h4>
                    <input type="number" step="0.01" name="cpi"></li>

                <li><h4><label for="degree"><b>COURSE</b></label></h4>
                    <select name="degree">
                        <option value="btech">B.Tech</option>
                        <option value="mbatech">MBA.Tech</option>
                        <option value="be">MCA</option>
                        <option value="me">B.TECH(INTEGRATED)</option>
                        <option value="bca">B.PHARM</option>
                        <option value="mca">M.PHARM</option>
                        <option value="msc">MBA.PHARM</option>
                    </select></li>

                <li><h4><label for="branch"><b>BRANCH</b></label></h4>
                    <select name="branch">
                        <option value="cse">CSE</option>
                        <option value="it">IT</option>
                        <option value="ece">ECE</option>
                        <option value="mee">EXTC</option>
                        <option value="msme">MXTC</option>
                        <option value="che">CIVIL</option>
                    </select></li>

                <li><h4><label for="age"><b>MAXIMUM AGE</b></label></h4>
                    <input type="number" name="age"></li>
            </ol>
        </div>
        <br>
        <p>By creating an account you agree to our <a href="#">Terms & Privacy</a>.</p>
        <br>
        <button type="submit" class="registerbtn btn">Create Vacancy</button>
    </form>
</div>

</body>
</html>
