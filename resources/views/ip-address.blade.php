@extends('layouts.app')
@section('title')
  <title>IP Address - MIKBAM</title>
@endsection

@section('pagetitle')
  <h1>IP Address</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('') }}">Home</a></li>
      <li class="breadcrumb-item active">IP Address</li>
    </ol>
  </nav>    
@endsection

@section('main')
  <div class="row">
    <div class="col-lg-12">

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">
            IP Address List
            <button class="float-end btn btn-primary" onclick="modalAdd()">Add</button>
          </h5>
          
          <!-- Table with stripped rows -->
          <div class="table-responsive">
            <table class="table" id="tabel-ip">
              <thead>
                <tr>
                  <th scope="col" class="text-center">#</th>
                  <th scope="col" class="text-center">Flags</th>
                  <th scope="col">Address</th>
                  <th scope="col">Network</th>
                  <th scope="col">Interfaces</th>
                  <th scope="col" class="text-center">Action</th>
                </tr>
              </thead>
              <tbody id="table-body">
                <tr>
                  <td colspan="6" class="text-center">No Data</td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- End Table with stripped rows -->

        </div>
      </div>

    </div>
  </div>
@endsection

@push('js')
  <script src="{{ asset('assets/pages/ip-address.js') }}"></script>
@endpush