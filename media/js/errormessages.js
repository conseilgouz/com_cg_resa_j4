/**
 * @component     CG Résa
 * Version			: 2.2.3
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2022 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
Joomla.renderMessages = function(messages) {
	var messageContainer = document.getElementById( 'cg_resa_messages' ),
		buttonsubmit = document.getElementById( 'resasubmit' ),	// coming from module
		type, typeMessages, messagesBox, title, titleWrapper, i, messageWrapper, alertClass;
	for ( type in messages ) {
		if ( !messages.hasOwnProperty( type ) ) { continue; }
		// Array of messages of this type
		typeMessages = messages[ type ];
		// Create the alert box
		messagesBox = document.createElement( 'div' );
		// Message class
		alertClass = (type === 'notice') ? 'alert-info' : 'alert-' + type;
		alertClass = (type === 'message') ? 'alert-success' : alertClass;
		alertClass = (type === 'error') ? 'alert-error alert-danger' : alertClass;
		messagesBox.className = 'alert ' + alertClass;
		// Close button
		var buttonWrapper = document.createElement( 'button' );
		buttonWrapper.setAttribute('type', 'button');
		buttonWrapper.setAttribute('data-dismiss', 'alert');
		buttonWrapper.className = 'close';
		buttonWrapper.innerHTML = '×';
		messagesBox.appendChild( buttonWrapper );
		// Title
		title = Joomla.JText._( type );
		// Skip titles with untranslated strings
		if ( typeof title != 'undefined' ) {
			titleWrapper = document.createElement( 'h4' );
			titleWrapper.className = 'alert-heading';
			titleWrapper.innerHTML = Joomla.JText._( type );
			messagesBox.appendChild( titleWrapper );
		}
		// Add messages to the message box
		for ( i = typeMessages.length - 1; i >= 0; i-- ) {
			messageWrapper = document.createElement( 'div' );
			messageWrapper.innerHTML = typeMessages[ i ];
			messagesBox.appendChild( messageWrapper );
		}
		messageContainer.appendChild( messagesBox );
		if (buttonsubmit) { // coming from module : restore button 
			buttonsubmit.disabled=false; // enable button again
			buttonsubmit.innerHTML = buttonsubmit.attributes['data-init'].value; // restore button text
		}
	}	
}
