/**
 * @component     CG Résa
 * Version			: 1.4.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2022 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
jQuery(function() {
    document.formvalidator.setHandler('size',
        function (value) {
            regex=/^[0-9]+$/;
            return regex.test(value);
        });
    document.formvalidator.setHandler('aphone',
        function (value) {
            regex=/^[0]{1}[1-8]{1}[0-9]{2}[0-9]{2}[0-9]{2}[0-9]{2}$/;
            return regex.test(value);
        });		
});