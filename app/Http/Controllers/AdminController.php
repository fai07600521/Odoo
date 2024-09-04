<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Help;
use App\Products;
use App\Product_variant;
use App\System_unit;
use App\Purchaseorders;
use App\Purchaseorder_item;
use App\Paymenttypes;
use App\Stocks;
use App\Branch;
use App\Branch_user;
use App\Invoices;
use App\Invoice_item;
use App\Invoice_promotion;
use App\Members;
use App\Stock_adjustment;
use App\Stockadj_item;
use App\Promotions;
use App\Startmoney;
use App\Stock_transfer;
use App\Stocktransfer_item;
use App\Promotion_notification;
use App\Promotion_product;
use App\Notification_branch;
use Validator;
use Carbon\Carbon;
use DB;
use Exception;
use DateTime;
class AdminController extends Controller
{	
	protected $user;

	public function __construct(){
		$this->middleware('auth'); 
		$this->middleware(function ($request, $next) {
			if(Auth::user()->role!="2"){
				return redirect('/');
			}
			return $next($request);
		}); 

	}
	public static function getDashboardIncome($branch_id){
		// $todaydiscount = DB::select(DB::raw("SELECT SUM(ip.discount) as discount
		// 	FROM invoices i JOIN invoice_promotion ip ON ip.invoice_id = i.id
		// 	WHERE i.branch_id = '$branch_id' AND i.status = '1' AND i.created_at LIKE CONCAT(CURDATE(),'%')"));

		$todayincome = DB::select(DB::raw("SELECT it.product_id as product_id,SUM(it.suminput) as summary ,i.created_at
			FROM invoice_item it JOIN invoices i ON it.invoice_id = i.id
			WHERE i.branch_id = '$branch_id' AND it.product_id IN (SELECT pv.id FROM products p JOIN product_variant pv ON pv.product_id = p.id) AND i.created_at like CONCAT(CURDATE(),'%') AND i.status = '1'"));
		// $todayincome = ($todayincome[0]->summary)-$todaydiscount[0]->discount;
		$todayincome = ($todayincome[0]->summary);

		// $monthdiscount = DB::select(DB::raw("SELECT SUM(ip.discount) as discount
		// 	FROM invoices i JOIN invoice_promotion ip ON ip.invoice_id = i.id
		// 	WHERE i.branch_id = '$branch_id' AND i.status = '1' AND MONTH(i.created_at) like MONTH(CURRENT_DATE())"));

		$monthincome = DB::select(DB::raw("SELECT it.product_id as product_id,SUM(it.suminput) as summary ,i.created_at
			FROM invoice_item it JOIN invoices i ON it.invoice_id = i.id
			WHERE i.branch_id = '$branch_id' AND it.product_id IN (SELECT pv.id FROM products p JOIN product_variant pv ON pv.product_id = p.id) AND MONTH(i.created_at) like MONTH(CURRENT_DATE()) AND YEAR(i.created_at) like YEAR(CURRENT_DATE()) AND i.status = '1';"));
		// $monthincome = ($monthincome[0]->summary)-$monthdiscount[0]->discount;
		$monthincome = ($monthincome[0]->summary);

		return array(
			"today" => $todayincome,
			"month" => $monthincome
		);
	}
	public function getDashboard(){

		$user = Auth::user();
		if($user->role!="2"){
			echo "Permission Denied!";
			die();
		}
		$products = Products::where("status",'=',"1")->get();
		//Lastest Order
		$lastest = DB::select(DB::raw("SELECT product_id,quantity,price,created_at 
			FROM `invoice_item` 
			WHERE product_id IN (SELECT pv.id FROM products p JOIN product_variant pv ON pv.product_id = p.id)
			ORDER BY created_at DESC LIMIT 10;"));
		//Top Product
		$topproducts = DB::select(DB::raw("SELECT product_id,SUM(quantity) as quantity
			FROM invoice_item
			GROUP BY product_id
			HAVING product_id IN (SELECT pv.id FROM products p JOIN product_variant pv ON pv.product_id = p.id)
			ORDER BY quantity DESC LIMIT 10;"));

		//Graph Income
		/*$today_date = date('Y-m-d');
		$graphcount = 7;
		$graph = array();
		for($i=0;$i<$graphcount;$i++){
			$graph[$i]["date"] = date('Y-m-d', strtotime(((-1*$graphcount)+$i+1)." days"));
			$graph[$i]["income"] = 0;
		}*/
		//Income

		/*$weeklyincome = DB::select(DB::raw("SELECT it.product_id,SUM(it.suminput) as summary ,i.created_at created_at,i.status
			FROM invoice_item it JOIN invoices i ON i.id = it.invoice_id
			GROUP BY DATE(it.created_at) , i.status
			HAVING it.product_id IN (SELECT pv.id FROM products p JOIN product_variant pv ON pv.product_id = p.id ) AND created_at >= CONCAT((CURDATE() - INTERVAL 7 DAY),'%') AND i.status = 1"));

		foreach($weeklyincome as $week){
			$time = date('Y-m-d', strtotime($week->created_at));
			for($i=0;$i<$graphcount;$i++){
				if($time==$graph[$i]["date"]){
					$graph[$i]["income"] = $week->summary;
				}
			}
		}*/
		//return view('admin.dashboard',compact('products','todayincome','monthincome','graphcount','graph','lastest','topproducts'));
		$branchs = Branch::all();
		return view('admin.dashboard',compact('products','lastest','topproducts','branchs'));
	}

	public function getBrandSales(Request $request){
		$startdate = $request->startdate;
		$enddate = $request->enddate;
		$branch_id = $request->branch_id;
		$brands = User::where('role','=','1')->where('status','=','1')->orderBy('name','asc')->get();
		$branchs = Branch::all();
		if($startdate!=null&&$enddate!=null){
			$startdate = $startdate;
			$enddate = $enddate;
			if($startdate==$enddate){
				$invoices = Invoices::where("created_at",'like',$startdate.'%')->where('branch_id','=',$branch_id)->where("status",'=','1')->get();
			}else{
				$startdate = $startdate." 00:00:00";
				$enddate = $enddate." 23:59:59";
				$invoices = Invoices::where("created_at",'>=',$startdate)->where("created_at",'<=',$enddate)->where('branch_id','=',$branch_id)->where("status",'=','1')->get();
			}
			$branchname = Branch::find($branch_id);
			$branchname = $branchname->name;
			$report = array();
			$discountreport = array();
			foreach($brands as $brand){
				$report[$brand->id] = 0;
				$discountreport[$brand->id] = 0;
				$gp[$brand->id] = 0;
			}
			$gps = Branch_user::where('branch_id','=',$branch_id)->get();
			foreach($gps as $res){
				$gp[$res->user_id] = $res->gp;
			}
			foreach($invoices as $invoice){
				foreach($invoice->getItem as $item){
					try{
					$productdata = Product_variant::find($item->product_id);
					$report[$productdata->getProduct->user_id] += $item->quantity*$item->price;
					$discountreport[$productdata->getProduct->user_id] += ($item->quantity*$item->price)-$item->suminput; 
					}catch(Exception $e){
						$productdata = Product_variant::find($item->product_id);
					$report[$productdata->getProduct->user_id] = $item->quantity*$item->price;
					$discountreport[$productdata->getProduct->user_id] = ($item->quantity*$item->price)-$item->suminput; 

					}
				}
			}
			
			return view('admin.order.brandsales',compact('brands','report','startdate','enddate','discountreport','branchs','branchname','branch_id','gp'));
		}else{
			return view('admin.order.brandsales',compact('brands','branchs'));
		}

		
	}

//======================Start Order Manage=======================
	public function getChooseOrder(){
		$branchs = Branch::all();
		return view('admin.order.choose',compact('branchs'));
	}
	public function getAllOrder(Request $request){
		$branch_id = $request->branch_id;
		$startdate = $request->start_date;
		$enddate = $request->end_date;
		$startdate = $startdate." 00:00:00";
		$enddate = $enddate." 23:59:59";
		if($branch_id!=null||$branch_id!=0){
			$invoices = Invoices::where("branch_id",'=',$branch_id)->where("created_at",'>=',$startdate)->where("created_at",'<=',$enddate)->orderBy('id','desc')->get();
		}else{
			$invoices = Invoices::all();
		}
		return view('admin.order.index',compact('invoices'));
	}
	public function getOrder(Request $request){
		$invoice = Invoices::find($request->id);
		if(isset($invoice)){
			return view('admin.order.manage',compact('invoice'));
		}else{
			$message = array(
				"msgcode" => "500",
				"msg" => "ไม่พบใบออเดอร์ดังกล่าว"
			);
			return redirect('/admin/order')->with('sysmessage',$message);
		}

	}
	public static function getSumOrder($id){
		$invoice = Invoices::find($id);
		$sum = 0;
		foreach($invoice->getItem as $item){
			$sum += $item->price*$item->quantity;
		}
		return $sum;
	}
	public function voidOrder(Request $request){
		$invoice = Invoices::find($request->id);
		$remark = $request->remark;
		if(isset($invoice)){
			if($remark!=null){
				$invoice->status = 9;
				$invoice->remark = $remark;
				$invoice->save();
				foreach($invoice->getItem as $item){
					$laststock = Stocks::where("branch_id",'=',$invoice->branch_id)->where("product_id",'=',$item->product_id)->orderBy('id','desc')->first();
					$lastsum = 0;
					if(isset($laststock)){
						$lastsum = $laststock->sum;
					}

					$stock = new Stocks;
					$stock->product_id = $item->product_id;
					$stock->branch_id = $invoice->branch_id;
					$stock->type = "void";
					$stock->quantity = $item->quantity;
					$stock->sum = $lastsum+$item->quantity;
					$stock->remark = "ยกเลิกใบสั่งซื้อ#".$invoice->id;
					$stock->save();
				}
				$message = array(
					"msgcode" => "200",
					"msg" => "ทำการ Void บิลเรียบร้อยแล้ว"
				);
				return redirect('/admin/order/get/'.$invoice->id)->with('sysmessage',$message);

			}else{
				$message = array(
					"msgcode" => "500",
					"msg" => "กรุณาระบุเหตุผลสำหรับการ Void บิล"
				);
				return redirect('/admin/order/get/'.$invoice->id)->with('sysmessage',$message);
			}
		}else{
			$message = array(
				"msgcode" => "500",
				"msg" => "ไม่พบใบออเดอร์ดังกล่าว"
			);
			return redirect('/admin/order')->with('sysmessage',$message);
		}
		
		
	}

//======================End Order Manage=======================	

//======================Start Brand Manage=======================
	public function getBrand(){
		$brands = User::where('role','=','1')->where('status','=','1')->get();
		return view('admin.brand.index',compact('brands'));
	}
	public function getAddBrand(){
		return view('admin.brand.manage');
	}
	public function addBrand(Request $request){
		$validator = $this->validateManageBrand($request->all());
		$tmpuser = User::where("email",'=',$request->email)->first();
		if($tmpuser!=null){
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "อีเมลล์นี้มีใช้แล้วในระบบกรุณาใช้อีเมลล์อื่น"
			);
			return view('admin.brand.manage',compact('sysmessage'));
		}
		if(sizeOf($validator->errors())==0){
			$user = new User;
			$user->name = $request->name;
			$user->email = $request->email;
			$user->password = Hash::make($request->password);
			$user->role = "1";
			$user->line = $request->line;
			$user->branch = $request->branch;
			$user->tax_id = $request->tax_id;
			$user->address = $request->address;
			$user->brand_name = $request->brand_name;
			$user->status = "1";
			if($request->vat!=null){
				$user->vat = "1";
			}else{
				$user->vat = "0";
			}
			$user->save();
			$message = array(
				"msgcode" => "200",
				"msg" => "เพิ่มแบรนด์ ".$request->brand_name."เรียบร้อยแล้ว"
			);
			return redirect('/admin/brand/get/'.$user->id)->with('sysmessage',$message);

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
			return view('admin.brand.manage',compact('sysmessage'));
		}

	}
	public function getEditBrand(Request $request){
		$user = User::find($request->id);
		if(isset($user)){
			return view('admin.brand.manage',compact('user'));
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบแบรนด์สำหรับอัพเดทข้อมูล"
			);
			return redirect('/admin/brand')->with('sysmessage',$sysmessage);
		}
	}
	public function updateBrand(Request $request){
		$validator = $this->validateManageBrand($request->all());
		$user = User::where("id",'=',$request->id)->first();
		if(!isset($user)){
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบแบรนด์สำหรับอัพเดทข้อมูล"
			);
			return redirect('/admin/brand')->with('sysmessage',$sysmessage);;
		}else{
			if(sizeOf($validator->errors())==0){
				$user->name = $request->name;
				$user->email = $request->email;
				if($request->password!=null){
					$user->password = Hash::make($request->password);
				}
				$user->role = "1";
				$user->line = $request->line;
				$user->address = $request->address;
				$user->tax_id = $request->tax_id;
				$user->branch = $request->branch;
				$user->brand_name = $request->brand_name;
				$user->status = "1";
				if($request->vat!=null){
					$user->vat = "1";
				}else{
					$user->vat = "0";
				}
				$user->save();
				$message = array(
					"msgcode" => "200",
					"msg" => "เพิ่มแบรนด์ ".$request->brand_name."เรียบร้อยแล้ว"
				);
				return redirect('/admin/brand/get/'.$user->id)->with('sysmessage',$message);

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
				return redirect('/admin/brand/get/'.$user->id)->with('sysmessage',$sysmessage);
			}
		}
	}

	public function suspendBrand(Request $request){
		$user = User::find($request->id);
		if(isset($user)){
			$user->status = "0";
			$user->save();
			$message = array(
				"msgcode" => "200",
				"msg" => "ระบบระงับการใช้งานแบรนด์ ".$user->brand_name."เรียบร้อยแล้ว"
			);
			return redirect('/admin/brand/get/'.$user->id)->with('sysmessage',$message);

		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบแบรนด์สำหรับอัพเดทข้อมูล"
			);
			return redirect('/admin/brand')->with('sysmessage',$sysmessage);;
		}
	}

	public function unsuspendBrand(Request $request){
		$user = User::find($request->id);
		if(isset($user)){
			$user->status = "1";
			$user->save();
			$message = array(
				"msgcode" => "200",
				"msg" => "ปลดระบบระงับการใช้งานแบรนด์ ".$user->brand_name."เรียบร้อยแล้ว"
			);
			return redirect('/admin/brand/get/'.$user->id)->with('sysmessage',$message);

		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบแบรนด์สำหรับอัพเดทข้อมูล"
			);
			return redirect('/admin/brand')->with('sysmessage',$sysmessage);;
		}
	}

	public function validateManageBrand(array $data){
		return Validator::make($data, [          
			'name' => 'required|string',
			'email' => 'required|string',
			'brand_name' => 'required|string'
		]);
	}

//========================End Brand Manage=======================


//======================Start Admin Manage=======================
	public function getAdmin(){
		$admins = User::where('role','=','2')->where('status','=','1')->get();
		return view('admin.admin.index',compact('admins'));
	}
	public function getAddAdmin(){
		return view('admin.admin.manage');
	}
	public function addAdmin(Request $request){
		$validator = $this->validateManageAdmin($request->all());
		$tmpuser = User::where("email",'=',$request->email)->first();
		if($tmpuser!=null){
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "อีเมลล์นี้มีใช้แล้วในระบบกรุณาใช้อีเมลล์อื่น"
			);
			return view('admin.admin.manage',compact('sysmessage'));
		}
		if(sizeOf($validator->errors())==0){
			$user = new User;
			$user->name = $request->name;
			$user->email = $request->email;
			$user->password = Hash::make($request->password);
			$user->branch = "";
			$user->role = "2";
			$user->line = "";
			$user->brand_name ="";
			$user->status = "1";
			$user->save();
			$message = array(
				"msgcode" => "200",
				"msg" => "เพิ่มผู้ดูแลระบบ ".$request->name."เรียบร้อยแล้ว"
			);
			return redirect('/admin/admin/get/'.$user->id)->with('sysmessage',$message);

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
			return view('admin.admin.manage',compact('sysmessage'));
		}

	}
	public function getEditAdmin(Request $request){
		$user = User::find($request->id);
		if(isset($user)){
			return view('admin.admin.manage',compact('user'));
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบผู้ดูแลระบบสำหรับอัพเดทข้อมูล"
			);
			return redirect('/admin/admin')->with('sysmessage',$sysmessage);
		}
	}
	public function updateAdmin(Request $request){
		$validator = $this->validateManageAdmin($request->all());
		$user = User::where("email",'=',$request->email)->first();
		if(!isset($user)){
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบผู้ดูแลระบบสำหรับอัพเดทข้อมูล"
			);
			return redirect('/admin/brand')->with('sysmessage',$sysmessage);;
		}else{
			if(sizeOf($validator->errors())==0){
				$user->name = $request->name;
				$user->email = $request->email;
				$user->branch = "";
				if($request->password!=null){
					$user->password = Hash::make($request->password);
				}
				$user->save();
				$message = array(
					"msgcode" => "200",
					"msg" => "อัพเดทผู้ดูแลระบบ ".$request->name."เรียบร้อยแล้ว"
				);
				return redirect('/admin/admin/get/'.$user->id)->with('sysmessage',$message);

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
				return redirect('/admin/admin/get/'.$user->id)->with('sysmessage',$sysmessage);
			}
		}
	}

