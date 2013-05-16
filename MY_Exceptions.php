<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Override default CI errors for more verbose error handling
 * 
 * @package     Brazier\Core\Controllers
 * @author      Brazier Dev Team
 */

class MY_Exceptions extends CI_Exceptions {

	public $dom = array();

	public $css_classes = array(

		// bootstrap specific grid styling
		'exception_container'			=> 'container',
		'exception_row'					=> 'row',
		'exception_grid12'				=> 'span12',

		// exception common
		'exception_file_path'			=> 'exception_file_path',
		'exception_line_number'			=> 'label label-important exception_line_number',

		// exception message
		'exception_alert'				=> 'alert alert-error exception_alert',

		// exception trace
		'exception_parent_wrapper'		=> 'exception_wrapper',
		'exception_content_wrapper'		=> 'span12 exception_content_wrapper',
	);
	
	public function __construct() {
		parent::__construct();
	}

	public function show_error($heading, $message, $template = 'error_general', $status_code = 500) {
		try {
			$str = parent::show_error($heading, $message, $template = 'error_general', $status_code = 500);
			throw new Exception($str);
		} catch ( Exception $e ) {

			// get trace
			$trace			= $e->getTrace();

			// template head
			$this->template_head();

			// exception message
			$this->parseMessage( $trace );

			// parse message
			$this->parseError( $trace );

			// template footer
			$this->template_footer();

			// output everything to the browser
			echo implode( $this->dom, "\n" );
		}
	}

