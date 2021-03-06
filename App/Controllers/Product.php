<?php

namespace App\Controllers;

use App\Auth;
use App\Extra;
use App\Models;
use App\Models\ProductCategory;
use App\Models\Shop;
use App\Models\User;
use App\Models\Validation\ProductValidation;
use Core\View;

/**
 * Home controller
 *
 * PHP version 7.3
 */
class Product extends \Core\Controller
{
    /**
     * Each product page
     * 
     * @return void
     */
    public function productAction()
    {
        $product_id = $this->route_params['product_id'];
        $product = Models\Product::getProductObjectById($product_id);
        if(!$product){
            $this->show404();
        }

        View::renderTemplate('Product/product.html', [
            'product' => $product
        ]);
    }

    /**
     * Restrict authorization to product owner only
     * 
     * @param object product
     * @return void
     */
    public function restrictToProductOwner($product)
    {
        $product_owner_id = $product->getOwnerId();

        if($product_owner_id != Auth::getUserId() && !Auth::isAdminAuthenticated()){
            $this->redirect("/products/{$product->product_id}");
        }
    }

    /**
     * Page to add product
     * 
     * @return void
     */
    public function addProductAction()
    {
        $this->requireTrader();

        $errors = [];
        $validated_data = [];

        $shops = Shop::getTraderShops(Auth::getUserId());
        $categories = ProductCategory::getAllCategories();

        if(count($shops) == 0){
            Extra::setMessageCookie("You must create at least one shop to add product.", Extra::COOKIE_MESSAGE_INFO);
            $this->redirect("/trader/add-shop/?next=/trader/add-product/");
        }

        if(!empty($_POST)){
            $product = new Models\Product($_POST);
            $validated_data = $product;
            
            $validation = new ProductValidation($_POST);
            $errors = $validation->getErrors($_FILES);

            if(empty($errors)){
                $product->save($_FILES);

                Extra::setMessageCookie("Product added successfully");

                $this->redirect('/');
            }
        }
        View::renderTemplate('Product/add-product.html', [
            'errors' => $errors,
            'valid_data' => $validated_data,
            'shops' => $shops,
            'categories'=> $categories
        ]);
    }

    /**
     * Manage products page
     * 
     * @return void
     */
    public function manageProductsAction()
    {
        $this->requireTrader();

        $rating = isset($_GET['rating']) ? $_GET['rating'] : "";
        $minPrice = isset($_GET['minPrice']) ? $_GET['minPrice'] : 0;
        $maxPrice = isset($_GET['maxPrice']) ? $_GET['maxPrice'] : 200;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        $products = Models\Product::getAllProductsByTrader(Auth::getUserId());
        $isVisitorOwner = true;

        View::renderTemplate('Product/manage-products.html', [
            'products' => $products,
            'isVisitorOwner' =>  $isVisitorOwner,
                'selected_rating' => $rating,
            'selected_minPrice' => $minPrice,
            'selected_maxPrice' => $maxPrice,
            'page' => $page
        ]);
    }

    /**
     * Products by trader
     * 
     * @return void
     */
    public function traderProductsAction()
    {
        $trader_id = $this->route_params['trader_id'];
        $trader = User::getUserObjectFromId($trader_id);

        $rating = isset($_GET['rating']) ? $_GET['rating'] : "";
        $minPrice = isset($_GET['minPrice']) ? $_GET['minPrice'] : 0;
        $maxPrice = isset($_GET['maxPrice']) ? $_GET['maxPrice'] : 200;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        $products = Models\Product::searchByTrader($trader_id, $rating, $minPrice, $maxPrice);

        $isVisitorOwner = Auth::getUserId() == $trader_id;

        View::renderTemplate('Product/manage-products.html', [
            'products' => $products,
            'isVisitorOwner' => $isVisitorOwner,
            'trader' => $trader,
            'selected_rating' => $rating,
            'selected_minPrice' => $minPrice,
            'selected_maxPrice' => $maxPrice,
            'page' => $page
        ]);
    }

    /**
     * Page to add product
     * 
     * @return void
     */
    public function editProductAction()
    {
        $product_id = $this->route_params['product_id'];
        $product = Models\Product::getProductObjectForFormById($product_id);

        $this->restrictToProductOwner($product);

        $errors = [];

        $shops = Shop::getTraderShops($product->getOwnerId());
        $categories = ProductCategory::getAllCategories();

        if(!empty($_POST)){
            $namesArray = [];
            foreach ($_POST as $key => $value) {
                array_push($namesArray, $key);
            }
            
            $validation = new ProductValidation($_POST, $namesArray);
            $errors = $validation->getNamedErrors();

            if(empty($errors)){
                $product->update($_POST);

                Extra::setMessageCookie("Product updated successfully");

                $this->redirect("/products/{$product->product_id}/");
            }

            $product = new Models\Product($_POST);
        }
        View::renderTemplate('Product/edit-product.html', [
            'product' => $product,
            'errors' => $errors,
            'shops' => $shops,
            'categories'=> $categories
        ]);
    }

    /**
     * Delete product data 
     * 
     * @return void
     */
    public function deleteProductAction()
    {
        $product_id = $this->route_params['product_id'];
        $product = Models\Product::getProductObjectForFormById($product_id);
        $nextRoute = isset($_GET['next']) ? "?next=" . $_GET['next'] : "/";

        $this->restrictToProductOwner($product);
        
        if(empty($_POST)){
            $this->redirect("/products/{$product_id}/");
        }

        if($product->delete()){
            Extra::setMessageCookie("Product deleted successfully");
            if(isset($_GET['next'])){
                $this->redirect($_GET['next']);
            }
            $this->redirect("/");
        }
        Extra::setMessageCookie("Could not delete product.", Extra::COOKIE_MESSAGE_FAIL);

        $this->redirect("/products/{{ $product_id }}/");
    }
}
