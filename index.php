<?php
    session_start();
    include("connect.php");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }


    $sql = "SELECT * FROM events ORDER BY id DESC LIMIT 4";
    $result = mysqli_query($conn, $sql);


    $clubSql = "SELECT * FROM clubs ORDER BY id DESC LIMIT 4";
    $clubResult = mysqli_query($conn, $clubSql);

    if (!$result || !$clubResult) {
        die("Error: " . mysqli_error($conn));
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Club - Home</title>
    <link rel="icon" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMwAAADACAMAAAB/Pny7AAAAsVBMVEX///9XO4PtbVgZmartalWs1twknKxQMX7sZk/98/LueWbi3un63Njn4+18aZx9a53++Pftc1/ym474zsja7fD0q6HO5+tPqbdarbr5080AlKZ5ZJpTNYD6+ftNLX1KKHvy8PXq9fayqMT3wLnxkYLvgG7Y0+HsYUn85+S5sMnOyNrzpJliSYrwintuWJKJeaWLxc6qnb7rWj+cj7T2uK9DHXc2AHGTha0+EnS93+RrucSTtQqWAAAFPklEQVR4nO2aaXOqPBSARVZ3sW5VNlEBqSJWe732//+wF7FvawlL4E4gOuf51LHJzHnmJCHLqdUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACelP43ubqpGL+USr/htJdvq3Wz2VyvBqMXp4ErpFqHTuSnzsGqUMdpn1b1d9uWuBDJfn9fv2D1VK2j7kVlWp52rErHWa7kwKP+C+6E0TPIylbX5ojMXNO3pkUi1gwayxUXNQlcZIzMtA6erjAiKtMVGUX3zBaRgJPpvww4KWqCJ9Mx56LIMAkywe9i14z+hyiNURPNCpaMYXaVa8jJMsF/mLlpkAodwVnFq2TKqJsucws4TSbU2ZS0ErTXcSMMQ8adMwrDYMgwQTvPJenwP46c6JIqY3n6T7BZMoHOx5z8SuDYyS7JMmqn9+c+1GyZoMWffYvsYOun5CVZRj3oGsNkyczFSBtNPxgkXQZ2ikuiTGcedcHIzNWG6FBbproky8TEuUf2Zj2kkdgjKNNuJqzJ+WX0oxFpZJz1MmX6g3SXXJnpuZ376a123HIz8ypnyNTlUxulESfDMF5vfzA3rmVZ7sY8nrse2oSkzFuWS2DTRNi9xssoAcz2BqOIilKqTHudLcMhSHKCzI9SnAdpmVO2S5xeM1UmFXIyjUHa9/LBZNq7J8pM9lr2QDLLIiq0ypwKTRk6Zfqjp5JJ32SCTJZM95lkeoRunf5FxtgjhzM8GeTQU7XM2qmp52Iy2t6gTGbXqNWOSVvJDJkjoRuNwjKDoLNZyIVRTDIuhWXq14cBd1skNcqW1E1gQRlObteu1zNFZMSeQZnMLux9LpSZIyGXqwx6jMyWkW7PT24RF2KjLJCpywjZMnYj7K0WGGdil9ztbL+B4GQePu3RV+8NcimWnZkNMZc4Mk/SnP39/MzkTY04L9UlW8b+ebBt/c0po5fyQoMvI63uGqO3r6l8EFvKislITeeusYE+BaSgeWWXBKTLRG+eWx6+jeaV/YKeLsPVl5HiE3eLe6zRyH1iCslI8ilaSKO6W7zcaOU8z2LLSHI0L6EN1kjTq3BJkbGbr7EFTlY3c01T9F4VBTSJMtz7wEnoYhw/0ieOSPZVNpl4Gc6202obLOZv8mZA+butJC21sPAEMeEkDpn5ETaKFq+jaEy5+7F7nJ3N/RLh6vIuS+XKprdlfr+Vhc9ovepUApm3dVOuSzcCkfVqFD/vUSxz720DBTEkEPH2lRTO3eO8Lk9vgytvo9NLO0/NqdpyzcP5vN/vz+eD6Zb+xU/getLJVzr7jaoahqFWXDcLRFCHF573r/D8eFh1NP/C4uJPJ4IgsCGCMJn6l6pjKsjQn3xp3CFM/AfMz/BzhpjcmE0WVQeXj0AlySWwmX0+kI56YZNVQh3hYebOwk9XCfEfIznDKYYLO/t8hIVgjOUS2Ezpt8HLy2PYLLBdrjaUz5vEr0uszWfV4abC53EJbPiqA05hkc8lgOKBlmPCfKWG3oF2yZ0YlqV2K5A7MRSnZozu+LMRxlWHHY9fwCXYpFUddiyLaREXSo8DlyKjjGUnVI4zvpALy1L54Sw2ZdiZT+GNmpprW3YnQ+OkWTyTDP5BJiJD47EGZECmBEAGZEoAZECmBJ5MhhUQkNBjmtAos7jwCH7URvDRRhcKN5pxjCeRITWhMAu4gAytgAytgAytgAytgAytoDJU3pLjgTwMTKh99cuGF2az71qt65+PU82Eog7H/FQINWas8MmPhxTe+OdBVRc3oBgYAAAAAAAAAAAAAAAAAAAAAAAAAAAAAIDn5T+7UaFJLPD3FgAAAABJRU5ErkJggg==">
    <link rel="stylesheet" href="assets/css/Header&Footer.css">
    <!--<link rel="stylesheet" href="assets/css/main.css">--> 
</head>
<body>
<div id="header-container"></div>
<main class="container">
    <div class="row">

        <div class="col-lg-6 event-section">
            <h2>Upcoming Events</h2>


            <div class="carousel">
                <div class="carousel-images">
                    <img src="assets\images\img1.jpg" alt="Event 1" class="carousel-item active">
                    <img src="assets\images\img2.png" alt="Event 2" class="carousel-item">
                    <img src="assets\images\img3.jpg" alt="Event 3" class="carousel-item">
                </div>
                <div class="carousel-buttons">
                    <button class="carousel-control-prev">Prev</button>
                    <button class="carousel-control-next">Next</button>
                </div>
            </div>                  
            <div class="event-list">
                <?php

                    while ($row = mysqli_fetch_assoc($result)) {
                        $eventName = htmlspecialchars($row['name']);
                        $eventDescription = htmlspecialchars($row['description']);
                        $eventCategory = htmlspecialchars($row['category']);
                        $eventMaxCapacity = htmlspecialchars($row['max_capacity']);
                        ?>
                        <a href="Event_Details.php?id=<?php echo urlencode($row['id']); ?>" class="event-item">
                            <h5><?php echo $eventName; ?></h5>
                            <p>Description: <?php echo $eventDescription; ?></p>
                            <small>Category: <?php echo $eventCategory; ?></small><br>
                            <small>Max Capacity: <?php echo $eventMaxCapacity; ?></small>
                        </a>
                        <?php
                    }
                ?>
            </div>

            <?php

                mysqli_close($conn);
            ?>

        </div>


        <div class="col-lg-6 activity-summary">
            <h2>Club Activities Summary</h2>


            <div class="card">
                <?php

                    while ($club = mysqli_fetch_assoc($clubResult)) {
                        $clubName = htmlspecialchars($club['name']);
                        $clubDescription = htmlspecialchars($club['description']);
                        ?>
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $clubName; ?></h5>
                                    <p class="card-text"><?php echo $clubDescription; ?></p>
                                    <a href="user_clubs.php" class="btn btn-primary">Learn More</a>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                ?>
            </div>

        </div>
    </div>
</main>

<footer>
    <div class="footer-container">
        <div class="about-us">
            <h5>About Us</h5>
            <p>We are a community focused on bringing people together through events and activities. Join us and be part of the journey!</p>
        </div>
        <div class="quick-links">
            <h5>Quick Links</h5>
            <ul>
                <li><a href="Club_Management.php">About</a></li>
                <li><a href="#!">Events</a></li>
                <li><a href="#!">Contact</a></li>
            </ul>
        </div>
        <div class="contact-info">
            <h5>Contact</h5>
            <ul>
                <li><span class="icon">&#9742;</span> +123 456 7890</li>
                <li><span class="icon">&#9993;</span> <a href="mailto:support@club.com">support@club.com</a></li>
            </ul>
        </div>
    </div>
    <div id="footer-container"></div>
</footer>

<script>
let currentIndex = 0;
const items = document.querySelectorAll('.carousel-item');
const totalItems = items.length;

document.querySelector('.carousel-control-next').addEventListener('click', () => {
    items[currentIndex].classList.remove('active');
    currentIndex = (currentIndex + 1) % totalItems;
    items[currentIndex].classList.add('active');
});

document.querySelector('.carousel-control-prev').addEventListener('click', () => {
    items[currentIndex].classList.remove('active');
    currentIndex = (currentIndex - 1 + totalItems) % totalItems;
    items[currentIndex].classList.add('active');
});

fetch('header.php')
    .then(response => response.text())
    .then(data => {
        document.getElementById('header-container').innerHTML = data;
    });

fetch('footer.php')
    .then(response => response.text())
    .then(data => {
        document.getElementById('footer-container').innerHTML = data;
    });
</script>
</body>
</html>