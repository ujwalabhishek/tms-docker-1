 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once BASEPATH . '/libraries/Session.php';

class TMS_Session extends CI_Session
{
     /**
     * Update an existing session
     *
     * @access    public
     * @return    void
    */
    function sess_update() {
       // skip the session update if this is an AJAX call! This is a bug in CI; see:
       // https://github.com/EllisLab/CodeIgniter/issues/154
       // http://codeigniter.com/forums/viewthread/102456/P15
       $CI =& get_instance();
       if (!($CI->input->is_ajax_request()) ) {
            log_message('debug', 'I updated the session');
            parent::sess_update();
       }else {
            log_message('debug', 'I did not updated the session hence it was an ajax call');
       }
    }

}