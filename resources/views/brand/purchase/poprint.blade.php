<html>
<head>
	<title>ใบนำเข้าหมายเลข {{$purchase->id}}</title>
	<meta charset="utf-8">
	<link href="//fonts.googleapis.com/css?family=Kanit&display=swap" rel="stylesheet">
	<style>
		h1,h2,h3,h4,h5,h6,p,span,a,input,label,button,a,html,body{
			font-family: 'Kanit', sans-serif !important;
		}
		page[size="po-page"] {
			background: white;
			width: 21cm;
			height: 29.7cm;
			display: block;
			margin: 0 auto;
		}
		table{
			margin-top: 2mm;
			width: 100%;
		}
		@media print {
			body, page[size="po-page"] {
				margin: 0;
				box-shadow: 0;
			}
			* {
				-webkit-transition: none !important;
				transition: none !important;
			}
		}
		.pagefix{
			padding: 5px 30px;
		}
		.row div{
			padding: 6px !important;
		}
				table{
			width: 100%;
		}
		.table{
			border-collapse: collapse;
		}
		.table,.table th,.table td {
  border: 1px solid black;
}
.table td{
	font-size:16px;
}
	</style>

</head>
<body>
	<page size="po-page">
		<table>
			<tr>
				<td style="width: 90px">
					<img src="/assets/logo.png" style="width: 70px;">
				</td>
				<td style="width: 400px;" >
					<p><font style="font-size: 20px; font-weight: bolder;">{{$purchase->getBranch->companyname}}</font><br>
						ที่อยู่: {!!$purchase->getBranch->address!!}<br>
						เลขประจำตัวผู้เสียภาษี: {{$purchase->getBranch->taxid}}
					</p>
				</td>
				<td style="text-align: right;">
					<p>
						<font style="font-size: 20px; font-weight: bolder;">ใบนำเข้าสินค้า</font><br>
						ใบนำเข้าสินค้าเลขที่ : {{$purchase->id}}<br>
						วันที่พิมพ์เอกสาร: {{date("Y-m-d")}}<br>
					</p>
				</td>

			</tr>
		</table>
		<hr style="width: 100%; border:solid #ccc 1px; margin-top: -1px;">
		<table style="margin-top:-3px;">
			<tr>
				<td>
					<p style="line-height: 20px;">
						แบรนด์ : {{$purchase->getUser->brand_name}}<br>
						บริษัท : {{$purchase->getUser->name}}<br>
						ที่อยู่ : {{$purchase->getUser->address}}<br>
						สาขาที่นำเข้า : {{$purchase->getBranch->name}}<br>
						วันจัดส่งสินค้า : {{$purchase->shipdate}}
					</p>
				</td>
			</tr>
		</table>
		<center style="margin-top:-25px;"><p>รายการสินค้า</p></center>
		<table class="table">
			<tr>
				<td style="text-align: center;">ลำดับ</td>
				<td style="text-align: center;">สินค้า</td>
				<td style="text-align: center;">ราคา (บาท)</td>
				<td style="text-align: center;">จำนวน</td>
				<td style="width: 200px; text-align: center;">หมายเหตุ</td>
			</tr>
			<?php
				$count = 0;
			?>
			@foreach($purchase->getItem as $key=>$item)
			<?php
			$productdata = \App\Http\Controllers\BrandController::getProductData($item->product_id);
			if(!isset($productdata)){
				continue;
			}
			?>
			<tr>
				<td style="text-align: center;">{{$key+1}}</td>
				<td>{{$productdata->getProduct->name}} ({{$productdata->variant}})</td>
				<td style="text-align: right;">{{number_format($productdata->getProduct->price,2)}}</td>
				<td style="text-align: center;">{{$item->quantity}}</td>
				<td></td>
				<?php
					$count += $item->quantity;
				?>
			</tr>
			@endforeach
			<tr>
				<td colspan="3" style="text-align: center;">รวม</td>
				<td style="text-align: center;">{{$count}}</td>
				<td></td>
			</tr>
		</table>
		<br>
		<table>
			<tr>
				<td style="text-align: center;">
					....................................................................<br>
					(ผู้จัดส่งสินค้า)
				</td>
				<td style="text-align: center;">
					....................................................................<br>
					(ผู้รับสินค้า)
				</td>
			</tr>
		</table>
	</page>
</body>
<script type="text/javascript">
	//window.print();
</script>
</html>



