var isExtended = 0;
/**
 * slides the sidebar
 */
function slideSideBar(){
    new Effect.toggle('sideBarContents', 'blind', {scaleX: 'true', scaleY: 'true;', scaleContent: false});
	if(isExtended==0){
		new Effect.Fade('sideBarContents',
   	{ duration:0.5, from:0.0, to:1.0 });
		isExtended++;
	}
	else{
		new Effect.Fade('sideBarContents',
   	{ duration:0.5, from:1.0, to:0.0 });
		isExtended=0;
	}	
}

function init(){
	Event.observe('sideBarTab', 'click', slideSideBar, true);
}
