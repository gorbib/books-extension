<form id="mod_book_ext_form" method="post" action="?book_ext_action=request">
	
	<style>
		#mod_book_ext_form {
			margin:          20px 17px !important;
		}
		#mod_book_ext_form table {
			border-collapse: collapse;
			border-spacing:  0;
			margin:          10px 0;
			width:           100%;
		}
		#mod_book_ext_form table td, #mod_book_ext_form table tr {
			vertical-align:  top;
		}

		#mod_book_ext_form table input, #mod_book_ext_form table select {
			width:           100%;
		}
	</style>

	<?php

	require 'mod_books_extension_class.php';
	$bookExtModule = new Book_extension;


	$abonements = array('Младший', 'Юношеский', 'Старший');


	switch ( $_GET['book_ext_action'] ) {
		
		case 'request':
			$name       = $_POST['name'];
			$email      = $_POST['email'];
			$abonement  = $_POST['abonement'];
			$book       = $_POST['book'];

			if (! validateEmail($email) ) {
				echo '<p class="tip">Ошибка: Введён неправильный email!</p>';
				break;
			}
			
			try {
				if ( $bookExtModule-> addRequest($name, $email, $abonement, $book) ) {
					echo '<p class="tip">Спасибо, Ваша заявка на продление принята в обработку.</p>';
				} else {
					echo '<p class="tip">Извините, не удалось принять заявку в обработку.</p>';
				}

			} catch (Book_extension_exception $e) {
				echo '<p class="tip">Ошибка: '.$e-> getMessage().'</p>';
			}
		break;
	}
	?>

	<table>
		<tr>
			<td style="width:30%;"><label for="bookext_form_name">Введите Ваши Ф.И.О:</label></td><td><input id="bookext_form_name" name="name" required></td>
		</tr>
		<tr>
			<td><label for="bookext_form_abonement">Выберите абонемент:</label></td>
			<td>
				<select id="bookext_form_abonement" name="abonement" required>
					<?php
					foreach ($abonements as $id => $name) {
						echo "<option value=\"$id\">$name</option>";
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for="bookext_form_email">Введите email:</label></td><td><input id="bookext_form_email" name="email" type="email" required><br><span style="color:#555;font:13px arial">(мы уведомим Вас о продлении)</span></td>
		</tr>
		<tr>
			<td><label for="bookext_form_book">И название книги:</label></td><td><input id="bookext_form_book" name="book" required></td>
		</tr>
	</table>
	<p><button type="submit" class="button">Запросить продление</button></p>
</form>