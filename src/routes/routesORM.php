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

        //Para Alta de Usuario debo recibir dni, nombre, clave y tipo
        $this->post('/alta', function ($request, $response, $args) {

            $parsedBody = $request->getParsedBody();            
            $respuesta=Empleado::AltaUsuario($parsedBody);   
                  
            return $response->withJson( $respuesta);
        });

        //Para Login debo recibir dni y clave
        $this->post('/login', function ($request, $response, $args) {
            $parsedBody = $request->getParsedBody();
            $respuesta=Empleado::login($parsedBody); 
            return $response->withJson($respuesta);
        });

        $this->post('/listado', function ($request, $response, $args) {
            $list=Empleado::listado();                        
            return $response->withJson($list);
        });

    });

    $app->group('/mesa',function() {

        //Debo ingresar codigomesa
        $this->post('/alta',function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $respuesta=Mesa::AltaMesa($parsedBody);

            if ($respuesta==1) {
                $newresponse=$response->withJson(array("guardado"=>"Se guardo con exito"),200);
            }else {
                $newresponse=$response->withJson(array("guardado"=>"No se pudo guardar"),403);
            }

            return $newresponse;
        });
        

        $this->post('/estadomozo',function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();            
            $respuesta=Mesa::EstadoMesaMozo($parsedBody);
            
            return $response->withJson($respuesta);
        
        })->add(Middleware::class.':mozo');

        $this->post('/estadosocio',function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $respuesta=Mesa::EstadoMesaSocio($parsedBody);
            return $response->withJson($respuesta);
        
        })->add(Middleware::class.':socio');

        //debo recibir codigomesa, numpedido
        $this->post('/cobrar',function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();            
            $respuesta=Mesa::cobrar($parsedBody);
            
            return $response->withJson($respuesta);

        })->add(Middleware::class.':mozo');
        
    }); 

    $app->group('/pedido',function() {

        $this->post('/alta', function ($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $respuesta= pedido::Orden($parsedBody);
            return $response->withJson($respuesta);
        })->add(Middleware::class.':mozo');

        $this->post('/estadopedido',function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $respuesta=pedido::EstadoPedido($parsedBody);
            return $response->withJson($respuesta);
        });

        
    });
    
    
    $app->group('/cocinero',function() {

        //debo recibir los valores de las variables numpedido y tiempoentrega
        $this->post('/alta',function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $respuesta=cocinero::EstadoPedido($parsedBody);
            return $response->withJson($respuesta);        
        });

        $this->post('/listado',function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $respuesta=cocinero::listado($parsedBody);
            return $response->withJson($respuesta);    
        });

    })->add(Middleware::class.':cocinero');

    $app->group('/bartender',function() {

        //debo recibir los valores de las variables numpedido y tiempoentrega
        $this->post('/alta',function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $respuesta=bartender::EstadoPedido($parsedBody);
            return $response->withJson($respuesta);              
        });

        $this->post('/listado',function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $respuesta=bartender::listado($parsedBody);
            return $response->withJson($respuesta);  
        });

    })->add(Middleware::class.':bartender');

    $app->group('/cervecero',function() {

        //debo recibir los valores de las variables numpedido y tiempoentrega
        $this->post('/alta',function($request, $response, $args) {
            $parsedBody=$request->getParsedBody();
            $respuesta=cervecero::EstadoPedido($parsedBody);
            return $response->withJson($respuesta);
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