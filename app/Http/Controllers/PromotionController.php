<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Brandpromotion;
use App\Products;
use App\Product_variant;
use App\Branch;
use App\Promotion_auto;
use App\Promotionauto_product;
use App\Auto_branch;
use App\Promotions;
use App\User;
use Auth;
use Validator;
use DateTime;

class PromotionController extends Controller
{
	public function __construct(){
		$this->middleware('auth'); 
		$this->middleware(function ($request, $next) {
			if(Auth::user()->role!="2"){
				return redirect('/');
			}
			return $next($request);
		}); 

	}
	public function getManagePromotion(){
		$promotions = Brandpromotion::all();
		return view('admin.promotion.index',compact('promotions'));
	}
	public function printDiscountprice(Request $request){
		$type = "single";
		$product = Promotionauto_product::find($request->id);
		$productdata = $product->getProductVariant;
		$productdata = $productdata->getProduct;
		return view('admin.promotion.print',compact('product','productdata','type'));
	}

	public function printDiscountpriceGroup(Request $request){
		$type = "multi";
		$products = Promotion_auto::find($request->id);
		$products = $products->getProduct;
		return view('admin.promotion.print',compact('products','type'));
	}

	public function printDiscountpriceGroupDate(Request $request){
		$type = "multi";
				if($request->branch_id==0){
			$promotions = Promotion_auto::whereBetween('startdate', [$request->start_date, $request->end_date])->orderBy('id','desc')->get();
		}else{
			$autobranch = Auto_branch::where('branch_id','=',$request->branch_id)->get();
			$branchsid = array();
			foreach($autobranch as $br){
				array_push($branchsid,$br->promotion_id);
			}
			
			$promotions = Promotion_auto::whereBetween('startdate', [$request->start_date, $request->end_date])->orderBy('id','desc')->whereIn('id', $branchsid)->get();

		}
		$promoid = array();
		foreach($promotions as $res){
			array_push($promoid,$res->id);
		}
		$products = Promotionauto_product::whereIn('promotionauto_id',$promoid)->get();

		return view('admin.promotion.print',compact('products','type'));
	}
	public function managePromotion(Request $request){
		$promotion = Brandpromotion::find($request->id);
		if(isset($promotion)){
			$type = $promotion->type;

			switch ($type) {
				case 'discountprice':
				$targetproducts = Products::where('discount_type','=','1')->get();
				return view('admin.promotion.discountprice',compact('targetproducts'));
				break;
				
				default:
				$message = array(
					"msgcode" => "500",
					"msg" => "ไม่พบรูปแบบโปรโมชั่น"
				);
				return redirect('/admin/promotions')->with('sysmessage',$message);
				break;
			}


		}else{
			$message = array(
				"msgcode" => "500",
				"msg" => "ไม่พบรูปแบบโปรโมชั่น"
			);
			return redirect('/admin/promotions')->with('sysmessage',$message);
		}
	}

	public function apiGetproductDiscountprice(Request $request){
		$products = Products::where('name','like','%'.$request->term.'%')->where('discount_type','<>','1')->get();
		$count = 0;
		foreach ($products as $product) {
			foreach($product->getVariant as $key=>$variant){
				$list[$count]['id'] = $variant->id;
				$list[$count]['text'] = $product->name.' ('.$variant->variant.')'.' ('.$product->price.' บาท)'.' (แบรนด์: '.$product->getUser->brand_name.')'; 
				$count++;
			}
		}

		return response()->json($list);
	}

	public function apiGetproductNotification(Request $request){
		$products = Products::where('name','like','%'.$request->term.'%')->get();
		$count = 0;
		foreach ($products as $product) {
			foreach($product->getVariant as $key=>$variant){
				$list[$count]['id'] = $variant->id;
				$list[$count]['text'] = $product->name.' ('.$variant->variant.')'.' ('.$product->price.' บาท)'.' (แบรนด์: '.$product->getUser->brand_name.')'; 
				$count++;
			}
		}

		return response()->json($list);
	}

	public function addDiscountprice(Request $request){
		$products = $request->products;
		for($i=0;$i<sizeOf($products);$i++){
			$variant = Product_variant::find($products[$i]);
			if(isset($variant)){
				$product = Products::find($variant->product_id);
				if(isset($product)){
					$product->discount_type = 1;
					$product->discount_price = $request->price;
					$product->save();
				}
			}
		}
		$message = array(
			"msgcode" => "200",
			"msg" => "ปรับปรุงโปรโมชั่นเรียบร้อยแล้ว"
		);
		return redirect('/admin/promotions/discountprice')->with('sysmessage',$message);
	}
	public function deleteDiscountprice(Request $request){
		$product = Products::find($request->id);
		if(isset($product)){
			$product->discount_type = 0;
			$product->discount_price = 0;
			$product->save();
			$message = array(
				"msgcode" => "200",
				"msg" => "ปรับปรุงโปรโมชั่นเรียบร้อยแล้ว"
			);
			return redirect('/admin/promotions/discountprice')->with('sysmessage',$message);
		}else{
			$message = array(
				"msgcode" => "500",
				"msg" => "ไม่พบสินค้า"
			);
			return redirect('/admin/promotions/discountprice')->with('sysmessage',$message);
		}
	}
	public function getDiscountprice(){
		$targetproducts = Products::where('discount_type','=','1')->get();
		return view('admin.promotion.discountprice',compact('targetproducts'));
	}