	public function suspendAdmin(Request $request){
		$user = User::find($request->id);
		if(isset($user)){
			$user->status = "0";
			$user->save();
			$message = array(
				"msgcode" => "200",
				"msg" => "ระบบระงับการใช้งานผู้ดูแลระบบ ".$user->name."เรียบร้อยแล้ว"
			);
			return redirect('/admin/admin/get/'.$user->id)->with('sysmessage',$message);

		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบผู้ดูแลระบบสำหรับอัพเดทข้อมูล"
			);
			return redirect('/admin/admin')->with('sysmessage',$sysmessage);;
		}
	}

	public function unsuspendAdmin(Request $request){
		$user = User::find($request->id);
		if(isset($user)){
			$user->status = "1";
			$user->save();
			$message = array(
				"msgcode" => "200",
				"msg" => "ระบบปลดระงับการใช้งานผู้ดูแล ".$user->name."เรียบร้อยแล้ว"
			);
			return redirect('/admin/admin/get/'.$user->id)->with('sysmessage',$message);

		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบผู้ดูแลสำหรับอัพเดทข้อมูล"
			);
			return redirect('/admin/admin')->with('sysmessage',$sysmessage);;
		}
	}

	public function validateManageAdmin(array $data){
		return Validator::make($data, [          
			'name' => 'required|string',
			'email' => 'required|string',
			'password'=> 'required',
		]);
	}

