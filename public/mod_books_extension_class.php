<?php

class Book_extension  extends JObject {
	/**
	 * Объект для взаимодействия с базой данных
	 * @var Object
	 */
	protected $_db;

	/**
	 * Имя таблицы с данными модуля в базе данных
	 * @var string
	 */
	private $_db_tablename = 'booksextension';

	/**
	 * Инициализация модуля
	 */
	function __construct() {
		$this->_db = JFactory::getDbo();
	}

	/**
	 * Добавление заявки в базу данных
	 * @param string  $name      Имя пользователя
	 * @param string  $email     Электропочта пользователя
	 * @param boolean $abonement Абонемент
	 * @param string  $book      Название книги
	 */
	public function addRequest($name, $email, $abonement, $book) {
		$name       = addslashes( htmlspecialchars($name) );
		$email      = addslashes( htmlspecialchars($email) );
		$abonement  = intval($abonement);
		$book       = addslashes( htmlspecialchars($book) );

		$ip = $_SERVER['REMOTE_ADDR'];

		if(!empty($name) && !empty($email) && !empty($book)) {
			$this->_db->setQuery("INSERT INTO `#__{$this->_db_tablename}` (`ip`, `name`, `abonement`, `email`, `book`) VALUES ('$ip', '$name', '$abonement', '$email',  '$book');");
			return $this->_db->execute();
		} else throw new Book_extension_exception('Не все поля были заполнены! Повторите снова.');
	}
}


class Book_extension_exception extends Exception { }


/**
 * Проверка валидности электропочты
 * @param  string  $email Электропочта
 * @return boolean        Результат проверки
 */
function validateEmail($email) {
  return !(preg_match('/^([\w\-\.])+@([\w\-\.])+\.([a-z0-9])+$/i', $email) == 0);
}
