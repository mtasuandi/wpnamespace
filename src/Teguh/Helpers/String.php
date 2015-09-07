<?php
namespace Teguh\Helpers;

class String {
	/**
	 * Generate random string
	 * @param 		$length 	integer
	 * @return 		string
	 */
	public static function generate_key( $length = 35 ) {
		$characters = '013De2w45gGfe789abFc56defGghig46jkl3mno56pqrstuDSvwx56yzABC56DEg6gFGHI6JKjLui898MggN5OPQ5hRShT6UV45asdWjYuZ';
		$string = '';
		for ( $i = 0; $i < $length; $i++ ) {
			$string .= $characters[rand( 0, strlen( $characters ) -1 )];
		}
		return sanitize_text_field( $string );
	}
}