//========================End Admin Manage=======================

//======================Start Member Manage=======================
	public function getMemberOrder(Request $request){
		$member = Members::where('id','=',$request->id)->first();
		if(isset($member)){
			return view('admin.member.order',compact('member'));
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบสมาชิกที่จะดูข้อมูล"
			);
			return redirect('/admin/member')->with('sysmessage',$sysmessage);
		}
	}
	public function getMember(){
		$members = Members::all();
		return view('admin.member.index',compact('members'));
	}
	public function getAddMember(){
		return view('admin.member.manage');
	}
	public function addMember(Request $request){
		$validator = $this->validateManageMember($request->all());
		if(sizeOf($validator->errors())==0){
			$member = new Members;
			$member->name = $request->name;
			$member->detail = $request->detail;
			$member->status = "1";
			$member->save();
			$message = array(
				"msgcode" => "200",
				"msg" => "เพิ่มสมาชิก ".$request->name."เรียบร้อยแล้ว"
			);
			return redirect('/admin/member/get/'.$member->id)->with('sysmessage',$message);

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
			return view('admin.member.manage',compact('sysmessage'));
		}

	}
	public function getEditMember(Request $request){
		$member = Members::find($request->id);
		if(isset($member)){
			return view('admin.member.manage',compact('member'));
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบสมาชิกสำหรับอัพเดทข้อมูล"
			);
			return redirect('/admin/member')->with('sysmessage',$sysmessage);
		}
	}
	public function updateMember(Request $request){
		$validator = $this->validateManageMember($request->all());
		$member = Members::find($request->id);
		if(!isset($member)){
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบสมาชิกสำหรับอัพเดทข้อมูล"
			);
			return redirect('/admin/member')->with('sysmessage',$sysmessage);;
		}else{
			if(sizeOf($validator->errors())==0){
				$member->name = $request->name;
				$member->detail = $request->detail;
				$member->status = $request->status;
				$member->save();
				$message = array(
					"msgcode" => "200",
					"msg" => "อัพเดทสมาชิก ".$request->name."เรียบร้อยแล้ว"
				);
				return redirect('/admin/member/get/'.$member->id)->with('sysmessage',$message);

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
				return redirect('/admin/member/get/'.$member->id)->with('sysmessage',$sysmessage);
			}
		}
	}

	public function suspendMember(Request $request){
		$member = Members::find($request->id);
		if(isset($member)){
			$member->status = "0";
			$member->save();
			$message = array(
				"msgcode" => "200",
				"msg" => "ระบบระงับสมาชิก ".$member->name."เรียบร้อยแล้ว"
			);
			return redirect('/admin/member/get/'.$member->id)->with('sysmessage',$message);

		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบสมาชิกสำหรับอัพเดทข้อมูล"
			);
			return redirect('/admin/member')->with('sysmessage',$sysmessage);;
		}
	}

	public function unsuspendMember(Request $request){
		$member = Members::find($request->id);
		if(isset($member)){
			$member->status = "1";
			$member->save();
			$message = array(
				"msgcode" => "200",
				"msg" => "ระบบปลดระงบสมาชิก ".$member->name."เรียบร้อยแล้ว"
			);
			return redirect('/admin/member/get/'.$member->id)->with('sysmessage',$message);

		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบข้อมูลสมาชิก"
			);
			return redirect('/admin/member')->with('sysmessage',$sysmessage);;
		}
	}

	public function validateManageMember(array $data){
		return Validator::make($data, [          
			'detail' => 'required|string',
			'name' => 'required|string'
		]);
	}

//========================End Member Manage=======================

//========================Start Help Manage=======================

	public function getHelp(Request $request){
		$help = Help::find($request->id);
		if(isset($help)){
			return view('help',compact('help'));
		}else{
			echo "No Help!";
			die();
		}
		
	}

	public function getAddHelp(){
		return view('managehelp');
	}

	public function getUpdateHelp(Request $request){
		$help = Help::find($request->id);
		return view('managehelp',compact('help'));
	}

	public function addHelp(Request $request){
		$help = new Help;
		$help->title = $request->title;
		$help->detail = $request->detail;
		$help->role = '0';
		$help->status = '1';
		$help->save();
		return redirect('/help/'.$help->id);
	}

	public function updateHelp(Request $request){
		$help = Help::find($request->id);
		$help->title = $request->title;
		$help->detail = $request->detail;
		$help->save();
		return redirect('/help/'.$help->id);
	}

//========================End Help Manage=======================

//========================Start Purchase Manage=======================
	public function recieveProduct(Request $request){
		$user = Auth::user();
		if($user->role!=2){
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่มีสิทธิในการรับสินค้า"
			);
			return redirect('/purchase')->with('sysmessage',$sysmessage);
		}
		$purchase = Purchaseorders::where('id','=',$request->id)->first();
		

		$items = $purchase->getItem;

		foreach($items as $item){
			$laststock = Stocks::where("branch_id",'=',$purchase->branch_id)->where("product_id",'=',$item->product_id)->orderBy('id','desc')->first();
			$lastsum = 0;
			if(isset($laststock)){
				$lastsum = $laststock->sum;
			}

			$stock = new Stocks;
			$stock->product_id = $item->product_id;
			$stock->branch_id = $purchase->branch_id;
			$stock->type = "add";
			$stock->quantity = $item->quantity;
			$stock->sum = $lastsum+$item->quantity;
			$stock->remark = "รับสินค้าเข้าจากใบนำเข้าเลขที่ ".$purchase->id;
			$stock->save();
		}
		$purchase->admin_id = $user->id;
		$purchase->status = 1;
		$purchase->save();
		$message = array(
			"msgcode" => "200",
			"msg" => "นำสินค้าเข้าร้านเรียบร้อยแล้ว"
		);
		return redirect('/purchase/get/'.$purchase->id)->with('sysmessage',$message);
	}
//========================End Purchase Manage=======================

