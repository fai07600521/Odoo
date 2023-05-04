@extends('master')
@section('title','ภาพรวมระบบ')
@section('style')
@endsection
@section('content')
<div class="content">
	<div class="row js-appear-enabled animated fadeIn" data-toggle="appear">
		<div class="col-12 col-xl-4">
			<a class="block block-rounded block-bordered block-link-shadow" href="/products">
				<div class="block-content block-content-full clearfix">
					<div class="float-right mt-15 d-none d-sm-block">
						<i class="si si-bag fa-2x text-primary-light"></i>
					</div>
					<div class="font-size-h3 font-w600 text-primary js-count-to-enabled" data-toggle="countTo" data-speed="10" data-to="{{sizeOf($products)}}">{{sizeOf($products)}}</div>
					<div class="font-size-sm font-w600 text-uppercase text-muted">จำนวนสินค้าในระบบ</div>
				</div>
			</a>
		</div>
		<div class="col-12 col-xl-4">
			<a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
				<div class="block-content block-content-full clearfix">
					<div class="float-right mt-15 d-none d-sm-block">
						<i class="si si-wallet fa-2x text-earth-light"></i>
					</div>
					<div class="font-size-h3 font-w600 text-earth"><span data-toggle="countTo" data-speed="10" data-to="{{number_format($todayincome,2)}}" class="js-count-to-enabled">{{number_format($todayincome,2)}}  บาท</span></div>
					<div class="font-size-sm font-w600 text-uppercase text-muted">ยอดขายรายวัน</div>
				</div>
			</a>
		</div>
		<div class="col-12 col-xl-4">
			<a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
				<div class="block-content block-content-full clearfix">
					<div class="float-right mt-15 d-none d-sm-block">
						<i class="si si-wallet fa-2x text-elegance-light"></i>
					</div>
					<div class="font-size-h3 font-w600 text-elegance js-count-to-enabled" data-toggle="countTo" data-speed="10" data-to="{{number_format($monthincome,2)}}">{{number_format($monthincome,2)}} บาท</div>
					<div class="font-size-sm font-w600 text-uppercase text-muted">ยอดขายรายเดือน</div>
				</div>
			</a>
		</div>
	</div>

	<div class="row js-appear-enabled animated fadeIn" data-toggle="appear">
		<div class="col-md-6">
			<div class="block block-rounded block-bordered">
				<div class="block-header block-header-default border-b">
					<h3 class="block-title">10 รายการขายล่าสุด</h3>
				</div>
				<div class="block-content">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th class="text-center">สินค้า</th>
								<th class="text-center">จำนวน</th>
								<th class="text-center">หน่วยนับหลัก</th>
								<th class="text-center">ราคา</th>
								<th class="text-center">รวม</th>
							</tr>
						</thead>
						<tbody>
							@foreach($lastest as $item)
							<?php
							$productdata = \App\Http\Controllers\BrandController::getProductData($item->product_id);
							if(!isset($productdata)){
								continue;
							}
							?>
							<tr>
								<td class="text-left">{{$productdata->getProduct->name}} ({{$productdata->variant}})</td>
								<td class="text-center">{{$item->quantity}} </td>
								<td class="text-center">{{$productdata->getProduct->getUnit->name}}</td>
								<td class="text-right">{{number_format($item->price,2)}}</td>
								<td class="text-right">{{number_format($item->price*$item->quantity,2)}}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- <div class="col-md-6">
			<div class="block block-rounded block-bordered">
				<div class="block-header block-header-default border-b">
					<h3 class="block-title">10 อันดับสินค้าขายดี</h3>
				</div>
				<div class="block-content">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th class="text-center">สินค้า</th>
								<th class="text-center">จำนวนที่ขายได้</th>
								<th class="text-center">หน่วยนับหลัก</th>
							</tr>
						</thead>
						<tbody>
							@foreach($topproducts as $item)
							<?php
							$productdata = \App\Http\Controllers\BrandController::getProductData($item->product_id);
							if(!isset($productdata)){
								continue;
							}
							?>
							<tr>
								<td class="text-left">{{$productdata->getProduct->name}} ({{$productdata->variant}})</td>
								<td class="text-center">{{$item->quantity}} </td>
								<td class="text-center">{{$productdata->getProduct->getUnit->name}}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div> -->
	</div>
</div>
@endsection
@section('script')
<script type="text/javascript">
	$("#dashboardbtn").addClass("active");
</script>
@endsection