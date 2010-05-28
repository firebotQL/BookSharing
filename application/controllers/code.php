<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Form Validation extention with File for Code Igniter
* developer : devbro , devbro@devbro.com
* version : 2.1
* last date of modification: 8/8/2009
*
* Rules supported:
* file_required
* file_allowed_type[type]
* file_disallowed_type[type]
* file_size_min[size]
* file_size_max[size]
* file_image_mindim[x,y]
* file_image_maxdim[x,y]
*
*
* info:
* size can be in format of 20KB (kilo Byte) or 20Kb(kilo bit) or 20MB or 20GB or ....
* size with no unit is assume as KB
* type is evaluated based on the file extention.
* type can be given as several types seperated by camma
* type can be one of the groups of: image,application,php_code,word_document,compressed
*
* fixes/added:
* if it is only a file field it still works
* size can have unit
* image testing for dimension
* tests the file for errors before doing anything else, WARNING: this behaviour may change in future.
*
* Notes:
* there is no update or changes toward $_FILES. once validation is done the changes will not leave the class
*
* Change Log:
* 2.1:
*  fixed the issue: http://codeigniter.com/forums/viewthread/123816/P30/#629711
*
*
* future plans:
* mime_test rule
* mp3, ID3 testing
*
*/

class MY_Form_validation extends CI_Form_validation {

    function __construct()
    {
        parent::CI_Form_validation();
    }

    function set_rules($field, $label = '', $rules = '')
    {
        if(count($_POST)===0 AND count($_FILES) > 0)//it will prevent the form_validation from working
        {
            //add a dummy $_POST
            $_POST['DUMMY_ITEM'] = '';
            parent::set_rules($field,$label,$rules);
            unset($_POST['DUMMY_ITEM']);
        }
        else
        {
            //we are safe just run as is
            parent::set_rules($field,$label,$rules);
        }
    }

    function run($group='')
    {
        $rc = FALSE;
        log_message('DEBUG','called MY_form_validation:run()');
        if(count($_POST)===0 AND count($_FILES)>0)//does it have a file only form?
        {
            //add a dummy $_POST
            $_POST['DUMMY_ITEM'] = '';
            $rc = parent::run($group);
            unset($_POST['DUMMY_ITEM']);
        }
        else
        {
            //we are safe just run as is
            $rc = parent::run($group);
        }

        return $rc;
    }

    function file_upload_error_message($error_code)
    {
        switch ($error_code)
        {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload stopped by extension';
            default:
                return 'Unknown upload error';
        }
    }

