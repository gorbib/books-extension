<style>
#mod_book_ext_form_user table {
	width:100%;

}

#mod_book_ext_form_user table input {
	width:70%;
}

#mod_book_ext_form_requests ul li {
	list-style: none;
	list-style-type: none;

	padding-bottom: 1em;
}
</style>


<?php

require 'mod_books_extension_class.php';

$bookExtModule = new Book_extension;

switch ( $_GET['book_ext_action'] ) {

	// Обработка запроса на продление
	case 'accept':
		if(! $bookExtModule->isEditor) break;

		$requestsToAccept = $_POST['requests'];

		if(isset($requestsToAccept)) {
			foreach ($requestsToAccept as $requestID) {
				
				if ($bookExtModule-> acceptRequest($requestID)) {

					// Уведомим пользователя
					$bookExtModule-> notificate($requestID, 1);
				}
			}
		}

	break;

	case 'discard':

		//echo "Sorry this function is temponary not available.";
		if(! $bookExtModule->isEditor) break;

		$requestsToDiscard = intval($_GET['request']);

		if(isset($requestsToDiscard)) {
			if ($bookExtModule-> discardRequest($requestsToDiscard)) {
				// Уведомим пользователя
				$bookExtModule-> notificate($requestsToDiscard, 0);
			}
				
		}

	break;
	
	default:
		# code...
	break;
} ?>

<form id="mod_book_ext_form_requests" method="post" action="?book_ext_action=accept">
	<h1>Список заявок</h1>

	<ul>
<?php

$requests = $bookExtModule->getRequests();
//print_r($request);
if($requests) {
	//echo "<p>{$requests['count']} заявок</p>";
	foreach ($requests['list'] as $request) {
		?>
		<li><input type="checkbox" name="requests[]" value="<?=$request['id']?>"><date style="color:grey"><?=$request['date']?></date>, <a href="mailto:<?=$request['email']?>"><?=$request['name']?></a> запросил(а) продление книги <strong><?=$request['book']?></strong> <a href="<?=JURI::current()?>?book_ext_action=discard&amp;request=<?=$request['id']?>" title="Отклонить">&times;</a></li>
		<?php
	}
	echo '<button type="submit">Продлить выделенные заявки</button>';
} else echo '<p>Все заявки обработаны!</p>';

?>
</ul>
</form>