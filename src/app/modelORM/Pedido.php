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
        
        //Inicializo variables acumulativas de precio y de pedido
        $costoTotal=0;
        $pedidoMesa='';

        // Inicializo el pedido en la BD
        try {

            $orden=new pedido();
            $orden->mesa=$datos["mesa"];            
            $orden->estado="pendiente";            
            $orden->orden="";
            $orden->precio=0;
            $orden->tiempoentrega=1000000;
            $orden->foto='';
            $guardar=$orden->save();           
            $numPedido=$orden->numPedido;  

        } catch (\Throwable $th) {throw $th;}                
        
        
        //BEBIDAS
        try {
            if (array_key_exists("bebida",$datos)) {

                //Averiguo la cantidad de bebidas y me fijo en la BD el costo
                //Ademas, guardo el total del costo y el pedido en el listado de pedidos

                $bebidasPed=explode(' ',$datos['bebida']); 
                $bebida=comida::find($bebidasPed[1]);
                $costoBebidas=$bebida->precio*$bebidasPed[0];
                $costoTotal+=$costoBebidas;
                $pedidoMesa.=$bebidasPed[0].' '.$bebidasPed[1].'  ';

                //Guardo el pedido en el listado de los bartenders
                $trago=new bartender();           
                $trago->numPedido=$numPedido;           
                $trago->estado='Pendiente';
                $trago->orden=$bebidasPed[0].' '.$bebidasPed[1];
                $trago->save();                

             }

        } catch (\Throwable $th) {throw $th;}        

        //CERVEZAS
        try {
            if (key_exists("cerveza",$datos)) {

                //Averiguo la cantidad de cervezas y me fijo en la BD el costo
                //Ademas, guardo el total del costo y el pedido en el listado de pedidos

                $cervezasPed=explode(' ',$datos['cerveza']);
                $cervezas=comida::find($cervezasPed[1]); 
                $costoCervezas=$cervezas->precio * $cervezasPed[0];
                $costoTotal+=$costoCervezas;
                $pedidoMesa.=$cervezasPed[0].' '.$cervezasPed[1].'  ';
               
                //Guardo el pedido en el listado de los cerveceros
                $cerveza=new cervecero();
                $cerveza->numPedido=$numPedido;
                $cerveza->estado='Pendiente';
                $cerveza->orden=$cervezasPed[0].' '.$cervezasPed[1];
                $cerveza->save();
               
            }

        } catch (\Throwable $th) {throw $th; }
        

        //COMIDAS
        try {
            $costoComidas=0; 
            $listado='';

            for ($i=0; $i <count($datos) ; $i++) { 
            
                if (key_exists('comida'.($i+1),$datos)) {
                    
                    //comidas[0]:cantidad - comidas[1]:nombre comida
                    //Averiguo la cantidad de comidas y me fijo en la BD el costo
                    //Ademas, guardo el total del costo y el pedido en el listado de pedidos
                    $comidas=explode(' ',$datos['comida'.($i+1)]);
                    $morfi=comida::find($comidas[1]);
                    $costoComidas+=$morfi->precio*$comidas[0];  
                    $listado.=$comidas[0].' '. $comidas[1].' ';           
                }  
            }

            //Guardo los totales de costo y pedido
            $costoTotal+=$costoComidas;
            $pedidoMesa.=$listado;

            //Guardo el pedido en el listado de los cocineros
            $comi=new cocinero();
            $comi->numPedido=$numPedido;
            $comi->estado='Pendiente';
            $comi->orden=$listado;
            $comi->save();
                
        }catch (\Throwable $th) {print(   $th);}  


        //Actualizo la BD de pedido
        try {
            $ord=pedido::find($numPedido); 
            $ord->precio=$costoTotal;
            $ord->orden=$pedidoMesa;
            $ord->save();
        } catch (\Throwable $th) {throw $th;}
        

        //Guardo en la BD los datos de la mesa
        try {
            $mesa=mesa::find($datos["mesa"]);
            $mesa->ventas+=$costoTotal;
            $mesa->estado='con cliente esperando pedido';            
            $mesa->save();
        } catch (\Throwable $th) {throw $th; }        
          
        // Armo el JSON para responder
        $numCliente='PE'.substr($numPedido,2);

        $datos=array('nropedido'=>$numCliente,'mesa'=>$datos["mesa"],'pedido'=>$pedidoMesa,'costo'=>$costoTotal);
        return $datos; 
    }

    public function EstadoPedido($datos)
    {
        $numPedido='10'.substr($datos['numpedido'],2);
        $ord=new pedido();
        $ord=pedido::find($numPedido);
       
        if ($ord!=null) {
            if ($datos['nummesa']==$ord->mesa) {
                return array('mensaje'=>'Tiempo restante'.$ord->tiempoentrega);
            }else {
                return array('mensaje'=>'Ingreso mal el numero de mesa');
            }
        }else {
            return array('mensaje'=>'Ingreso mal el numero de pedido');
        }
        
    }

}

?>