//========================Start Branch Manage=======================
	public function getAllBranch(){
		$branchs = Branch::all();
		return view('admin.branch.index',compact('branchs'));
	}

	public function getBranch(Request $request){
		$branch = Branch::find($request->id);
		if(isset($branch)){
			$users = User::where("status",'=','1')->get();
			$branch_users = Branch_user::where('branch_id','=',$branch->id)->get();
			$checkhas = array();
			foreach($branch_users as $item){
				array_push($checkhas, $item->user_id);
			}
			return view('admin.branch.assign',compact('branch','users','checkhas'));
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่มีสาขานี้"
			);
			return redirect('/admin/branch')->with('sysmessage',$sysmessage);
		}
	}

	public function assigntoBranch(Request $request){
		$branch = Branch::find($request->branch_id);

		$user = User::find($request->user_id);
		if(isset($branch)){
			if(isset($user)){
				$branch_user = Branch_user::where('user_id','=',$user->id)->where('branch_id','=',$branch->id)->first();
				if(isset($branch_user)){
					$sysmessage = array(
						"msgcode" => "500",
						"msg" => "แบรนด์ ".$user->brand_name." อยู่ในสาขานี้อยู่แล้ว"
					);
					return redirect('/admin/branch/get/'.$branch->id)->with('sysmessage',$sysmessage);
				}else{
					$branch_user = new Branch_user;
					$branch_user->branch_id = $branch->id;
					$branch_user->user_id = $user->id;
					$branch_user->gp = $request->gp;
					$branch_user->save();
					$sysmessage = array(
						"msgcode" => "200",
						"msg" => "เพิ่มแบรนด์ ".$user->brand_name." เข้าสาขา".$branch->name."เรียบร้อยแล้ว"
					);
					return redirect('/admin/branch/get/'.$branch->id)->with('sysmessage',$sysmessage);
				}
			}else{
				$sysmessage = array(
					"msgcode" => "500",
					"msg" => "ไม่มีแบรนด์ที่จะเพิ่ม"
				);
				return redirect('/admin/branch/get/'.$branch->id)->with('sysmessage',$sysmessage);
			}
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่มีสาขานี้"
			);
			return redirect('/admin/branch')->with('sysmessage',$sysmessage);
		}
	}
	public function removefromBranch(Request $request){
		$branch_user = Branch_user::where('user_id','=',$request->user_id)->where('branch_id','=',$request->branch_id)->first();
		if(isset($branch_user)){
			$branch_user->delete();
			$sysmessage = array(
				"msgcode" => "200",
				"msg" => "ลบแบรนด์ออกจากสาขาเรียบร้อยแล้ว"
			);
			return redirect('/admin/branch/get/'.$request->branch_id)->with('sysmessage',$sysmessage);
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "แบรนด์ไม่ได้อยู่ในสาขาที่เลือก"
			);
			return redirect('/admin/branch')->with('sysmessage',$sysmessage);
		}
	}
//========================End Branch Manage=======================

//========================Start Stock=======================
	public function getProductStock(Request $request){
		$stock = Stocks::where('product_id','=',$request->product_id)->where('branch_id','=',$request->branch_id)->orderBy('id','desc')->first();
		if(isset($stock)){
			echo json_encode(array(
				"stock" => $stock->sum
			));
		}else{
			echo json_encode(array(
				"stock" => "0"
			));
		}
	}
	public function getStockReport(){
		$users = User::where("status",'=','1')->where("role",'=',"1")->get();
		$flag = 0;
		return view('admin.stock.report',compact('users','flag'));
	}
	public function getStockBrand(Request $request){
		$user = User::find($request->user_id);
		$users = User::where("status",'=','1')->where("role",'=',"1")->get();
		$branchs = Branch::all();
		if(isset($user)){
			$flag = 1;
			return view('admin.stock.report',compact('user','users','flag','branchs'));
		}else{
			$flag = 2;
			$results = array();
			$products = Products::where('status','=','1')->get();
			foreach($products as $product){
				foreach($product->getVariant as $prodvr){
					$results[$prodvr->id]["product_id"] = $prodvr->id;
					$results[$prodvr->id]["product_name"] = $product->name;
					$results[$prodvr->id]["brand_name"] = $product->getUser->brand_name;
					$results[$prodvr->id]["price"] = $product->price;
					$results[$prodvr->id]["stock"] = array();
					foreach($branchs as $branch){
						$results[$prodvr->id]["stock"][$branch->id] = 0;
					}
				}
			}
			
			$stocks = DB::select(DB::raw("SELECT pv.id as product_id,b.id as branch_id , s.sum as remain
				FROM `stocks`s JOIN product_variant pv ON pv.id = s.product_id JOIN products p ON pv.product_id = p.id JOIN branch b ON b.id = s.branch_id JOIN users u ON u.id = p.user_id
				WHERE s.id IN (
				SELECT MAX(id) as id
				FROM stocks 
				GROUP BY product_id,branch_id
				)  
				ORDER BY `product_id` ASC"));
			foreach($stocks as $stock){
				$results[$stock->product_id]["stock"][$stock->branch_id] = $stock->remain;
			}
			return view('admin.stock.report',compact('user','users','flag','branchs','results'));
		}


		
	}
	public static function getOnhand($product_id){
		$result = array();
		$branchs = Branch::all();
		foreach($branchs as $branch){
			$result[$branch->id] = 0;
			$stock = Stocks::where("product_id",'=',$product_id)->where("branch_id",'=',$branch->id)->orderBy('id','desc')->first();
			if(isset($stock)){
				$result[$branch->id] = $stock->sum;
			}else{
				$result[$branch->id] = 0;
			}
		}

		return $result;
	}
	public function getStockAdjust(){
		$products = Products::where('status','=','1')->get();
		$branchs = Branch::all();
		$users = User::where('role','=','1')->where('status','=','1')->orderBy('brand_name','ASC')->get();
		return view('admin.stock.adjust',compact('products','branchs','users'));
	}
	public function adjustStock(Request $request){
		$remark = $request->remark;
		$product = $request->products;
		$quantity = $request->quantity;
		if($product!=null&&$quantity!=null&&$remark!=null){
			$stockadj = new Stock_adjustment;
			$stockadj->remark = $request->remark;
			$stockadj->admin_id = Auth::user()->id;
			$stockadj->branch_id = $request->branch_id;
			$stockadj->save();

			for($i=0;$i<sizeOf($product);$i++){
				$item = new Stockadj_item;
				$item->stockadj_id = $stockadj->id;
				$item->product_id = $product[$i];
				$item->quantity = $quantity[$i];
				$item->save();
			}

			foreach($stockadj->getItem as $item){
				$stock = new Stocks;
				$stock->product_id = $item->product_id;
				$stock->branch_id = $stockadj->branch_id;
				$stock->type = "adjust";
				$stock->quantity = $item->quantity;
				$stock->sum = $item->quantity;
				$stock->remark = "ปรับปรุงสินค้า: ".$stockadj->remark."#".$stockadj->id."#";
				$stock->save();
			}
			$sysmessage = array(
				"msgcode" => "200",
				"msg" => "ปรับปรุงยอดเรียบร้อยแล้ว"
			);
			return redirect('/admin/stock/report')->with('sysmessage',$sysmessage);
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "กรอกข้อมูลไม่ครบ! กรุณาตรวจสอบ"
			);
			return redirect('/admin/stock/adjust')->with('sysmessage',$sysmessage);
		}

	}

	public function getStockTransfer(){
		// $stockadjs = Stock_transfer::orderBy('id','desc')->get();
		return view('admin.stock.transferindex');
	}


	public function getStockCreate(){
		$products = Products::where('status','=','1')->get();
		$branchs = Branch::all();
		$users = User::where('role','=','1')->where('status','=','1')->orderBy('brand_name','ASC')->get();
		return view('admin.stock.transfer',compact('products','branchs','users'));
	}
	public function getEditTransfer(Request $request){
		$stockadj = Stock_transfer::where('id','=',$request->id)->first();
		if(isset($stockadj)){
			$products = Products::where('status','=','1')->get();
			$branchs = Branch::all();
			$users = User::where('role','=','1')->where('status','=','1')->orderBy('brand_name','ASC')->get();
			return view('admin.stock.transfer',compact('stockadj','products','branchs','users'));
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบใบย้ายสินค้า"
			);
			return redirect('/admin/stock/adjust')->with('sysmessage',$sysmessage);
		}
	}

	public function cancelTransfer(Request $request){
		$stockadj = Stock_transfer::where('id','=',$request->id)->first();
		if(isset($stockadj)){
			$stockadj->status = 9;
			$stockadj->save();
			$sysmessage = array(
				"msgcode" => "200",
				"msg" => "ยกเลิกใบย้ายสินค้าเรียบร้อยแล้ว"
			);
			return redirect('/admin/stock/transfer/get/'.$stockadj->id)->with('sysmessage',$sysmessage);
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบใบย้ายสินค้า"
			);
			return redirect('/admin/stock/adjust')->with('sysmessage',$sysmessage);
		}
	}

	public function submitTransfer(Request $request){
		$stockadj = Stock_transfer::where('id','=',$request->id)->first();
		if(isset($stockadj)){
		//Clear Source Stock
			foreach($stockadj->getItem as $item){
				$oldstock = Stocks::where('product_id','=',$item->product_id)->where('branch_id','=',$stockadj->src_id)->orderBy('id','desc')->first();
				if(isset($oldstock)){
					$oldstock = $oldstock->sum;
				}else{
					$oldstock = 0;
				}
				$stock = new Stocks;
				$stock->product_id = $item->product_id;
				$stock->branch_id = $stockadj->src_id;
				$stock->type = "transfer";
				$stock->quantity = $item->quantity;
				$stock->sum = $oldstock-$item->quantity;
				$stock->remark = "ย้ายคลังสินค้า: ".$stockadj->remark."#".$stockadj->id."#";
				$stock->save();

			}

			//Add Destination Stock
			foreach($stockadj->getItem as $item){
				$oldstock = Stocks::where('product_id','=',$item->product_id)->where('branch_id','=',$stockadj->dst_id)->orderBy('id','desc')->first();
				if(isset($oldstock)){
					$oldstock = $oldstock->sum;
				}else{
					$oldstock = 0;
				}
				$stock = new Stocks;
				$stock->product_id = $item->product_id;
				$stock->branch_id = $stockadj->dst_id;
				$stock->type = "transfer";
				$stock->quantity = $item->quantity;
				$stock->sum = $oldstock+$item->quantity;
				$stock->remark = "ย้ายคลังสินค้า: ".$stockadj->remark."#".$stockadj->id."#";
				$stock->save();
			}
			$stockadj->status = 1;
			$stockadj->save();
			$sysmessage = array(
				"msgcode" => "200",
				"msg" => "ย้ายคลังสินค้าเรียบร้อยแล้ว"
			);
			return redirect('/admin/stock/transfer/get/'.$stockadj->id)->with('sysmessage',$sysmessage);

		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบใบย้ายสินค้า"
			);
			return redirect('/admin/stock/adjust')->with('sysmessage',$sysmessage);


		}
	}
	public function transferStock(Request $request){
		$remark = $request->remark;
		$product = $request->products;
		$quantity = $request->quantity;
		$src_id = $request->src_id;
		$dst_id = $request->dst_id;

		if($product!=null&&$quantity!=null&&$remark!=null){
			$stockadj = new Stock_transfer;
			$stockadj->remark = $request->remark;
			$stockadj->admin_id = Auth::user()->id;
			$stockadj->src_id = $request->src_id;
			$stockadj->dst_id = $request->dst_id;
			$stockadj->save();

			for($i=0;$i<sizeOf($product);$i++){
				$item = new Stocktransfer_item;
				$item->stocktransfer_id = $stockadj->id;
				$item->product_id = $product[$i];
				$item->quantity = $quantity[$i];
				$item->save();
			}


			$sysmessage = array(
				"msgcode" => "200",
				"msg" => "เพิ่มรายการย้ายคลังสินค้าเรียบร้อยแล้ว"
			);
			return redirect('/admin/stock/transfer/get/'.$stockadj->id)->with('sysmessage',$sysmessage);
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "กรอกข้อมูลไม่ครบ! กรุณาตรวจสอบ"
			);
			return redirect('/admin/stock/transfer')->with('sysmessage',$sysmessage);
		}

	}
	public function updateTransfer(Request $request){
		$remark = $request->remark;
		$product = $request->products;
		$quantity = $request->quantity;

		if($product!=null&&$quantity!=null&&$remark!=null){
			$stockadj = Stock_transfer::where('id','=',$request->id)->first();
			if(isset($stockadj)){
				$stockadj->remark = $request->remark;
				$stockadj->admin_id = Auth::user()->id;
				$stockadj->save();
				Stocktransfer_item::where('stocktransfer_id','=',$stockadj->id)->delete();
				for($i=0;$i<sizeOf($product);$i++){
					$item = new Stocktransfer_item;
					$item->stocktransfer_id = $stockadj->id;
					$item->product_id = $product[$i];
					$item->quantity = $quantity[$i];
					$item->save();
				}
				$sysmessage = array(
					"msgcode" => "200",
					"msg" => "แก้ไขรายการย้ายคลังสินค้าเรียบร้อยแล้ว"
				);
				return redirect('/admin/stock/transfer/get/'.$stockadj->id)->with('sysmessage',$sysmessage);

			}else{
				$sysmessage = array(
					"msgcode" => "500",
					"msg" => "ไม่พบรายการสำหรับแก้ไข"
				);
				return redirect('/admin/stock/transfer')->with('sysmessage',$sysmessage);
			}



		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "กรอกข้อมูลไม่ครบ! กรุณาตรวจสอบ"
			);
			return redirect('/admin/stock/transfer')->with('sysmessage',$sysmessage);
		}

	}

	public function printTransfer(Request $request){
		$stockadj = Stock_transfer::where('id','=',$request->id)->first();
		if(isset($stockadj)){
			return view('admin.stock.transferprint',compact('stockadj'));
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบใบย้ายสินค้า"
			);
			return redirect('/admin/stock/transfer')->with('sysmessage',$sysmessage);
		}
	}



