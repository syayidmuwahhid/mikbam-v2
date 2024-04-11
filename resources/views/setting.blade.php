@extends('layouts.app')
@section('title')
  <title>Setting - MIKBAM</title>
@endsection

@section('pagetitle')
  <h1>Setting</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('') }}">Home</a></li>
      <li class="breadcrumb-item active">Setting</li>
    </ol>
  </nav>    
@endsection

@section('main')
  <div class="row">
    <div class="col-lg-6">

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Pengaturan Sistem</h5>

          <div class="row">
              <label class="col-6">Sumber Internet</label>
              <div class="col-6">
                <select class="form-select" id="setting-interface" onchange="ubahDefInterface()">
                </select>
              </div>
          </div> <br>
          
          <div class="row">
              <label class="col-6">Refresh Page (detik)</label>
              <div class="col-6">
                <input type="number" id="setting-timeout" class="form-control" min="5" onchange="ubahTimeout()"/>
              </div>
          </div>
          
          
  
        </div>
      </div>
      

    </div>
  </div>
@endsection

@push('js')
  <script src="{{ asset('assets/pages/setting.js') }}"></script>
@endpush