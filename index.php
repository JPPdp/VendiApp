<?php
session_start(); // Start the session at the very beginning
$fullname = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Vendi</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="icon" href="assets/images/VendiBLK2_NoBG.png" type="image/icon type">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

    <header>
        <div class="LOGO">
            <a href="index.php" class="LOGO-NAME">Vendi</a>
        </div>
        <nav class="MAIN-NAV">
            <div class="DROPDOWN">
                <a href="#HOME" class="DROP-BUTTON">Home &#9662;</a>
                    <div class="DROPDOWN-LIST">
                        <a href="#FOOD-SECTION">Food</a>
                        <a href="#BEVERAGES-SECTION">Beverages</a>
                        <a href="#ENTERTAINMENT-SECTION">Entertainment</a>
                    </div>
            </div>
            <a href="ABOUT.html">About</a>
            <a href="CONTACT.html">Contact Us</a>
        </nav>

        <div class="ACCOUNT">
            <?php if ($fullname): ?>
                <a href="profile.html" id="btnPROFILE"><?php echo htmlspecialchars($fullname); ?></a>
            <?php else: ?>
                <a href="dashboard.html" id="btnDASHBOARD">Dashboard</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- HOME SECTION // BANNER -->
    <main class="HOME" id="HOME">
        <div class="BANNER">
            <div class="BANNER-INTRODUCTION">
                <h1>Find your <span>perfect</span> events partner.</h1>
                <p>Unforgettable events start here. Craft your ultimate event.</p>
                <div class="SEARCH-BAR">
                    <input type="text" placeholder="Search City | Ideal Stall | Event Essentials">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </div>
    </main>

    <!-- ABOUT SECTION 
    <section class="ABOUT" id="ABOUT">
        <h2 class="ABOUT-HEADER">Save Time and Effort, Book Online</h2>
          <div class="CONTENT">
            <p>Let us worry </p>
  
        </div>
       </section>-->


<!-- 🛍️ STALLS SECTION 🛍️ -->
<section class="FOOD-SECTION" id="FOOD-SECTION">
    <h1 class="HEADER">Food Stalls. <span>Catering to every craving.</span></h1>
    <div class="LIST">

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/food/potatocorner/thumbnail.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Potato Corner</div>
                    <div class="SUB-TITLE">World's Best Flavored Fries</div>
                    <p class="CATEGORY">Fast Food</p>
                    <p class="PRICE">Starting ₱1,450/hour</p>
                </div>
            </a>
        </div>

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/images/comingsoon.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Coming Soon</div>
                    <div class="SUB-TITLE"></div>
                    <p class="CATEGORY"></p>
                    <p class="PRICE"></p>
                </div>
            </a>
        </div>

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/images/comingsoon.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Coming Soon</div>
                    <div class="SUB-TITLE"></div>
                    <p class="CATEGORY"></p>
                    <p class="PRICE"></p>
                </div>
            </a>
        </div>

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/images/comingsoon.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Coming Soon</div>
                    <div class="SUB-TITLE"></div>
                    <p class="CATEGORY"></p>
                    <p class="PRICE"></p>
                </div>
            </a>
        </div>

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/images/comingsoon.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Coming Soon</div>
                    <div class="SUB-TITLE"></div>
                    <p class="CATEGORY"></p>
                    <p class="PRICE"></p>
                </div>
            </a>
        </div>

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/images/comingsoon.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Coming Soon</div>
                    <div class="SUB-TITLE"></div>
                    <p class="CATEGORY"></p>
                    <p class="PRICE"></p>
                </div>
            </a>
        </div>

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/images/comingsoon.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Coming Soon</div>
                    <div class="SUB-TITLE"></div>
                    <p class="CATEGORY"></p>
                    <p class="PRICE"></p>
                </div>
            </a>
        </div>

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/images/comingsoon.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Coming Soon</div>
                    <div class="SUB-TITLE"></div>
                    <p class="CATEGORY"></p>
                    <p class="PRICE"></p>
                </div>
            </a>
        </div>

    </div>
</section>


