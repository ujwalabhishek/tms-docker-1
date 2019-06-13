<?php

class TMS_Exceptions extends Exception {

    public function __construct()
    {
        parent::__construct();
    }

    public function show_php_error($severity, $message, $filepath, $line)
    {	
    
	$severity = ( ! isset($this->levels[$severity])) ? $severity : $this->levels[$severity];
	$filepath = str_replace("\\", "/", $filepath);
	
	// For safety reasons we do not show the full file path
	if (FALSE !== strpos($filepath, '/'))
	{
            $x = explode('/', $filepath);
	    $filepath = $x[count($x)-2].'/'.end($x);
	}
		
	if (ob_get_level() > $this->ob_level + 1)
	{
	    ob_end_flush();	
	}
	ob_start();
	include(APPPATH.'errors/error_php'.EXT);
	$buffer = ob_get_contents();
	ob_end_clean();
		
	$msg = 'Severity: '.$severity.'  --> '.$message. ' '.$filepath.' '.$line;

        log_message('error', $msg , TRUE);

	mail('errors@errors.com', 'TMS: An Error Occurred', $msg, 'From: errors@errors.com');	
	
    }
    

    public function errorMessage()
    {
        log_message('error', $this->getMessage());
        return $this->getMessage();
    }

	function show_error($heading, $message, $template = 'error_general', $status_code = 500)
	{
		set_status_header($status_code);

		$message = '<p>'.implode('</p><p>', ( ! is_array($message)) ? array($message) : $message).'</p>';

//		if (ob_get_level() > $this->ob_level + 1)
//		{
//			ob_end_flush();
//		}
                
//              ob_start();
                
		include(APPPATH.'errors/error_general.php');
		$buffer = ob_get_contents();
		//ob_end_clean();
		return $buffer;
	}
}
