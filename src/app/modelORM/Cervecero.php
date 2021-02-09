<?php
 
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\AutentificadorJWT;


include_once __DIR__ . '/../modelORM/AutentificadorJWT.php';
include_once __DIR__ . '/../modelORM/Pedido.php';


class Cervecero extends \Illuminate\Database\Eloquent\Model
{
    protected $primaryKey='numPedido';
     
    public function EstadoPedido($datos)
    {
        $numPedido='10'.substr($datos['numpedido'],2);
        $pedi=cervecero::find($numPedido);
        
        if ($pedi!=null) {
            $pedi->estado='En preparacion';
            $pedi->save();

            $time=pedido::find($numPedido);
            $time->tiempoentrega=$datos['tiempoentrega'];
            $time->estado='En preparacion';
            $time->save();

            return 'El pedido de '.$pedi->orden.' esta en preparacion';
        }else {
            return 'Mal el numero de pedido';
        }

    }
    
    public function listado()
    {
        $list=cervecero::all();
        $cer=[];
        $i=0;
        
        foreach ($list as $key => $value) {
             
            $cer[$i]="Pedido Nro: ".$value->numPedido.' Estado: '.$value->estado.' Orden: '.$value->orden;
            $i++;         
        }
        
        return $cer;

    }
}

?>