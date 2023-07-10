<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Products;
use App\Product_variant;
use App\System_unit;
use App\Purchaseorders;
use App\Purchaseorder_item;
use App\Stocks;
use App\Branch;
use App\Branch_user;
use App\Invoices;
use App\Invoice_item;
use App\Company;
use App\Paymenttypes;
use Validator;
use Carbon\Carbon;
use App\User;
use App\Product_tag;
use App\Tags;
use DB;
use Exception;
class BrandController extends Controller
{
	public function __construct(){
		$this->middleware('auth'); 
		/*$this->middleware(function ($request, $next) {
			if(Auth::user()->role!="1"){
				return redirect('/admin');
			}
			return $next($request);
		}); */

	}
	public function getDashboard(){
		$user = Auth::user();
		if($user->role=="2"){
			return redirect("/admin");
		}
		$products = Products::where("user_id",'=',$user->id)->where("status",'=',"1")->get();

		$productsArray = [];
		foreach($products as $product){
			array_push($productsArray, $product->id);
		}
		// foreach
		//Lastest Order
		$lastest = DB::select(DB::raw("SELECT product_id,quantity,price,created_at 
			FROM `invoice_item` 
			WHERE product_id IN (SELECT pv.id FROM products p JOIN product_variant pv ON pv.product_id = p.id WHERE p.user_id = '$user->id')
			ORDER BY created_at DESC LIMIT 10;"));
		//Top Product
		$topproducts = DB::select(DB::raw("SELECT product_id,SUM(quantity) as quantity
			FROM invoice_item
			GROUP BY product_id
			HAVING product_id IN (SELECT pv.id FROM products p JOIN product_variant pv ON pv.product_id = p.id WHERE p.user_id = '$user->id')
			ORDER BY quantity DESC LIMIT 10;"));

		//Graph Income
		$today_date = date('Y-m-d');
		$graphcount = 7;
		$graph = array();
		for($i=0;$i<$graphcount;$i++){
			$graph[$i]["date"] = date('Y-m-d', strtotime(((-1*$graphcount)+$i+1)." days"));
			$graph[$i]["income"] = 0;
		}
		//Income
		$current = Carbon::now();
		$startdate = $current->format('Y-m-01');
		$enddate = $current->format('Y-m-t');

		$startdate = $startdate." 00:00:00";
		$enddate = $enddate." 23:59:59";

		// $invoices = Invoices::with(['getItem' => function ($q) use ($productsArray){
		// 	$q->whereIn('product_id' , $productsArray);
		// }])
		
		$invoices = Invoices::with(['getItem'])
		->where('status', '=', 1)
		->where("created_at",'>=',$startdate)
		->where("created_at",'<=',$enddate)
		->get();
		
		$summ = 0;
		
		foreach($invoices as $invoice){
			if(!$invoice->getItem->isEmpty()){
				foreach($invoice->getItem as $item){
					$productdata = $this->getProductData($item->product_id);
					$product = $productdata->getProduct;
					if($product->user_id == Auth::user()->id){
						$summ += $item->suminput;
					}
				}
			}
		}
		$todayincome = DB::select(DB::raw("SELECT it.product_id as product_id,SUM(it.suminput) as summary ,i.created_at
			FROM invoice_item it JOIN invoices i ON it.invoice_id = i.id
			WHERE it.product_id IN (SELECT pv.id FROM products p JOIN product_variant pv ON pv.product_id = p.id WHERE p.user_id = '$user->id') AND i.created_at like CONCAT(CURDATE(),'%') AND i.status = '1'"));
		$todayincome = $todayincome[0]->summary;


		
		// $monthincome = DB::select(DB::raw("SELECT it.product_id as product_id,SUM(it.suminput) as summary ,i.created_at
		// 	FROM invoice_item it JOIN invoices i ON it.invoice_id = i.id
		// 	WHERE it.product_id IN (SELECT pv.id FROM products p JOIN product_variant pv ON pv.product_id = p.id WHERE p.user_id = '$user->id') AND MONTH(i.created_at) like MONTH(CURRENT_DATE()) AND i.status = '1';"));
		// $monthincome = $monthincome[0]->summary;
		$monthincome = $summ;

		$weeklyincome = DB::select(DB::raw("SELECT it.product_id,SUM(it.suminput) as summary ,i.created_at created_at,i.status
			FROM invoice_item it JOIN invoices i ON i.id = it.invoice_id
			GROUP BY DATE(it.created_at) , i.status
			HAVING it.product_id IN (SELECT pv.id FROM products p JOIN product_variant pv ON pv.product_id = p.id WHERE p.user_id = '$user->id') AND created_at >= CONCAT((CURDATE() - INTERVAL 7 DAY),'%') AND i.status = 1"));

		foreach($weeklyincome as $week){
			$time = date('Y-m-d', strtotime($week->created_at));
			for($i=0;$i<$graphcount;$i++){
				if($time==$graph[$i]["date"]){
					$graph[$i]["income"] = $week->summary;
				}
			}
		}
		return view('brand.dashboard',compact('products','todayincome','monthincome','graphcount','graph','lastest','topproducts'));
	}

//======================Start Product Manage=======================
	public function getProductMove(Request $request){
		$product_variant = Product_variant::find($request->id);

		if(isset($product_variant)){
			$user = Auth::user();
			if($user->role=="2"){
				$product = Products::where('id','=',$product_variant->product_id)->first();
			}else{
				$product = Products::where('user_id','=',Auth::user()->id)->where('id','=',$product_variant->product_id)->first();
			}
			
			if(isset($product)){
				return view('brand.product.move',compact('product','product_variant'));
			}else{
				$sysmessage = array(
					"msgcode" => "500",
					"msg" => "ไม่พบสินค้า"
				);
				return redirect('/products')->with('sysmessage',$sysmessage);;
			}

		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบสินค้า"
			);
			return redirect('/products')->with('sysmessage',$sysmessage);
		}
	}
	public function getProduct(Request $request){
		$user = Auth::user();
		if($user->role=="2"){
			$products = Products::where('status','=','1')->get();
			$users = User::where('role','=','1')->where('status','=','1')->orderBy('brand_name','asc')->get();
			if($request->brand_id==null){
				return view('brand.product.index',compact('users'));
			}else{
				$products = Products::where('user_id','=',$request->brand_id)->where('status','=','1')->get();
				return view('brand.product.index',compact('products','users'));
			}

		}else{
			$products = Products::where('user_id','=',$user->id)->where('status','=','1')->get();
			return view('brand.product.index',compact('products'));
		}

		
	}

	public function getAddProduct(){
		$units = System_unit::all();
		$tags = Tags::all();
		$users = User::where('role','=','1')->where('status','=',1)->orderBy('brand_name','ASC')->get();
		return view('brand.product.manage',compact('units','users','tags'));
	}
	public function getEditProduct(Request $request){
		$user = Auth::user();
		if($user->role=="2"){
			$product = Products::where("id",'=',$request->id)->first();
		}else{
			$product = Products::where("id",'=',$request->id)->where('user_id','=',$user->id)->first();
		}
		
		if(isset($product)){
			$units = System_unit::all();
			$users = User::where('role','=','1')->where('status','=',1)->orderBy('brand_name','ASC')->get();
			$tags = Tags::all();
			return view('brand.product.manage',compact('units','product','users','tags'));
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบสินค้า"
			);
			return redirect('/products')->with('sysmessage',$sysmessage);
		}
		
	}

	public function addProduct(Request $request){
		$validator = $this->validateManageProduct($request->all());
		if(sizeOf($validator->errors())==0){
			$user = Auth::user();
			$product = new Products;
			$product->name = $request->name;
			if($user->role=="1"){
				$product->user_id = Auth::user()->id;
			}else{
				$product->user_id = $request->user_id;
			}
			$product->discount_type = $request->discount_type;
			$product->discount_price = $request->discount_price;
			if($request->hasFile('picture')){
				$frontpath = $_ENV['FRONTPATH'];
				$extension = $request->picture->getClientOriginalExtension();
				if($extension=="php"){
					echo "denied";
					die();
				}
				$fileName = $this->generateRandomString().".".$extension;
				$request->picture->move($frontpath, $fileName);
				$product->pic_url = "/storage/".$fileName;
			}else{
				$product->pic_url = "/assets/system/nopic.png";
			}
			$product->price = $request->price;
			$product->unit_id = $request->unit_id;
			$product->status = "1";
			$product->description = $request->description;
			$product->save();

			$product_variants = $request->product_variants;
			if($product_variants!=null){
				for($i=0;$i<sizeOf($product_variants);$i++){
					$prodvariant = new Product_variant;
					$prodvariant->product_id = $product->id;
					$prodvariant->variant = $product_variants[$i];
					$prodvariant->barcode = $this->getNewBarcode();
					$prodvariant->save();
				}
			}else{
				$prodvariant = new Product_variant;
				$prodvariant->product_id = $product->id;
				$prodvariant->variant = "";
				$prodvariant->barcode = $request->barcode;
				$prodvariant->save();
			}
							$sizetag = sizeOf(explode(',',$request->tags));
				$intag = explode(',',$request->tags);
				if($sizetag!=0){
					for($i=0;$i<$sizetag;$i++){
						if($intag[$i]!=""){
							$tag = Tags::where('name','=',$intag[$i])->first();
							if(!isset($tag)){
								$tag = new Tags;
								$tag->name = $intag[$i];
								$tag->save();
							}
								$product_tag = new Product_tag;
								$product_tag->product_id = $product->id;
								$product_tag->tag_id = $tag->id;
								$product_tag->save();
						}
					}
				}

			$message = array(
				"msgcode" => "200",
				"msg" => "เพิ่มสินค้า ".$request->name."เรียบร้อยแล้ว"
			);
			return redirect('/products/get/'.$product->id)->with('sysmessage',$message);

		}else{
			$units = System_unit::all();
			$error = $validator->errors()->all();
			$message = "";
			for($i=0;$i<sizeOf($error);$i++){
				$message.= $error[$i];
			}
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => $message
			);
			return view('brand.product.manage',compact('sysmessage','units'));
		}

	}

	public function updateProduct(Request $request){
		
		$user = Auth::user();
		if($user->role==2){
			$product = Products::where("id",'=',$request->id)->first();
		}else{
			$product = Products::where("id",'=',$request->id)->where('user_id','=',$user->id)->first();
		}
		if(isset($product)){
			$validator = $this->validateManageProduct($request->all());
			if(sizeOf($validator->errors())==0){
				$product->name = $request->name;
				$product->discount_type = $request->discount_type;
				$product->discount_price = $request->discount_price;
				if($request->hasFile('picture')){
					$frontpath = $_ENV['FRONTPATH'];
					$extension = $request->picture->getClientOriginalExtension();
					if($extension=="php"){
						echo "denied";
						die();
					}
					$fileName = $this->generateRandomString().".".$extension;
					$request->picture->move($frontpath, $fileName);
					$product->pic_url = "/storage/".$fileName;
				}
				$product->price = $request->price;
				$product->unit_id = $request->unit_id;
				if($user->role=="2"){
					$product->user_id = $request->user_id;
				}
				$product->description = $request->description;
				$product->save();

				$old_variants = $request->old_variants;
				if($old_variants!=null){
					foreach($old_variants as $key=>$val){
						$prodvariant = Product_variant::find($key);
						$prodvariant->variant = $val;
						$prodvariant->barcode = $request->barcode;
						$prodvariant->save();
					}
				}

				$product_variants = $request->product_variants;
				if($product_variants!=null){
					for($i=0;$i<sizeOf($product_variants);$i++){
						$prodvariant = new Product_variant;
						$prodvariant->product_id = $product->id;
						$prodvariant->variant = $product_variants[$i];
						$prodvariant->barcode = $request->barcode;
						$prodvariant->save();
					}
				}

				Product_tag::where('product_id','=',$product->id)->delete();
				$sizetag = sizeOf(explode(',',$request->tags));
				$intag = explode(',',$request->tags);
				if($sizetag!=0){
					for($i=0;$i<$sizetag;$i++){
						if($intag[$i]!=""){
							$tag = Tags::where('name','=',$intag[$i])->first();
							if(!isset($tag)){
								$tag = new Tags;
								$tag->name = $intag[$i];
								$tag->save();
							}
								$product_tag = new Product_tag;
								$product_tag->product_id = $product->id;
								$product_tag->tag_id = $tag->id;
								$product_tag->save();
						}
					}
				}


				$message = array(
					"msgcode" => "200",
					"msg" => "อัพเดท ".$request->name."เรียบร้อยแล้ว"
				);
				return redirect('/products/get/'.$product->id)->with('sysmessage',$message);

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
				return redirect('/products/get/'.$product->id)->with('sysmessage',$sysmessage);
			}
		}else{
			$message = array(
				"msgcode" => "500",
				"msg" => "ไม่พบสินค้าสำหรับอัพเดทข้อมูล"
			);
			return redirect('/products')->with('sysmessage',$message);
		}
	}

	public function suspendProduct(Request $request){
		$user = Auth::user();
		if($user->role=="2"){
			$product = Products::find($request->id);
		}else{
			$product = Products::where("id",'=',$request->id)->where("user_id",'=',$user->id)->first();;
		}
		
		if(isset($product)){
			$product->status = "0";
			$product->save();
			$message = array(
				"msgcode" => "200",
				"msg" => "ระบบระงับการขาย ".$product->name."เรียบร้อยแล้ว"
			);
			return redirect('/products/get/'.$product->id)->with('sysmessage',$message);

		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบสินค้าสำหรับระงับการขาย"
			);
			return redirect('/products')->with('sysmessage',$sysmessage);
		}
	}

	public function unsuspendProduct(Request $request){
		$user = Auth::user();
		if($user->role=="2"){
			$product = Products::find($request->id);
		}else{
			$product = Products::where("id",'=',$request->id)->where("user_id",'=',$user->id)->first();;
		}
		if(isset($product)){
			$product->status = "1";
			$product->save();
			$message = array(
				"msgcode" => "200",
				"msg" => "ระบบยกเลิกระงับการขาย ".$product->name."เรียบร้อยแล้ว"
			);
			return redirect('/products/get/'.$product->id)->with('sysmessage',$message);

		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบสินค้าสำหรับระงับการขาย"
			);
			return redirect('/products')->with('sysmessage',$sysmessage);
		}
	}

	public function validateManageProduct(array $data){
		return Validator::make($data, [          
			'name' => 'required|string',
			'price' => 'required|numeric',
			'unit_id' => 'required|numeric'
		]);
	}
	public function getNewBarcode(){
		$barcode = rand(100000000,999999999);
		$product = Product_variant::where('barcode','=',$barcode)->first();
		while(isset($product)){
			$barcode = rand(100000000,999999999);
			$product = Product_variant::where('barcode','=',$barcode)->first();
		}
		return $barcode;
	}
	public function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
//======================End Product Manage=======================

//======================Start Purchase Manage=======================

	public function getPurchase(){
		$user = Auth::user();
		if($user->role=="2"){
			$purchases = Purchaseorders::where("status",'<>','9')->orderBy('id','desc')->get();
		}else{
			$purchases = Purchaseorders::where('user_id','=',$user->id)->where("status",'<>','9')->orderBy('id','desc')->get();
		}
		
		return view('brand.purchase.index',compact('purchases'));
	}

	public function getAddPurchase(){
		$users = User::where('role','=','1')->where('status','=','1')->orderBy('brand_name','ASC')->get();
		$companies = Company::all();
		$branchs = Branch::all();

		return view('brand.purchase.manage',compact('users','companies','branchs'));
	}
	public function showPurchase(Request $request){
		$user = Auth::user();
		if($user->role=="2"){
			$branchs = Branch::all();
			$purchase = Purchaseorders::where("id",'=',$request->id)->first();
		}else{
			$branchs = Branch_user::where("user_id",'=',$user->id)->get();
			$purchase = Purchaseorders::where("id",'=',$request->id)->where("user_id",'=',$user->id)->first();
		}
		
		
		if(isset($purchase)){
			$companies = Company::all();
			$users = User::where('role','=','1')->where('status','=','1')->orderBy('brand_name','ASC')->get();
			return view('brand.purchase.manage',compact('purchase','branchs','companies','users'));
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบใบนำเข้า"
			);
			return redirect('/purchase')->with('sysmessage',$sysmessage);;
		}
	}
	public function printBarcode(Request $request){

		$user = Auth::user();
		if($user->role=="2"){
			$purchase = Purchaseorders::where("id",'=',$request->id)->first();
		}else{
			$purchase = Purchaseorders::where("id",'=',$request->id)->where("user_id",'=',$user->id)->first();
		}
		if(isset($purchase)){
			$quantity = 0;
			$products = array();
			foreach($purchase->getItem as $item){
				array_push($products, array(
					"product_id" => $item->product_id,
					"quantity" => $item->quantity
				));
				$quantity += $item->quantity;
			}
			return view('brand.purchase.barcodeprint',compact('products','quantity'));
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบใบนำเข้า"
			);
			return redirect('/purchase')->with('sysmessage',$sysmessage);;
		}
	}
	public function printBarcodeCustom(Request $request){
		$productin = $request->products;
		$quantityin = $request->quantity;
		if(sizeOf($productin)!=0){
			$quantity = 0;
			$products = array();
			for($i=0;$i<sizeOf($productin);$i++){
				array_push($products, array(
					"product_id" => $productin[$i],
					"quantity" => $quantityin[$i]
				));
				$quantity +=  $quantityin[$i];
			}
			return view('brand.purchase.barcodeprint',compact('products','quantity'));
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบสินค้าสำหรับพิมพ์บาร์โค๊ด"
			);
			return redirect('/products/barcodeprint')->with('sysmessage',$sysmessage);;
		}
	}
	public function addPurchase(Request $request){
		$validator = $this->validateManagePurchase($request->all());
		if(sizeOf($validator->errors())==0){
			$purchase = new Purchaseorders;
			$purchase->user_id = $request->user_id;
			$purchase->company_id = $request->company_id;
			$purchase->shipdate = $request->shipdate;
			$purchase->branch_id = $request->branch_id;
			$purchase->admin_id = Auth::user()->id;
			$purchase->status = 0;
			$purchase->remark = $request->remark;
			$purchase->save();
			
			$po_product = $request->products;
			$po_quantity = $request->quantity;
			if($po_product!=null){
				for($i=0;$i<sizeOf($po_product);$i++){
					$poi = new Purchaseorder_item;
					$poi->purchaseorder_id = $purchase->id;
					$poi->product_id = $po_product[$i];
					$poi->quantity = $po_quantity[$i];
					$poi->save();
				}
			}

			$message = array(
				"msgcode" => "200",
				"msg" => "เพิ่มใบนำเข้าสินค้าเรียบร้อยแล้ว"
			);
			return redirect('/purchase/get/'.$purchase->id)->with('sysmessage',$message);

		}else{
			$user = Auth::user();
			$branchs = Branch_user::where("user_id",'=',$user->id)->get();
			$products = Products::where("user_id",'=',$user->id)->where('status','=','1')->get();
			$units = System_unit::all();
			$error = $validator->errors()->all();
			$message = "";
			for($i=0;$i<sizeOf($error);$i++){
				$message.= $error[$i];
			}
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => $message
			);
			return view('brand.purchase.manage',compact('sysmessage','units','branchs','products'));
		}

	}

	public function updatePurchase(Request $request){
		$user = Auth::user();
		if($user->role=="2"){
			$purchase = Purchaseorders::where('id','=',$request->id)->first();
		}else{
			$purchase = Purchaseorders::where('id','=',$request->id)->where('user_id','=',$user->id)->first();
		}
		
		if(isset($purchase)){
			$validator = $this->validateManagePurchase($request->all());
			if(sizeOf($validator->errors())==0){
				$purchase->shipdate = $request->shipdate;
				$purchase->branch_id = $request->branch_id;
				//$purchase->user_id = $request->user_id;
				$purchase->company_id = $request->company_id;
				$purchase->remark = $request->remark;
				$purchase->status = 0;
				$purchase->save();

				/*$quantity = $request->quantity;

				if($quantity!=null){
					foreach($quantity as $key=>$val){
						$poi = Purchaseorder_item::find($key);
						$poi->quantity = $val;
						$poi->save();
					}
				}*/
				Purchaseorder_item::where('purchaseorder_id','=',$purchase->id)->delete();
				$po_product = $request->products;
				$po_quantity = $request->quantity;
				if($po_product!=null){
					for($i=0;$i<sizeOf($po_product);$i++){
						$poi = new Purchaseorder_item;
						$poi->purchaseorder_id = $purchase->id;
						$poi->product_id = $po_product[$i];
						$poi->quantity = $po_quantity[$i];
						$poi->save();
					}
				}

				$message = array(
					"msgcode" => "200",
					"msg" => "อัพเดทใบนำเข้าเรียบร้อยแล้ว"
				);
				return redirect('/purchase/get/'.$purchase->id)->with('sysmessage',$message);

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
				return redirect('/purchase/get/'.$purchase->id)->with('sysmessage',$sysmessage);
			}
		}else{
			$message = array(
				"msgcode" => "500",
				"msg" => "ไม่พบใบนำเข้าสำหรับอัพเดทข้อมูล"
			);
			return redirect('/purchase')->with('sysmessage',$message);
		}
	}

	public function cancelPurchase(Request $request){
		$user = Auth::user();
		if($user->role==2){
			$purchase = Purchaseorders::where('id','=',$request->id)->first();
		}else{
			$purchase = Purchaseorders::where('id','=',$request->id)->where('user_id','=',$user->id)->first();
		}
		if(isset($purchase)){
			$purchase->status = "9";
			$purchase->save();
			$message = array(
				"msgcode" => "200",
				"msg" => "ยกเลิกใบนำเข้า ".$purchase->id." เรียบร้อยแล้ว"
			);
			return redirect('/purchase')->with('sysmessage',$message);

		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบใบนำเข้าสำหรับยกเลิก"
			);
			return redirect('/purchase')->with('sysmessage',$sysmessage);
		}
	}

	public static function getProductData($product_id){
		$product = Product_variant::find($product_id);
		return $product;
	}
	public function getPrintBarcode(){
		$user = Auth::user();
		if($user->role=="2"){
			$products = Products::where("status",'=','1')->get();
		}else{
			$products = Products::where('user_id','=',$user->id)->where("status",'=','1')->get();
		}
		
		return view('brand.product.barcode',compact('products'));
	}

	public function printPO(Request $request){
		$user = Auth::user();
		if($user->role==2){
			$purchase = Purchaseorders::where('id','=',$request->id)->first();
		}else{
			$purchase = Purchaseorders::where('id','=',$request->id)->where('user_id','=',$user->id)->first();
		}
		if(isset($purchase)){
			return view('brand.purchase.poprint',compact('purchase'));

		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบใบนำเข้าสำหรับพิมพ์"
			);
			return redirect('/purchase')->with('sysmessage',$sysmessage);
		}
	}

	public function printPONew(Request $request){
		$user = Auth::user();
		if($user->role==2){
			$purchase = Purchaseorders::where('id','=',$request->id)->first();
		}else{
			$purchase = Purchaseorders::where('id','=',$request->id)->where('user_id','=',$user->id)->first();
		}
		if(isset($purchase)){
			$gp = Branch_user::where('user_id','=',$purchase->user_id)->where('branch_id','=',$purchase->branch_id)->first();
			if(isset($gp)){
				$gp = $gp->gp;
			}else{
				$gp = 0;
			}
			return view('brand.purchase.poprintnew',compact('purchase','gp'));

		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบใบนำเข้าสำหรับพิมพ์"
			);
			return redirect('/purchase')->with('sysmessage',$sysmessage);
		}
	}



	public function validateManagePurchase(array $data){
		return Validator::make($data, [          
			'branch_id' => 'required|numeric',
			'shipdate' => 'required'
		]);
	}


