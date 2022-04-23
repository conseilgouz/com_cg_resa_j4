/**
 * @component     CG Résa
 * Version			: 2.2.3
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2022 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
Joomla.submitbutton = function(task,mybutton,text)
{
	if (task == '')
	{
		return false;
	}
	else
	{
		var isValid=true;
		var action = task.split('.');
		if (action[1] != 'cancel' && action[1] != 'close')
		{
			var forms = jQuery('form.form-validate');
			for (var i = 0; i < forms.length; i++)
			{
				if (!document.formvalidator.isValid(forms[i]))
				{
					isValid = false;
					mybutton.disabled=false; // enable button again
					mybutton.innerHTML = text; // restore button text
					break;
				}
			}
		}
	
		if (isValid)
		{
			if (task == 'module') return true;
			Joomla.submitform(task);
			return true;
		}
		else
		{
			return false;
		}
	}
}