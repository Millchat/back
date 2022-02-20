<?php
namespace Millchat\Models;

use Millchat\Core\DB;
use DateTime;
use Exception;
use JsonSerializable;
use PDO;


/**
 * clase posteo
 */
class Posteos implements JsonSerializable
{

    /**
     * Identifica el posteo
     *
     * @var int
     */
    protected $id_posteo;

    /**
     * Identificador de la persona que creo el posteo
     *
     * @var int
     */
    protected $id_usuario_posteo;

    /**
     * El titulo posteo
     *
     * @var string
     */
    protected $titulo_posteo;

    /**
     * El texto del posteo
     *
     * @var string
     */
    protected $texto_posteo;

    /**
     * La imagen del posteo
     *
     * @var string
     */
    protected $imagen_posteo;

    /**
     * La fecha del posteo
     *
     * @var DateTime
     */
    protected $fecha;

    /**
     * Cantidad de likes
     *
     * @var int
     */
    protected $contador_likes;

    /**
     * Esta funcion recive un id_posteo y retorna un objeto en la base.
     *
     * @param int $id_posteo
     * @throw Exception
     * @return Posteos
     */
    public static function getById($id_posteo)
    {
        $db = DB::getConnection();
        $query = "SELECT * FROM posteo p JOIN perfil_usuario u ON u.id_usuario = p.id_usuario_posteo WHERE p.id_posteo=?";
        $statement = $db->prepare($query);
        $statement->bindParam(1, $id_posteo);
        $statement->execute();

        if ($statement->rowCount() == 0) {
            throw new Exception("no se encuentra el posteo publicado");
        }
        return $statement->fetchObject(self::class);
    }

    /**
     * Recorrer base y trae todos los posteos de un usuario
     */
    public function traer_todos_usuario($id)
    {
        if ($id == "" || $id == null || empty($id)) {
            return false;
        }
        $db = DB::getConnection();
        $query = "SELECT * FROM posteo WHERE id_usuario_posteo = ? ORDER BY fecha DESC";
        $statement = $db->prepare($query);
        $statement->bindParam(1, $id);

        $statement->execute();
        $posteos = array();

        while ($fila = $statement->fetch(PDO::FETCH_OBJ)) {
            $posteos[] = $fila;
        }
        return $posteos;
    }

    /**
     * Recorrer base y trae todos los posteos
     */
    public function traer_todos()
    {
        $db = DB::getConnection();
        $query = "SELECT * FROM posteo p JOIN perfil_usuario u ON u.id_usuario = p.id_usuario_posteo ORDER BY p.fecha DESC";
        $statement = $db->prepare($query);
        $statement->execute();
        $posteos = array();
        while ($fila = $statement->fetch(PDO::FETCH_OBJ)) {
            $posteos[] = $fila;
        }
//         $arr = $statement->errorInfo();
//         print_r($arr);
         
        
        return $posteos;
    }

    /**
     * Inserta un posteo en la base
     */
    public function insertPosteo($array)
    {
        $db = DB::getConnection();
        $query = "INSERT INTO posteo (id_usuario_posteo,titulo_posteo,texto_posteo,imagen_posteo,fecha,contador_likes) VALUES (?,?,?,?,?,?)";
        $statement = $db->prepare($query);
        $statement->bindParam(1, $array['id_usuario_posteo']);
        $statement->bindParam(2, $array['titulo_posteo']);
        $statement->bindParam(3, $array['texto_posteo']);
        $statement->bindParam(4, $array['imagen_posteo']);
        $statement->bindParam(5, $array['fecha']);
        $statement->bindParam(6, $array['contador_likes']);
        if (! $statement->execute()) {

            echo "\nPDOStatement::errorInfo():\n";
            $arr = $statement->errorInfo();
            print_r($arr);

            return false;
            // throw new Exception('Nose pudo insertar el posteo');
        } else {
            return true;
        }
    }

    /**
     * Guarda la informacion del posteo en la base
     *
     * @throws Exception
     * @return boolean
     */
    public function editarPosteo()
    {
        $db = DB::getConnection();
        $query = "UPDATE posteo SET titiulo_posteo=?,texto_posteo=?,imagen_posteo=? WHERE id_posteo=?";
        $statement = $db->prepare($query);
        $statement->bindParam(1, $this->titulo_posteo);
        $statement->bindParam(2, $this->texto_posteo);
        $statement->bindParam(3, $this->imagen_posteo);
        $statement->bindParam(4, $this->id_posteo);
        if (! $statement->execute()) {
            throw new Exception('Nose pudo modificar el posteo');
        }
        return true;
    }

    /**
     *
     * @return MIXED[]
     */
    public function jsonSerialize()
    {
        $array = array();
        $array['id_posteo'] = $this->getid();
        $array['titulo_posteo'] = $this->gettitulo_posteo();
        $array['texto_posteo'] = $this->gettexto_posteo();
        $array['imagen_posteo'] = $this->getimagen_posteo();
        $array['usuario_posteo'] = $this->getusuario_posteo();
        $array['fecha'] = $this->getfecha();
        $array['contador_likes'] = $this->getcontador_likes();
        return $array;
    }

    /**
     * Devuelve el id de posteo
     */
    public function getid()
    {
        return $this->id_posteo;
    }

    /**
     * Devuelve el titulo de un posteo
     */
    public function gettitulo_posteo()
    {
        return $this->titulo_posteo;
    }

    /**
     * Devuelve el texto de un posteo
     */
    public function gettexto_posteo()
    {
        return $this->texto_posteo;
    }

    /**
     * Devuelve la imagen de un posteo
     */
    public function getimagen_posteo()
    {
        return $this->imagen_posteo;
    }

    /**
     * Devuelve la fecha de un posteo
     */
    public function getfecha()
    {
        return $this->fecha;
    }

    /**
     * Devuelve la cantidad de likes de un posteo
     */
    public function getcontador_likes()
    {
        return $this->contador_likes;
    }

    /**
     * Setea el id del posteo
     *
     * @param int $id
     */
    public function setid($id)
    {
        $this->id = $id;
    }

    /**
     * Setea el titulo del posteo
     *
     * @param String $titulo_posteo
     */
    public function settitulo_posteo($titulo_posteo)
    {
        $this->email = $titulo_posteo;
    }

    /**
     * Setea el texto del posteo
     *
     * @param String $texto_posteo
     */
    public function settexto_posteo($texto_posteo)
    {
        $this->texto_posteo = $texto_posteo;
    }

    /**
     * Setea el imagen del posteo
     *
     * @param String $imagen_posteo
     */
    public function setimagen_posteo($imagen_posteo)
    {
        $this->imagen_posteol = $imagen_posteo;
    }

    /**
     * Setea la fecha del posteo
     *
     * @param DateTime $fecha
     */
    public function fecha($fecha)
    {
        $this->imagen_posteol = $fecha;
    }

    /**
     * Setea el contador de likes del posteo
     *
     * @param int $contador_likes
     */
    public function setcontador_likes($contador_likes)
    {
        $this->contador_likes = $contador_likes;
    }
    
    public function getusuario_posteo(){
        return $this->id_usuario_posteo;
    }
}

?>
