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
       
        try {
            $pedi=cervecero::find($numPedido);
            
            if ($pedi!=null) {
                $pedi->estado='En preparacion';
                $pedi->save();

                $time=pedido::find($numPedido);
                $time->tiempoentrega=$datos['tiempoentrega'];
                $time->estado='En preparacion';
                $time->save();

                return array('mensaje'=>'El pedido de '.$pedi->orden.' esta en preparacion');

            }else {return array('mensaje'=>'Mal el numero de pedido');}

        } catch (\Throwable $th) {throw $th;}
        

    }
    
    public function listado()
    {
        //Traigo el listado de pedidos filtrando por pendiente
        $list=cervecero::where('estado', 'Pendiente')->get();  
        $cer=[];
        $i=0;
        
        foreach ($list as $key => $value) {
            $cer[$i]=array( "Pedido"=>$value->numPedido,'Estado'=>$value->estado,'Orden'=>$value->orden);
            $i++;     
        }
        
        return $cer;

    }
}

?>