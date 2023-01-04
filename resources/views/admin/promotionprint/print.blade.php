<html>
<head>
	<title>พิมพ์บาร์โค๊ด</title>
	<meta charset="utf-8">
	<link href="/assets/fonts/fontlabel.css" rel="stylesheet">
	<style>
		.boxbarcode{
			width: 8cm !important;
			height: 3.8cm !important;
			border: 1px solid #EEE;
			margin-bottom: 3mm;
			background-image: url('/template-barcode.png');
			background-size: contain;
			-webkit-print-color-adjust: exact;
		}
		.boxbarcode-brand{
			font-family: 'db_heaventroundedbold' !important;
			color: rgb(41,62,128);
			position: relative;
			left: 0.5cm;
			top:-0.5cm;
			z-index: 999;
		}
		.boxbarcode-productname{
			font-family: 'db_heaventroundedregular' !important;
			color: rgb(41,62,128);
			font-size: 1.3em;
		}
		.boxbarcode-price{
			font-family: 'db_heaventroundedbold' !important;
			font-size: 3em;
			color: rgb(224,104,136);
			
		}
		.boxprice{
text-align: center;
    left: 2cm;
    top: -1.55cm;
    z-index: 999;
    position: relative;
		}
		.boxproductname{
			    position: relative;
    left: 0.5cm;
    top: -3.9cm;
    width: 3.5cm;
    word-break: break-word;
    z-index: 999;

		}

		page[size="barcode-page"] {
			background: white;
			width: 21cm;
			height: 29.7cm;
			display: block;
			margin: 0 auto;
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
	$sumquantity = 0;
	$numberofproduct = sizeOf($product_name);
	$productpointer = 0;
	$currentquantity = $quantity[0];
	foreach($quantity as $res){
	$sumquantity+= $res;
}
$count = 0;

$numberofpage = ceil($sumquantity/10);
?>
@for($p=0; $p < $numberofpage;$p++)
<page size="barcode-page">
	<table>
		@for($k=0;$k<=4;$k++)
		<tr>
			<?php

			?>
			@for($i=0;$i<=1;$i++)
			<td>
				<div class="boxbarcode">

					<h1 class="boxbarcode-brand">{{$brand_name[$productpointer]}}</h1>
					<div class="boxprice"><h1 class="boxbarcode-price">{{number_format($price[$productpointer])}}.-</h1></div>
					<div class="boxproductname"><h4 class="boxbarcode-productname">{{$product_name[$productpointer]}}</h4></div>
					

				</div>
			</td>
			<?php
			$count++;
			if($count==$currentquantity){
			$productpointer++;
			if(isset($quantity[$productpointer])){
			$currentquantity += $quantity[$productpointer];
		}else{
		break;
	}

}

if($count>=$sumquantity||$count%10==0){
break;
$page++;
}

?>
@endfor
</tr>
<?php
if($count>=$sumquantity||$count%10==0){
break;
$page++;
}
?>
@endfor


</table>


</page>
@if($p<$numberofpage-1)
<p style="page-break-before: always">
@endif
@endfor


</body>
<script type="text/javascript">
	window.print();
</script>
</html>



