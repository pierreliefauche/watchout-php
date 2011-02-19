/* Variables globales */
// sliders des détails des programmes. Il y en a 18 pour les chaînes.
var sliders = new Array();
// effets sur le changement du header
var tabEffect;
var weekEffect;
// code html des détails et des listes
var haveHtml = new Array();
// heure de dernière update de la liste des programmes actuels
var lastNowUpdate;
// délai en secondes entre chaque update de la liste des programmes actuels
var betweenUpdate = 120;

//on dom ready...
window.addEvent('load', function() {
	leftIsIn = true;
	timeSelected = 'now';
	$('listRight').setStyle('display', 'none');
	
	tabEffect = new Fx.Morph('tab', {duration: '500', fps: '50'});
	$('week').setStyle('display', 'block');
	weekEffect = new Fx.Slide($('week'), {mode: 'vertical', fps: 50, link: 'chain'}).hide();
	
	// on a déjà deux listes de prêtes, on les garde en mémoire
	haveHtml['now'] = $('listLeft').innerHTML;
	haveHtml['tonight'] = $('listRight').innerHTML;
	lastNowUpdate = new Date().getTime();
	
	if (navigator.userAgent.indexOf('iPhone') != -1) 
	setTimeout(hideURLbar, 0);
});

function hideURLbar() { 
	window.scrollTo(0, 1); 
}

function findParent(node, localName) {
	while (node && (node.nodeType != 1 || node.localName.toLowerCase() != localName))
		node = node.parentNode;
	return node;
}

// click sur un onglet
addEventListener("click", function(event) {
	event.preventDefault();
	var element = findParent(event.target, "a");

	if (element.hasClass('tab')) {
		var time = new String(element.get('href').replace(/\#/, ''));
		if ( time == 'night' ) {
			tabEffect.start({'opacity': '0'});
			weekEffect.slideIn();
		} else {
			changeList(time);
		}

	} else if (element.hasClass('show')) {
		if (!haveHtml[element.get('href')]) {
			requestShowDetails(element);
		} else {
			fillShowDetails(element);
		}

	} else if (element.hasClass('separator')) {
		sliders.each(function(slide, index){
			slide.slideOut();
		});
	}
}, true);

// récupère les infos d'un show et les stocke dans le tableau haveHtml[]
function requestShowDetails(element) {
	var channel = new String(/#\d{4,5}/.exec(element.get('class'))).replace(/\#/, '');
	var req = new Request.HTML({
		method: 'get',
		url: 'showDetails.php',
		data: { 
			'link' : element.get('href'),
			'time' : timeSelected,
		},
		update: $('showDetails-'+channel),
		onRequest: function() { 
			sliders.each(function(slide, index){
				slide.slideOut();
			});
			if (!sliders[channel])
				sliders[channel] = new Fx.Slide($('showDetails-'+channel), {mode: 'vertical', fps: 50, link: 'chain'}).hide();
		},
		onComplete: function(response) { 
			haveHtml[element.get('href')] = $('showDetails-'+channel).innerHTML;
			var scroller = new Fx.Scroll($(document.body), {
				wait: false,
				duration: 1200,
				offset: {'x': 0, 'y': $('channel-'+channel).getPosition()['y']},
				transition: Fx.Transitions.Back.easeOut,
				onComplete: function() { sliders[channel].slideIn(); }
			});
			scroller.start(0, $('channel-'+channel).getPosition()['y']).chain(function(){
				scroller.start(0, 0);
			});
		}
	}).send();
}

// affiche les infos d'un show
function fillShowDetails(element) {
	var channel = new String(/#\d{4,5}/.exec(element.get('class'))).replace(/\#/, '');
	if (!sliders[channel])
		sliders[channel] = new Fx.Slide($('showDetails-'+channel), {mode: 'vertical', fps: 50, link: 'chain'}).hide();
	$('showDetails-'+channel).innerHTML = haveHtml[element.get('href')];
	sliders.each(function(slide, index, array){
		if ( index == (array.length-1) ) {
			slide.slideOut().chain(function(){
				var scroller = new Fx.Scroll($(document.body), {
					wait: false,
					duration: 1200,
					offset: {'x': 0, 'y': $('channel-'+channel).getPosition()['y']},
					transition: Fx.Transitions.Back.easeOut,
					onComplete: function() { sliders[channel].slideIn(); }
				});
				scroller.start(0, $('channel-'+channel).getPosition()['y']).chain(function(){
					scroller.start(0, 0);
				});
			});
		} else slide.slideOut();
	});
}

/****************************************************
 Mécanisme de changement de la liste des programmes
 avec un effet de slide.
****************************************************/

// savoir quel panneau est actif
var leftIsIn = true;
// time affiché
var timeSelected = 'now';

function updateNeeded() {
	var currentTime = new Date().getTime();
	if ( lastNowUpdate+1000*betweenUpdate <= currentTime ) return true;
	else return false;
}


function changeList(time) {
	// goes back to the top
	hideURLbar();

	if (leftIsIn) {
		//var listBefore = $('listLeft');
		var listAfter = $('listRight');
	} else {
		//var listBefore = $('listRight');
		var listAfter = $('listLeft');
	}
	
	timeSelected = time;
	
	if ( (!haveHtml[time]) || (updateNeeded()) ) requestList(time, listAfter);
	else fillList(time, listAfter);
}

// récupère la nouvelle liste des programmes
function requestList(time, listAfter) {
	var req = new Request.HTML({
		method: 'get',
		url: 'channelList.php',
		data: { 
			'time' : time,
		},
		update: listAfter,
		onComplete: function(response) { 
			haveHtml[time] = listAfter.innerHTML;
			fillList(time, listAfter);
			if (time='now') lastNowUpdate = new Date().getTime();
		}
	}).send();
}

// affiche la nouvelle liste
function fillList(time, listAfter) {
	//listAfter.innerHTML = haveHtml[time];
	if (leftIsIn) {
		var efefou = new Fx.Morph('channels', {
			duration: '1000', 
			fps: '50',
			transition: Fx.Transitions.Linear,
			onStart: function() {
				$('channels').setStyle('width', '640px');
				$('listRight').setStyle('display', 'block');
				listAfter.innerHTML = haveHtml[time];
			},
			onComplete: function() {
				$('listLeft').setStyle('height', $('listRight').getStyle('height'));
			}
		});
		efefou.start({
			'margin-left': '-320px',
		}).chain(function(){ afterListChange(time); });
	} else {
		var efefou = new Fx.Morph('channels', {
			duration: '1000', 
			fps: '50', 
			transition: Fx.Transitions.Linear,
			onStart: function() {
				$('listLeft').setStyle('height', 'auto');
				listAfter.innerHTML = haveHtml[time];
			},
			onComplete: function() {
				$('listRight').setStyle('display', 'none');
				$('channels').setStyle('width', '320px');
			}
		});
		efefou.start({
			'margin-left': '0px',
		}).chain(function(){ afterListChange(time); });
	}
}


function afterListChange(time) {
	leftIsIn=!leftIsIn;
	$each($$('.tab'), function(onglet) {
		onglet.removeClass('selected');
	});
	if ( time=='tonight' || time=='now' ) 
		$('tab-'+time).addClass('selected');
	else {
		$('tab-night').addClass('selected');
		weekEffect.slideOut();
		tabEffect.start({'opacity': '1'});
	}
}