//========================End Stock=======================	

//========================Start POS=======================
	public function getPOS(){
		$user = Auth::user();
		$branch_user = Branch_user::where('user_id','=',$user->id)->get();
		$branchin = array();
		foreach($branch_user as $result){
			array_push($branchin, $result->branch_id);
		}
		$branchs = Branch::whereIn('id',$branchin)->get();
		return view('admin.pos.index',compact('branchs'));
	}
	public function getBarcode(Request $request){
		$barcode = $request->barcode;
		$product_variant = Product_variant::where("barcode",'=',$barcode)->orderBy('id', 'desc')->first();
		if(isset($product_variant)){
			$product = $product_variant->getProduct;
			$price = 0;

			if($product->discount_type!=0){
				$price = $product->discount_price;
			}else{
				$price = $product->price;
			}
			$result = array(
				"msg" => "Found",
				"product_id" => $product_variant->id,
				"product_name" => $product_variant->getProduct->name.' ('.$product_variant->variant.')',
				"product_price" => $price
			);
		}else{
			$result = array(
				"msg" => "Not",
				"product_id" => '0',
				"product_name" => '0',
				"product_price" => '0'
			);
		}
		echo json_encode($result);

	}
	public function getMainPOS(Request $request){
		$branch = Branch::find($request->branch_id);
		if(isset($branch)){
			$branch_id = $branch->id;
			$members = Members::where("status",'=',"1")->get();
			$brands = User::where("status","=","1")->where("role",'=','1')->orderBy('brand_name','asc')->get();
			$products = Products::where('status','=',1)->where('name', 'not like', "%(Tester)%")->get();
			$payments = Paymenttypes::all();
			$promotions = Promotions::where("status",'=',"1")->get();
			return view('admin.pos.main',compact('members','brands','payments','branch_id','promotions','products'));
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบสาขาสำหรับเปิด POS"
			);
			return redirect('/admin/pos')->with('sysmessage',$sysmessage);
		}
	}
	public static function getOnhandPOS($variant,$branchs){
		$result = "";
		foreach($branchs as $branch){
			$stock = Stocks::where("product_id",'=',$variant->id)->where("branch_id",'=',$branch->id)->orderBy('id','desc')->first();
			if(isset($stock)){
				$result .= $branch->name.": <font id='prod".$variant->id."s".$branch->id."'>".$stock->sum."</font> ".$variant->getProduct->getUnit->name."<br>";
			}else{
				$result .= $branch->name.": <font id='prod".$variant->id."s".$branch->id."'>"."0 </font>".$variant->getProduct->getUnit->name."<br>";
			}
		}
		
		return $result;
	}
	public function searchItem(Request $request){
		$name = strtolower($request->name);
		$result = DB::select(DB::raw("SELECT pv.id as id FROM products p JOIN product_variant pv ON pv.product_id = p.id JOIN users u ON u.id = p.user_id WHERE CONCAT(LOWER(p.name), ' ', LOWER(pv.variant),' ',LOWER(u.brand_name)) LIKE '%$name%' AND p.status = '1' AND p.name not like '%(Tester)%' AND u.status = '1'"));
		return json_encode($result);
	}
	public function POSmakeOrder(Request $request){
		$orders = $request->orders;
		if($orders!=null && sizeOf($orders)!=0){
			$lastinv = Invoices::where("branch_id",'=',$request->branch_id)->orderBy("tax_id",'DESC')->first();
			$tax_id = 1;
			if(isset($lastinv)){
				$tax_id = $lastinv->tax_id+1;
			}

			$invoice = new Invoices;
			$invoice->tax_id = $tax_id;
			$invoice->branch_id = $request->branch_id;
			$invoice->admin_id = Auth::user()->id;
			$invoice->paymenttype_id = $request->payment_type;
			$invoice->recieve = $request->recieve;
			$invoice->member_id = $request->member_id;
			$invoice->status = '1';
			$invoice->save();

			$product = $orders;
			$suminput = $request->suminput;
			$itempromotion = $request->itempromotion;
			for($i=0;$i<sizeOf($product);$i++){
				if($product[$i]!=null){
					if($product[$i]!=0){
						$data = explode("|", $product[$i]);
				
						$sumdata = explode("|", $suminput[$i]);
						if($data[1]==0){
							continue;
						}
						$productinfo = Product_variant::find($data[0]);
						$productinfo = $productinfo->getProduct;
						$promotionchk = 0;
						$price = $productinfo->price;
				

						$invoiceitem = new Invoice_item;
						$invoiceitem->invoice_id = $invoice->id;
						$invoiceitem->product_id = $data[0];

						$invoiceitem->price = $price;

						$invoiceitem->quantity = $data[1];
						$invoiceitem->suminput = $sumdata[1];
						if(isset($itempromotion[$data[0]])){
							$invoiceitem->itempromotion = $itempromotion[$data[0]];
						}else{
							$invoiceitem->itempromotion = 0;
						}
						$invoiceitem->save();

						$laststock = Stocks::where("product_id",'=',$data[0])->where("branch_id",'=',$invoice->branch_id)->orderBy('id','desc')->first();
						$lastsum = 0;
						if(isset($laststock)){
							$lastsum = $laststock->sum;
						}
						$stock = new Stocks;
						$stock->product_id = $data[0];
						$stock->branch_id = $request->branch_id;
						$stock->type = "sell";
						$stock->quantity = $invoiceitem->quantity;
						$stock->sum = $lastsum - $invoiceitem->quantity;
						$stock->remark = "ขายสินค้าหน้าร้าน#".$invoice->id;
						$stock->save();
					}
				}
			}
			if($request->promotion_id!=0){
				$invoice_promotion = new Invoice_promotion;
				$invoice_promotion->promotion_id = $request->promotion_id;
				$invoice_promotion->invoice_id = $invoice->id;
				$invoice_promotion->discount = $request->discount;
				$invoice_promotion->save();
			}
			return $invoice->id;
		}else{
			return "Failed";
		}
	}
	public function getPrintSlip(Request $request){
		$invoice = Invoices::find($request->id);
		if(isset($invoice)){
			$invoiceItems = Invoice_item::where("invoice_id", $invoice->id)->get();
			$sumItem = 0;
			foreach ($invoiceItems as $item){ 
				$sumItem += $item->quantity;
			}
			$promotions = $invoice->getPromotion;
			return view('admin.pos.slip',compact('invoice','promotions', 'sumItem'));
		}else{
			$message = array(
				"msgcode" => "500",
				"msg" => "ไม่พบใบออเดอร์ดังกล่าว"
			);
			return redirect('/admin/order')->with('sysmessage',$message);
		}
		
	}