    function _execute($row, $rules, $postdata = NULL, $cycles = 0)
    {
        log_message('DEBUG','called MY_form_validation::_execute ' . $row['field']);
        //changed based on
        //http://codeigniter.com/forums/viewthread/123816/P10/#619868
        if(isset($_FILES[$row['field']]))
        {// it is a file so process as a file
            log_message('DEBUG','processing as a file');
            $postdata = $_FILES[$row['field']];


            //before doing anything check for errors
            if($postdata['error'] !== UPLOAD_ERR_OK)
            {
                $this->_error_array[$row['field']] = $this->file_upload_error_message($postdata['error']);
                return FALSE;
            }


            $_in_array = FALSE;

            // If the field is blank, but NOT required, no further tests are necessary
            $callback = FALSE;
            if ( ! in_array('file_required', $rules) AND $postdata['size']==0)
            {
                // Before we bail out, does the rule contain a callback?
                if (preg_match("/(callback_\w+)/", implode(' ', $rules), $match))
                {
                    $callback = TRUE;
                    $rules = (array('1' => $match[1]));
                }
                else
                {
                    return;
                }
            }

            foreach($rules as $rule)
            {
                /// COPIED FROM the original class

                // Is the rule a callback?
                $callback = FALSE;
                if (substr($rule, 0, 9) == 'callback_')
                {
                    $rule = substr($rule, 9);
                    $callback = TRUE;
                }

                // Strip the parameter (if exists) from the rule
                // Rules can contain a parameter: max_length[5]
                $param = FALSE;
                if (preg_match("/(.*?)\[(.*?)\]/", $rule, $match))
                {
                    $rule    = $match[1];
                    $param    = $match[2];
                }

                // Call the function that corresponds to the rule
                if ($callback === TRUE)
                {
                    if ( ! method_exists($this->CI, $rule))
                    {
                        continue;
                    }

                    // Run the function and grab the result
                    $result = $this->CI->$rule($postdata, $param);

                    // Re-assign the result to the master data array
                    if ($_in_array == TRUE)
                    {
                        $this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
                    }
                    else
                    {
                        $this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
                    }

                    // If the field isn't required and we just processed a callback we'll move on...
                    if ( ! in_array('file_required', $rules, TRUE) AND $result !== FALSE)
                    {
                        return;
                    }
                }
                else
                {
                    if ( ! method_exists($this, $rule))
                    {
                        // If our own wrapper function doesn't exist we see if a native PHP function does.
                        // Users can use any native PHP function call that has one param.
                        if (function_exists($rule))
                        {
                            $result = $rule($postdata);

                            if ($_in_array == TRUE)
                            {
                                $this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
                            }
                            else
                            {
                                $this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
                            }
                        }

                        continue;
                    }

                    $result = $this->$rule($postdata, $param);

                    if ($_in_array == TRUE)
                    {
                        $this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
                    }
                    else
                    {
                        $this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
                    }
                }

                //this line needs testing !!!!!!!!!!!!! not sure if it will work
                //it basically puts back the tested values back into $_FILES
                //$_FILES[$row['field']] = $this->_field_data[$row['field']]['postdata'];

                // Did the rule test negatively?  If so, grab the error.
                if ($result === FALSE)
                {
                    if ( ! isset($this->_error_messages[$rule]))
                    {
                        if (FALSE === ($line = $this->CI->lang->line($rule)))
                        {
                            $line = 'Unable to access an error message corresponding to your field name.';
                        }
                    }
                    else
                    {
                        $line = $this->_error_messages[$rule];
                    }

                    // Is the parameter we are inserting into the error message the name
                    // of another field?  If so we need to grab its "field label"
                    if (isset($this->_field_data[$param]) AND isset($this->_field_data[$param]['label']))
                    {
                        $param = $this->_field_data[$param]['label'];
                    }

                    // Build the error message
                    $message = sprintf($line, $this->_translate_fieldname($row['label']), $param);

                    // Save the error message
                    $this->_field_data[$row['field']]['error'] = $message;

                    if ( ! isset($this->_error_array[$row['field']]))
                    {
                        $this->_error_array[$row['field']] = $message;
                    }

                    return;
                }
            }
        }
        else
        {
            log_message('DEBUG','Called parent _execute');
            parent::_execute($row, $rules, $postdata,$cycles);
        }
    }


    /**
    * Future function. To return error message of choice.
    * It will use $msg if it cannot find one in the lang files
    *
    * @param string $msg the error message
    */
    function set_error($msg)
    {
        $CI =& get_instance();
        $CI->lang->load('upload');
        return ($CI->lang->line($msg) == FALSE) ? $msg : $CI->lang->line($msg);
    }

    /**
    * tests to see if a required file is uploaded
    *
    * @param mixed $file
    */
    function file_required($file)
    {
        if($file['size']===0)
        {
            $this->set_message('file_required','Uploading a file for %s is required.');
            return FALSE;
        }

        return TRUE;
    }

    /**
    * tests to see if a file is within expected file size limit
    *
    * @param mixed $file
    * @param mixed $max_size
    */
    function file_size_max($file,$max_size)
    {
        $max_size_bit = $this->let_to_bit($max_size);
        if($file['size']>$max_size_bit)
        {
            $this->set_message('file_size_max',"%s is too big. (max allowed is $max_size)");
            return FALSE;
        }
        return true;
    }

    /**
    * tests to see if a file is bigger than minimum size
    *
    * @param mixed $file
    * @param mixed $min_size
    */
    function file_size_min($file,$min_size)
    {
        $max_size_bit = $this->let_to_bit($max_size);
        if($file['size']<$min_size_bit)
        {
            $this->set_message('file_size_min',"%s is too small. (Min allowed is $max_size)");
            return FALSE;
        }
        return true;
    }

    /**
    * tests the file extension for valid file types
    *
    * @param mixed $file
    * @param mixed $type
    */
    function file_allowed_type($file,$type)
    {
        //is type of format a,b,c,d? -> convert to array
        $exts = explode(',',$type);

        //is $type array? run self recursively
        if(count($exts)>1)
        {
            foreach($exts as $v)
            {
                $rc = $this->file_allowed_type($file,$v);
                if($rc===TRUE)
                {
                    return TRUE;
                }
            }
        }

        //is type a group type? image, application, word_document, code, zip .... -> load proper array
        $ext_groups = array();
        $ext_groups['image'] = array('jpg','jpeg','gif','png');
        $ext_groups['application'] = array('exe','dll','so','cgi');
        $ext_groups['php_code'] = array('php','php4','php5','inc','phtml');
        $ext_groups['word_document'] = array('rtf','doc','docx');
        $ext_groups['compressed'] = array('zip','gzip','tar','gz');

        if(array_key_exists($exts[0],$ext_groups))
        {
            $exts = $ext_groups[$exts[0]];
        }

        //get file ext
        $file_ext = strtolower(strrchr($file['name'],'.'));
        $file_ext = substr($file_ext,1);

        if(!in_array($file_ext,$exts))
        {
            $this->set_message('file_allowed_type',"%s should be $type.");
            return false;
        }
        else
        {
            return TRUE;
        }
    }

