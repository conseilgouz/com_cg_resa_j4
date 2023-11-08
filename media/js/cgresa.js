/**
 * @component     CG Resa
 * Version			: 2.3.5
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2023 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
jQuery(document).ready(function($) {
	jQuery('#cg_resa_privacy').click(function(){ // aide
		if (jQuery('#cg_resa_privacy_text').hasClass('cg_show')) {
			jQuery('#cg_resa_privacy_text').removeClass("cg_show");	
		} else {
			jQuery('#cg_resa_privacy_text').addClass("cg_show");	
		}
		return false;
	});
	jQuery('#cg_resa_privacy_text').click(function(){
		jQuery('#cg_resa_privacy_text').removeClass("cg_show");	
	});
    document.formvalidator.setHandler('size',
        function (value) {
            regex=/^[1-9]+$/;
            return regex.test(value);
        });
    document.formvalidator.setHandler('aphone',
        function (value) {
            regex=/^[0]{1}[1-8]{1}[0-9]{2}[0-9]{2}[0-9]{2}[0-9]{2}$/;
            return regex.test(value);
        });		
	
});

/* CSP : externalisation du code javscript */
var time_options,date_options;
var $open, $openlib,$except, $except_time, $except_lib;
var $events, $events_lib ,$events_open;
var $closingday;
var openning, openninglib;
jQuery(document).ready(function() {
	
	if (typeof Joomla === 'undefined' || typeof Joomla.getOptions === 'undefined') {
		console.error('Joomla.getOptions not found!\nThe Joomla core.js file is not being loaded.');
		return true;
	}
	date_options = Joomla.getOptions('com_cg_resa_date'); // param de datepicker
	$events = JSON.parse(date_options.events);
	$events_lib = JSON.parse(date_options.events_lib);
	$except = JSON.parse(date_options.except);
	$except_time = JSON.parse(date_options.except_time);
	$except_lib = JSON.parse(date_options.except_lib);
	$closingday =  JSON.parse(date_options.closingday);
	time_options = Joomla.getOptions('com_cg_resa_time'); // params de timepicker
	$open = JSON.parse(time_options.open);
	$events_open = JSON.parse(time_options.events_open);
	$openlib = JSON.parse(time_options.openlib);
	go_datepicker();
	go_timepicker();
});

function go_datepicker() {
	if (date_options.date_format== '%d-%m-%Y' ) { 
	jQuery( "#"+date_options.id_datepicker).datepicker( "option",
        jQuery.datepicker.regional[ "fr" ] );		
	} 
    jQuery( "#"+date_options.id_datepicker ).datepicker({
	minDate: 0,
	beforeShowDay: specialDays
	})
	.on( "change", function() { // update timepicker
          cal_updated( this  );
        });
	jQuery( "#"+date_options.id_datepicker).datepicker( "option", "showAnim", "slide" );	
	if (date_options.date == "") { // no date in init : set it to today
		cal_updated();	
	}
};

function go_timepicker() {
	
	var d;
    if (time_options.date == '') {
		d = new Date();
	} else {
		var newdate = time_options.date;
		if (time_options.date_format== '%d-%m-%Y' ) { 
			newdate = newdate.split("-").reverse().join("-"); // french format
		} 
		d=new Date(newdate);
	}
	var day = d.getDay();
	openning = $open[day];
	openninglib = $openlib[day];
	// check special events to add openning hours
	eventdate = checkZero((d.getMonth() + 1)+ "") + '-'+ checkZero(d.getDate()+ "") + '-' + d.getFullYear();
	check_hours(eventdate);
	fill_times();
 }
 jQuery('.timepicker_button').on('click',function() {
	 jQuery('#'+time_options.id_timer).val('');
 });
 // Calendar Date updated => update openning hours
