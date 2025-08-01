<?php

/**
 * Responsive Header Component
 * Provides navigation, branding, and user controls
 * Mobile-first responsive design with hamburger menu
 */
?>
<header class="header-component">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
        <div class="container-fluid px-3 px-lg-4">
            <!-- Brand Logo -->
            <a class="navbar-brand d-flex align-items-center <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="/php/fooddelivery/index.php">
                <i class="fas fa-utensils me-2 fs-4"></i>
                Food Delivery
            </a>

            <!-- Mobile Menu Toggle -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                        <!-- Admin Navigation -->
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="/php/fooddelivery/admin/">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'menu_items.php' ? 'active' : ''; ?>" href="/php/fooddelivery/admin/menu_items.php">
                                <i class="fas fa-utensils me-1"></i>Menu Items
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>" href="/php/fooddelivery/admin/orders.php">
                                <i class="fas fa-shopping-cart me-1"></i>Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'discounts.php' ? 'active' : ''; ?>" href="/php/fooddelivery/admin/discounts.php">
                                <i class="fas fa-tags me-1"></i>Discounts
                            </a>
                        </li>
                    <?php else: ?>
                        <!-- Customer Navigation -->
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="../fooddelivery/index.php">
                                <i class="fas fa-home me-1"></i>Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'active' : ''; ?>" href="../fooddelivery/menu.php">
                                <i class="fas fa-book-open me-1"></i>Menu
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <!-- User Controls -->
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Cart (Customer Only) -->
                        <?php if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'): ?>
                            <li class="nav-item me-2">
                                <a class="nav-link position-relative" href="../fooddelivery/cart.php">
                                    <i class="fas fa-shopping-cart fs-5"></i>
                                    <?php if ($cart_count > 0): ?>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
                                            <?php echo $cart_count; ?>
                                        </span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- User Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar me-2">
                                    <i class="fas fa-user-circle fs-4"></i>
                                </div>
                                <span class="d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <h6 class="dropdown-header"><?php echo htmlspecialchars($_SESSION['full_name']); ?></h6>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                    <li><a class="dropdown-item" href="/php/fooddelivery/admin/store.php"><i class="fas fa-store me-2"></i>View Store</a></li>
                                <?php else: ?>
                                    <li><a class="dropdown-item" href="../fooddelivery/orders.php"><i class="fas fa-receipt me-2"></i>My Orders</a></li>
                                    <li><a class="dropdown-item" href="../fooddelivery/profile.php"><i class="fas fa-user-edit me-2"></i>Profile</a></li>
                                <?php endif; ?>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="/php/fooddelivery/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Guest User Options -->
                        <li class="nav-item">
                            <a class="nav-link" href="../fooddelivery/login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-light ms-2 px-3" href="../fooddelivery/register.php">
                                Sign Up
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<style>
    .header-component {
        position: sticky;
        top: 0;
        z-index: 1030;
    }

    .navbar-brand {
        transition: transform 0.2s ease;
    }

    .navbar-brand:hover {
        transform: scale(1.05);
    }

    .nav-link {
        transition: all 0.3s ease;
        border-radius: 0.375rem;
        margin: 0 0.125rem;
    }

    .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        transform: translateY(-1px);
    }

    .nav-link.active {
        background-color: rgba(255, 255, 255, 0.2);
        font-weight: 600;
    }

    .user-avatar {
        transition: transform 0.2s ease;
    }

    .dropdown-toggle:hover .user-avatar {
        transform: scale(1.1);
    }

    .dropdown-menu {
        border: none;
        border-radius: 0.5rem;
        min-width: 200px;
    }

    .dropdown-item {
        transition: all 0.2s ease;
        border-radius: 0.375rem;
        margin: 0.125rem 0.5rem;
    }

    .dropdown-item:hover {
        background-color: var(--bs-primary);
        color: white;
        transform: translateX(5px);
    }

    /* Mobile Optimizations */
    @media (max-width: 991.98px) {
        .navbar-nav {
            padding: 1rem 0;
        }

        .nav-link {
            padding: 0.75rem 1rem;
            margin: 0.125rem 0;
        }

        .dropdown-menu {
            position: static !important;
            transform: none !important;
            border: none;
            box-shadow: none;
            background-color: rgba(255, 255, 255, 0.1);
            margin-top: 0.5rem;
        }
    }

    /* Tablet Optimizations */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .container-fluid {
            padding-left: 2rem;
            padding-right: 2rem;
        }
    }
</style>