<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <title>{{$location->name}}</title>
		<meta charset="utf-8">
		<meta name="description" content="Promoção - Zelo.com.vc">
		<meta name="keywords" content="promocao">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
    	<style>
			*, html, body, p, i, b{
				font-family: Calibri;
			}
			html, body{
				height: 100%;
			}
    		header{
    			background-color: #dedede;
    			background-image: url("{{ url('/') }}/storage/cover-images/{{$location->id}}/{{$location->mch_location_tag_name}}.png");
			    background-position: center;
			    background-repeat: no-repeat;
		        background-size: cover;
    		}
    		.logo-img-wrapper{
    			background-color: #fff;
    			background-image: url("{{ url('/') }}/storage/logo-images/{{$location->id}}/{{$location->mch_location_tag_name}}.png");
			    background-position: center;
		        background-size: contain;
			    background-repeat: no-repeat;
				border-radius: 500px;
				border: 3px solid #333;
				height: 260px;
				width: 260px;
				display: block;
    		}
    		.location-name{
    			font-size: 44px;
    		}
    		.location-hours{
    			font-size: 24px;
    		}
    		.location-small-desc{
			    color: #868686;
				font-size: 18px;
    		}
    		.location-description{
    			background-color: #f2f2f2;
    		}
    		.location-description h1{
    			text-align: center;
    		}

			@media only screen and (max-width: 1025px) {
	    		.logo-img-wrapper{
					height: 180px;
					width: 180px;
	    		}
	    		.location-name{
	    			font-size: 18px;
	    		}
	    		.location-hours{
	    			font-size: 15px;
	    		}
    		}

			@media only screen and (max-width: 767px) {
	    		header{
				    background-size: contain;
	    		}
	    		.logo-img-wrapper{
					height: 120px;
					width: 120px;
	    		}
	    		.location-small-desc{
					font-size: 12px;
	    		}
			}

    	</style>
    </head>
    <body>
    	<div class="container-fluid h-100">
    		<div class="row h-100">
				<header class="col-12" style="height: 40%;"></header>
    			<div class="col-12" style="
    				height: 50%;
    				max-height: 330px;
    			">
	    			<div class="container">
	    				<div class="row pt-4">
	    					<div class="col-12">
	    						<div class="logo-img-wrapper float-left"></div>
	    						<div class="row">
									<div class="col-12 px-3 py-2">
										<div class="text-center location-name"> {{$location->name}} </div>
									</div>
									<div class="col-12 px-3 py-2">
										<div class="text-center location-small-desc"> {{$location->small_desc}} </div>
									</div>
									<div class="col-12 px-3 py-2 d-none d-md-block">
										<div class="text-center location-hours"> {{$location->operation_hours}} </div>
									</div>
									<div class="col-12 px-3 py-2 d-none d-md-block">
										<div class="text-center"> 
											<form action="tel:{{$location->phone}}">
												<button class="btn btn-lg btn-primary">
													<i class="material-icons float-left mr-2" style="font-size: 32px;">phone</i>
													Ligar {{$location->phone}}
												</button>
											</form>
										</div>
									</div>
								</div>
	    					</div>
							<div class="col-12 px-3 py-2 mt-4 d-block d-md-none">
								<div class="text-center location-hours"> {{$location->operation_hours}} </div>
							</div>
							<div class="col-12 p-3 d-block d-md-none">
								<div class="text-center"> 
									<form action="tel:{{$location->phone}}">
										<button class="btn btn-lg btn-success">
											<i class="material-icons float-left mr-2" style="font-size: 32px;">phone</i>
											Ligar {{$location->phone}}
										</button>
									</form>
								</div>
							</div>
	    				</div>
    				</div>
    			</div>
    			<div class="col-12 mt-3 location-description">
    				<div class="container"> 
	    				{!!$location->description!!}
	    			</div>
    			</div>
    			<footer class="col-12" style="
    				background-color: #333;
    				color: #fff;
    			">
    				<div class="row p-5">
    					<div class="col-12 text-center">
    						<i>Powered by </i> <a href="{{ url('/') }}/sobre" style="color: #fff;">Zelo.com.vc</a>
    					</div>
    					<div class="col-12 text-center">
    						Zelo Digital LTDA - Todos os direitos reservados.
    					</div>
    				</div>
    			</footer>
    		</div>
    	</div>
    	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    </body>
</html>