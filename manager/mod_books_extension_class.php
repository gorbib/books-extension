<?php

class Book_extension  extends JObject {
	/**
	 * Объект для взаимодействия с базой данных
	 * @var Object
	 */
	protected $_db;

	/**
	 * Уведомлять ли пользователя по электропочте
	 * @var boolean
	 */
	private static $notificateByEmail = true;

	/**
	 * Подпись в уведомительном письме электропочты
	 * @var string
	 */
	private static $email_signature = '<br><p>-- <br>Качканарская городская библиотека им. Ф. Т. Селянина<br>Адрес: 5а мкр.&nbsp;7а&nbsp;дом. Тел: (+7 34341)&nbsp;6–02–99.<br>Сайт: <a href="http://gorbib.org.ru">http://gorbib.org.ru</a></p>';

	/**
	 * Имя таблицы с данными модуля в базе данных
	 * @var string
	 */
	private $_db_tablename = 'booksextension';

	/**
	 * Удалять ли заявку из базы данных после обработки
	 * (При отключёнии, записи в базе данных можно использовать как логи)
	 * @var boolean
	 */
	private static $removeOnProcessed = false;


	/**
	 * Инициализация модуля
	 */
	function __construct() {
		$this->_db = JFactory::getDbo();
	}

	/**
	 * Отправка уведомления пользователю на электропочту
	 * @param  array   $requestData Информация заявки
	 * @param  boolean $accepted    Статус обработки (продления)
	 * @param  string  $comment     Комментарий администратора
	 * @return boolean              Статус отправки сообщения
	 */
	public function notificate($requestData, $accepted, $comment = '') {

		if( !self::$notificateByEmail ) return;

		$email = $requestData['email'];
		$name  = $requestData['name'];

		if ( empty($email) ) return false;
		if ( empty($name) ) $name = 'Дорогой читатель';

		// Сообщения, отправляемые пользователю
		$Messages = array(
			// Продлено
			'accepted' => array(
				'subject' => 'Книга продлена',
				'text'    => "<p>Здравствуйте, $name. </p><br><p>Мы обработали Вашу заявку и продлили книгу <i>{$requestData['book']}</i>. Приятного чтения.</p>"
			),
			// Не продлено
			'rejected' => array(
				'subject' => 'Книга НЕ продлена',
				'text'    => "<p>Здравствуйте, $name. </p><br><p>К сожалению, мы не продлили книгу <i>{$requestData['book']}</i>. Для получения подробной информации свяжитесь с библиотекой.</p>"
		));
		
		$statusAsText = ( $accepted? 'accepted' :'rejected' );


		// Заголовки письма
		$headers  = "From: gorbib@yandex.ru\r\n"; 
		$headers .= "Content-type: text/html;charset=utf-8\r\n";

		// Собственно, отправка
		return mail(
			$email,
			'['.$Messages[$statusAsText]['subject'].'] Качканарская городская библиотека им. Ф. Т. Селянина',
			$Messages[$statusAsText]['text'].(!empty($comment)? "<blockquote><p>Комментарий библиотекаря:</p><p><cite>$comment</cite></p></blockquote>": '').(!empty(self::$email_signature)? self::$email_signature: ''),
			$headers
		);
	}

	/**
	 * Обработать заявку
	 * @param  integer $requestID Номер заявки в базе данных
	 * @param  boolean $accepted  Статус обработки (продления)
	 * @return boolean            Статус исполнения запроса
	 */
	public function processRequest($requestID, $accepted = true) {

		$requestID = intval($requestID);
		
		if (self::$removeOnProcessed) {
			$this->_db->setQuery("DELETE FROM `#__{$this->_db_tablename}` WHERE `id` = '$requestID';");
		} else {
			$this->_db->setQuery("UPDATE `#__{$this->_db_tablename}` SET `processed` = '1', `accepted` = '$accepted' WHERE `id` = '$requestID';");
		}

		return $this->_db->execute();
	}

	/**
	 * Вывести из базы данных одну заявку на продление
	 * @param  integer $requestID Номер заявки в базе данных
	 * @return array              Массив с данными заявки
	 */
	public function getRequest($requestID) {

		$requestID = intval($requestID);

		$this->_db->setQuery("SELECT * FROM `#__{$this->_db_tablename}` WHERE `id` = '$requestID' LIMIT 1;");

		return $this->_db->loadAssoc();
	}

	/**
	 * Вывести из базы данных список заявок на продление
	 * @param  integer $limit         Ограничение на количество доставаемых из базы данных записей
	 * @param  boolean $withProcessed Если «true» — выводит все заявки, в том числе обработанные
	 * @return array                  Массив с данными заявок или false в случае ошибки
	 */
	public function getRequests($limit = 5, $withProcessed = false) {

		$this->_db->setQuery("SELECT * FROM `#__{$this->_db_tablename}` ".(!$withProcessed? "WHERE `processed` = '0'": '').($limit? " LIMIT $limit": '').';');
		
		return $this->_db->loadAssocList();
	}

	/**
	 * Получить количество всех заявок в базе данных
	 * @param  boolean $withProcessed Если «true» — считать все заявки, в том числе обработанные
	 * @return integer                Количество записей
	 */
	public function getRequestsCount($withProcessed = false) {
		$this->_db->setQuery("SELECT * FROM `#__{$this->_db_tablename}` ".(!$withProcessed? "WHERE `processed` = '0'": ''));
		$this->_db->query();
		return $this->_db->getNumRows();
	}
}

class Book_extension_exception extends Exception { }


function declOfNum($number, $titles) {
	$cases = array (2, 0, 1, 1, 1, 2);
	return $titles[ ($number%100>4 && $number%100<20)? 2 : $cases[min($number%10, 5)] ];
}