    function file_disallowed_type($file,$type)
    {
        $rc = $this->file_allowed_type($file,$type);
        if(!$rc)
        {
            $this->set_message('file_disallowed_type',"%s cannot be $type.");
        }

        return $rc;
    }




    //http://codeigniter.com/forums/viewthread/123816/P20/
    /**
    * given an string in format of ###AA converts to number of bits it is assignin
    *
    * @param string $sValue
    * @return integer number of bits
    */
    function let_to_bit($sValue)
    {
        // Split value from name
        if(!preg_match('/([0-9]+)([ptgmkb]{1,2}|)/ui',$sValue,$aMatches))
        { // Invalid input
            return FALSE;
        }

        if(empty($aMatches[2]))
        { // No name -> Enter default value
            $aMatches[2] = 'KB';
        }

        if(strlen($aMatches[2]) == 1)
        { // Shorted name -> full name
        $aMatches[2] .= 'B';
        }

        $iBit   = (substr($aMatches[2], -1) == 'B') ? 1024 : 1000;
        // Calculate bits:

        switch(strtoupper(substr($aMatches[2],0,1)))
        {
            case 'P':
                $aMatches[1] *= $iBit;
            case 'T':
                $aMatches[1] *= $iBit;
            case 'G':
                $aMatches[1] *= $iBit;
            case 'M':
                $aMatches[1] *= $iBit;
            case 'K':
                $aMatches[1] *= $iBit;
            break;
        }

        // Return the value in bits
        return $aMatches[1];
    }

    /**
    * returns false if image is bigger than the dimensions given
    *
    * @param mixed $file
    * @param array $dim
    */
    function file_image_maxdim($file,$dim)
    {
        log_message('debug','MY_form_validation:file_image_maxdim ' . $dim);
        $dim = explode(',',$dim);

        if(count($dim)!==2)
        {
            //bad size given
            $this->set_message('file_image_maxdim','%s has invalid rule expected similar to 150,300 .');
            return FALSE;
        }

        log_message('debug','MY_form_validation:file_image_maxdim ' . $dim[0] . ' ' . $dim[1]);

        //get image size
        $d = $this->get_image_dimension($file['tmp_name']);

        log_message('debug',$d[0] . ' ' . $d[1]);

        if(!$d)
        {
            $this->set_message('file_image_maxdim','%s dimensions was not detected.');
            return FALSE;
        }

        if($d[0] < $dim[0] && $d[1] < $dim[1])
        {
            return TRUE;
        }

        $this->set_message('file_image_maxdim','%s image size is too big.');
        return FALSE;
    }

    /**
    * returns false is the image is smaller than given dimension
    *
    * @param mixed $file
    * @param array $dim
    */
    function file_image_mindim($file,$dim)
    {
        $dim = explode(',',$dim);

        if(count($dim)!==2)
        {
            //bad size given
            $this->set_message('file_image_mindim','%s has invalid rule expected similar to 150,300 .');
            return FALSE;
        }

        //get image size
        $d = $this->get_image_dimension($file['tmp_name']);

        if(!$d)
        {
            $this->set_message('file_image_mindim','%s dimensions was not detected.');
            return FALSE;
        }

        log_message('debug',$d[0] . ' ' . $d[1]);

        if($d[0] > $dim[0] && $d[1] > $dim[1])
        {
            return TRUE;
        }

        $this->set_message('file_image_mindim','%s image size is too big.');
        return FALSE;
    }

    /**
    * attempts to determine the image dimension
    *
    * @param mixed $file_name path to the image file
    * @return array
    */
    function get_image_dimension($file_name)
    {
        log_message('debug',$file_name);
        if (function_exists('getimagesize'))
        {
            $D = @getimagesize($file_name);

            return $D;
        }

        return FALSE;
    }
}



/* End of file MY_form_validation.php */
/* Location: ./system/application/libraries/MY_form_validation.php */