//======================End Purchase Manage=======================

//======================Start Report Manage=======================
	public function getChooseReport(){
		$user = Auth::user();
		$branchs = Branch_user::where("user_id",'=',$user->id)->get();
		return view('brand.report.index',compact('branchs'));
	}
	public function getReport(Request $request){
		$startdate = $request->start_date;
		$enddate = $request->end_date;
		$branch_id = $request->branch_id;
		$user = Auth::user();
		if($startdate!=null&&$enddate!=null&&$branch_id!=null){
			$branch = Branch::find($branch_id);
			$gp = Branch_user::where('user_id','=',$user->id)->where('branch_id','=',$branch_id)->first();
			if(isset($gp)){
				$gp = $gp->gp;
			}else{
				$gp = 0;
			}
			$startdate = $startdate;
			$enddate = $enddate;
			if($startdate==$enddate){
				$invoices = Invoices::where("created_at",'like',$startdate.'%')->where('branch_id','=',$branch->id)->where("status",'=','1')->get();
			}else{
				$startdate = $startdate." 00:00:00";
				$enddate = $enddate." 23:59:59";
				$invoices = Invoices::where("created_at",'>=',$startdate)->where("created_at",'<=',$enddate)->where('branch_id','=',$branch->id)->where("status",'=','1')->get();
			}
			
			$reportsum = array();
			$reportquantity = array();
			$reportsuminput = array();
			$sumdiscount = 0;
			$discountpayment = array();
			$pmethods = Paymenttypes::all();
			$paymentincome = array();
			foreach($pmethods as $pment){
				$discountpayment[$pment->id] = 0;
				$paymentincome[$pment->id] = 0;
			}

			foreach($invoices as $invoice){
				foreach($invoice->getItem as $item){
					try{
						$reportsum[$item->product_id] += $item->price*$item->quantity;
						$reportquantity[$item->product_id] += $item->quantity;
						$reportsuminput[$item->product_id] += $item->suminput;
						if($invoice->paymenttype_id==9){
							$paymentincome[$invoice->paymenttype_id]  += $item->price*$item->quantity;
						}else{
							$paymentincome[$invoice->paymenttype_id]  += $item->suminput;
						}
						

					}catch(Exception $e){
						$reportsum[$item->product_id] = $item->price*$item->quantity;
						$reportquantity[$item->product_id] = $item->quantity;
						$reportsuminput[$item->product_id] = $item->suminput;
						if($invoice->paymenttype_id==9){
							$paymentincome[$invoice->paymenttype_id]  += $item->price*$item->quantity;
						}else{
							$paymentincome[$invoice->paymenttype_id]  += $item->suminput;
						}
					}
					
				}
				foreach($invoice->getPromotion as $promo){
					$sumdiscount += $promo->discount;
					$discountpayment[$invoice->paymenttype_id] += $promo->discount;
				}
			}
			
			return view('brand.report.report',compact('reportsum', 'gp','reportquantity','startdate','enddate','branch','pmethods','sumdiscount','discountpayment','reportsuminput','paymentincome', 'user'));
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "เลือกวันที่เพื่อดูยอดขาย"
			);
			return redirect('/report')->with('sysmessage',$sysmessage);
		}


	}