function cal_updated(cal) {
	let d,newdate;
	if (!cal) { // no date => set it to today
		d= new Date();
		d= d.getDate().toString()+ "-" + (d.getMonth() + 1).toString() + "-" + d.getFullYear().toString() 
		newdate = d;
		jQuery( "#"+date_options.id_datepicker).val(newdate);
	} else {
		d = cal.value;
		newdate = d;
	}
	if (time_options.date_format== '%d-%m-%Y' ) { 
		 newdate = d.split("-").reverse().join("-"); // french format
	}
	d=new Date(newdate);
	var day = d.getDay(); // jour de la semaine 
	openning = $open[day];
	openninglib = $openlib[day];
	// check special events to add openning hours (date format mm-dd-yyyy)
	eventdate = checkZero((d.getMonth() + 1)+ "") + '-'+ checkZero(d.getDate()+ "") + '-' + d.getFullYear();
	check_hours(eventdate);
	fill_times();
}
function fill_times() {
	const sb = document.querySelector('#'+time_options.id_timer);
	removeAll(sb);
	let newOption = new Option('Heure souhaitée ?','');
	sb.add(newOption);
    libix = 0;
	openning.forEach(function(item){ 
	   // fill times
	   times = item.split(',');
	   onetime = times[0];
	   // libellé
	   var newOption = document.createElement("option");
	   if (openninglib[libix]) {
			lib = '-----'+openninglib[libix]+'-----';
	   } else { // special event 
			lib = '-----'+$events_lib[0]+'-----';
	   }
	   newOption = new Option(lib,onetime.toString().replace(':','h'));
	   sb.add(newOption);
	   libix +=1;
	   interval = 15;
	   while (onetime <= times[1]) {
			var newOption = document.createElement("option");
            newOption.text = onetime.toString();
			newOption.value = onetime.toString().replace(':','h');
			if ( time_options.time && (onetime == time_options.time) )	newOption.selected = true; 
            sb.add(newOption);

			tmp = onetime.split(":");
			heure = parseInt(tmp[0]);
			minutes = parseInt(tmp[1]) + interval;
			if (minutes >= 60) {
				heure += 1;
				minutes = minutes - 60;
			}
			onetime = padLeadingZeros(heure.toString(),2) + ":" +padLeadingZeros(minutes.toString(),2);
		}
    });	
 };   
function padLeadingZeros(num, size) {
    var s = num+"";
    while (s.length < size) s = "0" + s;
    return s;
}
	
function removeAll(selectBox) {
	while (selectBox.options.length > 0) {
       selectBox.remove(0);
	}
}
function specialDays(date) {
    for (var i = 0; i < $except.length; i++) { // closing dates
        if (new Date($except[i]).toString() == date.toString()) {
			if ($except_time[i] == date_options.allday) { // full day closed
				if ($except_lib[i]) {
					return [false, 'closed',$except_lib[i]];
				} else {
					return [false, 'closed',date_options.except_text];
				}
			} 
        }
    }
    for (var i = 0; i < $events.length; i++) { // special dates
        if (new Date($events[i]).toString() == date.toString()) {
            return [true, 'events',$events_lib[i]];
        }
    }
	var day = date.getDay();
	var first = jQuery( "#"+date_options.id_datepicker ).datepicker( "option","firstDay");
    for (var i = 0; i < $closingday.length; i++) {
		if ($closingday[i] == day)  return [false, 'closed',date_options.except_weekly_text];
	}
    return [true, ''];
}
function check_hours(eventdate) {
	if ($events.indexOf(eventdate) > -1 ) { // date has special event
		arr = [openning[0]];
		ev = $events_open[$events.indexOf(eventdate)][0];
		if (arr.length == 0) { // day closed
			arr = [ev];
		} else { // special opening hours
			arr.push(ev);
			arr.sort();
			for (ix = 0;ix < arr.length - 1;ix ++) {
				if (arr[ix].length = 0)  continue;
				$el = arr[ix];
				$el = $el.split(',');
				$next = arr[ix + 1];
				if (typeof $next === 'undefined') { // no std time defined for selected day
					$next = "24:00,00:00";
				}
				$next = $next.split(',');
				$start = $el[0];
				$end = $el[1];
				if ( (($el[0] <= $next[0]) && ($el[1] >= $next[0])) || 
				     (($el[0] >= $next[0]) && ($el[1] <= $next[1])) ) {
					$start = $el[0];
					if ($el[1] >= $next[1]) $end = $el[1] 
					else $end = $next[1];
					arr[ix] = $start+','+$end;
					arr[ix + 1] = '';
				}
			}
			arr.sort();
			if (arr[0].length == 0) arr.shift(); // suppression element vide
		}
		openning= arr;
	}
	// Exceptions : fermeture matin/midi ou soir (toute la journee dans datepicker)
	if ($except.indexOf(eventdate) > -1 ) { // exception
		arr = [];
		lib = [];
		$exc = $except_time[$except.indexOf(eventdate)];
		for (ix = 0;ix < openninglib.length;ix ++) {
			if (openninglib[ix] != $exc) {
				arr.push(openning[ix]);
				lib.push(openninglib[ix]);
			}
		}
		openning = arr;
		openninglib = lib;
	}
}
function checkZero(data){
  if(data.length == 1){
    data = "0" + data;
  }
  return data;
}
 
