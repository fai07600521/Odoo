<?php
const BAHT_TEXT_NUMBERS = array('ศูนย์', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า');
const BAHT_TEXT_UNITS = array('', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน');
const BAHT_TEXT_ONE_IN_TENTH = 'เอ็ด';
const BAHT_TEXT_TWENTY = 'ยี่';
const BAHT_TEXT_INTEGER = 'ถ้วน';
const BAHT_TEXT_BAHT = 'บาท';
const BAHT_TEXT_SATANG = 'สตางค์';
const BAHT_TEXT_POINT = 'จุด';

/**
 * Convert baht number to Thai text
 * @param double|int $number
 * @param bool $include_unit
 * @param bool $display_zero
 * @return string|null
 */
function baht_text ($number, $include_unit = true, $display_zero = true)
{
    if (!is_numeric($number)) {
        return null;
    }

    $log = floor(log($number, 10));
    if ($log > 5) {
        $millions = floor($log / 6);
        $million_value = pow(1000000, $millions);
        $normalised_million = floor($number / $million_value);
        $rest = $number - ($normalised_million * $million_value);
        $millions_text = '';
        for ($i = 0; $i < $millions; $i++) {
            $millions_text .= BAHT_TEXT_UNITS[6];
        }
        return baht_text($normalised_million, false) . $millions_text . baht_text($rest, true, false);
    }

    $number_str = (string)floor($number);
    $text = '';
    $unit = 0;

    if ($display_zero && $number_str == '0') {
        $text = BAHT_TEXT_NUMBERS[0];
    } else for ($i = strlen($number_str) - 1; $i > -1; $i--) {
        $current_number = (int)$number_str[$i];

        $unit_text = '';
        if ($unit == 0 && $i > 0) {
            $previous_number = isset($number_str[$i - 1]) ? (int)$number_str[$i - 1] : 0;
            if ($current_number == 1 && $previous_number > 0) {
                $unit_text .= BAHT_TEXT_ONE_IN_TENTH;
            } else if ($current_number > 0) {
                $unit_text .= BAHT_TEXT_NUMBERS[$current_number];
            }
        } else if ($unit == 1 && $current_number == 2) {
            $unit_text .= BAHT_TEXT_TWENTY;
        } else if ($current_number > 0 && ($unit != 1 || $current_number != 1)) {
            $unit_text .= BAHT_TEXT_NUMBERS[$current_number];
        }

        if ($current_number > 0) {
            $unit_text .= BAHT_TEXT_UNITS[$unit];
        }

        $text = $unit_text . $text;
        $unit++;
    }

    if ($include_unit) {
        $text .= BAHT_TEXT_BAHT;

        $satang = explode('.', number_format($number, 2, '.', ''))[1];
        $text .= $satang == 0
            ? BAHT_TEXT_INTEGER
            : baht_text($satang, false) . BAHT_TEXT_SATANG;
    } else {
        $exploded = explode('.', $number);
        if (isset($exploded[1])) {
            $text .= BAHT_TEXT_POINT;
            $decimal = (string)$exploded[1];
            for ($i = 0; $i < strlen($decimal); $i++) {
                $text .= BAHT_TEXT_NUMBERS[$decimal[$i]];
            }
        }
    }

    return $text;
}
?>
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
	font-size:14px;
}
	</style>

</head>
<body>
	<page size="po-page">
		<table>
			<tr>
				<td style="width: 60px">
					<img src="/assets/logo.png" style="width: 50px;">
				</td>
				<td style="width: 400px;" >
					<p style="font-size: 14px;"><font style="font-weight: bolder;">{{$purchase->getBranch->companyname}}</font><br>
						ที่อยู่: {!!$purchase->getBranch->address!!}<br>
						เลขประจำตัวผู้เสียภาษี: {{$purchase->getBranch->taxid}}
					</p>
				</td>
				<td style="text-align: right;">

				</td>

			</tr>
		</table>
		<table style="margin-top:10px;">
			<tr>
				<td style="border:1px solid black; border-radius: 10px; width: 400px; padding:10px;">
					<p style="line-height: 20px; font-size: 14px;">
						รหัสผู้จำหน่าย : CASTLE-{{$purchase->getUser->id}}<br>
						ชื่อผู้จำหน่าย : {{$purchase->getUser->name}}<br>
						ที่อยู่ : {{$purchase->getUser->address}}<br>
						เลขประจำตัวผู้เสียภาษี : {{$purchase->getUser->tax_id}}<br>
						วันจัดส่งสินค้า : {{$purchase->shipdate}}
					</p>
				</td>
				<td style="width: 10px;"></td>
				<td style="border:1px solid black; border-radius: 10px; padding: 10px; vertical-align: top;">
					<p style="line-height: 20px; font-size: 14px;">
					วันที่ : {{$purchase->created_at->format('d/m/Y')}}<br>
					เลขที่ใบสั่งซื้อ : PO-{{$purchase->id}}<br>
					สาขา: {{$purchase->getBranch->name}}<br>
					GP: {{$gp}}
				</p>
				</td>
			</tr>
		</table>
		<center style="margin-top:10px;"><p>รายการสินค้า</p></center>
		<table class="table">
			<tr>
				<td style="text-align: center;">ลำดับ</td>
				<td style="text-align: center;">รหัสสินค้า</td>
				<td style="text-align: center;">แบรนด์</td>
				<td style="text-align: center;">รายละเอียด</td>
				<td style="text-align: center;">จำนวน</td>
				<td style="text-align: center;">หน่วย</td>
				<td style="text-align: center;">ราคา/หน่วย</td>
				<td style="text-align: center;">รวม</td>
			</tr>
			<?php
				$count = 0;
				$sum = 0;
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
				<td>PR-{{$productdata->id}}</td>
				<td style="text-align: center;">{{$productdata->getProduct->getUser->brand_name}}</td>
				<td>{{$productdata->getProduct->name}} ({{$productdata->variant}})</td>
				<td style="text-align: center;">{{$item->quantity}}</td>
				<td style="text-align: center;">{{$productdata->getProduct->getUnit->name}}</td>
				<td style="text-align: right;">{{number_format($productdata->getProduct->price*((100-$gp)/100),2)}}</td>
				<td style="text-align: right;">{{number_format(($productdata->getProduct->price*((100-$gp)/100))*$item->quantity,2)}}</td>
				<?php
					$count += $item->quantity;
					$sum += ($productdata->getProduct->price*((100-$gp)/100))*$item->quantity;
				?>
			</tr>
			@endforeach
			<tr>
				<td colspan="4" style="text-align: center;">รวมจำนวนสินค้า</td>
				<td style="text-align: center;">{{$count}}</td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td rowspan="2" colspan="6">หมายเหตุ: {{$purchase->remark}}</td>
				<td>รวมราคา</td>
				<td style="text-align: right;">{{number_format($sum,2)}}</td>
			</tr>
			<tr>
				<td>มูลค่า</td>
				<td style="text-align: right;">{{number_format($sum/1.07,2)}}</td>
			</tr>
			<tr>
				<td colspan="6" style="text-align: center; background: #eee;">{{baht_text($sum)}}</td>
				<td>ภาษีมูลค่าเพิ่ม</td>
				<td style="text-align: right;">{{number_format($sum-($sum/1.07),2)}}</td>
			</tr>


		</table>
		<br>

		<br>
		<table>
			<tr>
				<td style="text-align: center;">
					....................................................................<br>
					(ผู้จัดทำ)
				</td>
				<td style="text-align: center;">
					....................................................................<br>
					(ผู้รับสินค้า)
				</td>
				<td style="text-align: center;">
					....................................................................<br>
					(ผู้ส่งสินค้า)
				</td>
			</tr>
		</table>
	</page>
</body>
<script type="text/javascript">
	//window.print();
</script>
</html>



