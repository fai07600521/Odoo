@extends('master')
@section('title','รายงานยอดขาย')
@section('content')
<div class="content">
	<h2 class="content-heading">รายงานยอดขาย</h2>
	<div class="col-12">
		<div class="block" style="padding: 20px;">
			<div class="row">
				<div class="col-12">
					<button>Export</button>
					<table id="table2excel" class="table">
						<thead>
							<tr>
								<th class="text-center" colspan="7"><h2>รายงานยอดขาย จาก</h2></th>
							</tr>
							<tr>
								<th class="text-center" colspan="7"><p style="font-size: 1.2em;"><b>ยอดขายตั้งแต่วันที่ :</b> {{$startdate}} - {{$enddate}}<br>สาขา :</b> {{$branch->name}}</p></th>
							</tr>
							<tr>
								<th class="text-center">#</th>
								<th class="text-center">แบรนด์</th>
								<th class="text-center">สินค้า</th>
								<th class="text-center">ราคา</th>
								<th class="text-center">จำนวนที่ขาย</th>
								<th class="text-center">รวมขาย</th>
								<th class="text-center">ส่วนลด</th>
								<th class="text-center">คงเหลือ</th>
								<th class="text-center">ยอดขายก่อน VAT</th>	
								<th class="text-center">VAT</th>
								<th class="text-center">คำนวณ GP ที่</th>
								<th class="text-center">ยอดขายหลังหัก GP</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$sumquantity = 0;
							$sumsell = 0;
							$sumsellAfterGP = 0;
							$count = 1;

							?>
							@foreach($reportsum as $key=>$report)
							<?php
							$productdata = \App\Http\Controllers\BrandController::getProductData($key);
							$sellprice = $report; //ราคาจริง*จำนวน
							$sellinput = $reportsuminput[$key];//ราคาที่ขาย
							$vat = $sellinput-($sellinput/1.07);//หา VAT จากราคาที่ขาย
							$discount =$sellprice-$sellinput;//ส่วนลด
							$result = 0;
							if($report==0){
								$discountrate = 0;
							}else{
								$discountrate = $discount*100/$report;
							}

							$gpcalculate = $gp;

							if($discountrate >= 70){
								$gpcalculate = $gpcalculate*0.75;
							}else if($discountrate >= 50){
								$gpcalculate = $gpcalculate*0.85;
							}else if($discountrate >= 30){
								$gpcalculate = $gpcalculate*0.95;
							}

							if($user->vat==0){
								$result = ($sellinput-$vat)-(($sellinput-$vat)*$gpcalculate/100);
							}else{
								$result = $sellinput-($sellinput*$gpcalculate/100);
							}

							if(!isset($productdata)){
								continue;
							}
							$product = $productdata->getProduct;
							?>
							@if(Auth::user()->role!=2)
							@if($product->user_id!=Auth::user()->id)
							@continue
							@endif
							@endif
							<?php
							$count++;
							?>
							<tr>
								<td class="text-center">{{$count}}</td>
								<td>{{$product->getUser->brand_name}}</td>
								<td>{{$product->name}} ({{$productdata->variant}})</td>
								<td class="text-center">{{number_format($product->price,2)}}
								</td>
								<td class="text-center">{{$reportquantity[$key]}}</td>
								<td class="text-center"> {{number_format($report,2)}}</td>
								<td class="text-center"> {{number_format($report-$reportsuminput[$key],2)}} ({{number_format($discountrate,2)}}%)</td>
								<td class="text-center">{{number_format($reportsuminput[$key])}}</td>
								<td class="text-center">{{number_format($sellinput/1.07,2)}}</td>
								<td class="text-center">{{number_format($vat,2)}}</td>
								<td class="text-center">{{$gpcalculate}}</td>
								<td class="text-center">{{number_format($result,2)}}</td>
								<?php
								$sumquantity += $reportquantity[$key];
								$sumsell += $reportsuminput[$key];
								$sumsellAfterGP += $result;
								?>
							</tr>
							@endforeach
							@if($sumquantity!=0)
							<tr>
								<td class="text-center" colspan="4">รวม</td>
								<td class="text-center">{{number_format($sumquantity,2)}}</td>
								<td colspan="2"></td>
								<td class="text-right">{{number_format($sumsell,2)}}</td>
								<td colspan="3"></td>
								<td class="text-center">{{number_format($sumsellAfterGP,2)}}</td>
							</tr>
							@if($sumdiscount!=0&&Auth::user()->role=="2")
							<tr>
								<td class="text-center" colspan="4" style="vertical-align: middle;">ส่วนลด</td>
								
								<td colspan="2" >@foreach($pmethods as $method) {{$method->name}} <br> @endforeach</td>
								<td style="text-align: right;">@foreach($pmethods as $method) {{$discountpayment[$method->id]}} <br>@endforeach</td>
								<td class="text-right" style="vertical-align: middle;">{{number_format($sumdiscount,2)}}</td>
							</tr>
							<tr>
								<td class="text-center" colspan="4">คงเหลือ</td>
								<td colspan="3"></td>
								<td class="text-right">{{number_format($sumsell-$sumdiscount,2)}}</td>
							</tr>
							@endif
							@else
							<tr>
								<td colspan="7" class="text-center"><p>ไม่มียอดขายในวันดังกล่าว</p></td>
							</tr>
							@endif
							@if(Auth::user()->role==2)
							<tr>
								<td colspan="7" class="text-center"><h2>ยอดขายตามการชำระเงิน</h2></td>
							</tr>
							
							<tr>
								<td colspan="7" class="text-center" style="font-weight: bold;">ช่องทาง</td>
								<td  class="text-center" style="font-weight: bold;">จำนวนเงิน</td>
							</tr>
							@foreach($pmethods as $method)
							<tr>
								<td colspan="7" class="text-center">{{$method->name}}</td>
								<td class="text-right">{{number_format($paymentincome[$method->id],2)}}</td>
							</tr>
							@endforeach
							@endif
						</tbody>
					</table>
				</div>
			</div>
			
		</div>
	</div>
</div>
@endsection
@section('script')
<script type="text/javascript">
	$("#reportdaybtn").addClass("active");
</script>
<script src="/assets/js/jquery.table2excel.js"></script>
<script type="text/javascript">
	$("#reportdaybtn").addClass("active");
	$("button").click(function(){
		$("#table2excel").table2excel({
			name:"Report-{{$branch->name}}",
			filename:"Report-{{$branch->name}}.xls",
			fileext:"" 
		});
	});


</script>
@endsection