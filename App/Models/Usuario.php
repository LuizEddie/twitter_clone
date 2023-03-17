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
            $query = "SELECT u.id, u.nome, u.email, (
                SELECT COUNT(*) FROM usuarios_seguidores as us
                WHERE us.id_usuario = :id AND us.id_usuario_seguindo = u.id
            ) as seguindo_sn 
            FROM usuarios as u WHERE u.nome LIKE :nome AND u.id != :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(":nome", "%". $this->__get("nome")."%");
            $stmt->bindValue(":id", $this->__get("id"));
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function seguirUsuario($id_usuario_seguindo){
            $query = "INSERT INTO usuarios_seguidores(id_usuario, id_usuario_seguindo) VALUES (:id_usuario, :id_usuario_seguindo)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(":id_usuario", $this->__get("id"));
            $stmt->bindValue(":id_usuario_seguindo", $id_usuario_seguindo);
            $stmt->execute();

            return true;
        }

        public function deixarSeguirUsuario($id_usuario_seguindo){
            $query = "DELETE FROM usuarios_seguidores WHERE id_usuario = :id_usuario AND id_usuario_seguindo = :id_usuario_seguindo";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(":id_usuario", $this->__get("id"));
            $stmt->bindValue(":id_usuario_seguindo", $id_usuario_seguindo);
            $stmt->execute();

            return true;
        }

        public function getInfoUsuario(){
            $query = "SELECT nome FROM usuarios WHERE id = :id_usuario";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(":id_usuario", $this->__get("id"));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function getTotalTweets(){
            $query = "SELECT COUNT(*) as total_tweets FROM tweets WHERE id_usuario = :id_usuario";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(":id_usuario", $this->__get("id"));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function getTotalSeguindo(){
            $query = "SELECT COUNT(*) as total_seguindo FROM usuarios_seguidores WHERE id_usuario = :id_usuario";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(":id_usuario", $this->__get("id"));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function getTotalSeguidores(){
            $query = "SELECT COUNT(*) as total_seguidores FROM usuarios_seguidores WHERE id_usuario_seguindo = :id_usuario";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(":id_usuario", $this->__get("id"));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

    }

?>