document.addEventListener('DOMContentLoaded', function () {
  var footerHTML = `
  <style>
  footer {
      margin-top:10rem;
      background-color: #F8F9FA;
      color: #6c757d;
    }
    .footer-section {
      background-color: #007BFF;
      color: #fff;
    }
    .footer-links h6 {
      font-weight: bold;
      color: #343a40;
    }
    .footer-links a {
      color: #343a40;
      text-decoration: none;
    }
    .footer-links a:hover {
      text-decoration: underline;
    }
    .footer-contact i {
      margin-right: 10px;
      color: #007BFF;
    }
  </style>
  <!-- Footer -->
  <footer class="text-center text-lg-start">
    <!-- Section: Social media -->
    <section class="footer-section d-flex justify-content-between p-4">
      <!-- Left -->
      <div class="me-5">
        <span>Get connected with us on social networks:</span>
      </div>
      <!-- Right -->
      <div>
        <a href="https://www.facebook.com/"  target="_blanck" class="text-white me-4"><i class="fab fa-facebook-f"></i></a>
        <a href="https://x.com/" target="_blanck" class="text-white me-4"><i class="fab fa-twitter"></i></a>
        <a href="https://www.google.com/" target="_blanck" class="text-white me-4"><i class="fab fa-google"></i></a>
        <a href="https://www.instagram.com/" target="_blanck" class="text-white me-4"><i class="fab fa-instagram"></i></a>
        <a href="https://www.linkedin.com/" target="_blanck" class="text-white me-4"><i class="fab fa-linkedin"></i></a>
        <a href="https://github.com/" target="_blanck" class="text-white me-4"><i class="fab fa-github"></i></a>
      </div>
    </section>

    <!-- Section: Links -->
    <section>
      <div class="container text-center text-md-start mt-5">
        <!-- Grid row -->
        <div class="row mt-3">
          <!-- Grid column -->
          <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4 footer-links">
            <h6 class="text-uppercase">Elite Zone </h6>
            <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: #007BFF; height: 2px;">
            <p>
             Elite Zone offers a premier selection of electronic components and PC parts, catering to both tech enthusiasts and professionals. Our extensive inventory ensures you find the latest and highest-quality products for all your computing needs.
            </p>
          </div>



          <!-- Grid column -->
          <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4 footer-links">
            <h6 class="text-uppercase">Useful links</h6>
            <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: #007BFF; height: 2px;">
            <p><a href="profile.php" class="text-dark">Your Account</a></p>
            <p><a href="items.php" class="text-dark">Item Shop</a></p>
            <p><a href="review.php" class="text-dark">Rate Us</a></p>
            <p><a href="contactus.php" class="text-dark">Contact Us</a></p>
          </div>

          <!-- Grid column -->
          <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4 footer-contact">
            <h6 class="text-uppercase">Contact</h6>
            <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: #007BFF; height: 2px;">
            <p><i class="fas fa-home"></i> Borj Rahal, MS 10324, LB</p>
            <p><i class="fas fa-envelope"></i> husseinatwi708@gmail.com</p>
            <p><i class="fas fa-phone"></i> +961 71 773 735</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Copyright -->
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
      © 2024 Copyright:
      <a class="text-dark" href="#">Elite Zone</a>
    </div>
  </footer>
  <!-- Footer -->
  `;
  document.body.insertAdjacentHTML('beforeend', footerHTML);
});
