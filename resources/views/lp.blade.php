<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <title>{{$lp_title}}</title>
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
    					<div class="col-12 col-lg-6">{{$location->name}}</div>
    					<div class="col-12 col-lg-6">Zelo.com.vc</div>
    				</div>
    			</header>
    			<div class="col-12 col-lg-9">
    				<div class="row">
    					<div class="col-12">
		    				<h1>{{$promotion->title}}</h1>
		    			</div>
    					<div class="col-12">
    						<div class="image-wrapper">
    							<img src="{{$image_link}}">
    						</div>
    					</div>
    					<div class="col-12">
    						<h3>Descrição</h3>
    						{{$promotion->description}}
    					</div>
    					<div class="col-12">
    						<h3>Regras</h3>
    						{{$promotion->rules}}
    					</div>
	    			</div>
    			</div>
    			<div class="col-12 col-lg-3">
    				<div class="row">
	    				<form class="col-12" method="POST" action="{{action('LandingPageController@store')}}">
						    @csrf
						    <h3>Cadastre-se aqui para reservar a promoção:</h3>
							<div class="form-group">
								<label for="firstnameInput">Nome</label>
								<input type="text" class="form-control" id="firstnameInput" name="firstname" placeholder="Digite aqui o seu nome...">
							</div>
							<div class="form-group">
								<label for="lastnameInput">Sobrenome</label>
								<input type="text" class="form-control" id="lastnameInput" name="lastname" placeholder="Digite aqui o seu sobrenome...">
							</div>
							<div class="form-group">
								<label for="emailInput">E-mail</label>
								<input type="email" class="form-control" id="emailInput" name="email" placeholder="Digite aqui o seu e-mail...">
							</div>
							<div class="form-group">
								<label for="phoneInput">Telefone</label>
								<input type="text" class="form-control" id="phoneInput" name="phone" placeholder="Digite aqui o seu telefone...">
							</div>
							<div class="form-group">
								<label for="birthdateInput">Aniversário</label>
								<input type="date" class="form-control" id="birthdateInput" name="birthdate" placeholder="Digite aqui o seu aniversário...">
							</div>
							<div class="form-group">
								<label for="documentInput">CPF para confirmação no local</label>
								<input type="text" class="form-control" id="documentInput" name="document" placeholder="Digite aqui o seu CPF...">
							</div>
							<input type="hidden" id="pidInput" name="pid" value="{{$promotion->id}}">
							<input type="submit" name="Enviar">
						</form>
					</div>
    			</div>
    		</div>
    	</div>
    </body>
</html>