	public function parseMessage( $trace ) {

		// error message
		$this->append( '
			<div class="' . $this->css_classes[ 'exception_row' ] . '">
				<div class="' . $this->css_classes[ 'exception_grid12' ] . '">
					<div class="' . $this->css_classes[ 'exception_alert' ] . '">
						<h4>' . $trace[ 0 ][ 'args' ][ 0 ] . '</h4>
						<p>' . $trace[ 0 ][ 'args' ][ 1 ] . '</p>
						<span class="' . $this->css_classes[ 'exception_file_path' ] . '"><small>Path: ' . $trace[ 0 ][ 'file' ] . ' <span class="' . $this->css_classes[ 'exception_line_number' ] . '">' . $trace[ 0 ][ 'line' ] . '</span></small></span>
					</div>
				</div>
			</div>
		' );
		// call stack display trigger
		$this->append( '
			<div class="' . $this->css_classes[ 'exception_row' ] . '">
				<div class="' . $this->css_classes[ 'exception_grid12' ] . '">
					<a href="#" onclick="showStack();" class="button">Show Trace</a>
				</div>
			</div>
		' );
	}

	public function parseError( $trace ) {

		$this->append( '<div id="trace_stack">' );

		foreach ( $trace AS $item ) {

			$this->append( '<div class="' . $this->css_classes[ 'exception_row' ] . '"> ');
			$this->append( '<div class="' . $this->css_classes[ 'exception_content_wrapper' ] . '"> ');

			if ( isset( $item[ 'class' ] ) OR isset( $item[ 'function' ] ) ) {
				$this->append( '<h3>' );
				if ( isset( $item[ 'class' ] ) ) $this->append( $item[ 'class' ] );
				if ( isset( $item[ 'type' ] ) ) $this->append( $item[ 'type' ] );
				if ( isset( $item[ 'function' ] ) ) $this->append( $item[ 'function' ] . '()' );
				$this->append( '</h3>' );
			}

			if ( isset( $item[ 'file' ] ) ) 
				$this->append( '
					<span class="' . $this->css_classes[ 'exception_file_path' ] . '">
						<small>Path: ' . $item[ 'file' ] . ' <span class="' . $this->css_classes[ 'exception_line_number' ] . '">' . $item[ 'line' ] . '</span></small>
					</span> 
				');

			$this->append( '</div> ');
			$this->append( '</div> ' );
		}
		
		$this->append( '</div>' );
	}

	public function template_head() {
		$this->append( '
		<!DOCTYPE html>
		<html lang="en">
		<head>
			<meta charset="utf-8">
			<title>Error - Call Trace</title>
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<meta name="description" content="">
			<meta name="author" content="">			
		' );
		
		// include css
		$this->append( '<style type="text/css">' );
		$this->append('

		/* Bootstrap
		========================== */
		html {
			font-size: 100%;
			-webkit-text-size-adjust: 100%;
			-ms-text-size-adjust: 100%;
		}
		body {
			margin: 0;
			background-color: #eee;
			font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
			font-size: 14px;
			line-height: 20px;
			color: #333;
		}
		.container {
			margin-right: auto;
			margin-left: auto;
			width: 940px;
		}
		.container:before, .container:after {
			display: table;
			line-height: 0;
			content: "";
		}
		.container:after {
			clear: both;
		}
		.row {
			margin-left: -20px;
		}
		.row:before, .row:after {
			display: table;
			line-height: 0;
			content: "";
		}
		.row:after {
			clear: both;
		}
		[class*="span"] {
			float: left;
			min-height: 1px;
			margin-left: 20px;
		}
		.span12 {
			width: 940px;
		}
		.alert {
			position: relative;
			margin: 2em 0 0 0;
			padding: 8px 35px 30px 14px;
			background-color: #F2DEDE;
			border: 1px solid #EED3D7;
			color: #B94A48;
			border-radius: 4px;
				-webkit-border-radius: 4px;
				-moz-border-radius: 4px;
			text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
		}
		.alert h4 {
			margin: 0 0 0.2em 0;
			font-size: 17.5px;
		}
		.alert p {
			margin: 0;
		}
		.label {
			display: inline-block;
			padding: 2px 4px;
			font-size: 11.844px;
			font-weight: bold;
			line-height: 14px;
			color: #FFF;
			text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
			white-space: nowrap;
			vertical-align: baseline;
			background-color: #B94A48;
			border-radius: 3px;
			-webkit-border-radius: 3px;
			-moz-border-radius: 3px;
		}
		.button {
			display: block;
			background: #2f2f2f;
			color: #FFF;
			line-height: 25px;
			text-align: center;
			text-decoration: none;
			font-size: 14px;
		}

		/* Trace Stack
		========================== */
		#trace_stack {
			display: none;
		}
		.exception_content_wrapper {
			position: relative;
			width: 925px;
			margin-bottom: 1px;
			padding: 0 0.5em 2em 0.5em;
			background: #E4E4E4;
		}
		.exception_wrapper .row:nth-child(even) .exception_content_wrapper {
			background: #eee;
		}
		.exception_content_wrapper h3 {
			padding: 0;
			margin: 8px 0 0 0;
			font-size: 16px;
			line-height: 16px;
		}
		.exception_file_path {
			display: block;
			font-size: 90%;
			position: absolute;
			left: 0;
			bottom: 0;
			width: 100%;
			background: rgba(0, 0, 0, 0.4);
			color: #fff;
			text-shadow: none;
		}
		.exception_file_path > small {
			display: block;
			margin: 0 14px;
		}
		.exception_line_number {
			position: absolute;
			right: 0;
			top: 0;
			padding: 0 4px;
			height: 100%;
			min-width: 30px;
			text-align: center;
			border-radius: 0;
			line-height: 20px;
		}
		');
		$this->append( '</style>' );

		// javascript
		$this->append( '
		<script type="text/javascript">
		/*
		 * no jquery here - using this to show / hide the trace stack
		 * feel free to dump this and use jquery - I wanted to do this
		 * without adding any further dependencies.
		 */
		var showStack = function(e) {
			var $this	= e ? e:window.event,
				$c		= document.getElementById("trace_stack");

			if(document.defaultView && document.defaultView.getComputedStyle)
				strValue = document.defaultView.getComputedStyle($c, null).getPropertyValue("display");
			else if($c.currentStyle)
				strValue = $c.currentStyle["display"];

			$c.style.display = strValue === "none" ? "block" : "none";
			$this.target.innerHTML = strValue === "none" ? "Hide Trace" : "Show Trace";

			if ($this.preventDefault) $this.preventDefault();
			$this.returnValue = false;
			return false;
		};
		</script>
		' );
			
		$this->append( '</head>' );
		$this->append( '<body>' );
		$this->append( '<div class="' . $this->css_classes[ 'exception_container' ] . '">' );
	}

	public function template_footer() {
		$this->append( '
				</div>
			</body>
		</html>' );
	}

	protected function append( $content ) {
		array_push( $this->dom, $content );
	}
}