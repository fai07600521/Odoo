@extends('master')
@section('title','รายงานยอดขาย')
@section('style')
<link rel="stylesheet" href="/assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css">
<link rel="stylesheet" href="/assets/js/plugins/datatables/dataTables.bootstrap4.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/buttons/1.6.0/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="/assets/js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css">
@endsection
@section('content')
<div class="content">
	<h2 class="content-heading">รายงานยอดขาย</h2>
	<div class="col-12">
		<div class="block" style="padding: 20px;">
			<div class="row">
				<div class="col-12">
					<button>Export</button>

					<table id="table2excel" class="table data-table">
						<thead>
							<tr>
								<th colspan="12" class="text-center"><h2>รายงานยอดขายแบรนด์ {{$brand->brand_name}}</h2></th>
							</tr>
														<tr>
								<th colspan="12" class="text-center"><p style="font-size: 1.2em;"><b>ยอดขายตั้งแต่วันที่ :</b> {{$startdate}} - {{$enddate}}<br>สาขา :</b> {{$branch->name}}<br>GP: {{$gp}}</p></th>
							</tr>
							<tr>
								<th class="text-center">#</th>
								<th class="text-center">แบรนด์</th>
								<th class="text-center">สินค้า</th>
								<th class="text-center">ราคาในระบบ</th>
								<th class="text-center">ราคาขาย</th>
								<th class="text-center">จำนวนที่ขาย</th>
								<th class="text-center">รวมขาย</th>
								<th class="text-center">ส่วนลด</th>	
								<th class="text-center">ขายจริง</th>	
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
							$count = 0;
							?>
							@foreach($reportsum as $key=>$report)
							<?php


							$tmpprodid = \App\Http\Controllers\AdminController::get_string_between($key, "id", "|");
							
							$productdata = \App\Http\Controllers\BrandController::getProductData($tmpprodid);
							if(!isset($productdata)){
								continue;
							}
							
							$product = $productdata->getProduct;
							?>
							@if(Auth::user()->role!=2)
							@if($product->user_id!=Auth::user()->id)
							@continue
							@endif
							@else
							@if($product->user_id!=$brand_id)
							@continue
							@endif
							@endif
							<?php
							$count++;

							$realproductprice = $reportrealprice[$key];//ราคาจริง
							$sellprice = $report; //ราคาจริง*จำนวน
							$sellquantity = $reportquantity[$key];//จำนวนที่ขาย
							$sellinput = $reportsuminput[$key];//ราคาที่ขาย
							$vat = $sellinput-($sellinput/1.07);//หา VAT จากราคาที่ขาย
							$discount = $sellprice-$sellinput;//ส่วนลด
							$result = 0;
							if($report==0){
								$discountrate = 0;
							}else{
								$discountrate = $discount*100/$report;
							}
							
							$gpcalculate = $gp;

							if($discountrate >= 70){
								$gpcalculate = $gpcalculate*0.4;
							}else if($discountrate >= 50){
								$gpcalculate = $gpcalculate*0.6;
							}else if($discountrate >= 30){
								$gpcalculate = $gpcalculate*0.8;
							}

							if($brand->vat==0){
								$result = ($sellinput-$vat)-(($sellinput-$vat)*$gpcalculate/100);
							}else{
								$result = $sellinput-($sellinput*$gpcalculate/100);
							}
							?>
							<tr>
								<td class="text-center">{{$count}}</td>
								<td class="text-center">{{$product->getUser->brand_name}}</td>
								<td>{{$product->name}} ({{$productdata->variant}})</td>
								<td class="text-center">{{number_format($realproductprice,2)}}
								</td>
								<td class="text-center">{{number_format($sellinput/$sellquantity,2)}}
								</td>
								<td class="text-center">{{$sellquantity}}</td>
								<td class="text-center"> {{number_format($report,2)}}</td>
								<td class="text-center"> {{number_format($discount,2)}} ({{number_format($discountrate)}}%)</td>
								<td class="text-center">{{number_format($sellinput,2)}}</td>
								<td class="text-center">{{number_format($sellinput/1.07,2)}}</td>
								<td class="text-center">{{number_format($vat,2)}}</td>
								<td class="text-center">{{$gpcalculate}}</td>
								<td class="text-center">{{number_format($result,2)}}</td>
								<?php
								$sumquantity += $reportquantity[$key];
								$sumsell += $result;
								?>
							</tr>
							@endforeach
							@if($sumquantity!=0)
							<tr>
								<td class="text-center" colspan="5">รวม</td>
								<td class="text-center">{{$sumquantity}}</td>
								<td colspan="6"></td>
								<td class="text-center">{{number_format($sumsell,2)}}</td>
							</tr>
							@else
							<tr>
								<td colspan="9" class="text-center"><p>ไม่มียอดขายในวันดังกล่าว</p></td>
							</tr>
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
<script src="/assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.6.0/js/dataTables.buttons.min.js"></script>
<script src="/assets/js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="/assets/js/plugins/datatables/buttons/buttons.colVis.min.js"></script>
<script src="/assets/js/plugins/datatables/buttons/buttons.flash.min.js"></script>
<script src="/assets/js/plugins/datatables/buttons/buttons.html5.min.js"></script>
<script src="/assets/js/plugins/datatables/buttons/buttons.print.min.js"></script>

<script src="/assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="/assets/js/jquery.table2excel.js"></script>
<script type="text/javascript">
	$("#reportdaybtn").addClass("active");
$("button").click(function(){
  $("#table2excel").table2excel({
    name:"Report-{{$brand->brand_name}}",
    filename:"Report-{{$brand->brand_name}}.xls",
    fileext:"" 
  });
});


</script>
@endsection