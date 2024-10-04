@extends('master')
@section('title','ใบย้ายสินค้าทั้งหมด')
@section('style')
<link rel="stylesheet" href="/assets/js/plugins/datatables/dataTables.bootstrap4.css">
@endsection
@section('content')
<div class="content">
	<h2 class="content-heading">ใบย้ายสินค้าทั้งหมด</h2>
	<div class="block">
		<div class="block-content">
			<div style="width: 100%; text-align: right;">
				<a href="/admin/stock/create" class="btn btn-success"><i class="fa fa-plus"></i> เพิ่มใบย้ายสินค้า</a><br><br>
			</div>
			<div class="table-responsive">
				<table class="table table-hover data-table">
					<thead>
						<tr>
							<td class="text-center">#</td>
							<td class="text-center">รายละเอียด</td>
							<td class="text-center">สถานะ</td>
							<td class="text-center">การกระทำ</td>
						</tr>
					</thead>
					<tbody>
					</tbody>
					
				</table>
			</div>
		</div>
	</div>
</div>
@endsection
@section('script')
<script src="/assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var table = $('.data-table').DataTable({
            "processing": true,
            "serverSide": true, // Enable server-side processing
            "ajax": {
                "url": "{{ route('tranferStock') }}", // Adjust to your route
                "type": "GET",
				data: function(d) {
                    d.search = d.search.value;
					d.page = (d.start / d.length) + 1;
					d.per_page = d.length;
            	}
            },
            "columns": [
                { data: 'id', name: 'id' , className: 'text-center'},
                { 
                    data: 'details', 
                    name: 'details', 
                    render: function(data, type, row) {
                        return `<b>ต้นทาง</b> : ${row.get_source?.name ?? ''}<br>
                                <b>ปลายทาง</b> : ${row.get_destination?.name ?? ''}<br>
                                <b>เหตุผล</b> : ${row.remark ?? ''}<br>
                                <b>วันที่สร้าง</b> : ${row.created_at}`;
                    }
                },
                { 
                    data: 'status', 
                    name: 'status', 
					className: 'text-center',
                    render: function(data, type, row) {
                        if(row.status === 9) {
                            return '<span class="badge badge-danger">ยกเลิกใบย้ายสินค้า</span>';
                        } else if(row.status === 0) {
                            return '<span class="badge badge-primary">รอย้ายสินค้า</span>';
                        } else {
                            return '<span class="badge badge-success">ย้ายสินค้าเรียบร้อยแล้ว</span>';
                        }
                    }
                },
                { 
                    data: 'actions', 
                    name: 'actions',
					className: 'text-center', 
                    orderable: false, 
                    searchable: false,
                    render: function(data, type, row) {
                        let cancelButton = '';
                        if(row.status === 0) {
                            cancelButton = `<a href="/admin/stock/transfer/cancel/${row.id}" onclick="return confirm('คุณมั่นใจที่จะยกเลิกใบนำเข้านี้ใช่หรือไม่?')" class="btn btn-danger"><i class="si si-ban"></i> ยกเลิกใบย้ายสินค้า</a>`;
                        }
                        return cancelButton + ` <a href="/admin/stock/transfer/get/${row.id}" class="btn btn-primary"><i class="fa fa-info"></i> ดูใบย้ายสินค้า</a>`;
                    }
                }
            ],
            "paging": true,
            "pageLength": 10,
            "lengthChange": true,
            "searching": true,
            "info": true,
            "autoWidth": false,
            "responsive": true
        });

        $("#poallbtn").addClass("active");
    });
</script>
@endsection