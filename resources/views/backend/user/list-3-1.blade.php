@extends('master.backend')

@section('content')

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            
            <h1><span class="text-danger" style="border-bottom: 1px dotted red;">List Users</span></h1>
            
          </div>

         
        
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            
            <!-- /.card -->

            <div class="card">
             
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                   
                    <th>Sl</th>
                    <th>Account Number</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>Action</th>
          
                  
                  </tr>
                  </thead>
                  <tbody>

                    @foreach($users as $key => $user)
                  <tr>
                   
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $user->account->account_number ?? 'N/A' }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->phone ?? 'N/A'}}</td>
                    <td>{{ $user->email ?? 'N/A' }}</td>
                    <td>{{ number_format($user->account->balance ?? 0, 2) }}</td>
                    <td>
                   
                        

                        @if($user->status == 'approved')
                        <span class="badge badge-success">Approved</span>
                        @else($user->status == 'pending')
                        <span class="badge badge-warning">Pending</span>
                        @endif

                    </td>
                      
                    <td>

              @if ($user->status === 'pending')

    {{-- Approve --}}
    <form method="POST" action="{{ route('admin.users.approve', $user->id) }}" style="display:inline;">
        @csrf
        <button type="submit"
                class="btn btn-sm btn-success"
                onclick="return confirm('Are you sure you want to approve this user?')">
            Approve
        </button>
    </form>

    {{-- Reject --}}
    <form method="POST" action="{{ route('admin.users.reject', $user->id) }}" style="display:inline;">
        @csrf
        <button type="submit"
                class="btn btn-sm btn-danger"
                onclick="return confirm('Are you sure you want to reject this user?')">
            Reject
        </button>
    </form>

@elseif ($user->status === 'approved')

    {{-- Reject approved user --}}
    <form method="POST" action="{{ route('admin.users.reject', $user->id) }}" style="display:inline;">
        @csrf
        <button type="submit"
                class="btn btn-sm btn-danger"
                onclick="return confirm('Are you sure you want to reject this user?')">
            Reject
        </button>
    </form>

@else
    <span class="badge bg-secondary">No Action</span>
@endif

                    </td>

                    </tr>
                    @endforeach

                
                 
                  </tbody>
                 
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>

  <script>
    $(document).on("click","#delete", function(e){
     e.preventDefault();
     var link = $(this).attr("href");
     swal({
       title: "Are you want to delete?",
       text: "Once Delete, This will be Permanently Delete!",
       icon: "warning",
       buttons: true,
       dangerMode: true,
     })
     .then((willDelete) => {
       if(willDelete){
         window.location.href = link;
       }else{
         swal("Safe Data!");
       }
     });
    });

    
   </script>



  
@endsection