//======================End Report Manage=========================
	//======================Stock Report=========================
	public function getStock(){
		$user = Auth::user();
		$products = Products::where('user_id','=',$user->id)->where('status','=','1')->get();
		$branchs = Branch_user::where('user_id','=',$user->id)->get();
		return view('brand.stock.index',compact('products','branchs'));
	}
	public static function getOnhand($variant,$branchs){
		$result = "";
		foreach($branchs as $branch){
			$stock = Stocks::where("product_id",'=',$variant->id)->where("branch_id",'=',$branch->branch_id)->orderBy('id','desc')->first();
			if(isset($stock)){
				$result .= $branch->getBranch->name.": ".$stock->sum." ".$variant->getProduct->getUnit->name."<br>";
			}else{
				$result .= $branch->getBranch->name.": "."0 ".$variant->getProduct->getUnit->name."<br>";
			}
		}
		
		return $result;
	}

//======================End Stock Report=========================
	
	public function apiGetproduct(Request $request){

			$products = Products::where('name','like','%'.$request->term.'%')->where('status','=','1')->where('user_id','=',Auth::user()->id)->get();
		
		
		$count = 0;
		foreach ($products as $product) {
			foreach($product->getVariant as $key=>$variant){
				$list[$count]['id'] = $variant->id;
				if($request->mode!=null){
					$list[$count]['text'] = $product->name.' ('.$product->price.' บาท)'; 
				}else{
					$list[$count]['text'] = $product->name.' ('.$variant->variant.')'.' ('.$product->price.' บาท)'.' (แบรนด์: '.$product->getUser->brand_name.')'; 
				}
				
				$count++;
			}
		}

		return response()->json($list);
	}
}






