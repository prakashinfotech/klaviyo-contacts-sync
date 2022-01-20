@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <h1 class="h3 text-gray-800">{{ __('Contacts') }}</h1>
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="row">
                        <div id="change" class="col-8">
                            <button class="btn btn-danger" onclick="deletePlanFeature()">Delete</button>
                        </div>
                        <div class="col-4">
                            <a href="{{ asset('sample.csv') }}" class="btn btn-secondary" target="_blank"> Download Sample CSV File </a>
                            <a href="{{ route('contact.create') }}" class="btn btn-primary"> Create </a>
                            <a href="{{ route('import') }}" class="btn btn-success"> Import CSV </a>
                        </div>
                    </div>
                    <br>
                    <table class="table table-bordered" id="contact" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th data-orderable="false">
                                <div class="checkbox-inline">
                                    <div class="checkbox-inline">
                                        <input type="checkbox" id="selectAll" class="checkbox-inline">
                                        <label for="selectAll"></label>
                                    </div>
                                </div>
                            </th>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th data-orderable="false" width="142px">Action</th>
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

@section('import-style')
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('import-javascript')
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
@endsection

@section('page-javascript')
    <script>
        $(document).ready(function(){
            $.fn.dataTable.ext.errMode = 'throw';
            $('#contact').DataTable({
                "ajax":{
                    "url": "{{ route('contact-list') }}",
                    "type": "POST",
                    dataType:"json",
                    "data": function(d){
                        d._token = "{{csrf_token()}}";
                        d.id=$("#id").val();
                        d.name=$("#name").val();
                        d.email=$("#email").val();
                        d.phone=$("#phone").val();
                    }
                },
                "searching": true,
                "processing": true,
                "serverSide": true,
                "order": [[ 0, 'DESC' ]],
                "columns": [
                    {"name": "id"},
                    {"name": "id"},
                    {"name": "name"},
                    {"name": "email"},
                    {"name": "phone"},
                    {"name": "action", "class": "action-col"}
                ],
                "drawCallback": function(settings) {
                    $('.chkbox').on('click', function() {
                        $('#selectAll').prop('checked', ($('.chkbox').length == $('.chkbox:checked').length));
                        planFeatureChange();
                    });
                },
                "dom": '<"top"rf>t<"bottom"pli>',
            });
        });
        $('#selectAll').on('click', function() {
            $('.chkbox').prop('checked', $(this).is(':checked'));
            planFeatureChange();
        });
        function planFeatureChange(){
            if($('.chkbox:checked').length > 0){
                $("#change").removeClass('hide');
            }else{
                $("#change").addClass('hide');
            }
        }
        function deletePlanFeature() {
            bootbox.confirm({
                message: 'Do you really want to delete record?',
                callback: function(result) {

                    var arrSelectIds = [];
                    var id = $('#id').val();

                    $('.chkbox:checked').each(function() {
                        arrSelectIds.push($(this).data('id'));
                    });
                    if (result) {
                        if (arrSelectIds.length) {
                            $.ajax({
                                url: "{{ route('contact-delete') }}",
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                    _method: "POST",
                                    contact_ids: arrSelectIds,
                                    id: id
                                },
                                success: function (response) {
                                    $('#selectAll').prop("checked", false);
                                    $('#contact').DataTable().ajax.reload();
                                    $('#id').val('');
                                    if (response.status == "success") {
                                        $message = '<p class="mt-3 alert alert-success">' + response.message + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>';
                                        $(".flash-message").html($message);
                                        $('.flash-message .alert').not('.alert-important').delay(5000).slideUp(350);
                                    }
                                },
                                error: function (response) {
                                    $message = '<p class="mt-3 alert alert-danger">' + error_occurred + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>';
                                    $(".flash-message").html($message);
                                    $('.flash-message .alert').not('.alert-important').delay(5000).slideUp(350);
                                }
                            });
                        }
                    }
                }
            });
        }
        function deleteRecord(id)
        {
            bootbox.confirm({
                message: 'Do you really want to delete record?',
                callback: function(result) {
                    if (result) {
                        $.ajax({
                            url: '{{ route("contact.destroy", "") }}/'+id,
                            type: 'DELETE',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            data: 'ids='+id,
                            success: function (response) {
                                $('#contact').DataTable().ajax.reload();
                                if (response.status == "success") {
                                    $message = '<p class="mt-3 alert alert-success">' + response.message + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>';
                                    $(".flash-message").html($message);
                                    $('.flash-message .alert').not('.alert-important').delay(5000).slideUp(350);
                                }
                            },
                            error: function (response) {
                                $message = '<p class="mt-3 alert alert-danger">' + error_occurred + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>';
                                $(".flash-message").html($message);
                                $('.flash-message .alert').not('.alert-important').delay(5000).slideUp(350);
                            }
                        });
                    }
                }
            });
        }
    </script>
@endsection