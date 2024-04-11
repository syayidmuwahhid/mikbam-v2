@extends('layouts.app')
@section('title')
  <title>Internet Gateway - MIKBAM</title>
@endsection

@section('pagetitle')
  <h1>Internet Gateway</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('') }}">Home</a></li>
      <li class="breadcrumb-item active">Internet Gateway</li>
    </ol>
  </nav>    
@endsection

@section('main')
<div class="row">
  <div class="col-lg-6">
      <div class="card">
          <div class="card-body">
              <h5 class="card-title" id="status-title">
                  Status Router
              </h5>
              <div class="row">
                  <div class="col-sm-6">
                      <h6 class="fw-bold">DHCP Client</h6>
                  </div>
                  <div class="col-sm-6" id="status-dhcp">
                    <label class='badge bg-warning text-dark'><i class='bi bi-exclamation-triangle'></i> Not Data</label>
                  </div>
              </div>
              <div class="row">
                  <div class="col-sm-6">
                      <h6 class="fw-bold">IP Address</h6>
                  </div>
                  <div class="col-sm-6" id="status-ip-address">
                    <label class='badge bg-warning text-dark'><i class='bi bi-exclamation-triangle'></i> Not Data</label>
                  </div>
              </div>
              <div class="row">
                  <div class="col-sm-6">
                      <h6 class="fw-bold">DNS</h6>
                  </div>
                  <div class="col-sm-6" id="status-dns">
                    <label class='badge bg-warning text-dark'><i class='bi bi-exclamation-triangle'></i> Not Data</label>
                  </div>
              </div>
              <div class="row">
                  <div class="col-sm-6">
                      <h6 class="fw-bold">Gateway</h6>
                  </div>
                  <div class="col-sm-6" id="status-gateway">
                    <label class='badge bg-warning text-dark'><i class='bi bi-exclamation-triangle'></i> Not Data</label>
                  </div>
              </div>
              <div class="row">
                  <div class="col-sm-6">
                      <h6 class="fw-bold">Firewall NAT</h6>
                  </div>
                  <div class="col-sm-6" id="status-nat">
                    <label class='badge bg-warning text-dark'><i class='bi bi-exclamation-triangle'></i> Not Data</label>
                  </div>
              </div>
              <div class="row">
                  <div class="col-sm-6">
                      <h6 class="fw-bold">Koneksi Internet</h6>
                  </div>
                  <div class="col-sm-6" id="status-internet">
                    <label class='badge bg-warning text-dark'><i class='bi bi-exclamation-triangle'></i> Not Data</label>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <div class="col-lg-4">
      <div class="card">
          <div class="card-body">
              <h5 class="card-title">Konfigurasi Internet Gateway 
                <br> <span id="konfig-title">Sumber Internet</span> 
              </h5>
              <div class="text-center">
                <button class="btn btn-success" onclick="konfAuto()">Otomatis</button>
                <button class="btn btn-primary" onclick="konfMan()">Manual</button>
              </div>
          </div>
      </div>
  </div>
</div>
@endsection

@push('js')
  <script src="{{ asset('assets/pages/internet-gateway.js') }}"></script>
@endpush