<!-- 🛍️ STALLS SECTION 🛍️ -->
<section class="BEVERAGES-SECTION" id="BEVERAGES-SECTION">
    <h1 class="HEADER">Beverages. <span>Pouring the perfect drink.</span></h1>
    <div class="LIST">

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/images/comingsoon.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Coming Soon</div>
                    <div class="SUB-TITLE"></div>
                    <p class="CATEGORY"></p>
                    <p class="PRICE"></p>
                </div>
            </a>
        </div>

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/images/comingsoon.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Coming Soon</div>
                    <div class="SUB-TITLE"></div>
                    <p class="CATEGORY"></p>
                    <p class="PRICE"></p>
                </div>
            </a>
        </div>

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/images/comingsoon.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Coming Soon</div>
                    <div class="SUB-TITLE"></div>
                    <p class="CATEGORY"></p>
                    <p class="PRICE"></p>
                </div>
            </a>
        </div>

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/images/comingsoon.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Coming Soon</div>
                    <div class="SUB-TITLE"></div>
                    <p class="CATEGORY"></p>
                    <p class="PRICE"></p>
                </div>
            </a>
        </div>

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/images/comingsoon.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Coming Soon</div>
                    <div class="SUB-TITLE"></div>
                    <p class="CATEGORY"></p>
                    <p class="PRICE"></p>
                </div>
            </a>
        </div>

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/images/comingsoon.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Coming Soon</div>
                    <div class="SUB-TITLE"></div>
                    <p class="CATEGORY"></p>
                    <p class="PRICE"></p>
                </div>
            </a>
        </div>

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/images/comingsoon.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Coming Soon</div>
                    <div class="SUB-TITLE"></div>
                    <p class="CATEGORY"></p>
                    <p class="PRICE"></p>
                </div>
            </a>
        </div>

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/images/comingsoon.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Coming Soon</div>
                    <div class="SUB-TITLE"></div>
                    <p class="CATEGORY"></p>
                    <p class="PRICE"></p>
                </div>
            </a>
        </div>

    </div>
</section>


<!-- 🛍️ STALLS SECTION 🛍️ -->
<section class="ENTERTAINMENT-SECTION" id="ENTERTAINMENT-SECTION">
    <h1 class="HEADER">Entertainment. <span>Ignite the celebration.</span></h1>
    <div class="LIST">

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/images/comingsoon.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Coming Soon</div>
                    <div class="SUB-TITLE"></div>
                    <p class="CATEGORY"></p>
                    <p class="PRICE"></p>
                </div>
            </a>
        </div>

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/images/comingsoon.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Coming Soon</div>
                    <div class="SUB-TITLE"></div>
                    <p class="CATEGORY"></p>
                    <p class="PRICE"></p>
                </div>
            </a>
        </div>

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/images/comingsoon.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Coming Soon</div>
                    <div class="SUB-TITLE"></div>
                    <p class="CATEGORY"></p>
                    <p class="PRICE"></p>
                </div>
            </a>
        </div>

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/images/comingsoon.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Coming Soon</div>
                    <div class="SUB-TITLE"></div>
                    <p class="CATEGORY"></p>
                    <p class="PRICE"></p>
                </div>
            </a>
        </div>

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/images/comingsoon.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Coming Soon</div>
                    <div class="SUB-TITLE"></div>
                    <p class="CATEGORY"></p>
                    <p class="PRICE"></p>
                </div>
            </a>
        </div>

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/images/comingsoon.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Coming Soon</div>
                    <div class="SUB-TITLE"></div>
                    <p class="CATEGORY"></p>
                    <p class="PRICE"></p>
                </div>
            </a>
        </div>

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/images/comingsoon.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Coming Soon</div>
                    <div class="SUB-TITLE"></div>
                    <p class="CATEGORY"></p>
                    <p class="PRICE"></p>
                </div>
            </a>
        </div>

        <div class="CARD">
            <a href="food.html" class="CARD-LINK">
                <div class="CARD-IMAGE">
                    <img src="assets/images/comingsoon.jpg" alt="Potato Corner">
                </div>
                <div class="CARD-DESCRIPTION">
                    <div class="TITLE">Coming Soon</div>
                    <div class="SUB-TITLE"></div>
                    <p class="CATEGORY"></p>
                    <p class="PRICE"></p>
                </div>
            </a>
        </div>

    </div>
</section>






    <footer class="FOOTER">
        <p>
            <a href="PRIVACY.html">Privacy Policy </a>
            <span> • </span>
            <a href="TERMS.html">Terms and Regulations</a>
        </p>
    </footer>


    <!-- SCROLL SKIPPER -->
    <div class="skip">
        <a href="#HOME"><i class="fas fa-arrow-up"></i></a>
      </div>
</body>
</html>
