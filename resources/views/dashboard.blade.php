@extends('layouts.app')
@section('title')
  <title>Dashboard - MIKBAM</title>
@endsection

@section('pagetitle')
  <h1>Dashboard</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('') }}">Home</a></li>	
      <li class="breadcrumb-item active">Dashboard</li>
    </ol>
  </nav>    
@endsection

@section('main')	
	<div class="row">

    <!-- Clock Card -->
    <div class="col-lg-4">
			<div class="card info-card sales-card">
				<div class="card-body">
						<h5 class="card-title"></span>Date <span id="clock-day">| {{ date('D') }}</span></h5>

						<div class="d-flex align-items-center">
								<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
										<i class="bi bi-alarm"></i>
								</div>
								<div class="ps-3">
										<h6 id="clock-time">{{ date('H:i:s') }}</h6>
										<span class="text-muted small pt-2 ps-1" id="clock-date">{{ date('d F Y') }}</span>

								</div>
						</div>
					</div>
        </div>
    </div><!-- End Clock Card -->

    <!-- Router Info Card -->
    <div class="col-lg-4">
			<div class="card info-card revenue-card">
				<div class="card-body">
					<h5 class="card-title" id="router-info-title">RouterBoard</h5>

					<div class="d-flex align-items-center">
						<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
							<i class="bi bi-exclamation-lg"></i>
						</div>
						<div class="ps-3">
							<h6 id="router-info-model">Model</h6>
							<span class='text-muted small pt-2 ps-1' id="router-info-version">Version</span>
						</div>
					</div>
				</div>

			</div>
    </div><!-- End Router Info Card -->

    <!-- CPU Info -->
    <div class="col-lg-4">
        <div class="card info-card revenue-card">

            <div class="card-body">
                <h5 class="card-title">CPU <span id="cpu-info">| Type</span></h5>

                <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-hdd-network"></i>
                    </div>
                    <div class="ps-3">
                        <h6 id="cpu-load">Load %</h6>
                        <span class='text-muted small pt-2 ps-1' id="cpu-frequency">Freq Mhz</span>
                    </div>
                </div>
            </div>

        </div><!-- End CPU Info -->
    </div>
	</div>

	<div class="row">
		<div class="col-lg-8">
			 <div class="row">

					<!-- Memory Traffic -->
					<div class="col-lg-6">
						 <div class="card">
								<div class="card-body pb-0">
									 <h5 class="card-title" id="memory_dashboard_title">Memory Chart</h5>

									 <div id="memory_stat" style="min-height: 250px;"></div>

								</div>
						 </div>
					</div>
					<!-- End Memory Traffic -->

					<!-- HDD Traffic -->
					<div class="col-lg-6">
						 <div class="card">
								<div class="card-body pb-0">
									 <h5 class="card-title" id="hdd_dashboard_title">HDD Chart</h5>

									 <div id="hdd_stat" style="min-height: 250px;"></div>

								</div>
						 </div>
					</div>
					<!-- End HDD Traffic -->

					<!-- Reports -->
					<div class="col-12">
						 <div class="card">

								<div class="filter">
									 <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
									 <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
											<li class="dropdown-header text-start">
												 <h6>Filter</h6>
											</li>

											<li><a class="dropdown-item" href="#">Today</a></li>
											<li><a class="dropdown-item" href="#">This Month</a></li>
											<li><a class="dropdown-item" href="#">This Year</a></li>
									 </ul>
								</div>

								<div class="card-body">
									 <h5 class="card-title" id="traffic-title">Traffic Ether1</h5>

									 <!-- Line Chart -->
									 <canvas id="traffic_dashboard"></canvas>


									 <!-- End Line Chart -->

								</div>

						 </div>
					</div><!-- End Reports -->


			 </div>
		</div>

		<!-- Right side columns -->
		<div class="col-lg-4">
			 <!-- Budget Report -->
			 <div class="card">
					<div class="card-body pb-0">
						 <h5 class="card-title">Interface List </h5>
						 <div class="table-responsive">
								<table class="table">
									 <thead>
											<th>Name</th>
											<th>Type</th>
											<th class="text-center">Status</th>
									 </thead>
									 <tbody id="int_list">
									 </tbody>
								</table>
						 </div>
					</div>
			 </div><!-- End Budget Report -->

			 <!-- Recent Activity -->
			 <div class="card">

					<div class="card-body">
						 <h5 class="card-title">Log</h5>

						 <div class="activity table-responsive">
								<table class="table">
									 <thead>
											<tr>
												 <th>Waktu</th>
												 <th>Deskripsi</th>
												 <th>Status</th>
											</tr>
									 </thead>
									 <tbody id="log_dashboard">
									 </tbody>
								</table>
						 </div>

					</div>
			 </div><!-- End Recent Activity -->

		</div><!-- End Right side columns -->
 </div>
@endsection

@push('js')
  <script src="{{ asset('assets/pages/dashboard.js') }}"></script>
@endpush