//========================End POS=======================	

//=====Report=====
	public function getChooseReport(){
		$user = Auth::user();
		$brands = User::where("role",'=',"1")->where('status','=','1')->get();
		$branchs = Branch::all();
		return view('admin.report.index',compact('branchs','brands'));
	}
	public function getReport(Request $request){
		$startdate = $request->start_date;
		$enddate = $request->end_date;
		$branch_id = $request->branch_id;
		$brand_id = $request->brand_id;
		if($startdate!=null&&$enddate!=null&&$branch_id!=null){
			$branch = Branch::find($branch_id);
			$brand = User::where("id",'=',$brand_id)->first();
			$startdate = $startdate;
			$enddate = $enddate;
			$gp = Branch_user::where('user_id','=',$brand_id)->where('branch_id','=',$branch_id)->first();
			if(isset($gp)){
				$gp = $gp->gp;
			}else{
				$gp = 0;
			}
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
			$reportrealprice = array();
			foreach($pmethods as $pment){
				$discountpayment[$pment->id] = 0;
			}
			foreach($invoices as $invoice){
				foreach($invoice->getItem as $item){
					$tmpprodid = "id".$item->product_id."|". $item->suminput/$item->quantity;
					try{
						$reportsum[$tmpprodid] += $item->price*$item->quantity;
						$reportquantity[$tmpprodid] += $item->quantity;
						$reportsuminput[$tmpprodid] += $item->suminput;
						$reportinvoiceid[$tmpprodid] .= ",".$item->id;
					}catch(Exception $e){
						$reportsum[$tmpprodid] = $item->price*$item->quantity;
						$reportrealprice[$tmpprodid] = $item->price;
						$reportquantity[$tmpprodid] = $item->quantity;
						$reportsuminput[$tmpprodid] = $item->suminput;
						$reportinvoiceid[$tmpprodid] = $item->id;
					}
					
				}

				foreach($invoice->getPromotion as $promo){
					$sumdiscount += $promo->discount;
					$discountpayment[$invoice->paymenttype_id] += $promo->discount;
				}

			}
			// dd('Here');
			$payments = DB::select(DB::raw("SELECT i.paymenttype_id as id,pt.name as name, SUM(it.suminput) as sum, i.status ,i.created_at,i.branch_id
				FROM invoices i JOIN invoice_item it ON i.id = it.invoice_id JOIN paymenttypes pt ON pt.id = i.paymenttype_id
				GROUP BY i.status,i.created_at,i.paymenttype_id
				HAVING i.status = '1' AND i.created_at >= '$startdate' AND i.created_at <= '$enddate' AND branch_id = '2'"));
			
			return view('admin.report.report',compact('reportsum','reportquantity','startdate','enddate','payments','branch','pmethods','sumdiscount','discountpayment','brand','reportsuminput','brand_id','gp','reportrealprice'));
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "เลือกวันที่เพื่อดูยอดขาย"
			);
			return redirect('/admin/report')->with('sysmessage',$sysmessage);
		}


	}

	public function apiGetproduct(Request $request){
		if($request->brand_id==null||$request->brand_id == 0){
			$products = Products::where('name','like','%'.$request->term.'%')->where('status','=','1')->get();
		}else{
			$products = Products::where('name','like','%'.$request->term.'%')->where('status','=','1')->where('user_id','=',$request->brand_id)->get();
		}
		
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

	public function getChooseReportBrand(){
		$user = Auth::user();
		$branchs = Branch_user::where("user_id",'=',$user->id)->get();
		return view('admin.report.brand.index',compact('branchs'));
	}

	public function getReportBrand(Request $request){
		$startdate = $request->start_date;
		$enddate = $request->end_date;
		$branch_id = $request->branch_id;

		if($startdate!=null&&$enddate!=null&&$branch_id!=null){
			$branch = Branch::find($branch_id);
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
			//admin.report.brand.index
			return view('admin.report.brand.report',compact('reportsum','reportquantity','startdate','enddate','branch','pmethods','sumdiscount','discountpayment','reportsuminput','paymentincome'));
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "เลือกวันที่เพื่อดูยอดขาย"
			);
			return redirect('/report')->with('sysmessage',$sysmessage);
		}


	}
//====End Report=====	
//====POSStart=======

	public function indexPOSStart(){
		$starts = Startmoney::all();
		$user = Auth::user();
		$branch_user = Branch_user::where('user_id','=',$user->id)->get();
		$branchin = array();
		foreach($branch_user as $result){
			array_push($branchin, $result->branch_id);
		}
		$branchs = Branch::whereIn('id',$branchin)->get();
		return view('admin.pos.start',compact('starts','branchs'));
	}

	public function addPosstart(Request $request){
		$validator = $this->validateStart($request->all());
		if(sizeOf($validator->errors())==0){
			$user = Auth::user();
			$startmoney = new Startmoney;
			$startmoney->admin_id = $user->id;
			$startmoney->branch_id = $request->branch_id;
			$startmoney->onethousand = $request->onethousand;
			$startmoney->fivehundred = $request->fivehundred;
			$startmoney->onehundred = $request->onehundred;
			$startmoney->fifty = $request->fifty;
			$startmoney->twenty = $request->twenty;
			$startmoney->ten = $request->ten;
			$startmoney->five = $request->five;
			$startmoney->two = $request->two;
			$startmoney->one = $request->one;
			$startmoney->save();
			$sysmessage = array(
				"msgcode" => "200",
				"msg" => "เพิ่มเงินเริ่มต้นเรียบร้อยแล้ว"
			);
			return redirect('/admin/posstart')->with('sysmessage',$sysmessage);
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
			return redirect('/admin/posstart')->with('sysmessage',$sysmessage);

		}
	}
	public function getPosstart(Request $request){
		$start = Startmoney::find($request->id);
		if(isset($start)){
			
			$starts = Startmoney::all();
			$user = Auth::user();
			$branch_user = Branch_user::where('user_id','=',$user->id)->get();
			$branchin = array();
			foreach($branch_user as $result){
				array_push($branchin, $result->branch_id);
			}
			$branchs = Branch::whereIn('id',$branchin)->get();

			return view('admin.pos.start',compact('start','starts','branchs'));
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบรายการที่จะแก้ไข"
			);
			return redirect('/admin/posstart')->with('sysmessage',$sysmessage);
		}
	}

	public function editPosstart(Request $request){
		$startmoney = Startmoney::find($request->id);
		if(isset($startmoney)){
			$user = Auth::user();
			$startmoney->admin_id = $user->id;
			$startmoney->onethousand = $request->onethousand;
			$startmoney->fivehundred = $request->fivehundred;
			$startmoney->onehundred = $request->onehundred;
			$startmoney->fifty = $request->fifty;
			$startmoney->twenty = $request->twenty;
			$startmoney->ten = $request->ten;
			$startmoney->five = $request->five;
			$startmoney->two = $request->two;
			$startmoney->one = $request->one;
			$startmoney->save();
			$sysmessage = array(
				"msgcode" => "200",
				"msg" => "แก้ไขรายการเรียบร้อยแล้ว"
			);
			return redirect('/admin/posstart')->with('sysmessage',$sysmessage);
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบรายการที่จะแก้ไข"
			);
			return redirect('/admin/posstart')->with('sysmessage',$sysmessage);
		}
	}

	public function validateStart(array $data){
		return Validator::make($data, [          
			'onethousand' => 'integer',
			'fivehundred' => 'integer',
			'onehundred' => 'integer',
			'fifty' => 'integer',
			'twenty' => 'integer',
			'ten'=> 'integer',
			'five'=>'integer',
			'two'=>'integer',
			'one'=>'integer'
		]);
	}



