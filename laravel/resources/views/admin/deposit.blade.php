@extends('Layout.admindashboard')
@section('css')
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span> New Deposit
            </h3>
            {{-- <nav aria-label="breadcrumb">
      <ul class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page">
          <span></span>Overview <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
        </li>
      </ul>
    </nav> --}}
        </div>
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form class="forms-sample" action="{{ route('newDeposit') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="userId">User</label>
                                <select name="userId" id="userId" class="form-control" required>
                                    <option value="">Select User</option>
                                    @forelse ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->mobile }})</option>
                                    @empty
                                        <option value="">No User Found</option>
                                    @endforelse
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="amount">Amount</label>
                                <input type="number" class="form-control" id="amount" name="amount" placeholder="Amount" required min="1">
                            </div>
                            <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- content-wrapper ends -->
@endsection

@section('js')

@endsection
