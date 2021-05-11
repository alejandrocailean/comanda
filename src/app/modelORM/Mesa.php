<?php
 
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\AutentificadorJWT;


include_once __DIR__ . '/../modelORM/AutentificadorJWT.php';

class Mesa extends \Illuminate\Database\Eloquent\Model
{
    protected $primaryKey='codigomesa';

    public function AltaMesa($datos)
    {   

        if(strlen ( $datos["codigomesa"])===5){
            try {
                $Mesas=new Mesa();
                $Mesas->codigomesa=$datos["codigomesa"];
                $Mesas->estado='cerrada';        
                $Mesas->ventas=0;           
                $guardar= $Mesas->save();
            } catch (\Throwable $th) {
                $guardar=0;
            }             
            
        }else {
            $guardar=0;
            echo("   el codigo debe ser de 5 digitos ");
        }
            return $guardar;
    }

    public function EstadoMesaSocio($datos)
    { 
        $estado=mesa::find($datos['codigomesa']);
        if($estado!=null){
            $datos['estado']==='cerrada';
            $estado->estado=$datos['estado'];
            $estado->save;
            $data=array('mesa'=>$datos['codigomesa'],'estado'=> $datos['estado']);
            
        }else {
            $data= array('estado'=> 'Esta mal el numero de mesa');
        }

        return $data;
    }

    public function EstadoMesaMozo($datos)
    {
        $estado=mesa::find($datos['codigomesa']);
        if($estado!=null){
            if ($datos['estado']!='cerrada') {
                $estado->estado=$datos['estado'];
                $estado->save;
                $data=array('mesa'=>$datos['codigomesa'],'estado'=>$datos['estado']);
                
            }else {
                $data=array('estado'=> 'No tiene permiso para cerrar la mesa');
            }
            
        }else {
            $data=array('estado'=>'Esta mal el numero de mesa');
        }
        
        return $data;

    }

    public function cobrar($datos)
    {
        $estado=mesa::find($datos['codigomesa']);
        if($estado!=null){
            if ($datos['estado']==='cerrada') {
                $estado->estado=$datos['estado'];
                $estado->save;
                $data=array('mesa'=>$datos['codigomesa'],'estado'=>$datos['estado']);
                
            }else {
                $data=array('estado'=> 'No tiene permiso para cerrar la mesa');
            }
            
        }else {
            $data=array('estado'=>'Esta mal el numero de mesa');
        }
        
        return $data;
    }

   public function estadisticas()
    {
        $mesaestadistica=mesa::all();
        $maxVentas=0;
        $mesa=0;
        $listado=[];
        $i=0;

        foreach ($mesaestadistica as $key => $value) {
            if ($value->ventas >$maxVentas) {
                $maxVentas=$value->ventas;
                $mesa=$value->codigomesa;
            }
            
            $listado[$i]='La mesa: '.$value->codigomesa.' vendio: '.$value->ventas;
            $i++;
        }
        $i+=1;
        $listado[$i]= 'La maxima venta fue la mesa: '.$mesa.' vendio: '.$maxVentas;

        return $listado;
    }
    
}

?>