//====ENDPOSStart=======

//====StartPromoNotification====
	public function getAllPromoNotification(){
		$branchs = Branch::all();
		return view('admin.promonotification.index',compact('branchs'));
	}
	public function getPromoNotificationSpecific(Request $request){
		$branchs = Branch::all();
		if($request->branch_id==0){
			$promotions = Promotion_notification::whereBetween('startdate', [$request->start_date, $request->end_date])->orderBy('id','desc')->get();
		}else{
			$autobranch = Notification_branch::where('branch_id','=',$request->branch_id)->get();
			$branchsid = array();
			foreach($autobranch as $br){
				array_push($branchsid,$br->promotion_id);
			}
			$promotions = Promotion_notification::whereBetween('startdate', [$request->start_date, $request->end_date])->orderBy('id','desc')->whereIn('id', $branchsid)->get();
		}
		
		return view('admin.promonotification.index',compact('promotions','branchs'));
	}
	public function getAddPromoNotification(){
		$branchs = Branch::all();
		return view('admin.promonotification.manage',compact('branchs'));
	}
	public function printPromoNotification(Request $request){
		$type = "single";
		$promotion = Promotion_notification::find($request->id);
		return view('admin.promonotification.print',compact('promotion','type'));
	}

	public function printPromoNotificationGroupPrint(Request $request){
		$type = "date";
		if($request->branch_id==0){
			$promotions = Promotion_notification::whereBetween('startdate', [$request->start_date, $request->end_date])->orderBy('id','desc')->get();
		}else{
			$autobranch = Notification_branch::where('branch_id','=',$request->branch_id)->get();
			$branchsid = array();
			foreach($autobranch as $br){
				array_push($branchsid,$br->promotion_id);
			}
			$promotions = Promotion_notification::whereBetween('startdate', [$request->start_date, $request->end_date])->orderBy('id','desc')->whereIn('id', $branchsid)->get();
		}
		if(sizeOf($promotions)==0){
			dd("No promotion to print");
		}
		return view('admin.promonotification.print',compact('promotions','type'));
	}

	public function removePromotionNotification(Request $request){
		$promotion = Promotion_notification::find($request->id);
		if(isset($promotion)){
			Promotion_product::where('promotion_id','=',$promotion->id)->delete();
			Notification_branch::where('promotion_id','=',$promotion->id)->delete();
			$promotion->delete();
			$sysmessage = array(
				"msgcode" => "200",
				"msg" => "ลบโปรโมชั่นเรียบร้อยแล้ว"
			);
			return redirect('/admin/promonotification')->with('sysmessage',$sysmessage);
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบโปรโมชั่นที่ต้องการลบ"
			);
			return redirect('/admin/promonotification')->with('sysmessage',$sysmessage);
		}
	}
	public function parseBranch(){
		$promotions = Promotion_notification::all();
		foreach($promotions as $promotion){
			$tmp = new Notification_branch;
			$tmp->branch_id = $promotion->branch_id;
			$tmp->promotion_id = $promotion->id;
			$tmp->save();
		}

	}

	public function removeProductfromPromotion(Request $request){
		$promotion = Promotion_notification::find($request->promotion_id);
		Promotion_product::where('product_id','=',$request->product_id)->where('promotion_id','=',$promotion->id)->delete();
		$sysmessage = array(
			"msgcode" => "200",
			"msg" => "ปรับปรุงโปรโมชั่นเรียบร้อยแล้ว"
		);
		return redirect('/admin/promonotification/get/'.$promotion->id)->with('sysmessage',$sysmessage);
	}

	public function addProducttoPromotion(Request $request){
		$promotion = Promotion_notification::find($request->id);
		if(isset($promotion)){
			if($request->type=="product"){
				$products = $request->products;
				if($products==null){
					$sysmessage = array(
						"msgcode" => "500",
						"msg" => "กรุณาเลือกสินค้า"
					);
					return redirect('/admin/promonotification/get/'.$promotion->id)->with('sysmessage',$sysmessage);
				}else{
					for($i=0;$i<sizeOf($products);$i++){
						$variant = Product_variant::find($products[$i]);
						if(isset($variant)){
							$promoproduct = new Promotion_product;
							$promoproduct->promotion_id = $promotion->id;
							$promoproduct->product_id = $variant->id;
							$promoproduct->save();
						}
					}
				}

			}else{
				$products = Products::where('user_id','=',$request->brand_id)->where('status','=','1')->get();
				foreach($products as $product){
					$variants = Product_variant::where('product_id','=',$product->id)->get();
					foreach($variants as $variant){
						$promoproduct = new Promotion_product;
						$promoproduct->promotion_id = $promotion->id;
						$promoproduct->product_id = $variant->id;
						$promoproduct->save();
					}
				}
			}

			$sysmessage = array(
				"msgcode" => "200",
				"msg" => "ปรับปรุงโปรโมชั่นเรียบร้อยแล้ว"
			);
			return redirect('/admin/promonotification/get/'.$promotion->id)->with('sysmessage',$sysmessage);
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบรายการที่จะแก้ไข"
			);
			return redirect('/admin/promonotification')->with('sysmessage',$sysmessage);
		}
	}

	public function addPromoNotification(Request $request){
		$validator = $this->validatePromoNotification($request->all());
		$branchin = $request->branch;

		if(sizeOf($validator->errors())==0){
			$user = Auth::user();
			$promotion = new Promotion_notification;
			$promotion->description = $request->description;
			$promotion->startdate = $request->startdate;
			$promotion->enddate = $request->enddate;
			$promotion->status = 1;
			$promotion->admin_id = $user->id;
			$promotion->save();
			for($i=0;$i<sizeOf($branchin);$i++){
				$notibranch = new Notification_branch;
				$notibranch->promotion_id = $promotion->id;
				$notibranch->branch_id = $branchin[$i];
				$notibranch->save();
			}

			$sysmessage = array(
				"msgcode" => "200",
				"msg" => "เพิ่มโปรโมชั่นเรียบร้อยแล้ว"
			);
			return redirect('/admin/promonotification/get/'.$promotion->id)->with('sysmessage',$sysmessage);
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
			return redirect('/admin/promonotification/create')->with('sysmessage',$sysmessage);

		}
	}

	public function updatePromoNotification(Request $request){

		$validator = $this->validatePromoNotification($request->all());
		if(sizeOf($validator->errors())==0){
			$promotion = Promotion_notification::find($request->id);
			$branchin = $request->branch;
			if(isset($promotion)){
				$user = Auth::user();
				$promotion->description = $request->description;
				$promotion->startdate = $request->startdate;
				$promotion->enddate = $request->enddate;
				$promotion->status = 1;
				$promotion->admin_id = $user->id;
				$promotion->save();

				Notification_branch::where('promotion_id','=',$promotion->id)->delete();
				for($i=0;$i<sizeOf($branchin);$i++){
					$notibranch = new Notification_branch;
					$notibranch->promotion_id = $promotion->id;
					$notibranch->branch_id = $branchin[$i];
					$notibranch->save();
				}
				$sysmessage = array(
					"msgcode" => "200",
					"msg" => "ปรับปรุงโปรโมชั่นเรียบร้อยแล้ว"
				);
				return redirect('/admin/promonotification/get/'.$promotion->id)->with('sysmessage',$sysmessage);
			}else{
				$sysmessage = array(
					"msgcode" => "500",
					"msg" => "ไม่พบโปรโมชั่นสำหรับอัพเดท"
				);
				return redirect('/admin/promonotification/')->with('sysmessage',$sysmessage);
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
			return redirect('/admin/promonotification/create')->with('sysmessage',$sysmessage);

		}
	}

	public function getPromoNotification(Request $request){
		$promotion = Promotion_notification::find($request->id);
		if(isset($promotion)){
			$branchinuse = array();
			foreach($promotion->getBranch as $branch){
				array_push($branchinuse, $branch->branch_id);
			}
			$branchs = Branch::all();
			$brands = User::where('role','=','1')->where('status','=','1')->orderBy('brand_name','asc')->get();
			return view('admin.promonotification.manage',compact('branchs','promotion','brands','branchinuse'));
		}else{
			$sysmessage = array(
				"msgcode" => "500",
				"msg" => "ไม่พบรายการที่จะแก้ไข"
			);
			return redirect('/admin/promonotification')->with('sysmessage',$sysmessage);
		}
	}
	public function validatePromoNotification(array $data){
		return Validator::make($data, [          
			'startdate' => 'required',
			'enddate' => 'required',
			'description' => 'required'
		]);
	}
	public function checkNotiPromotion(Request $request){
		$promotion_products = Promotion_product::where('product_id','=',$request->product_id)
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
				array_push($tmp,$check->branch_id);
			}
			dd($tmp);
			if(in_array($request->branch_id,$tmp)){
				$startdate = new DateTime($promotion->startdate);
				$enddate = new DateTime($promotion->enddate);
				if($startdate > $today){
					continue;
				}
				if($enddate < $today){
					continue;
				}
				$message .= $promotion->description."<br>";
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
				"msg" => $message,
				"product" => $productname
			);
		}
		echo json_encode($sysmessage);
	}

	public function getPosPromotion(Request $request){
		$promotions = Notification_branch::where('branch_id','=',$request->branch_id)->get();
		$branchinuse = array();
		foreach($promotions as $promotion){
			array_push($branchinuse, $promotion->promotion_id);
		}


		$promotions = Promotion_notification::where("status",'=',1)->whereIn('id',$branchinuse)->get();

		$today = new DateTime();
		$productsresult = array();

		foreach($promotions as $promotion){
			$startdate = new DateTime($promotion->startdate);
			$enddate = new DateTime($promotion->enddate);
			if($startdate > $today){
				continue;
			}
			if($enddate < $today){
				continue;
			}
			$products = $promotion->getProduct;
			foreach($products as $product){
				array_push($productsresult,$product->product_id);
			}
		}
		echo json_encode($productsresult);
	}

	public function getTmpBranch(){
		echo "<table><tr><td>ชื่อแบรนด์</td><td>ชื่อสินค้า</td>";

		$branchs = Branch::all();
		$users = User::where("status",'=','1')->where('role','=','1')->get();
		$products = Products::where("status",'=','1')->get();

		foreach($branchs as $branch){
			echo "<td>".$branch->name."</td>";
		}

		echo "</tr>";
		foreach($users as $user){
			foreach($user->getProduct as $product){

				if($product->status!=1){
					continue;
				}
				foreach($product->getVariant as $pv){

					echo "<tr><td>".$user->brand_name."<td>";
					echo "<td>".$product->name."<td>";
					foreach($branchs as $branch){
						$stock = Stocks::where('branch_id','=',$branch->id)->where('product_id',$pv->id)->orderBy('id','desc')->first();
						if(isset($stock)){
							echo "<td>".$stock->sum."</td>";
						}else{
							echo "<td>0</td>";
						}
					}

				}
			}
		}


		echo '</table>';
		

	}

	public function posCheckStock(Request $request){
		$product_id = $request->product_id;
		$branchs = Branch::all();
		$result = array();
		$result["product_name"] = "";
		$result["result"] = array();
		$pv = Product_variant::where('id','=',$product_id)->first();
		if(isset($pv)){
			$product_name = $pv->getProduct->name;
			$result["product_name"] = $product_name;
			foreach($branchs as $branch){
				$stock = Stocks::where('branch_id','=',$branch->id)->where('product_id',$product_id)->orderBy('id','desc')->first();
				if(isset($stock)){
					array_push($result["result"],$branch->name."  ".$stock->sum);
				}else{
					array_push($result["result"],$branch->name."  0");
				}
			}
			$result["msg_code"] = 200;
			echo json_encode($result);
		}else{
			$result["msg_code"] = 500;
			echo json_encode($result);
		}
		
		
	}



