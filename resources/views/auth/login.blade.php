@extends('layouts.app-login')
@section('main')
<div class="row justify-content-center">
    <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

        <div class="d-flex justify-content-center py-4">
            <a href="index.html" class="logo d-flex align-items-center w-auto">
                <!-- <img src="{{-- asset('/img/logo.png') --}}" alt=""> -->
                <!-- <span class="d-none d-lg-block">MIKBAM</span> -->
            </a>
        </div><!-- End Logo -->

        <div class="card mb-3">

            <div class="card-body">

                <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Mikrotik Bandwidth Management</h5>
                    <p class="text-center small" id="login_title">Login Router</p>
                </div>

                <form class="g-3 needs-validation mb-10" id="form-login">
                    <div class="row g-3" id="ip-container">
                        <div class="col-12">
                            <label class="form-label">IP Address</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text" id="inputGroupPrepend">@</span>
                                <input type="text" name="ip" class="form-control" id="ip-address" list="listip" autocomplete="off" pattern="[0-9\.\/]*">
                            </div>
                        </div>

                        <div class="col-6">
                            <a href="{{ url('/demo') }}" class="btn btn-info w-100">Demo</a>
                            {{-- <button type="button" class="btn btn-info w-100" id="btn-scan">Scan</button> --}}
                            {{-- <div class="text-center"> --}}
                                {{-- <span class="spinner-border text-primary" style="width:30px; height: 30px;"></span> --}}
                            {{-- </div> --}}
                        </div>
                        <div class="col-6">
                            <button class="btn btn-primary w-100" type="button" id="btn-next">Next</button>
                            {{-- <div class="text-center">
                                <span class="spinner-border text-primary" style="width:30px; height: 30px;"></span>
                            </div> --}}
                        </div>

                        {{-- <br />
                        <h5 class="fw-bold">Scan Result</h5>
                        <ul>
                            
                        </ul> --}}
                    </div>
                    <div id="username-container" class="d-none">
                        <div class="row mb-6">
                            <div class="col-12">
                                <label class="form-label">Username</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text" id="inputGroupPrepend">@</span>
                                    <input type="text" name="username" class="form-control" id="username" required autocomplete="off">
                                    <div class="invalid-feedback">Username Wajib Diisi</div>
                                </div>
                            </div>
                        </div><br />

                        <div class="row mb-6">
                            <div class="col-12">
                                <label class="form-label">Password</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text" id="inputGroupPrepend">@</span>
                                    <input type="password" name="password" class="form-control" id="password" autocomplete="off">
                                    <div class="invalid-feedback">Password Wajib Diisi</div>
                                </div>
                            </div>
                        </div> <br />

                        <div class="row mb-6">
                            <div class="col-12">
                                <label class="form-label">Port API</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text" id="inputGroupPrepend">@</span>
                                    <input type="number" name="port" value="8728" class="form-control form-control-solid" id="port" required autocomplete="off">
                                    <div class="invalid-feedback">Port Wajib Diisi</div>
                                </div>
                            </div>
                        </div><br />
                        <div class="row mb-6">
                            <div class="col-6">
                                <button class="btn btn-primary w-100" type="button" id="btn-back">Back</button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-success w-100" type="submit">Login</button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>
@endsection

@push('js')
    <script src="{{ asset('assets/pages/login.js') }}"></script>
    <script>
        const scanningResult = `{{ json_encode(\App\Helpers\AnyHelper::scanRouter()) }}`;
    </script>
@endpush