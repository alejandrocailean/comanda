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
        //$empleado=AutentificadorJWT::ObtenerData($datos['token']);
        try {
            $orden=new pedido();
            $orden->mesa=$datos["mesa"];            
            // $orden->numEmpleado=$empleado->id;        
            $orden->estado="pendiente";            
            $orden->orden="";
            $orden->precio=0;
            $orden->tiempoentrega=0;
            $orden->foto='';
            $guardar=$orden->save();           
            $numPedido=$orden->numPedido;  
        } catch (\Throwable $th) {throw $th;}                
                
        try {
            if (array_key_exists("bebida",$datos)) {

                $bebidas_ped=explode(',',$datos['bebida']); 
                $bebida=comida::find($bebidas_ped[1]);
                $costo_bebidas=$bebida->precio*$bebidas_ped[0];
     
                $trago=new bartender();           
                $trago->numPedido=$numPedido;           
                $trago->estado='Pendiente';
                $trago->orden=$bebidas_ped[0].' '.$bebidas_ped[1];
                $trago->save();
                
             }else {
                 $bebida= new stdClass();
                 $bebida->precio=0;
             }

        } catch (\Throwable $th) {throw $th;}        

        try {
            if (key_exists("cerveza",$datos)) {

                $cervezas_ped=explode(',',$datos['cerveza']);
                $cervezas=comida::find($cervezas_ped[1]); 
                $costo_cervezas=$cervezas->precio * $cervezas_ped[0];
                
                $cerveza=new cervecero();
                $cerveza->numPedido=$numPedido;
                $cerveza->estado='Pendiente';
                $cerveza->orden=$cervezas_ped[0].' '.$cervezas_ped[1];
                $cerveza->save();
               
            }else {
                $cerveza= new stdClass();
                $cerveza->precio=0;
            }

        } catch (\Throwable $th) {throw $th; }
        
        try {
            $costocomidas=0; 
            $listado='';

            for ($i=0; $i <count($datos) ; $i++) { 
            
                if (key_exists('comida'.($i+1),$datos)) {
                    
                    //comidas[0]:cantidad/comidas[1]:nombre comida
                    $comidas=explode(',',$datos['comida'.($i+1)]);
                    $morfi=comida::find($comidas[1]);
                    $costocomidas+=$morfi->precio*$comidas[0];  
                    $listado.=$comidas[0].' '. $comidas[1].', ';           
                }  
            }

            $comi=new cocinero();
            $comi->numPedido=$numPedido;
            $comi->estado='Pendiente';
            $comi->orden=$listado;
            $comi->save();
                
        }catch (\Throwable $th) {print(   $th);}  

        $numCliente=substr($numPedido,2);
        $costo=$bebida->precio+$cerveza->precio+$costocomidas+$costo_bebidas+$costo_cervezas;
        $pedidomesa=$listado." ,".$datos["bebida"].", ".$datos["cerveza"];
        
        try {
            $ord=pedido::find($numPedido); 
            $ord->precio=$costo;
            $ord->orden=$pedidomesa;
            $ord->save();
        } catch (\Throwable $th) {throw $th;}
        
        try {
            $mesa=mesa::find($datos["mesa"]);
            $mesa->ventas+=$costo;
            $mesa->estado='con cliente esperando pedido';            
            $mesa->save();
        } catch (\Throwable $th) {throw $th; }        
                
        $datos=array('nropedido'=>$numCliente,'mesa'=>$datos["mesa"],'pedido'=>$pedidomesa,'costo'=>$costo);
        return $datos; 
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


    public function prueba ($var )
    {        
        
        try {
            $costocomidas=0; 
            $listado='';

            for ($i=0; $i <count($var) ; $i++) { 
            
                if (key_exists('comida'.($i+1),$var)) {
                    
                    $comidas=explode(',',$var['comida'.($i+1)]);
                    $morfi=comida::find($comidas[1]);
                    $costocomidas+=$morfi->precio*$comidas[0];    
                    $listado.=$comidas[0].' '. $comidas[1].', ';            
                }  
            }
    
            print($costocomidas);  
            print($listado); 
        }
        
        catch (\Throwable $th) {
           print(   $th);
        }  
        
        
        
       
    }
}

?>