//====EndPromoNotification====

//====Print Promtoion=====
	public function getPrintPromotion(){
		$brands = User::where('role','=','1')->where('status','=','1')->get();
		return view('admin.promotionprint.index',compact('brands'));
	}

	public function printPromotion(Request $request){
		$brand_name = $request->brand_name;
		$product_name = $request->product_name;
		$quantity = $request->quantity;
		$price = $request->price;
		return view('admin.promotionprint.print',compact('brand_name','product_name','quantity','price'));
	}
	public static function get_string_between($string, $start, $end){
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}

	//==== Count Stock =====
public function recieveNewProduct(Request $request){
	$user = Auth::user();
	if($user->role!=2){
		$sysmessage = array(
			"msgcode" => "500",
			"msg" => "ไม่มีสิทธิในการรับสินค้า"
		);
		return response()->json([
			'status' => 0, 
			'message' => $sysmessage
		]);
	}
	$purchase = Purchaseorders::where('id','=',$request->id)->first();
	

	$items = $purchase->getItem;
	$myArray = []; 
	foreach($items as $item){
		$laststock = Stocks::where("branch_id",'=',$purchase->branch_id)->where("product_id",'=',$item->product_id)->orderBy('id','desc')->first();
		$lastsum = 0;
		if(isset($laststock)){
			$lastsum = $laststock->sum;
		}
		$stock = new Stocks;
		$stock->product_id = $item->product_id;
		$stock->branch_id = $purchase->branch_id;
		$stock->type = "add";
		$stock->quantity = $item->quantity;
		$stock->sum = $lastsum+$item->quantity;
		$stock->remark = "รับสินค้าเข้าจากใบนำเข้าเลขที่ ".$purchase->id;
		$stock->save();
		array_push($myArray, $stock);
	}
	$purchase->admin_id = $user->id;
	$purchase->status = 1;
	$purchase->save();
	return response()->json([
		'status' => 1, 
		'message' => 'success',
		'type' => 'PO',
		'purchase'=> $purchase,
		'stock' => $myArray
	]);
}
}

