<?php

namespace Inc\Api\Callbacks;

class CategoriesCallbacks
{

	public function categoriesSectionManager()
	{
		echo '.';
	}


	public function categoriesSanitize($input)
	{
		return $input;
	}

	public function textField($args)
	{
		$name = $args['label_for'];
		$option_name = $args['option_name'];
		$value = '';

		if (isset($_POST["edit_taxonomy"])) {
			$input = get_option($option_name);
			$value = $input[$_POST["edit_taxonomy"]][$name];
		}

		echo '<input type="text" class="regular-text" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="' . $value . '" placeholder="' . $args['placeholder'] . '" required>';
	}

	public function checkboxField($args)
	{
		$name = $args['label_for'];
		$classes = $args['class'];
		$option_name = $args['option_name'];
		$checked = false;

		if (isset($_POST["edit_taxonomy"])) {
			$checkbox = get_option($option_name);
			$checked = isset($checkbox[$_POST["edit_taxonomy"]][$name]) ?: false;
		}

		echo '<div class="' . $classes . '"><input type="checkbox" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="1" class="" ' . ($checked ? 'checked' : '') . '><label for="' . $name . '"><div></div></label></div>';
	}
}
