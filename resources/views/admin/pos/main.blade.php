<!doctype html>
<html class="no-focus">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<title>{{$_ENV['APP_NAME']}} | POS</title>
	<meta name="description" content="GIST - Multibrand Store System">
	<meta name="author" content="Jirapat Hangjaraon">
	<meta property="og:title" content="GIST - Multibrand Store System">
	<meta property="og:site_name" content="GIST - Multibrand Store System">
	<meta property="og:description" content="GIST - Multibrand Store System for manage brand and sale system.">
	<meta property="og:type" content="website">
	<meta property="og:url" content="https://shop.castlec.in.th">
	<link rel="shortcut icon" href="/favicon.png">
	<link href="//fonts.googleapis.com/css?family=Kanit&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="/assets/js/plugins/sweetalert2/sweetalert2.min.css">
	<link rel="stylesheet" id="css-main" href="/assets/css/codebase.min.css">
	<link rel="stylesheet" id="css-main" href="/assets/css/pos.css">
	<link rel="stylesheet" href="/assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css">
	<link rel="stylesheet" href="/assets/js/plugins/select2/css/select2.min.css">
	<style type="text/css">
		.haspromotion{
			background-color: #dfddff;
		}
	</style>
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div id="mainleft" class="col-3">
				<div class="row">
					<div id="mainprice" class="col-12">
						<div id="mainorder">
							<h4>สมาชิก: <font id="membershow">ขายทั่วไป</font></h4>
							<h4 class="text-center">รายการขาย</h4>
							<table style="width: 100%;">
								<thead>
									<tr>
										<th class="text-center"></th>
										<th class="text-center">ราคา</th>
										<th class="text-center">จำนวน</th>
										<th class="text-right">รวม</th>
										<th class="text-right" style="width: 80px;"></th>
									</tr>
								</thead>
								<tbody id="tableorder">

								</tbody>
							</table>
							<br>
							<hr style="width: 100%; border: 1px solid #ccc;">
							<h4 class="text-center">ส่วนลด</h4>
							<table style="width: 100%">
								<tbody id="tablepromotion">
									
								</tbody>
							</table>
						</div>
					</div>
					<div id="sumprice" class="col-12 text-right">
						<h1 id="sumpricetxt">0</h1>
					</div>
					<div id="barcodein" class="col-12">
						<input class="form-control" type="text" placeholder="กรอกบาร์โค๊ด" id="barcodeinput">
					</div>
					<div id="submitorder" class="col-12">
						<br>
						<button type="button" class="btn btn-lg btn-success btn-block" data-toggle="modal" data-target="#modal-checkout"><i class="si si-briefcase"></i> ชำระเงิน</button>

						<button type="button" class="btn btn-lg btn-primary btn-block" data-toggle="modal" data-target="#modal-promotion"><i class="fa fa-gift"></i> โปรโมชั่น</button>

						<button class="btn btn-lg btn-warning btn-block" data-toggle="modal" data-target="#modal-member">
							<i class="fa fa-user"></i> สมาชิก
						</button>
					</div>

				</div>
			</div>
			<div id="mainright" class="col-9">
				<div class="row">
					<div id="mainbrandbar" class="col-2" style="height: 100vh; overflow: scroll;">
						<ul class="nav nav-tabs nav-tabs-block" data-toggle="tabs" role="tablist">
							<li class="nav-item" style="width: 100%;">
								<a class="nav-link active brandallbtn" href="#allproduct">ทั้งหมด</a>
							</li>
							@foreach($brands as $brand)
							<li class="nav-item" style="width: 100%;">
								<a class="nav-link brandnavbtn" data-brand="{{$brand->id}}" href="#brand{{$brand->id}}">{{$brand->brand_name}}</a>
							</li>
							@endforeach
						</ul>
					</div>
					<div id="mainproduct" class="col-10 tab-content">
						<div class="tab-pane active" id="allproduct" role="tabpanel">
							<h4 class="font-w400">สินค้า</h4>
							<div class="row">
								<div class="col-12">
									<div class="form-group row">
										<label class="col-12">ค้นหาสินค้า</label>
										<div class="col-10">
											<input type="text" placeholder="ค้นหาสินค้า" id="searchitem" class="form-control">
										</div>
										<div class="col-2">
											<button id="cancelsearch" class="btn btn-danger btn-block">ยกเลิกค้นหา</button>
										</div>
									</div>
								</div>
								@foreach($products as $product)
								@foreach($product->getVariant as $variant)
								<?php
								$price = 0;
								$promotionchk = 0;
								$brand = $product->getUser;
								if($product->discount_price!=0){
									if($product->discount_type==1){
										$price = $product->discount_price;
									}else{
										$price = $product->price-($product->price*($product->discount_price/100));
									}
									$promotionchk =1 ; 
								}else{
									$price = $product->price;
								}
								?>
								<div id="boxprod{{$variant->id}}" class="col-3 productbox productbrand{{$product->user_id}}">
									<div class="block">
										<a id="dataproduct{{$variant->id}}" class="block block-link-shadow text-center product" href="#" data-id="{{$variant->id}}" data-price="{{$price}}" data-name="{{$product->name}} ({{$variant->variant}})">
											<div class="block-content">
												<img src="/assets/system/nopic.png" style="height: 80px;">
												<p class="font-w600">

													<b>ชื่อสินค้า: </b>{{$product->name}}<br> <b>คุณลักษณะ: </b>{{$variant->variant}}<br>
													<b>แบรนด์: </b>{{$brand->brand_name}}<br>
													<font style="{{$promotionchk==1?'color:green;':''}}">
														<b>ราคา: </b><br>{{number_format($price,2)}} บาท</font><br>
													</p>
												</div>
											</a>
											<a class="btn btn-block btn-secondary" href="javascript:checkStock('{{$variant->id}}')" style="font-size: 1.5em;"><i class="si si-magnifier"></i></a>
										</div>
									</div>
									@endforeach
									@endforeach
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="modal-checkout" tabindex="-1" role="dialog" aria-labelledby="modal-fadein" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="block block-themed block-transparent mb-0">
						<div class="block-header bg-primary-dark">
							<h3 class="block-title">ชำระเงิน</h3>
							<div class="block-options">
								<button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
									<i class="si si-close"></i>
								</button>
							</div>
						</div>
						<div class="block-content">
							<div class="form-group row">
								<label class="col-12">ยอดที่ต้องชำระ</label>
								<div class="col-12">
									<input style="font-size:4.5em;" id="checkout-price" type="text" disabled="" value="" class="form-control">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-12">รับเงินมา</label>
								<div class="col-12">
									<input style="font-size:4.5em;" id="checkout-recieve" type="number" value="0" class="form-control">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-12">เงินทอน</label>
								<div class="col-12">
									<input style="font-size:4.5em;" id="checkout-change" type="number" value="0" class="form-control" disabled="">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-12">เลือกประเภทการชำะรเงิน</label>
								<div class="col-12">
									<select id="payment" class="form-control">
										@foreach($payments as $payment)
										<option value="{{$payment->id}}">{{$payment->name}}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-12">เลือกภาษาของใบเสร็จ</label>
								<div class="col-12">
									<select id="language-slip" class="form-control">
										<option value="TH">ไทย</option>
										<option value="EN">English</option>
									</select>
								</div>
							</div>

							<div class="form-group row">
								<div class="col-6 text-left">
									<i id="preload" style="display: none;" class="fa fa-2x fa-asterisk fa-spin"></i>
								</div>
								<div class="col-6 text-right">
									<button id="checkoutbtn" class="btn btn-lg btn-block btn-primary"><i class="fa fa-money"></i> ชำระเงิน/พิมพ์สลิป</button>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-alt-secondary" data-dismiss="modal">ปิด</button>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="modal-promotion" tabindex="-1" role="dialog" aria-labelledby="modal-fadein" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="block block-themed block-transparent mb-0">
						<div class="block-header bg-primary-dark">
							<h3 class="block-title">โปรโมชั่น</h3>
							<div class="block-options">
								<button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
									<i class="si si-close"></i>
								</button>
							</div>
						</div>
						<div class="block-content">

							<div class="form-group row">
								<label class="col-12">เลือกโปรโมชั่น</label>
								<div class="col-12">
									<select id="prmotion-selector" class="form-control">
										<option value="0"> เลือกโปรโมชั่น</option>
										@foreach($promotions as $promotion)
										<option value="{{$promotion->id}}">{{$promotion->name}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div id="promotion-customdiscountbox" class="form-group row" style="display: none;">
								<label class="col-12">กรอกจำนวนเงินที่จะลด</label>
								<div class="col-12">
									<input id="promotion-customdiscount" type="text" value="" class="form-control">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-12">ยอดที่ต้องชำระเดิม</label>
								<div class="col-12">
									<input id="promotion-sumprice" type="text" disabled="" value="" class="form-control">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-12">ส่วนลดโปรโมชั่น</label>
								<div class="col-12">
									<input id="promotion-discount" type="number" value="0" class="form-control" disabled="">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-12">คงเหลือ</label>
								<div class="col-12">
									<input id="promotion-endprice" type="number" value="0" class="form-control" disabled="">
								</div>
							</div>
							<div class="form-group row">
								<div class="col-6"></div>
								<div class="col-6 text-right">
									<button id="promotionbtn" class="btn btn-lg btn-block btn-primary"><i class="fa fa-cogs"></i> ยืนยัน</button>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-alt-secondary" data-dismiss="modal">ปิด</button>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="modal-editprice" tabindex="-1" role="dialog" aria-labelledby="modal-fadein" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="block block-themed block-transparent mb-0">
						<div class="block-header bg-primary-dark">
							<h3 class="block-title">แก้ไขราคา</h3>
							<div class="block-options">
								<button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
									<i class="si si-close"></i>
								</button>
							</div>
						</div>
						<div class="block-content">
							<h1 id="editprice-name"></h1><br>
							<div class="form-group row">
								<label class="col-12">ราคาปัจจุบัน</label>
								<div class="col-12">
									<input id="editprice-price" type="number" step="0.2" value="0" class="form-control">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-12">เลือกรูปแบบการปรับราคา</label>
								<div class="col-12">
									<select id="editprice-option" class="form-control">
										<option value="0"> รูปแบบ</option>
										<option value="1">ลด 5%</option>
										<option value="2">ลด 10%</option>
										<option value="3">ลด 15%</option>
										<option value="4">ลด 20%</option>
										<option value="5">ลด 25%</option>
										<option value="6">ลด 30%</option>
										<option value="7">ลด 35%</option>
										<option value="8">ลด 40%</option>
										<option value="9">ลด 45%</option>
										<option value="10">ลด 50%</option>
										<option value="11">ลด 55%</option>
										<option value="12">ลด 60%</option>
										<option value="13">1 แถม 1</option>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-12 text-right">
									<button id="editprice-btn" class="btn btn-primary">ยืนยัน</button>
								</div>
							</div>


						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-alt-secondary" data-dismiss="modal">ปิด</button>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="modal-promotionnotification" tabindex="-1" role="dialog" aria-labelledby="modal-fadein" aria-hidden="true" style="display: none;">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="block block-themed block-transparent mb-0">
						<div class="block-header bg-primary-dark">
							<h3 id="promotionnotificationproducttxt" class="block-title"></h3>
							<div class="block-options">
								<button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
									<i class="si si-close"></i>
								</button>
							</div>
						</div>
						<div class="block-content">
							<p id="promotionnotificationtxt"></p>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="modal-promotionauto" tabindex="-1" role="dialog" aria-labelledby="modal-fadein" aria-hidden="true" style="display: none;">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="block block-themed block-transparent mb-0">
						<div class="block-header bg-primary-dark">
							<h3 id="promotionautoproducttxt" class="block-title"></h3>
							<div class="block-options">
								<button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
									<i class="si si-close"></i>
								</button>
							</div>
						</div>
						<div class="block-content">
							<p id="promotionautotxt"></p>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="modal-member" tabindex="-1" role="dialog" aria-labelledby="modal-fadein" aria-hidden="true" style="display: none;">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="block block-themed block-transparent mb-0">
						<div class="block-header bg-primary-dark">
							<h3 class="block-title">เลือกสมาชิก</h3>
							<div class="block-options">
								<button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
									<i class="si si-close"></i>
								</button>
							</div>
						</div>
						<div class="block-content">
							<div class="form-group row">
								<label class="col-12">เลือกสมาชิก</label>
								<div class="col-12">
									<select id="memberselect" style="width:100%;">
										<option value="0">เลือกสมาชิก</option>
										@foreach($members as $member)
										<option value="{{$member->id}}">{{$member->name}} ({{$member->detail}})</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-alt-secondary" data-dismiss="modal">ปิด</button>
						<button type="button" class="btn btn-alt-success" data-dismiss="modal">ยืนยัน</button>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="checkstock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">จำนวนคงเหลือของ <font id="productnamecheckstock"></font> แต่ละสาขา</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div id="prodcutcheckstockresult" class="modal-body">

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>

	</body>
	<script src="/assets/js/codebase.core.min.js"></script>
	<script src="/assets/js/codebase.app.min.js"></script>
	<script src="/assets/js/plugins/chartjs/Chart.bundle.min.js"></script>
	<script src="/assets/js/plugins/bootstrap-notify/bootstrap-notify.min.js"></script>
	<script src="/assets/js/plugins/es6-promise/es6-promise.auto.min.js"></script>
	<script src="/assets/js/plugins/sweetalert2/sweetalert2.min.js"></script>
	<script src="/assets/js/plugins/select2/js/select2.full.min.js"></script>
	<script type="text/javascript">
		var product = [];
		var price = [];
		var sum = 0;
		var recieve = 0;
		var branch_id = '{{$branch_id}}';
		var member_id = 0;
		var promotion_id = 0;
		var discount = 0;
		var sumdiscount = 0;
		var editprice = 0;
		var itempromotion = [];
		$(document).ready(function() {
			highlightPromotion();

			$("#memberselect").on('change',function(){
				member_id = $("#memberselect").val();
				$("#membershow").html($("#memberselect option:selected").html());
			});


			$(".brandallbtn").on('click',function(){
				$(".productbox").show();
			});
			$(".brandnavbtn").on('click',function(){
				brand_id = $(this).attr('data-brand');
				$(".productbox").hide();
				$(".productbrand"+brand_id).show();
			});

			var productpack = [];
			$(".product").on('click',function(e){
				productin = $(this);
				addProduct(productin.attr('data-name'),productin.attr('data-price'),productin.attr('data-id'));
			});


			$('#barcodeinput').keyup(function(e){
				if(e.keyCode == 13)
				{
					barcode = $('#barcodeinput').val();
					$.ajax({
						type: "POST",
						dataType: "json",
						data: {barcode:barcode},
						url: "/admin/pos/getbarcode",
						success: function(barproduct){
							if(barproduct.msg=="Found"){
								addProduct(barproduct.product_name,barproduct.product_price,barproduct.product_id);
								$("#barcodeinput").val("");
							}else{
								$("#barcodeinput").val("");
								$.notify({
									message: 'ไม่พบสินค้า' 
								},{
									type: 'danger'
								});
							}
						}
					});
				}
				$("#barcodeinput").focus();
			});

			$("#checkout-recieve").keyup(function(e){
				recieve = $("#checkout-recieve").val();
				$("#checkout-change").val(parseFloat(sum)-parseFloat(recieve));
			});

			$("#checkoutbtn").on('click',function(){
				$("#checkoutbtn").fadeOut();
				$("#preload").fadeIn();
				payment_type = $("#payment").val();
				orders = [];
				suminput = [];
				productkey = product.keys();
				priceinput = price.keys();

				for(x of productkey){
					if(product[x]!=null){
						orders.push(x+"|"+product[x]);
					}
				}
				for(x of priceinput){
					if(price[x]!=null){
						suminput.push(x+"|"+price[x]);
					}
				}

				$.ajax({
					type: "POST",
					data: {orders:orders,recieve:recieve,payment_type:payment_type,branch_id:branch_id,member_id:member_id,promotion_id:promotion_id,discount:discount,suminput:suminput,itempromotion:itempromotion},
					url: "/admin/pos/makeorder",
					success: function(msg){
						if(msg!="Failed"){
							sum = 0;
							product = [];
							recieve = 0;
							member_id = 0;
							promotion_id = 0;
							sumdiscount = 0;
							discount = 0;
							price = [];
							editprice = 0;
							itempromotion = [];
							member_id = 0;
							$("#membershow").html("ขายทั่วไป");
							$("#sumpricetxt").html(numberWithCommas(0));
							$("#checkout-price").val(0);
							$("#checkout-recieve").val(0);
							$("#checkout-change").val(0);
							$("#promotion-endprice").val(0);
							$("#modal-checkout").modal("hide");
							$("#tableorder").html("");
							$("#tablepromotion").html("");
							$("#memberselect").val(0);
							
							window.open("{{$_ENV['APP_URL']}}/admin/pos/slip/"+msg+"?lang="+$("#language-slip").val());
							Swal.fire({
								type: 'success',
								title: 'บันทึกข้อมูลเรียบร้อย',
								text: 'ระบบบันทึกข้อมูลการขายเรียบร้อยแล้ว'
							})
							$("#language-slip").val("TH")
							$("#checkoutbtn").fadeIn();
						}else{
							Swal.fire({
								type: 'error',
								title: 'ขัดข้อง',
								text: 'ระบบบันทึกข้อมูลการขายไม่สำเร็จ'
							})
						}
					}
				});
				$("#preload").fadeOut();
			});

			$("#editprice-option").on('change',function(){
				oldprice = price[editprice];
				switch($("#editprice-option").val()){
					case "1":
					oldprice = oldprice-(oldprice*5/100);
					itempromotion[editprice] = 1;
					break;
					case "2":
					oldprice = oldprice-(oldprice*10/100);
					itempromotion[editprice] = 2;
					break;
					case "3":
					oldprice = oldprice-(oldprice*15/100);
					itempromotion[editprice] = 3;
					break;
					case "4":
					oldprice = oldprice-(oldprice*20/100);
					itempromotion[editprice] = 4;
					break;
					case "5":
					oldprice = oldprice-(oldprice*25/100);
					itempromotion[editprice] = 5;
					break;
					case "6":
					oldprice = oldprice-(oldprice*30/100);
					itempromotion[editprice] = 6;
					break;
					case "7":
					oldprice = oldprice-(oldprice*35/100);
					itempromotion[editprice] = 7;
					break;
					case "8":
					oldprice = oldprice-(oldprice*40/100);
					itempromotion[editprice] = 8;
					break;
					case "9":
					oldprice = oldprice-(oldprice*45/100);
					itempromotion[editprice] = 9;
					break;
					case "10":
					oldprice = oldprice-(oldprice*50/100);
					itempromotion[editprice] = 10;
					break;
					case "11":
					oldprice = oldprice-(oldprice*55/100);
					itempromotion[editprice] = 11;
					break;
					case "12":
					oldprice = oldprice-(oldprice*60/100);
					itempromotion[editprice] = 12;
					break;
					case "13":
					oldprice = oldprice-(oldprice/product[editprice]);
					itempromotion[editprice] = 13;
					break;
				}
				$("#editprice-price").val(oldprice);
			});

			$("#editprice-btn").on('click',function(){
				sum = sum-price[editprice];
				price[editprice]= $("#editprice-price").val();
				sum = parseFloat(sum)+parseFloat(price[editprice]);
				updateSum();
				$("#sumprod"+editprice).html(price[editprice]);
				editprice = 0;
				$("#modal-editprice").modal('hide');
			});

			$("#prmotion-selector").on('change',function(){
				input_discount = 0;
				promotion_id = $("#prmotion-selector").val();
				if($("#prmotion-selector").val()==1){
					$("#promotion-customdiscountbox").fadeIn();
				}else{
					$("#promotion-customdiscountbox").fadeOut();
				}
				getPromotion(promotion_id,input_discount);
			});

			$("#promotion-customdiscount").on('change',function(){
				input_discount = $("#promotion-customdiscount").val();
				$("#promotion-discount").val(input_discount);
				getPromotion("1",parseFloat(input_discount));
			});

			$("#promotionbtn").on('click',function(){
				sum = sum - discount;
				promotion_name = $( "#prmotion-selector option:selected" ).text();
				updateSum();
				tablepromotion = `<tr id="promotion-row"><td>${promotion_name}</td><td class="text-center">${discount}</td><td>&nbsp;&nbsp;<a href="javascript:delPromotion()" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a></td></tr>`;
				$("#tablepromotion").append(tablepromotion);
				$("#modal-promotion").modal('hide');

			});




			$("#checkout-price").on("change",function(){
				getPromotion(promotion_id,input_discount);
			});
			$("#barcodeinput").focus();

			$("#searchitem").keyup(function(e){
				if(e.keyCode == 13)
				{
					if($("#searchitem").val()!=null || $("#searchitem").val()!=""){
						$(".productbox").hide();
						searchItem($("#searchitem").val());
					}
				}
			});
			$("#cancelsearch").on('click',function(){
				$("#searchitem").val();
				$(".productbox").show();
			});


		});
function geteditPrice(product_id){
	editprice = product_id;
	$("#editprice-name").html($("#dataproduct"+product_id).attr('data-name'));
	$("#editprice-price").val(price[product_id]);
	$("#editprice-option").val(0);
	$("#modal-editprice").modal();
}
function searchItem(name){
	$.ajax({
		type: "POST",
		data: {name:name},
		url: "/admin/pos/searchorder",
		dataType: 'json',
		success: function(data){
			$.each(data, function(index, item) {
				$("#boxprod"+item.id).show();
			});
		}
	});
}
function checkPromotion(product_id){
	$.ajax({
		type: "POST",
		data: {product_id:product_id,branch_id:branch_id},
		url: "/admin/promonotification/checkpromotion",
		dataType: 'json',
		success: function(data){
			if(data.msgcode=="200"){
				$("#promotionnotificationtxt").html(data.msg);
				$("#promotionnotificationproducttxt").html(data.product);
				$("#modal-promotionnotification").modal();
			}

		}
	});


}
function highlightPromotion(){
	$.ajax({
		type: "GET",
		url: "/admin/promonotification/getpospromotion/"+branch_id,
		dataType: 'json',
		success: function(data){
			$.each(data, function(index, item) {
				$("#dataproduct"+item).addClass("haspromotion");
			});
		}
	});
}
function delPromotion(){
	$("#promotion-row").remove();
	sum = sum+discount;
	discount = 0;
	promotion_id = 0;
	updateSum();
}
function getPromotion(promotion_id,input_discount){
	sumdiscount = 0;
	switch(promotion_id) {
		case "1":
					//custom discount
					sumdiscount = sum - input_discount;
					discount = input_discount;
					break;
					case "2":
					//5%
					discount = (sum*5/100);
					sumdiscount = sum - discount;
					break;
					case "3":
					//10%
					discount = (sum*10/100);
					sumdiscount = sum - discount;
					break;
					case "4":
					//15%
					discount = (sum*15/100);
					sumdiscount = sum - discount;
					break;
					case "5":
					//20%
					discount = (sum*20/100);
					sumdiscount = sum - discount;
					break;
					case "6":
					//25%
					discount = (sum*25/100);
					sumdiscount = sum - discount;
					break;
					case "7":
					//30%
					discount = (sum*30/100);
					sumdiscount = sum - discount;
					break;
					case "8":
					//35%
					discount = (sum*35/100);
					sumdiscount = sum - discount;
					break;
					case "9":
					//40%
					discount = (sum*40/100);
					sumdiscount = sum - discount;
					break;
					case "10":
					//45%
					discount = (sum*45/100);
					sumdiscount = sum - discount;
					break;
					case "11":
					//50%
					discount = (sum*50/100);
					sumdiscount = sum - discount;
					break;
					case "12":
					//55%
					discount = (sum*55/100);
					sumdiscount = sum - discount;
					break;
					case "13":
					//60%
					discount = (sum*60/100);
					sumdiscount = sum - discount;
					break;
					case "14":
					//65%
					discount = (sum*65/100);
					sumdiscount = sum - discount;
					break;
					case "15":
					//70%
					discount = (sum*70/100);
					sumdiscount = sum - discount;
					break;
					case "16":
					//75%
					discount = (sum*75/100);
					sumdiscount = sum - discount;
					break;
					case "17":
					//80%
					discount = (sum*80/100);
					sumdiscount = sum - discount;
					break;
					case "18":
					//85%
					discount = (sum*85/100);
					sumdiscount = sum - discount;
					break;
					case "19":
					//90%
					discount = (sum*90/100);
					sumdiscount = sum - discount;
					break;
					case "20":
					//95%
					discount = (sum*95/100);
					sumdiscount = sum - discount;
					break;
				}
				$("#promotion-discount").val(discount);
				$("#promotion-endprice").val(sumdiscount);
			}
			function numberWithCommas(x) {
				return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			}
			function updateSum(){
				$("#sumpricetxt").html(numberWithCommas(sum));
				$("#checkout-price").val(sum);
				$("#promotion-sumprice").val(sum);
			}
			function delProduct(product_id,product_price){
				$("#rowprod"+product_id).remove();
				sum = parseFloat(sum) - parseFloat(price[product_id]);
				product[product_id]=0;
				updateSum();
			}

			
			var promoprice = 0;
			var showmodal = 0;
			var modalmsg = "";
			function addProduct(product_name,product_price,product_id){
				promoprice = 0;
				showmodal = 0;
				//Check Main Promotion
				$.ajax({
     type: "POST",
     data: {product_id:product_id,branch_id:branch_id},
     url: "/admin/promotions/checkpromotion",
     'async': false,
     dataType: 'json',
     success: function(data){
      if(data.msgcode=="200"){
       showmodal = 1;
       promoprice = data.promoprice;
       modalmsg = data.msg;
      }
     }
    });
				if(promoprice!=0){
					product_price = promoprice;
				}
				if(showmodal==1){
					$("#promotionautotxt").html(modalmsg);
					$("#promotionautoproducttxt").html(product_name);
					$("#modal-promotionauto").modal();
				}

					//End Check Main Promotion
					sum = parseFloat(sum)+ parseFloat(product_price);
					updateSum();
					if(product[product_id]==null||product[product_id]=='0'){
						product[product_id] = 1;
						price[product_id] = product_price;
						tableorder = `<tr id="rowprod${product_id}"><td>${product_name}</td><td class="text-center">${product_price}</td><td class="text-center" id="quantity${product_id}">1</td><td class="text-right" id="sumprod${product_id}">${product_price}</td><td>&nbsp;&nbsp;<a class="btn btn-sm btn-warning" href="javascript:geteditPrice('${product_id}');"><i class="fa fa-cogs"></i></a><a href="javascript:delProduct('${product_id}','${product_price}')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a></td></tr>`;
						$("#tableorder").append(tableorder);
						checkPromotion(product_id);
					}else{
						product[product_id] = product[product_id] +1;
						price[product_id] = parseFloat(price[product_id])+parseFloat(product_price);
						$("#quantity"+product_id).html(product[product_id]);
						$("#sumprod"+product_id).html(price[product_id]);
					}


				}
				function checkStock(product_id){
					$("#productnamecheckstock").html("");
					$("#prodcutcheckstockresult").html("");
					$.ajax({
						type: "POST",
						data: {product_id:product_id},
						url: "/admin/stock/poscheck",
						dataType: 'json',
						success: function(data){
							if(data.product_name!=""){
								$("#productnamecheckstock").html(data.product_name);
								$.each(data.result, function(index, item) {
									$("#prodcutcheckstockresult").append(item+"<br>");
								});

								$("#checkstock").modal();
							}else{
								alert('ไม่พบสินค้าในระบบ');
							}

						}
					});
				}

				function checkMainPromotion(){

				}

				$('#memberselect').select2();

			</script>

			</html>