<?php

namespace Core\Session;

abstract class SessionManager
{
/**
 * methode qui alimente notre session
 * @return void
 * @param mixed $value
 * @param string $key
 */

 public static function set(string $key, mixed $value): void
 {
$_SESSION[$key] = $value;

 }

 /**methode qui permet de récupérer une valeur de la session
  * @return mixed
  * @param string $key
  */
 public static function get(string $key): mixed
 {
    if (!isset($_SESSION[$key])) return null;
    return $_SESSION[$key];
 }

 /**
  * methode qui permet de supprimer une valeur de la session
  * @return void
  * @param string $key
  */
 public static function delete(string $key): void
 {
   if(!self::get($key)) return;
   unset($_SESSION[$key]);
 }

}