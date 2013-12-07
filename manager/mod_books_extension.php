<?php

defined('_JEXEC') or die('Restricted access');

require 'mod_books_extension_class.php';

$bookExtModule = new Book_extension;

$abonements = array('Младший', 'Юношеский', 'Старший');

if ( isset($_GET['book_ext_action']) ) {

	$accepted = ($_GET['book_ext_action'] == 'accept');

	$requestsToProcess = $_REQUEST['requests'];

	if( isset($requestsToProcess) ) {
		foreach ($requestsToProcess as $requestID) {

			// Достанем данные заявки
			$request = $bookExtModule->getRequest($requestID);
			
			// Обработаем заявку
			if ($bookExtModule-> processRequest($requestID, $accepted)) {

				// Уведомим пользователя
				$bookExtModule-> notificate($request, $accepted);
			}

			// Очистим переменную с данными, дабы не засорять память
			unset($request);
		}
	}
}
?>

<form id="mod_book_ext_form_requests" method="post" action="?book_ext_action=accept">
	<?php

	$requestsCount = $bookExtModule->getRequestsCount();
	if ( $requestsCount ) {
		JToolBarHelper::title( $requestsCount.' '.declOfNum($requestsCount, array('непроверенная заявка', 'непроверенных заявки', 'непроверенных заявок')).' на продление' );
	}


	$requests = $bookExtModule->getRequests();
	if($requests) {

		echo '<table class="adminlist"><thead><tr><th>#</th><th>Дата</th><th>Пользователь</th>'.(($bookExtModule->enable_abonement)? '<th>Абонемент</th>':'').'<th>Книга</th><th>&hellip;</th></thead>';

		foreach ($requests as $request) {
			?>
			<tr>
				<td><input type="checkbox" name="requests[]" value="<?=$request['id']?>" title="#<?=$request['id']?>"></td>
				<td><date style="color:grey"><?=$request['date']?></date></td>
				<td><a href="mailto:<?=$request['email']?>" title="IP: <?=$request['ip']?>"><?=$request['name']?></a></td>
				<?php if ($bookExtModule->enable_abonement) { ?>
				<td class="center"><?=$abonements[ $request['abonement'] ]?></td>
				<?php } ?>
				<td><?=$request['book']?></td>
				<td><a href="<?=JURI::current()?>?book_ext_action=discard&amp;requests[]=<?=$request['id']?>" title="Отклонить">&times;</a></td>
			</tr>
			<?php
		}
		echo '</table><p><button type="submit">Продлить выделенные заявки</button></p>';

	} else echo '<p style="margin:30px;text-align:center">Отлично, все заявки обработаны!</p>';

	?>
</form>