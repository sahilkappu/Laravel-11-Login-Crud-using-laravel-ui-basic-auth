@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                        
                    <div class="row">
                        <div class="table-responsive">
                            <table class="table" id="user_table">
                                <thead>
                                    <th>Sr No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Action</th>

                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" >
    <form id="edit_form" action="">
        @csrf
        <input type="hidden" name="id">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <label for="name">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary saveUser">Save changes</button>
            </div>
        </div>
    </form>

  </div>
</div>
@endsection
@section('scripts')
    <script>
         var dataTable = $('#user_table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ordering: true,
                ajax: "{{ route('user.list') }}",

                columns: [
                    {
                        "data": "no"
                    },
                    {
                        "data": "name",
                    },
                    {
                        "data": "email",
                    },
                    {
                        "data": "action",
                    },
                ],
            });
        function editUser(id,name,email){
            $(`#edit_form input[name="id"]`).val(id);
            $(`#edit_form input[name="name"]`).val(name);
            $(`#edit_form input[name="email"]`).val(email);
            $(`#editModal`).modal('show');
        }

        $(`.saveUser`).on('click',function(){
            let form  = $(`#edit_form`);
            let formData = form.serialize();
            $.ajax({
                type: "POST",
                url: "{{route('user.update')}}",
                data: formData,
                success: function (response) {
                    alert(response.message);
                    $(`#editModal`).modal('hide');
                    dataTable.ajax.reload();
                },
                error: function (xhr, status, error) {
                    console.error(error);
                    alert('could not Update the User,Please try again');
                }
            });
        });
        function deleteUser(id){
            let ask = confirm("Are you sure you want to delete this user?");
            if(ask){
                $.ajax({
                    type: "POST",
                    headers:{
                        'X-CSRF-TOKEN': $(`meta[name="csrf-token"]`).attr('content'),
                    },
                    url: "/delete-user",
                    data: {id:id},
                    success: function (response) {
                        alert(response.message);
                        dataTable.ajax.reload();
                    },
                     error: function (xhr, status, error) {
                            console.error(error);
                            alert('could not delete the User');
                        }
                });
            }
        }
    </script>
@stop
