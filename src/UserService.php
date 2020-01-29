<?php

/**
 * Esta clase será un "servicio". La idea es que la clase nos facilite todas las
 * operaciones que podamos querer hacer con respecto a usuarios.
 *
 * Esta clase por dentro usará la clase FileStore para guardar la lista de usuarios
 * que tendremos en nuestro sistema.
 *
 * El uso de dicha clase será la siguiente:
 *
 * $userService = new UserService();
 * $usuarios = $userService->getAllUsers();
 * 
 * $existe = $userService->userExists('un_usuario');
 *
 * $userService->saveUser('un_usuario_nuevo');
 *
 * Abajo un esquema de como sería la clase
 **/
namespace Source;
use \Source\FileStore;

class UserService {
  
  public function getAllUsers() {
    $fs = new FileStore('../db/usuarios.data');
    $usuarios = $fs->read();
    return $usuarios;
  }
  
  public function userExists(string $user) {
    $usuarios = $this->getAllUsers();
    foreach($usuarios as $u) {
      if ($u == $user) {
        return true;
      }
    }
    return false;
  }
  
  public function saveUser(string $user) {
    // completar
    // buscar todos los usuarios
    $fs = new FileStore('../db/usuarios.data');
    $usuarios = $fs->read();
    // agregar este usuario nuevo
    $usuarios[] = $user;
    // guardar los usuarios
    return $fs->save($usuarios);
  }
}