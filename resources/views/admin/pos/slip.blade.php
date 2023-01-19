<html>

<head>
    <title>ใบเสร็จรับเงิน {{ $invoice->id }}</title>
    <meta charset="utf-8">
    <link href="//fonts.googleapis.com/css?family=Kanit&display=swap" rel="stylesheet">
    <style>
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        span,
        a,
        input,
        label,
        button,
        a,
        html,
        body {
            font-family: 'Kanit', sans-serif !important;
        }

        page[size="pos-slip"] {
            background: white;
            width: 80mm;
            display: block;
            margin: 0 auto;
        }

        table {
            margin-top: 2mm;
            width: 100%;
        }

        @media print {

            body,
            page[size="pos-slip"] {
                margin: 0;
                box-shadow: 0;
            }

            * {
                -webkit-transition: none !important;
                transition: none !important;
            }
        }

        .row div {
            padding: 6px !important;
        }

        table {
            width: 100%;
        }

        .table {
            border-collapse: collapse;
        }

        .table td {
            font-size: 11px;
        }

        @media print {
            .hideonprint {
                display: none !important;
            }
        }
    </style>

</head>

<body>
    <page size="pos-slip">
        <table>
            <tr>
                <td style="text-align:center;">
                    <img src="/assets/logo-black.png" style="width: 70px;">
                </td>
            </tr>
            <tr>
                <td style="width: 400px; text-align:center;">
                    @if (app('request')->input('lang') == 'EN' && $invoice->getBranch->en_companyname && $invoice->getBranch->en_address)
                        <p>
                            <font style="font-size: 14px; font-weight: bolder;">{{ $invoice->getBranch->en_companyname }}
                            </font><br>
                            <font style="font-size: 12px;">{!! $invoice->getBranch->en_address !!}</font><br>
                            <font style="font-size: 12px;">Registration number: {!! $invoice->getBranch->taxid !!}</font>
                        </p>
                    @else
                        <p>
                            <font style="font-size: 14px; font-weight: bolder;">{{ $invoice->getBranch->companyname }}
                            </font><br>
                            <font style="font-size: 12px;">{!! $invoice->getBranch->address !!}</font><br>
                            <font style="font-size: 12px;">เลขประจำตัวผู้เสียภาษี: {!! $invoice->getBranch->taxid !!}</font>
                        </p>
                    @endif
                    </p>
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    <p>
                        @if (app('request')->input('lang') == 'EN')
                            <font style="font-size: 16px; font-weight: bolder;">Reciept/Tax Invoice</font>
                        @else
                            <font style="font-size: 16px; font-weight: bolder;">ใบเสร็จรับเงิน/ใบกำกับภาษีอย่างย่อ
                            </font>
                        @endif
                    </p>
                </td>

            </tr>
            <tr>
                <td>
                    @if (app('request')->input('lang') == 'EN')
                        <p style="font-size: 12px;">Doc no. : {{ $invoice->tax_id }}<br>
                            Date: {{ date('Y-m-d H:i:s') }}<br> Staff : {{ $invoice->getUser->name }}<br>Received by :
                            {{ $invoice->getPaymentType->name }}
                            @if ($invoice->member_id != 0)
                                ​<br>Member name : {{ $invoice->getMember->name }}
                            @endif
                        </p>
                    @else
                        <p style="font-size: 12px;">เลขที่เอกสาร : {{ $invoice->tax_id }}<br>
                            วันที่พิมพ์เอกสาร: {{ date('Y-m-d H:i:s') }}<br> พนักงานขาย :
                            {{ $invoice->getUser->name }}<br>ชำระโดย : {{ $invoice->getPaymentType->name }}
                            @if ($invoice->member_id != 0)
                                ​<br>สมาชิก : {{ $invoice->getMember->name }}
                            @endif
                        </p>
                    @endif

                </td>
            </tr>
        </table>
        <hr style="width: 100%; border:solid #ccc 1px; margin-top: -1px;">
        @if (app('request')->input('lang') == 'EN')
            <center>
                <p>Item List</p>
            </center>
        @else
            <center>
                <p>รายการสินค้า</p>
            </center>
        @endif
        <table class="table">
            <tr>
                @if (app('request')->input('lang') == 'EN')
                    <td style="text-align: center;">Item</td>
                    <td style="text-align: center;">Price</td>
                    <td style="text-align: center;">Amount</td>
                    <td style="text-align: center;">Total</td>
                @else
                    <td style="text-align: center;">สินค้า</td>
                    <td style="text-align: center;">ราคา</td>
                    <td style="text-align: center;">จำนวน</td>
                    <td style="text-align: center;">รวม</td>
                @endif
            </tr>
            <?php
            $count = 0;
            $itemdiscount = 0;
            ?>
            @foreach ($invoice->getItem as $key => $item)
                <?php
                $productdata = \App\Http\Controllers\BrandController::getProductData($item->product_id);
                if (!isset($productdata)) {
                    continue;
                }
                ?>
                <tr>
                    <td>{{ $productdata->getProduct->name }} ({{ $productdata->variant }})</td>
                    <td style="text-align: right;">{{ number_format($item->price, 2) }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right">{{ number_format($item->price * $item->quantity, 2) }}</td>
                    <?php
                    $count += $item->price * $item->quantity;
                    $itemdiscount += $item->suminput;
                    ?>
                </tr>
            @endforeach


            @foreach ($promotions as $promotion)
                <tr>
                    <td>{{ $promotion->getPromotion->name }}</td>
                    <td>{{ number_format($promotion->discount, 2) }}</td>
                    <td>1</td>
                    <td>-{{ number_format($promotion->discount, 2) }}</td>
                </tr>
                <?php
                $itemdiscount = $itemdiscount - $promotion->discount;
                ?>
            @endforeach
            <tr>
                <td colspan="4"><br>
                    <hr style="width: 100%; border:solid #ccc 1px; margin-top: -1px;">
                </td>
            </tr>
            <tr>
                @if (app('request')->input('lang') == 'EN')
                    <td style="font-size: 12px;">Total Amount {!! $sumItem !!}</td>
                    <td colspan="2" style="text-align: right; font-size: 12px;">Sub total</td>
                    <td style="text-align: right;font-size: 12px;">{{ number_format($count / 1.07, 2) }} </td>
                @else
                    <td style="font-size: 12px;">จำนวนรวม {!! $sumItem !!}</td>
                    <td colspan="2" style="text-align: right; font-size: 12px;">รวมมูลค่าสินค้า</td>
                    <td style="text-align: right;font-size: 12px;">{{ number_format($count / 1.07, 2) }} </td>
                @endif
            </tr>
            <tr>
                @if (app('request')->input('lang') == 'EN')
                    <td colspan="3" style="text-align: right; font-size: 12px;">Tax</td>
                    <td style="text-align: right;font-size: 12px;">{{ number_format($count - $count / 1.07, 2) }} </td>
                @else
                    <td colspan="3" style="text-align: right; font-size: 12px;">ภาษีมูลค่าเพิ่ม</td>
                    <td style="text-align: right;font-size: 12px;">{{ number_format($count - $count / 1.07, 2) }} </td>
                @endif
            </tr>

            @if ($count - $itemdiscount != 0)
                @if (app('request')->input('lang') == 'EN')
                    <tr>
                        <td colspan="3" style="text-align: right; font-size: 12px;">Total</td>
                        <td style="text-align: right;font-size: 12px;">{{ number_format($count, 2) }} </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="2" style="text-align: right;">Discount</td>
                        <td style="text-align: right;">{{ number_format($count - $itemdiscount, 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="3" style="text-align: right; font-size: 12px;">รวมเป็นเงิน</td>
                        <td style="text-align: right;font-size: 12px;">{{ number_format($count, 2) }} </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="2" style="text-align: right;">ส่วนลดสินค้า</td>
                        <td style="text-align: right;">{{ number_format($count - $itemdiscount, 2) }}</td>
                    </tr>
                @endif
            @endif
            @if (app('request')->input('lang') == 'EN')
                <tr>
                    <td colspan="3" style="text-align: right; font-size: 12px;">Total</td>
                    <td style="text-align: right;font-size: 12px;">{{ number_format($itemdiscount, 2) }} </td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right; font-size: 12px;">Paid</td>
                    <td style="text-align: right;font-size: 12px;">{{ number_format($invoice->recieve, 2) }} </td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right; font-size: 12px;">Changes</td>
                    <td style="text-align: right;font-size: 12px;">
                        {{ number_format($invoice->recieve - $itemdiscount, 2) }}
                    </td>
                </tr>
                <tr>
                    <td colspan="4"><br>
                        <hr style="width: 100%; border:solid #ccc 1px; margin-top: -1px;">
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center;"><br>Thank you for your support :)</td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center;">Please check your items before leaving​<br>
                        *We do not accept for changing items in any condition.<br><br></td>
                </tr>
            @else
                <tr>
                    <td colspan="3" style="text-align: right; font-size: 12px;">รวมเป็นเงินทั้งสิ้น</td>
                    <td style="text-align: right;font-size: 12px;">{{ number_format($itemdiscount, 2) }} </td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right; font-size: 12px;">รับเงินมา</td>
                    <td style="text-align: right;font-size: 12px;">{{ number_format($invoice->recieve, 2) }} </td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right; font-size: 12px;">เงินทอน</td>
                    <td style="text-align: right;font-size: 12px;">
                        {{ number_format($invoice->recieve - $itemdiscount, 2) }}
                    </td>
                </tr>
                <tr>
                    <td colspan="4"><br>
                        <hr style="width: 100%; border:solid #ccc 1px; margin-top: -1px;">
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center;"><br>Thank you for your support :)</td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center;">กรุณาตรวจสอบสินค้าก่อนออกจากร้าน​<br>
                        *เนื่องจากทางร้านไม่รับเปลี่ยนคืนสินค้าทุกกรณี<br><br></td>
                </tr>
            @endif
        </table>
        <br>
        <center>
            <button class="hideonprint" onclick="window.print();">พิมอีกครั้ง</button>
            <button class="hideonprint" onclick="window.close();">ทำรายการต่อไป</button>
        </center>
    </page>
</body>
<script type="text/javascript">
    window.print();
</script>

</html>
