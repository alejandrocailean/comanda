<?php
 
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\AutentificadorJWT;

include_once __DIR__ . '/../modelORM/AutentificadorJWT.php';
include_once __DIR__ . '/../modelORM/Login.php';

class Empleado extends \Illuminate\Database\Eloquent\Model
{
    protected $primaryKey='dni';


   //Para Alta de Usuario debo recibir nombre, clave y tipo
    public function AltaUsuario($datos)
    {   
        try {
            $user=new Empleado();
            $user->nombre=$datos["dni"];
            $user->nombre=$datos["nombre"];
            $user->clave=$datos["clave"];        
            $user->tipo=$datos["tipo"];           
            $guardar= $user->save();
            
        } catch (\Throwable $th) {
            printf($th);
            $guardar=0;
        }
        if ($guardar==1) {
            $newresponse=array('mensaje'=>"Se guardo con exito");
        }else {
            $newresponse=array('mensaje'=>"No se pudo guardar");
        }         
        return $newresponse;
    }
    
    public function Login($datos)
    {        
        $user=Empleado::find($datos["dni"]);          
        $newresponse=0;
        
        if ($user!=null) {
            if($user->clave===$datos["clave"]){
                $datosJWT=[
                    "dni"=>$user->dni,
                    "clave"=>$user->clave,  
                    "tipo"=>$user->tipo                  
                ];
            
                $token= AutentificadorJWT::CrearToken($datosJWT);
                $newresponse=array('token'=>$token);

                //Guardo sus logueos
                $log= new login();
                $log->dni=$datos["dni"];            
                $log->save();

            }else {
                $newresponse=array('mensaje'=>"Esta mal la clave");
            }
        }else {
            $newresponse=array('mensaje'=>"No se encontro Dni: ".$datos["dni"]);
        }
        
        return $newresponse;

    }

    public function ModificarDatos($leg,$datos)
    {
        $user=empleado::find($leg);
        try {
            $user->nombre=$datos["dni"];
            $user->tipo=$datos["tipo"];            
            $user->save();
            
            return array('mensaje'=>"Empleado Dni: ".$leg." se modifico con exito");

        } catch (\Throwable $th) {

            return array('mensaje'=>"No se pudo modficar");

        }  

    }

   public function estadisticas($empleado)
    {
        // $datos=Login::all();
        $log=[];
        $datos= Login::whereBetween('created_at', [$empleado['fec_ini'], $empleado['fec_fin']])->get();
        foreach ($datos as $key => $value) {
            $fulano=Empleado::find($value->dni);
            if ($fulano->nombre===$empleado['nombre']) {
                for ($i=0; $i <count($datos) ; $i++) { 
                    $log[$i]=$fulano->nombre.' se logueo el:'.$value->created_at;
                }                 
            }            
        }
        
        if ($log==null) {
            return 'No se encontraron datos entre esas fechas para '.$empleado['nombre'];
        }else{        
            return $log;
        }
    }

    public function listado()
    {
        $datos=Empleado::all();
        $emp=[];
        $i=0;
        
        foreach ($datos as $key => $value) {
            
            $emp[$i]=$value->nombre;
            $i++;         
        }
        
        return $emp;

    }

}



?>