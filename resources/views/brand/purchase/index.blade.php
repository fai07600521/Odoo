@extends('master')
@section('title', 'ใบนำเข้าสินค้าทั้งหมด')
@section('style')
<link rel="stylesheet" href="/assets/js/plugins/datatables/dataTables.bootstrap4.css">
@endsection
@section('content')
<div class="content">
    <h2 class="content-heading">ใบนำเข้าสินค้าทั้งหมด</h2>
    <div class="block">
        <div class="block-content">
            <div style="width: 100%; text-align: right;">
                <a href="/purchase/add" class="btn btn-success"><i class="fa fa-plus"></i> เพิ่มใบนำเข้าสินค้า</a><br><br>
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
                        <!-- Data will be dynamically injected here by DataTables -->
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
                "url": "{{ route('purchase') }}", // Adjust to your route
                "type": "GET",
				data: function(d) {
					d.page = (d.start / d.length) + 1;
					d.per_page = d.length;
                    d.search = d.search.value;
            	}
            },
            "columns": [
                { data: 'id', name: 'id' , className: 'text-center'},
                { 
                    data: 'details', 
                    name: 'details', 
					className: 'text-center',
                    render: function(data, type, row) {
                        return `<b>แบรนด์</b> : ${row.get_user?.brand_name ?? ''}<br>
                                <b>เข้าสาขา</b> : ${row.get_branch?.name ?? ''}<br>
                                <b>วันที่</b> : ${row.shipdate}`;
                    }
                },
                { 
                    data: 'status', 
                    name: 'status', 
					className: 'text-center',
                    render: function(data, type, row) {
                        if(row.status === 9) {
                            return '<span class="badge badge-danger">ยกเลิกใบนำเข้า</span>';
                        } else if(row.status === 0) {
                            return '<span class="badge badge-primary">รอทางร้านตอบรับ</span>';
                        } else {
                            return '<span class="badge badge-success">นำเข้าเรียบร้อยแล้ว</span>';
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
                            cancelButton = `<a href="/purchase/cancel/${row.id}" onclick="return confirm('คุณมั่นใจที่จะยกเลิกใบนำเข้านี้ใช่หรือไม่?')" class="btn btn-danger"><i class="si si-ban"></i> ยกเลิกใบนำเข้า</a>`;
                        }
                        return cancelButton + ` <a href="/purchase/get/${row.id}" class="btn btn-primary"><i class="fa fa-info"></i> ดูใบนำเข้า</a>`;
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
