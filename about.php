<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>about</title>
   <link rel="icon" href="images/content.jpg" type="image/x-icon">

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<div class="heading">
   <h3>about us</h3>
   <p><a href="home.php">home</a> <span> / about</span></p>
</div>

<!-- about section starts  -->

<section class="about">

   <div class="row">

      <div class="image">
         <img src="images/about-img.svg" alt="">
      </div>

      <div class="content">
         <h3>why choose us?</h3>
         <p> SLFood.lk was chosen as the project title and concept because it strongly reflects Sri Lanka’s identity while addressing a real and growing need in the tourism and hospitality sector. The name itself highlights the local context, with “SL” symbolizing Sri Lanka and “.lk” emphasizing its national domain, making it instantly recognizable as a Sri Lankan platform. By focusing on both food and tourism, SLFood.lk integrates two essential aspects of the visitor experience, offering tourists and locals an accessible way to explore authentic Sri Lankan cuisine. The brand is simple, memorable, and capable of building trust among users, while also filling a market gap between existing food delivery services and tourism-focused digital solutions. Furthermore, the concept has strong potential for future expansion, including partnerships with restaurants, hotels, and travel services, making it not only a timely idea but also a scalable and sustainable project.</p>
         <a href="menu.php" class="btn">our menu</a>
      </div>

   </div>

</section>

<!-- about section ends -->

<!-- steps section starts  -->

<section class="steps">

   <h1 class="title">simple steps</h1>

   <div class="box-container">

      <div class="box">
         <img src="images/step-1.png" alt="">
         <h3>choose order</h3>
         <p>Opt for the 'Choose Order' feature and tailor your dining experience to your preferences, ensuring a personalized and satisfying culinary journey at The Outer Clove Restaurant.</p>
      </div>

      <div class="box">
         <img src="images/step-2.png" alt="">
         <h3>fast delivery</h3>
         <p>Experience swift and efficient service with our fast delivery option, ensuring that your delicious meals from The Outer Clove Restaurant reach your doorstep promptly and in perfect condition.</p>
      </div>

      <div class="box">
         <img src="images/step-3.png" alt="">
         <h3>enjoy food</h3>
         <p>Savor the extraordinary flavors and culinary delights at The Outer Clove Restaurant, where each dish is meticulously crafted to provide you with an unforgettable and delightful dining experience.</p>
      </div>

   </div>

</section>

<!-- steps section ends -->


<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->=



<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<script>

var swiper = new Swiper(".reviews-slider", {
   loop:true,
   grabCursor: true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
   breakpoints: {
      0: {
      slidesPerView: 1,
      },
      700: {
      slidesPerView: 2,
      },
      1024: {
      slidesPerView: 3,
      },
   },
});

</script>

</body>
</html>