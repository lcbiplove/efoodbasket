<?php

namespace Core;

/**
 * View
 *
 * PHP version 7.0
 */
class View
{

    /**
     * Render a view file
     *
     * @param string $view  The view file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);

        $file = dirname(__DIR__) . "/App/Views/$view";  // relative to Core directory

        if (is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("$file not found");
        }
    }

    /**
     * Render a view template using Twig
     *
     * @param string $template  The template file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function renderTemplate($template, $args = [])
    {
        static $twig = null;

        if ($twig === null) {
            $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/App/Views');
            $twig = new \Twig\Environment($loader);
            $twig->addGlobal('is_authenticated', \App\Auth::isAuthenticated());
            $twig->addGlobal('user', \App\Auth::getUser());
            $twig->addGlobal('is_trader', \App\Auth::isTraderAuthenticated());
            $twig->addGlobal('is_admin', \App\Auth::isAdminAuthenticated());
            $twig->addGlobal('cookieMessage', \App\Extra::getMessageCookie());

            $contactFilter = new \Twig\TwigFilter('getBeautifulContact', function ($phone) {
                return \App\Extra::getBeautifulPhone($phone);
            });
            $productImageUrl = $imageFromId = new \Twig\TwigFilter('productImageUrl', function ($image_name) {
                return \App\Extra::productImageUrl($image_name);
            });
            $imageFromId = new \Twig\TwigFilter('getFirstImageUrl', function ($product_id) {
                return \App\Extra::getFirstImageUrl($product_id);
            });
            $productOwnerId = new \Twig\TwigFilter('getProductOwnerId', function ($product_id) {
                return \App\Extra::getProductOwnerId($product_id);
            });
            $twig->addFilter($contactFilter);
            $twig->addFilter($productImageUrl);
            $twig->addFilter($imageFromId);
            $twig->addFilter($productOwnerId);
        }

        echo $twig->render($template, $args);
    }
}
