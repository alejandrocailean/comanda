<?php
 
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\AutentificadorJWT;


include_once __DIR__ . '/../../../src/app/modelORM/AutentificadorJWT.php';
include_once __DIR__ . '/../../../src/app/modelORM/Comida.php';
include_once __DIR__ . '/../../../src/app/modelORM/Cervecero.php';
include_once __DIR__ . '/../../../src/app/modelORM/Bartender.php';
include_once __DIR__ . '/../../../src/app/modelORM/Cocinero.php';
include_once __DIR__ . '/../../../src/app/modelORM/Mesa.php';


class Pedido extends \Illuminate\Database\Eloquent\Model
{
    protected $primaryKey='numPedido';

     public function Orden($datos)
     {
        $empleado=AutentificadorJWT::ObtenerData($datos['token']);

        $orden=new pedido();
        $orden->mesa=$datos["mesa"];            
        $orden->numEmpleado=$empleado->id;        
        $orden->estado="pendiente";            
        $orden->orden="";
        $orden->precio=0;
        $orden->tiempoentrega=0;
        $orden->foto='';
        $guardar=$orden->save();           
        $numPedido=$orden->numPedido;           
            
    
      
        if (array_key_exists("bebida",$datos)) {
           $bebida=comida::find($datos["bebida"]);
           $trago=new bartender();           
           $trago->numPedido=$numPedido;           
           $trago->estado='Pendiente';
           $trago->orden=$datos["bebida"];
           $trago->save();
           
        }else {
            $bebida= new stdClass();
            $bebida->precio=0;
        }

        if (key_exists("cerveza",$datos)) {
            $bebida2=comida::find($datos["cerveza"]);            
            $cerveza=new cervecero();
            $cerveza->numPedido=$numPedido;
            $cerveza->estado='Pendiente';
            $cerveza->orden=$datos["cerveza"];
            $cerveza->save();
           
        }else {
            $bebida2= new stdClass();
            $bebida2->precio=0;
        }

        if (key_exists("comida",$datos)) {
            $comidas=explode(',',$datos['comida']);
            $costocomidas=0;
            for ($i=0; $i <count($comidas) ; $i++) { 
                $morfi=comida::find($comidas[$i]);
                $costocomidas+=$morfi->precio;
            }

            $comi=new cocinero();
            $comi->numPedido=$numPedido;
            $comi->estado='Pendiente';
            $comi->orden=$datos['comida'];
            $comi->save();
           
        }else {
            $costocomidas=0;
        }

        $numCliente=substr($numPedido,2);
        $costo=$bebida->precio+$bebida2->precio+$costocomidas;
        $pedidomesa=$datos["comida"]." ,".$datos["bebida"].", ".$datos["cerveza"];
        
        $ord=pedido::find($numPedido); 
        $ord->precio=$costo;
        $ord->orden=$pedidomesa;
        $ord->save();

        $mesa=mesa::find($datos["mesa"]);
        $mesa->ventas+=$costo;
        $mesa->estado='con cliente esperando pedido';
        $mesa->save();

        
        
        return 'Numero pedido: '.'PE'.$numCliente.PHP_EOL."Numero de mesa: ".$datos["mesa"].PHP_EOL. "Pedido: ".$pedidomesa.PHP_EOL."Costo: ".$costo.PHP_EOL;        
        
    }

    public function EstadoPedido($datos)
    {
        $numPedido='10'.substr($datos['numpedido'],2);
        $ord=new pedido();
        $ord=pedido::find($numPedido);
       
        if ($ord!=null) {
            if ($datos['nummesa']==$ord->mesa) {
                return 'Tiempo restante: '.$ord->tiempoentrega;
            }else {
                return 'Ingreso mal el numero de mesa';
            }
        }else {
            return 'Ingreso mal el numero de pedido';
        }
        
    }
}

?>