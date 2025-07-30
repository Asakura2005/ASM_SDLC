<?php
/**
 * Responsive Footer Component
 * Includes links, contact information, and legal notices
 * Mobile-first responsive design with collapsible sections
 */
?>
<footer class="footer-component bg-dark text-light mt-auto">
    <div class="container py-5">
        <div class="row g-4">
            <!-- Brand and Description -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-utensils me-2 text-primary"></i>
                        Food Delivery
                    </h5>
                    <p class="text-light-emphasis mb-3">
                        Delivering fresh, delicious meals from your favorite restaurants 
                        straight to your doorstep. Fast, reliable, and always satisfying.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link me-3" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link me-3" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link me-3" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6 col-sm-6">
                <h6 class="fw-bold mb-3 text-primary">Quick Links</h6>
                <ul class="footer-links list-unstyled">
                    <li><a href="/php/fooddelivery/index.php" class="footer-link">Home</a></li>
                    <li><a href="/php/fooddelivery/menu.php" class="footer-link">Menu</a></li>
                    <li><a href="/php/fooddelivery/about.php" class="footer-link">About Us</a></li>
                    <li><a href="#" class="footer-link">Contact</a></li>
                </ul>
            </div>
            
            <!-- Customer Service -->
            <div class="col-lg-2 col-md-6 col-sm-6">
                <h6 class="fw-bold mb-3 text-primary">Customer Service</h6>
                <ul class="footer-links list-unstyled">
                    <li><a href="#" class="footer-link">Track Order</a></li>
                    <li><a href="#" class="footer-link">Help Center</a></li>
                    <li><a href="#" class="footer-link">Returns</a></li>
                    <li><a href="#" class="footer-link">Feedback</a></li>
                    <li><a href="#" class="footer-link">Careers</a></li>
                </ul>
            </div>
            
            <!-- Contact Information -->
            <div class="col-lg-4 col-md-6">
                <h6 class="fw-bold mb-3 text-primary">Contact Information</h6>
                <div class="contact-info">
                    <div class="contact-item mb-3">
                        <i class="fas fa-map-marker-alt me-3 text-primary"></i>
                        <div>
                            <strong>Address:</strong><br>
                            <span class="text-light-emphasis">
                                Toà nhà FPT Polytechnic, 13 Trịnh Văn Bô<br>
                                Xuân Phương, Ha Noi City, Vietnam
                            </span>
                        </div>
                    </div>
                    <div class="contact-item mb-3">
                        <i class="fas fa-phone me-3 text-primary"></i>
                        <div>
                            <strong>Phone:</strong><br>
                            <a href="tel:+84396944022" class="footer-link">+84 396944022</a>
                        </div>
                    </div>
                    <div class="contact-item mb-3">
                        <i class="fas fa-envelope me-3 text-primary"></i>
                        <div>
                            <strong>Email:</strong><br>
                            <a href="mailto:support@fooddelivery.com" class="footer-link">support@fooddelivery.com</a>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-clock me-3 text-primary"></i>
                        <div>
                            <strong>Hours:</strong><br>
                            <span class="text-light-emphasis">24/7 Delivery Service</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bottom Bar -->
    <div class="footer-bottom bg-black py-3">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <p class="mb-0 text-light-emphasis text-center">
                        &copy; <?php echo date('Y'); ?> Food Delivery. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
.footer-component {
    margin-top: auto;
}

.footer-link {
    color: #adb5bd;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
}

.footer-link:hover {
    color: var(--bs-primary);
    transform: translateX(5px);
}

.footer-links li {
    margin-bottom: 0.5rem;
}

.social-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background-color: rgba(255, 255, 255, 0.1);
    color: #adb5bd;
    border-radius: 50%;
    text-decoration: none;
    transition: all 0.3s ease;
}

.social-link:hover {
    background-color: var(--bs-primary);
    color: white;
    transform: translateY(-3px);
}

.contact-item {
    display: flex;
    align-items: flex-start;
}

.contact-item i {
    margin-top: 0.25rem;
    flex-shrink: 0;
}

.newsletter-form .form-control {
    border: 1px solid #495057;
    background-color: rgba(255, 255, 255, 0.1);
    color: white;
}

.newsletter-form .form-control:focus {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
    background-color: rgba(255, 255, 255, 0.15);
}

.newsletter-form .form-control::placeholder {
    color: #adb5bd;
}

.footer-bottom {
    border-top: 1px solid #495057;
}

/* Mobile Optimizations */
@media (max-width: 767.98px) {
    .footer-component .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .contact-item {
        flex-direction: column;
        text-align: center;
    }
    
    .contact-item i {
        margin-bottom: 0.5rem;
        margin-right: 0;
    }
    
    .social-links {
        text-align: center;
    }
    
    .newsletter-form {
        max-width: 100%;
    }
    
    .legal-links {
        text-align: center !important;
        margin-top: 1rem;
    }
    
    .legal-links .list-inline-item {
        display: block;
        margin-bottom: 0.5rem;
    }
}

/* Tablet Optimizations */
@media (min-width: 768px) and (max-width: 991.98px) {
    .footer-component .row > div {
        margin-bottom: 2rem;
    }
}

/* Animation for footer links */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.footer-component .row > div {
    animation: slideInUp 0.6s ease-out;
}

.footer-component .row > div:nth-child(2) {
    animation-delay: 0.1s;
}

.footer-component .row > div:nth-child(3) {
    animation-delay: 0.2s;
}

.footer-component .row > div:nth-child(4) {
    animation-delay: 0.3s;
}
</style>