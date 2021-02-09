<?php

use App\Models\AutentificadorJWT;

include_once __DIR__ . '/../modelORM/AutentificadorJWT.php';

class Middleware{
    
    public function bartender($request, $response,$next)
    {
        $parsedBody=$request->getParsedBody();
        $tokenDecodificado=AutentificadorJWT::VerificarToken($parsedBody["token"]); 
        if(strtolower ($tokenDecodificado->data->tipo)==="bartender")        
        {
            $response = $next($request, $response);        
            return $response;

        }else {
            return $response->withJson("Acceso Prohibido",401);
        }  
    }

    public function cervecero($request, $response,$next)
    {
        $parsedBody=$request->getParsedBody();
        $tokenDecodificado=AutentificadorJWT::VerificarToken($parsedBody["token"]); 
        if(strtolower ($tokenDecodificado->data->tipo)==="cervecero")        
        {
            $response = $next($request, $response);        
            return $response;

        }else {
            return $response->withJson("Acceso Prohibido",401);
        } 
    }

    public function cocinero($request, $response,$next)
    {
        $parsedBody=$request->getParsedBody();
        $tokenDecodificado=AutentificadorJWT::VerificarToken($parsedBody["token"]); 
        if(strtolower ($tokenDecodificado->data->tipo)==="cocinero")        
        {
            $response = $next($request, $response);        
            return $response;

        }else {
            return $response->withJson("Acceso Prohibido",401);
        } 
    }

    public function mozo($request, $response,$next)
    {
        $parsedBody=$request->getParsedBody();
        $tokenDecodificado=AutentificadorJWT::VerificarToken($parsedBody["token"]); 
        if(strtolower ($tokenDecodificado->data->tipo)==="mozo")        
        {
            $response = $next($request, $response);        
            return $response;

        }else {
            return $response->withJson("Acceso Prohibido",401);
        } 
    }

    public function socio($request, $response,$next)
    {
        $parsedBody=$request->getParsedBody();
        $tokenDecodificado=AutentificadorJWT::VerificarToken($parsedBody["token"]); 
        if(strtolower ($tokenDecodificado->data->tipo)==="socio")        
        {
            $response = $next($request, $response);        
            return $response;

        }else {
            return $response->withJson("Acceso Prohibido",401);
        } 
    }

    public static function verificartoken($request, $response,$next){
        
        $parsedHead = $request->getQueryParams();            
        $tokenDecodificado=AutentificarToken::VerificarToken($parsedHead["token"]); 
        if(strtolower ($tokenDecodificado->data->cargo)==="encargado")        
        {
            $response = $next($request, $response);        
            return $response->withJson($tokenDecodificado, 200);

        }elseif (strtolower( $tokenDecodificado->data->cargo)=="gerente") {
            $response = $next($request, $response);        
            return $response->withJson($tokenDecodificado, 200);

        }else {
            return $response->withJson("Acceso Prohibido",401);
        }        
    }    
}
?>