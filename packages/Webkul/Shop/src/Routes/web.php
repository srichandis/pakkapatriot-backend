<?php

/**
 * WebMCP navigation routes. Declared before the store-front fallback so the
 * concrete `webmcp/*` paths are matched ahead of the slug catch-all.
 */
require 'webmcp-routes.php';

/**
 * Store front routes.
 */
require 'store-front-routes.php';

/**
 * Customer routes. All routes related to customer
 * in storefront will be placed here.
 */
require 'customer-routes.php';

/**
 * Checkout routes. All routes related to checkout like
 * cart, coupons, etc will be placed here.
 */
require 'checkout-routes.php';

/**
 * Blog frontend routes.
 *
 * Declared last so the root-level post permalink route ({slug}) matches only
 * URLs that no other shop/customer/checkout route handled.
 */
require base_path('routes/frontend-blog.php');
