<?php

class Book_extension  extends JObject {
	protected $_db;

	// Уведомлять ли по электропочте
	private static $notificateByEmail = true;

	// Если пользователь — редактор, (переменная = true)
	public static $isEditor;

	// Группа администраторов
	public static $allowedUserGroup = 9;


	function __construct() {
		jimport('joomla.access.access');

		$user = JFactory::getUser();

		$userGroup = JAccess::getGroupsByUser($user->id);

		$this-> isEditor = true;
		//($_SERVER['REMOTE_ADDR'] == '192.168.0.107');

		$this->_db = JFactory::getDbo();
	}

	// Отправка уведомления на почту
	public function notificate($requestID, $status, $reason = '') {
		$headers  = "From: gorbib@yandex.ru\r\n"; 
		$headers .= "Content-type: text/html;charset=utf-8\r\n";

		$data = $this-> getRequest($requestID);

		if ( empty($data) ) return false;

		$email = $data['email'];
		$name  = $data['name'];


		if ( empty($email) ) return false;

		if ( empty($name) ) $name = 'Дорогой читатель';

		// Сообщения, отправляемые пользователю
		$Messages = array(
			// Продлено
			'accepted' => array(
				'subject' => 'Книга продлена',
				'text'    => "<p>Здравствуйте, $name. </p><br><p>Мы обработали Вашу заявку и продлили книгу <i>{$data['book']}</i>. Приятного чтения.</p> <br> -- <br> С уважением, Качканарская городская бибилиотека им. Ф. Т. Селянина."
			),
			// Не продлено
			'rejected' => array(
				'subject' => 'Книга НЕ продлена',
				'text'    => "<p>Здравствуйте, $name. </p><br><p>К сожалению, не удалось продлить книгу <i>{$data['book']}</i>. </p><p>Вы можете узнать причину и продлить книгу, позвонив по номеру 6-02-99</p><br> -- \n<br> С уважением, Качканарская городская бибилиотека им. Ф. Т. Селянина."
		));
		
		$statusAsText = ( $status? 'accepted' :'rejected' );

		return mail($email, $Messages[$statusAsText]['subject'].'. Качканарская городская библиотека им. Ф. Т. Селянина', $Messages[$statusAsText]['text'], $headers);
	}

	// Добавить новую заявку
	public function addRequest($name, $email, $book) {
		$name   = htmlspecialchars($name);
		$email  = htmlspecialchars($email);
		$book   = htmlspecialchars($book);

		if(!empty($name) && !empty($email) && !empty($book)) {
			$this->_db->setQuery("INSERT INTO `booksextension` (`name`, `email`, `book`) VALUES ('$name',  '$email',  '$book');");
			return $this->_db->execute();
		} else throw new Book_extension_exception('Не все поля были заполнены! Повторите снова.');
	}

	/**
	 * @desc Одобрить заявку (продлить книгу)
	 * 
	 * @param <int> $requestID Уникальный номер запроса в базе данных
	 * 
	 * @return <boolean> Статус одобрения (удалось или нет)
	 */
	public function acceptRequest($requestID) {
		$requestID = intval($requestID);

		if ( empty($requestID) ) return false;

		$this->_db->setQuery("UPDATE `booksextension` SET `processed` = '1' WHERE `id` = '$requestID';");
		return $this->_db->execute();

	}

	/**
	 * @desc Одобрить заявку (продлить книгу)
	 * 
	 * @param <int> $requestID Уникальный номер запроса в базе данных
	 * 
	 * @return <boolean> Статус одобрения (удалось или нет)
	 */
	public function discardRequest($requestID) {
		$requestID = intval($requestID);

		if ( empty($requestID) ) return false;

		$this->_db->setQuery("UPDATE `booksextension` SET `processed` = '1' WHERE `id` = '$requestID';");
		return $this->_db->execute();
	}

	/**
	 * @desc Получить запрос на продление (один) из базы данных
	 * 
	 * @param <int> $requestID Уникальный номер запроса в базе данных
	 * 
	 * @return <array/boolean> Поля с данными запроса/false в случае неудачи
	 */
	public function getRequest($requestID) {
		if ( empty($requestID) ) return false;

		$this->_db->setQuery("SELECT * FROM `booksextension`  WHERE `id` = '$requestID' LIMIT 1;");
		$requestInfo = $this->_db->loadAssoc();
		

		if ( $requestInfo ) {
			return $requestInfo;
		} else return false;
	}

	/**
	 * @desc Получить запрос на продление (один) из базы данных
	 * 
	 * @param <int> $limit Ограничение на количество выводимых записей
	 * @param <boolean> $all Если true, то выводит все, даже обработанные заявки
	 * 
	 * @return <array/boolean> Поля с данными запроса/false в случае неудачи
	 */
	public function getRequests($limit = 10, $all = false) {
		if (! $this-> isEditor ) return false;

		$this->_db->setQuery("SELECT * FROM `booksextension` ".(!$all? "WHERE `processed` = '0'": '').($limit? " LIMIT $limit": '').';');
		
		$response = $this->_db->loadAssocList();
		//$count = $this->_db->getNumRows();
		
		if ( !empty($response) ) return array('list'=>$response);
	}

	public function test($value='') {
		
	}
}


class Book_extension_exception extends Exception { }
