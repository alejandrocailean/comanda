<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

include_once __DIR__ . '/../app/modelORM/Empleado.php';
include_once __DIR__ . '/../../src/app/modelORM/Mesa.php';
include_once __DIR__ . '/../../src/app/modelORM/Middleware.php';
include_once __DIR__ . '/../../src/app/modelORM/Pedido.php';

return function (App $app) {
    $container = $app->getContainer();

    $app->group('/empleado',function() {

        //Para Alta de Usuario debo recibir nombre, clave y tipo
        $this->post('/alta', function ($request, $response, $args) {

            $parsedBody = $request->getParsedBody();            
            $respuesta=Empleado::AltaUsuario($parsedBody);            

            return $response->getBody()->write( $respuesta);
        });

        //Para Login debo recibir id y clave
        $this->post('/login', function ($request, $response, $args) {
            $parsedBody = $request->getParsedBody();
            $respuesta=Empleado::login($parsedBody);            
            return $response->getBody()->write($respuesta);
        });

        $this->post('/listado', function ($request, $response, $args) {
            $list=Empleado::listado();                        
            return $response->withJson($list);
        });

    });

    $app->group('/mesa',function() {
        $this->post('/alta',function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $respuesta=Mesa::AltaMesa($parsedBody);

            if ($respuesta==1) {
                $newresponse=$response->getBody()->write("Se guardo con exito");
            }else {
                $newresponse=$response->getBody()->write("No se pudo guardar");
            }

            return $newresponse;
        });
        

        $this->post('/estadomozo',function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $respuesta=Mesa::EstadoMesaMozo($parsedBody);
            return $response->getBody()->write($respuesta);
        
        })->add(Middleware::class.':mozo');

        $this->post('/estadosocio',function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $respuesta=Mesa::EstadoMesaSocio($parsedBody);
            return $response->getBody()->write($respuesta);
        
        })->add(Middleware::class.':socio');
    
    }); 

    $app->group('/pedido',function() {

        $this->post('/alta', function ($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $respuesta= pedido::Orden($parsedBody);
            return $response->getBody()->write($respuesta);
        });

        $this->post('/estadopedido',function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $respuesta=pedido::EstadoPedido($parsedBody);
            return $response->getBody()->write($respuesta);
        });

        $this->post('/mozo',function($request, $response, $args) {
            
        });

        $this->post('/bartender',function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $respuesta=bartender::EstadoPedido($parsedBody);
            return $response->getBody()->write($respuesta);  
            
        })->add(Middleware::class.':bartender');

        $this->post('/cervecero',function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $respuesta=cervecero::EstadoPedido($parsedBody);
            return $response->getBody()->write($respuesta);

        })->add(Middleware::class.':cervecero');

        $this->post('/cocinero',function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $respuesta=cocinero::EstadoPedido($parsedBody);
            return $response->getBody()->write($respuesta);
            
        })->add(Middleware::class.':cocinero');

        $this->post('/socio',function($request, $response, $args) {
        });

    });
    
    
    $app->group('/cocinero',function() {

        $this->post('/alta',function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $respuesta=cocinero::EstadoPedido($parsedBody);
            return $response->getBody()->write($respuesta);        
        });

        $this->post('/listado',function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $respuesta=cocinero::listado($parsedBody);
            return $response->withJson($respuesta);    
        });

    })->add(Middleware::class.':cocinero');

    $app->group('/bartender',function() {

        $this->post('/alta',function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $respuesta=bartender::EstadoPedido($parsedBody);
            return $response->getBody()->write($respuesta);              
        });

        $this->post('/listado',function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $respuesta=bartender::listado($parsedBody);
            return $response->withJson($respuesta);  
        });

    })->add(Middleware::class.':bartender');

    $app->group('/cervecero',function() {
        $this->post('/alta',function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $respuesta=cervecero::EstadoPedido($parsedBody);
            return $response->getBody()->write($respuesta);
        });

        $this->post('/listado',function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $respuesta=cervecero::listado($parsedBody);
            return $response->withJson($respuesta);
        });
    })->add(Middleware::class.':cervecero');

    $app->group('/estadisticas',function ()
    {
        $this->post('/empleados', function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $datos= empleado::estadisticas($parsedBody);
            return $response->withJson($datos);
        });

        $this->post('/pedidos', function($request, $response, $args) {
            
        });

        $this->post('/mesas', function($request, $response, $args) {
               $datos= mesa::estadisticas();
               return $response->withJson($datos);
        });
    });

    


};