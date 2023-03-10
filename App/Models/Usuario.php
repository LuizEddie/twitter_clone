<?php
    namespace App\Models;

    use MF\Model\Model;

    use PDO;

    class Usuario extends Model{

        private $id;
        private $nome;
        private $email;
        private $senha;

        public function __get($atributo){
            return $this->$atributo;
        }

        public function __set($atributo, $valor){
            $this->$atributo = $valor;
        }

        public function salvar(){
            $query = "INSERT INTO usuarios(nome, email, senha) VALUES(:nome, :email, :senha)";
            $insert = $this->db->prepare($query);
            $insert->bindValue(":nome", $this->__get('nome'));
            $insert->bindValue(":email", $this->__get('email'));
            $insert->bindValue(":senha", $this->__get('senha'));
            $insert->execute();

            return $this;
        }

        public function validarCadastro(){
            $valido = true;
    
            if(strlen($this->__get("nome")) < 3){
                $valido = false;
            }

                
            if(strlen($this->__get("email")) < 3){
                $valido = false;
            }

                
            if(strlen($this->__get("senha")) < 3){
                $valido = false;
            }
    
            return $valido;
        }

        public function getUsuarioPorEmail(){
            $query = "SELECT nome, email FROM usuarios WHERE email = :email";
            $select = $this->db->prepare($query);
            $select->bindValue(":email", $this->__get("email"));
            $select->execute();

            return $select->fetchAll(PDO::FETCH_ASSOC);
        }

        public function autenticar(){
            $query = "SELECT id, nome, email FROM usuarios WHERE email = :email AND senha = :senha";
            $select = $this->db->prepare($query);
            $select->bindValue(":email", $this->__get("email"));
            $select->bindValue(":senha", $this->__get("senha"));
            $select->execute();

            $usuario = $select->fetch(PDO::FETCH_ASSOC);

            if(!empty($usuario['id']) && !empty($usuario['nome'])){
                $this->__set("id", $usuario['id']);
                $this->__set("nome", $usuario['nome']);
            }

            return $this;
        }

        public function getAll(){
            $query = "SELECT id, nome, email FROM usuarios WHERE nome LIKE :nome";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(":nome", "%". $this->__get("nome")."%");
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

    }

?>