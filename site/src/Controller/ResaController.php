<?php
/**
 * @component     CG Résa
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2025 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
namespace ConseilGouz\Component\CGResa\Site\Controller;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseInterface;

class ResaController extends BaseController {

    public function display($cachable = false, $urlparams = false) {

		$view = Factory::getApplication()->input->getCmd('view', 'resa');
        Factory::getApplication()->input->set('view', $view);

        parent::display($cachable, $urlparams);

        return $this;
    }
	public static $params;

    public function cancel($key = null)
    {
        $this->setRedirect(
            (string)Uri::getInstance(), 
            Text::_(COM_CGRESA_CANCELLED)
		);
    }
   public function save($key = null, $urlVar = null) {
// Check for request forgeries.
	Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));
     
	$app = Factory::getApplication(); 
	$input = $app->input; 
	$model = $this->getModel('resa');
	$currentUri = (string)Uri::getInstance();
    // get the data from the HTTP POST request
	$data = array();
	$data['view'] = $input->getString('view','','string');
	$data['layout'] = $input->getString('layout','','string');
	$data['name'] = $input->getString('name','','string');
	$data['aphone'] = $input->getString('aphone','','string');
	$data['email'] = $input->getString('email','','string');
	$data['size'] = $input->getString('size','','string');
	$data['datepick'] = $input->getString('datepick','','string');
	$data['timepick'] = $input->getString('timepick','','string');
	$data['msg'] = $input->getString('msg','','string');
	$data['task'] = $input->getString('task','','string');
	// set up context for saving form data
	//$context = "$this->option.resa.$this->context";
    // Validate the posted data.
	$form = $model->getForm($data, false);
	if (!$form) {
        $app->enqueueMessage($model->getError(), 'error');
        return false;
	}
	$validData = $model->validate($form, $data);
    $params = self::getParams();           
	if (( $validData) && ( $params['captcha'] != '0')) {
        PluginHelper::importPlugin('captcha',$params['captcha'] );
        $res = $app->triggerEvent('onCheckAnswer',array($input->get($params['captcha'].'_response_field','','post','')));
        if (!$res[0]) { 
			$validData = false;
			$app->enqueueMessage(Text::_('COM_CGRESA_CAPTCHA_ERR'),'error');
        }
	}
    // Handle the case where there are validation errors
	if ($validData === false) {
        $errors = $model->getErrors();
        // Display up to three validation messages to the user.
        for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
			if ($errors[$i] instanceof \Exception) {
				$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
			} else {
                $app->enqueueMessage($errors[$i], 'warning');
			}
		}
    // Save the form data in the session.
       //  $app->setUserState($context . '.data', $data);
    // Redirect back to the same screen.
        $this->setRedirect($currentUri);
        return false;
    }
    // send message
	$this->send_resa($params,$data);
    // redirect 
	$link = Route::_('index.php?option=com_cgresa&view=resa&layout=thankyou&date='.$data['datepick'].'&time='.$data['timepick'], false);
    $this->setRedirect($link);
	return true;
    }
    public static function send_resa($params,$data) {
		$body = $params['perso'];
		$mailfrom = $params['mailfrom'];
		$mailfromlib = $params['mailfromlib'];
		$mailcc = $params['mailcc'];
		if (isset($params['mailreplyto'])) {
		      $mailreplyto = $params['mailreplyto'];
		} else {
		    $mailreplyto =$mailfrom ;
		}
		$arr_css= array("{name}"=>$data['name'], "{phone}"=>$data['aphone'],"{email}"=>$data['email'],"{date}"=>$data['datepick'], "{time}" =>$data['timepick'], "{number}" => $data['size'],"{msg}" => $data['msg']);
		foreach ($arr_css as $key_c => $val_c) {
			$body = str_replace($key_c, Text::_($val_c),$body);
			$mailcc =str_replace($key_c, Text::_($val_c),$mailcc);
			$mailreplyto =str_replace($key_c, Text::_($val_c),$mailreplyto);
		}
        $subject    = $params['mailsubject'];  
        $mail = Factory::getMailer();
        $mail->setSender( array( $mailfrom, $mailfromlib) );
        $mail->addRecipient( $params['maildest'] );
		$mail->addReplyTo($mailreplyto);
		if ($mailcc) $mail->addCc($mailcc);
		if ($params['mailbcc'])  $mail->addBcc($params['mailbcc']);
		
        $mail->setSubject( mb_convert_encoding($subject, 'UTF-8', 'ISO-8859-1'));
        $mail->setBody(  $body );
        $mail->isHtml(true);
        $sent = $mail->Send();
        if ($params['confirm'] == "1") { // send a copy to customer
            $body = $params['confirmperso'];
            foreach ($arr_css as $key_c => $val_c) {
				$body = str_replace($key_c, Text::_($val_c),$body);
            }
            $subject    = $params['confirmsubject'];  
            $mail = Factory::getMailer();
            $mail->addRecipient( $data['email'], $data['name']);
            if ($params['confirmcc']) $mail->addCc($params['confirmcc']);
            if ($params['confirmbcc']) $mail->addBcc($params['confirmbcc']);
            $mail->setSender( array( $params['confirmfrom'], $params['confimfromlib'] ) );
            $mail->setSubject( mb_convert_encoding($subject, 'UTF-8', 'ISO-8859-1'));
            $mail->setBody(  $body );
            $mail->isHtml(true);
            $sent = $mail->Send();
		}		
    }
    /**
     *  Get Component Parameters
     *  @return  Array
     */
    public static function getParams() {
    	if (self::$params) {
            return self::$params;
		}
	    $db	= Factory::getContainer()->get(DatabaseInterface::class);
		$table = Table::getInstance('ConfigTable','ConseilGouz\\Component\\CGResa\Administrator\\Table\\', array('dbo' => $db));
		$lesparams = json_decode($table->getResaParams()->params,true);
		$lesparams = self::clean($lesparams,$table);
		
		return (self::$params = $lesparams);
    }
    // delete passed dates
    public static function clean($lesparams,$table) {
        $excepts = $lesparams['days'];
        $conges = $lesparams['conges'];
        $events = $lesparams['events'];
        $updated = false;
        if ($excepts) {
            foreach ($excepts as $key => $adate) {
                if (strtotime($adate['exception'].' 23:59:59') < time())  {
                    unset($excepts[$key]);
                    $updated = true;
                }
            }
        }
        if ($conges) {
            foreach ($conges as $key => $adate) {
                if (strtotime($adate['congesfin'].' 23:59:59') < time())  {
                    unset($conges[$key]);
                    $updated = true;
                }
            }
        }
        if ($events) {
            foreach ($events as $key => $adate) {
                if (strtotime($adate['event'].' 23:59:59') < time())  {
                    unset($events[$key]);
                    $updated = true;
                }
            }
        }
        if ($updated) { // store updated parameters
            $lesparams['days'] = $excepts;
            $lesparams['conges'] = $conges;
            $lesparams['events'] = $events;
            $table->upd_params(json_encode($lesparams));
            $table->store();
        }
        return $lesparams;
    }
    
}
