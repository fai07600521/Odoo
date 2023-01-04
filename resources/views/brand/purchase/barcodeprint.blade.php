<html>
<head>
	<title>พิมพ์บาร์โค๊ด</title>
	<meta charset="utf-8">
	<link href="//fonts.googleapis.com/css?family=Kanit&display=swap" rel="stylesheet">
	<style>
		h1,h2,h3,h4,h5,h6,p,span,a,input,label,button,a{
			font-family: 'Kanit', sans-serif !important;
		}
		.boxbarcode{
			width: :48.5mm !important;
			height: 25.4mm !important;
			text-align: center;
			line-height: 2.85px;
			padding-bottom: 16px;
		}
		.boxbarcode img{
			margin-bottom: -5px;
			height: 12mm;
		}
		p{
			font-size: 11px;
		}
		page[size="barcode-page"] {
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
		.pricefix{
			margin-bottom: 1px;
		}
		@media print {
			body, page[size="barcode-page"] {
				margin: 0;
				box-shadow: 0;
			}
			* {
				-webkit-transition: none !important;
				transition: none !important;
			}
		}
	</style>
</head>
<body>
	<?php
	$countpage = $quantity;
	$countpage = $countpage/44;
	$countpage = ceil($countpage);
	$colcount = 0;
	?>
	@for($k=1;$k<=$countpage;$k++)
	<?php
	$count2 = 0;
	?>
	<page size="barcode-page">
		<br>
		<table>
			@foreach($products as $product)
			<?php
			$productdata = \App\Http\Controllers\BrandController::getProductData($product["product_id"]);
			if(!isset($productdata)){
				continue;
			}
			?>
			@for($i=0;$i<$product["quantity"];$i++)
			<?php
			if($colcount>=(44*$k)){
				break;
			}
			?>
			<?php
			$count2++;
			if($count2<=(44*($k-1))){
				continue;
			}
			?>
			@if($colcount%4==0)
			<tr>
				@endif
				
				<td class="boxbarcode">
					<img src="/barcode.php?text={{$productdata->barcode}}"><br>
					<p>{{$productdata->barcode}}</p>
					<p>{{$productdata->getProduct->name}} ({{$productdata->variant}})</p>
					<p class="pricefix">ราคา {{number_format($productdata->getProduct->price,2)}} บาท</p>
					<?php 
					$colcount++;
					?>
				</td>
				

				@if($colcount%4==0)
			</tr>
			@endif
			@endfor
			@endforeach
		</table>
	</page>
	@endfor

</body>
<script type="text/javascript">
	window.print();
</script>
</html>



