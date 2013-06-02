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

function validateEmail($email) {
  if(preg_match('/^([\w\-\.])+@([\w\-\.])+\.([a-z0-9])+$/i', $email) == 0)  return FALSE; else  return TRUE;
}

require 'mod_books_extension_class.php';

$bookExtModule = new Book_extension;

switch ( $_GET['book_ext_action'] ) {
	
	case 'request':
		$name   = $_POST['name'];
		$email  = $_POST['email'];
		$book   = $_POST['book'];

		if (! validateEmail($email) ) {
			echo '<br><p class="tip">Ошибка: Введён неправильный email!</p>';
			break;
		}
		
		try {
			$requestStatus = $bookExtModule-> addRequest($name, $email, $book);

			if ($requestStatus) {
				echo '<br><p class="tip">Спасибо, Ваша заявка на продление принята в обработку.<p>';
			} else {
				echo '<br><p class="tip">Извините, не удалось принять заявку в обработку.<p>';
			}

		} catch (Book_extension_exception $e) {
			echo '<p class="tip">Ошибка: '.$e-> getMessage().'</p>';
		}
	break;
	
	default:
		# code...
	break;
}
?>
<div style="margin: 20px 17px">
	<form id="mod_book_ext_form_user" method="post" action="?book_ext_action=request">
	
		<table width="100%">
			<tr>
				<td style="width:30%;"><label for="bookext_form_name">Введите Ваши Ф.И.О:</label></td><td><input id="bookext_form_name" name="name" required></td>
			</tr>
			<tr>
				<td><label for="bookext_form_email">Введите Ваш email:</label></td><td><input id="bookext_form_email" name="email" type="email" required><br><span style="color:#555;font:13px arial">(мы уведомим Вас о продлении)</span></td>
			</tr>
			<tr>
				<td><label for="bookext_form_book">И название книги:</label></td><td><input id="bookext_form_book" name="book" required></td>
			</tr>
		</table>
		<p><button type="submit">Запросить продление</button></p>
	</form>
</div>