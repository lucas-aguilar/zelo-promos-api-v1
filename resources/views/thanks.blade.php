<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <title>Obrigado!</title>
		<meta charset="utf-8">
		<meta name="description" content="Promoção - Zelo.com.vc">
		<meta name="keywords" content="promocao">
    	<style>
			*, html, body, p, i, b{
				font-family: Calibri;
			}

    		header{
			    height: 50px;
			    left: 0;
			    padding: 15px;
			    position: absolute;
    			background-color: #dedede;
    		}

    	</style>
    </head>
    <body>
    	<div class="container">
    		<div class="row">
    			<header class="col-12">
    				<div class="row">
    					<div class="col-12 col-lg-6">{{$location_name}}</div>
    					<div class="col-12 col-lg-6">Zelo.com.vc</div>
    				</div>
    			</header>
    			<div class="col-12 col-lg-9">
    				<div class="row">
    					<div class="col-12">
		    				<h1>Sua promoção foi reservada com sucesso!</h1>
		    			</div>
    					<div class="col-12">
    						<div class="image-wrapper">
    							<img src="{{$image_link}}">
    						</div>
    					</div>
	    			</div>
    			</div>
    		</div>
    	</div>
    </body>
</html>