<?php
 
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\AutentificadorJWT;


include_once __DIR__ . '/../modelORM/AutentificadorJWT.php';



class Comida extends \Illuminate\Database\Eloquent\Model
{
    protected $primaryKey='nombre';
     
}

?>