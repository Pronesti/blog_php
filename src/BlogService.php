<?php

/**
 * Este servicio será el encargado de guardar texto asociado a un usuario
 *
 *
 * Por ej:
 * 
 * $blogService = new BlogService();
 * $blogService->savePost($contenido, $user);
 *
 * $posts = $blogService->getAllPosts($user);
 *
 *
 * La idea es que use por dentro el FileStore como la clase de usuarios.
 * Aquí lo que guardaremos, será el post correspondiente a un usuario por lo cual
 * si nos dan el usuario 'dario', el FileStore debería crear un archivo llamado
 * 'dario.posts'.
 *
 *
 *
 **/
namespace Source;
use \Source\FileStore;

class BlogService {
  
  public function savePost(string $content, string $user) {
    $fs = new FileStore('../db/posts/' . $user . '.posts');
    $posts = $fs->read();
    //Agregar $content a $posts
    $posts[]=$content;
    //Luego volver a guardar el archivo
    return $fs->save($posts);
  }
  
  public function getAllPosts(string $user) { 
    $fs = new FileStore('../db/posts/' . $user . '.posts');
    return $fs->read();
  }
}