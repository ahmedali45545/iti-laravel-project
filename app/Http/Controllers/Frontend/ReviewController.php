<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Api\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    use ApiResponseTrait;

    public function add($slug)
    {
        $product = Product::where('slug',$slug)->first();
        //replace 2 with Auth::id()
        if($product){
            $prod_id = $product->id;
            $review = Review::where('user_id','1')->where('product_id',$prod_id)->first();
            if($review)
            {
                return $this->apiResponse('already has review you can edit it','DONE', 200);
            }else{
                $verified_purchase = Order::where('orders.user_id','1')
                ->join('order_items','orders.id','order_items.order_id')
                ->where('order_items.product_id',$prod_id)->get();
                return $this->apiResponse([$product,$verified_purchase],'DONE', 200);
            }
        }else{
            return $this->apiResponse(null,'Page not found', 404);
        }
    }

    public function store(Request $request){
        $prod_id = $request->input('product_id');
        $product = Product::where('id',$prod_id)->first();
        if($product){
            $user_review = $request->input('user_review');
            $new_review = Review::create(
                [
                    'user_id'=>'1',
                    'product_id'=>$prod_id,
                    'user_review'=>$user_review
                ]);
                if($new_review){
                    return $this->apiResponse($user_review,'DONE', 202);
                }
        }
    }

    public function edit($slug)
    {
        $product = Product::where('slug',$slug)->where('status','1')->first();
        if($product)
        {
            $prod_id = $product->id;
            $review  = Review::where('user_id','2')->where('product_id',$prod_id)->first();
            if($review)
            {
                return $this->apiResponse($review,'DONE', 200);
            }else{
                return $this->apiResponse(null,'Page not found', 404);
            }
        }
        else{
            return $this->apiResponse(null,'Page not found', 404);
        }
    }

    public function update(Request $request)
    {
        $user_review = $request->input('user_review');
        if($user_review !=''){
            $review_id = $request->input('review_id');
            $review = Review::where('id',$review_id)->where('user_id','2')->first();
            if($review){
                $review->user_review = $request->input('user_review');
                $review->update();
                return $this->apiResponse($review,'DONE', 202);
            }  else{
                return $this->apiResponse(null,'Page not found', 404);
            }
        }  else{
            return $this->apiResponse(null,'Empty review', 401);
        }
    }


}
