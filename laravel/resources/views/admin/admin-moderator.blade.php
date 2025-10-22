@extends('Layout.admindashboard')
@section('css')
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span> Admin Moderator
            </h3>
            <a href="{{ url('admin/admin-moderator/create') }}" class="btn btn-primary btn-sm">Add New</a>
            {{-- <nav aria-label="breadcrumb">
      <ul class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page">
          <span></span>Overview <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
        </li>
      </ul>
    </nav> --}}
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Admin Moderator List</h4>
                        </p>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Sr.No</th>
                                    <th>Userid</th>
                                    <th>Role</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Created</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                    @forelse ($adminModerators as $adminModerator)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ appvalidate($adminModerator->id) }}</td>
                                                <td>{{ $adminModerator->isModerator ? 'Moderator' : 'Admin' }}</td>
                                                <td>{{ appvalidate($adminModerator->name) }}</td>
                                                <td>{{ appvalidate($adminModerator->mobile) }}</td>
                                                <td>{{ appvalidate($adminModerator->email) }}</td>
                                                <td>{{ dformat($adminModerator->created_at, 'd-m-Y') }}</td>
                                                <td><label
                                                        class="badge badge-{{ status($adminModerator->status, 'user')['color'] }}">{{ status($adminModerator->status, 'user')['name'] }}</label>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning"
                                                        onclick="redirect('admin-moderator/edit/{{ $adminModerator->id }}')">edit</button>
                                                    <button class="btn btn-sm btn-danger"
                                                        onclick="deleteuser('{{ $adminModerator->id }}')">Delete</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="13" class="text-center"> No User found!!</td>
                                            </tr>
                                        @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- content-wrapper ends -->
@endsection

@section('js')
    <script>
        function deleteuser(id) {
            if (!confirm("Are you sure you want to delete this user?")) {
                return;
            }
            let form = new FormData();
            form.append('id', id);
            form.append('_token', '{{ csrf_token() }}');
            apex("POST", "{{ url('admin/api/user/delete') }}", form, '', "/admin/admin-moderator", "#");
        }
    </script>
@endsection
