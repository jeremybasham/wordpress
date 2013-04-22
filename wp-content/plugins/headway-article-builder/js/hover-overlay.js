/* Credits to codedrops for the base code used for this hover effect which we modified
 * for our own use :http://tympanus.net/codrops/2012/04/09/direction-aware-hover-effect-with-css3-and-jquery/ */

(function($,undefined){$.fn.centerElement=function(){return this.each(function(index){var elHeight=$(this).outerHeight();var elWidth=$(this).outerWidth();var parentHeight=$(this).parent().outerHeight();var parentWidth=$(this).parent().outerWidth();$(this).css("position","absolute");var topOffset=elHeight/2;var leftOffset=elWidth/2;$(this).css("margin-top","-"+topOffset+"px");$(this).css("margin-left","-"+leftOffset+"px")})};$.HoverDir=function(options,element){this.$el=$(element);this._init(options)};
$.HoverDir.defaults={hoverDelay:0,reverse:false};$.HoverDir.prototype={_init:function(options){this.options=$.extend(true,{},$.HoverDir.defaults,options);this._loadEvents()},_loadEvents:function(){var _self=this;this.$el.find("figure").on("mouseenter.hoverdir, mouseleave.hoverdir",function(event){var $el=$(this),evType=event.type,$hoverElem=$el.find("span"),direction=_self._getDir($el,{x:event.pageX,y:event.pageY}),hoverClasses=_self._getClasses(direction);$hoverElem.removeClass();if(evType==="mouseenter"){$hoverElem.hide().addClass(hoverClasses.from);
clearTimeout(_self.tmhover);_self.tmhover=setTimeout(function(){$hoverElem.show(0,function(){$(this).addClass("spotlight-animate").addClass(hoverClasses.to)})},_self.options.hoverDelay);$hoverElem.find("i").centerElement()}else{$hoverElem.addClass("spotlight-animate");clearTimeout(_self.tmhover);$hoverElem.addClass(hoverClasses.from)}})},_getDir:function($el,coordinates){var w=$el.width(),h=$el.height(),x=(coordinates.x-$el.offset().left-w/2)*(w>h?h/w:1),y=(coordinates.y-$el.offset().top-h/2)*(h>
w?w/h:1),direction=Math.round((Math.atan2(y,x)*(180/Math.PI)+180)/90+3)%4;return direction},_getClasses:function(direction){var fromClass,toClass;switch(direction){case 0:!this.options.reverse?fromClass="spotlight-slideFromTop":fromClass="spotlight-slideFromBottom";toClass="spotlight-slideTop";break;case 1:!this.options.reverse?fromClass="spotlight-slideFromRight":fromClass="spotlight-slideFromLeft";toClass="spotlight-slideLeft";break;case 2:!this.options.reverse?fromClass="spotlight-slideFromBottom":
fromClass="spotlight-slideFromTop";toClass="spotlight-slideTop";break;case 3:!this.options.reverse?fromClass="spotlight-slideFromLeft":fromClass="spotlight-slideFromRight";toClass="spotlight-slideLeft";break}return{from:fromClass,to:toClass}}};var logError=function(message){if(this.console)console.error(message)};$.fn.hoverdir=function(options){if(typeof options==="string"){var args=Array.prototype.slice.call(arguments,1);this.each(function(){var instance=$.data(this,"hoverdir");if(!instance){logError("cannot call methods on hoverdir prior to initialization attempted to call method _");
return}if(!$.isFunction(instance[options])||options.charAt(0)==="_"){logError("no such method");return}instance[options].apply(instance,args)})}else this.each(function(){var instance=$.data(this,"hoverdir");if(!instance)$.data(this,"hoverdir",new $.HoverDir(options,this))});return this}})(jQuery);

/* Auto Align Horizontally */
(function ($) {
$.fn.hAlign = function() {
	return this.each(function(i){
	var w = $(this).width();
	var ow = $(this).outerWidth();	
	var ml = (w + (ow - w)) / 2;	
	$(this).css("margin-left", "-" + ml + "px");
	$(this).css("left", "50%");
	$(this).css("position", "relative");
	});
};
})(jQuery);