	public function getAllPromotion(){
		$branchs = Branch::all();
		return view('admin.promotion.index',compact('branchs'));
	}

	public function getPromotionSpecific(Request $request){
		$branchs = Branch::all();
		if($request->branch_id==0){
			$promotions = Promotion_auto::whereBetween('startdate', [$request->start_date, $request->end_date])->orderBy('id','desc')->get();
		}else{
			$autobranch = Auto_branch::where('branch_id','=',$request->branch_id)->get();
			$branchsid = array();
			foreach($autobranch as $br){
				array_push($branchsid,$br->promotion_id);
			}
			
			$promotions = Promotion_auto::whereBetween('startdate', [$request->start_date, $request->end_date])->orderBy('id','desc')->whereIn('id', $branchsid)->get();

		}

		return view('admin.promotion.index',compact('promotions','branchs'));
	}

	public function getAddPromotion(){
		$branchs = Branch::all();
		$promotiontypes = Promotions::all();
		return view('admin.promotion.manage',compact('branchs','promotiontypes'));
	}



	public function addPromotion(Request $request){
		$validator = $this->validatePromotion($request->all());
		$branchin = $request->branch;

		if(sizeOf($validator->errors())==0){
			$user = Auth::user();
			$promotion = new Promotion_auto;
			$promotion->description = $request->description;
			$promotion->startdate = $request->startdate;
			$promotion->enddate = $request->enddate;
			$promotion->status = 1;
			$promotion->admin_id = $user->id;
			$promotion->save();
			for($i=0;$i<sizeOf($branchin);$i++){
				$notibranch = new Auto_branch;
				$notibranch->promotion_id = $promotion->id;
				$notibranch->branch_id = $branchin[$i];
				$notibranch->save();
			}

			$sysmessage = array(
				"msgcode" => "200",
				"msg" => "เพิ่มโปรโมชั่นเรียบร้อยแล้ว"
			);
			return redirect('/admin/promotions/get/'.$promotion->id)->with('sysmessage',$sysmessage);
		}else{
			$error = $validator->errors()->all();
			$message = "";
			for($i=0;$i<sizeOf($error);$i++){
				$message.= $error[$i];
			}
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => $message
			);
			return redirect('/admin/promotions/create')->with('sysmessage',$sysmessage);

		}
	}

	public function getPromotion(Request $request){
		$promotion = Promotion_auto::find($request->id);
		if(isset($promotion)){
			$branchinuse = array();
			foreach($promotion->getBranch as $branch){
				array_push($branchinuse, $branch->branch_id);
			}
			$branchs = Branch::all();
			$brands = User::where('role','=','1')->where('status','=','1')->orderBy('brand_name','asc')->get();
			$promotiontypes = Promotions::all();
			return view('admin.promotion.manage',compact('branchs','promotion','brands','branchinuse','promotiontypes'));
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบรายการที่จะแก้ไข"
			);
			return redirect('/admin/promotions')->with('sysmessage',$sysmessage);
		}
	}


	public function addProducttoPromotion(Request $request){
		$promotion = Promotion_auto::find($request->id);
		if(isset($promotion)){
			if($request->type=="product"){
				$products = $request->products;
				if($products==null){
					$sysmessage = array(
						"msgcode" => "500",
						"msg" => "กรุณาเลือกสินค้า"
					);
					return redirect('/admin/promotions/get/'.$promotion->id)->with('sysmessage',$sysmessage);
				}else{
					for($i=0;$i<sizeOf($products);$i++){
						$variant = Product_variant::find($products[$i]);
						if(isset($variant)){
							$promoproduct = new Promotionauto_product;
							$promoproduct->promotionauto_id = $promotion->id;
							$promoproduct->product_id = $variant->id;
							$promoproduct->price = $variant->getProduct->price;
							$promoproduct->save();
						}
					}
				}

			}else{
				$products = Products::where('user_id','=',$request->brand_id)->where('status','=','1')->get();
				foreach($products as $product){
					$variants = Product_variant::where('product_id','=',$product->id)->get();
					foreach($variants as $variant){
						$promoproduct = new Promotionauto_product;
						$promoproduct->promotionauto_id = $promotion->id;
						$promoproduct->product_id = $variant->id;
						$promoproduct->price = $variant->getProduct->price;
						$promoproduct->save();
					}
				}
			}

			$sysmessage = array(
				"msgcode" => "200",
				"msg" => "ปรับปรุงโปรโมชั่นเรียบร้อยแล้ว"
			);
			return redirect('/admin/promotions/get/'.$promotion->id)->with('sysmessage',$sysmessage);
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบรายการที่จะแก้ไข"
			);
			return redirect('/admin/promotions')->with('sysmessage',$sysmessage);
		}
	}

	public function updatePromotion(Request $request){

		$validator = $this->validatePromotion($request->all());
		if(sizeOf($validator->errors())==0){
			$promotion = Promotion_auto::find($request->id);
			$branchin = $request->branch;
			if(isset($promotion)){
				$user = Auth::user();
				$promotion->description = $request->description;
				$promotion->startdate = $request->startdate;
				$promotion->enddate = $request->enddate;
				$promotion->status = 1;
				$promotion->admin_id = $user->id;
				$promotion->save();

				Auto_branch::where('promotion_id','=',$promotion->id)->delete();
				for($i=0;$i<sizeOf($branchin);$i++){
					$notibranch = new Auto_branch;
					$notibranch->promotion_id = $promotion->id;
					$notibranch->branch_id = $branchin[$i];
					$notibranch->save();
				}
				$sysmessage = array(
					"msgcode" => "200",
					"msg" => "ปรับปรุงโปรโมชั่นเรียบร้อยแล้ว"
				);
				return redirect('/admin/promotions/get/'.$promotion->id)->with('sysmessage',$sysmessage);
			}else{
				$sysmessage = array(
					"msgcode" => "500",
					"msg" => "ไม่พบโปรโมชั่นสำหรับอัพเดท"
				);
				return redirect('/admin/promotions/')->with('sysmessage',$sysmessage);
			}
		}else{
			$error = $validator->errors()->all();
			$message = "";
			for($i=0;$i<sizeOf($error);$i++){
				$message.= $error[$i];
			}
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => $message
			);
			return redirect('/admin/promotions/create')->with('sysmessage',$sysmessage);

		}
	}

	public function removeProductfromPromotion(Request $request){
		$promotion = Promotion_auto::find($request->promotion_id);
		Promotionauto_product::where('product_id','=',$request->product_id)->where('promotionauto_id','=',$promotion->id)->delete();
		$sysmessage = array(
			"msgcode" => "200",
			"msg" => "ปรับปรุงโปรโมชั่นเรียบร้อยแล้ว"
		);
		return redirect('/admin/promotions/get/'.$promotion->id)->with('sysmessage',$sysmessage);
	}

	public function removePromotion(Request $request){
		$promotion = Promotion_auto::find($request->id);
		if(isset($promotion)){
			Promotionauto_product::where('promotionauto_id','=',$promotion->id)->delete();
			Auto_branch::where('promotion_id','=',$promotion->id)->delete();
			$promotion->delete();
			$sysmessage = array(
				"msgcode" => "200",
				"msg" => "ลบโปรโมชั่นเรียบร้อยแล้ว"
			);
			return redirect('/admin/promotions')->with('sysmessage',$sysmessage);
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบโปรโมชั่นที่ต้องการลบ"
			);
			return redirect('/admin/promotions')->with('sysmessage',$sysmessage);
		}
	}
	public function updatePrice(Request $request){
		$product = Promotionauto_product::find($request->id);
		if(isset($product)){
			$product->price = $request->price;
			$product->save();
			$sysmessage = array(
				"msgcode" => "200",
				"msg" => "ปรับปรุงราคาเรียบร้อยแล้ว"
			);
			return redirect('/admin/promotions/get/'.$product->promotionauto_id."#listable")->with('sysmessage',$sysmessage);
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบโปรโมชั่นที่ต้องการแก้ไข"
			);
			return redirect('/admin/promotions')->with('sysmessage',$sysmessage);
		}
	}

	public function posCheckPromotion(Request $request){
		$promotion_products = Promotionauto_product::where('product_id','=',$request->product_id)
		->orderby('created_at', 'desc')
		->get();
		$productname = "";
		$product_variant = Product_variant::find($request->product_id);
		if(isset($product_variant)){
			$productname = $product_variant->getProduct->name;
		}
		$message = "";
		$today = new DateTime();
		foreach($promotion_products as $product){
			$promotion = $product->getPromotion;
			$branchcheck = $promotion->getBranch;
			$tmp =array();
			foreach($branchcheck as $check){
				array_push($tmp, $check->branch_id);
			}
			if(in_array($request->branch_id, $tmp)){
				$startdate = new DateTime($promotion->startdate);
				$enddate = new DateTime($promotion->enddate);
				if($startdate > $today){
					continue;
				}
				if($enddate < $today){
					continue;
				}
				$message .= $promotion->description."<br>";
				break;
			}

		}
		if($message==""){
			$sysmessage = array(
				"msgcode" => "404",
				"msg" => "ไม่มีโปรโมชั่น"
			);
		}else{
			$sysmessage = array(
				"msgcode" => "200",
				"msg" => "สินค้ามีโปรโมชั่นอัตโนมัติ: ".$promotion->description."<br>สินค้า: ".$productname."<br>ราคาปกติ: ".$product_variant->getProduct->price."<br>ลดเหลือ: ".$product->price,
				"promoprice" => $product->price
			);
		}
		echo json_encode($sysmessage);
	}

	public function validatePromotion(array $data){
		return Validator::make($data, [          
			'startdate' => 'required',
			'enddate' => 'required',
			'description' => 'required'
		]);
	}

}
