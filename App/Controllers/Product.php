<?php

namespace App\Controllers;

use App\Auth;
use App\Extra;
use App\Models;
use App\Models\ProductCategory;
use App\Models\Shop;
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
     * Before filter - called before an action method.
     *
     * @return void
     */
    protected function before()
    {
        $this->requireTrader();
    }

    /**
     * Page to add product
     * 
     * @return void
     */
    public function addProduct()
    {
        $errors = [];
        $validated_data = [];

        $shops = Shop::getTraderShops(Auth::getUserId());
        $categories = ProductCategory::getAllCategories();

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

}
