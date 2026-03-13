<?php
/**
 * Representación de los datos de usuario que se recuperan de la base de datos y se pasan por JSON. 
 * La contraseña no se pasa y se almacena cifrada. 
 */
class User{
    private int $id;
    private string $name;
    private string $email;

    public function __construct(int $id, string $name, string $email){
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }

    //Getters
    public function getId(){
        return $this->id;
    }

    public function getName(): string{
        return $this->name;
    }

    public function getEmail(): string{
        return $this->email;
    }

    /**
     * Devuelve el nombre e email del usuario en formato JSON
     * El ID se devuelve codificado en el JWT para no dar información sobre la base de datos
     * @return array{email: string, name: string}
     */
    public function jsonSerialize(){
        return [
            'name' => $this->name,
            'email' => $this->email
        ];
    }
}
?>