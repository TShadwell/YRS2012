(function(a){var r=document.defaultView&&document.defaultView.getComputedStyle,s=/([A-Z])/g,j=/-([a-z])/ig,k=function(a,j){return j.toUpperCase()},m=/float/i,n=/^-?\d+(?:px)?$/i,l=/^-?\d/;a.styles=function(a,o){if(!a)return null;var b;b=r?r(a,null):a.currentStyle?a.currentStyle:void 0;for(var e,c,p=a.style,d={},x=0,g,y;x<o.length;x++)(g=o[x],e=g.replace(j,k),m.test(g)&&(g=jQuery.support.cssFloat?"float":"styleFloat",e="cssFloat"),r)?(g=g.replace(s,"-$1").toLowerCase(),c=b.getPropertyValue(g),"opacity"=== g&&""===c&&(c="1"),d[e]=c):(c=g.replace(j,k),d[e]=b[g]||b[c],!n.test(d[e])&&l.test(d[e])&&(g=p.left,y=a.runtimeStyle.left,a.runtimeStyle.left=a.currentStyle.left,p.left="fontSize"===c?"1em":d[e]||0,d[e]=p.pixelLeft+"px",p.left=g,a.runtimeStyle.left=y));return d};a.fn.styles=function(){return a.styles(this[0],a.makeArray(arguments))}})(jQuery); (function(a){var r=0,s=null,j=[],k=null,m=a.fn.animate,n=function(b,e){var c=!(this[0]&&this[0].nodeType),j=!c&&"inline"===a(this).css("display")&&"none"===a(this).css("float"),d;for(d in b)if("show"==b[d]||"hide"==b[d]||"toggle"==b[d]||a.isArray(b[d])||0>b[d]||"zIndex"==d||"z-index"==d)return!0;return!0===b.jquery||null===l()||a.isEmptyObject(b)||4==e.length||"string"==typeof e[2]||a.isPlainObject(e)||j||c},l=function(){if(!k){var b,a=document.createElement("fakeelement"),c={transition:{transitionEnd:"transitionEnd", prefix:""},MozTransition:{transitionEnd:"animationend",prefix:"-moz-"},WebkitTransition:{transitionEnd:"webkitAnimationEnd",prefix:"-webkit-"}};for(b in c)void 0!==a.style[b]&&(k=c[b])}return k},A={top:function(a){return a.position().top},left:function(a){return a.position().left},width:function(a){return a.width()},height:function(a){return a.height()},fontSize:function(){return"1em"}},o=function(b){var e={};a.each(b,function(a,b){e[l().prefix+a]=b});return e};a.fn.animate=function(b,e,c,k){if(n.apply(this, arguments))return m.apply(this,arguments);var d=jQuery.speed(e,c,k);this.queue(d.queue,function(e){var g,c=[],k="",f,h=a(this),p=d.duration,m,t="{ from {",u=function(b,i){h.css(b);h.css(o({"animation-duration":"","animation-name":"","animation-fill-mode":"","animation-play-state":""}));d.old&&i&&d.old.call(h[0],!0);a.removeData(h,m,!0)};for(f in b)c.push(f);"-moz-"===l().prefix&&a.each(c,function(b,i){var c=A[a.camelCase(i)];c&&"auto"==h.css(i)&&h.css(i,c(h))});g=h.styles.apply(h,c);a.each(c,function(c, i){var d=i.replace(/([A-Z]|^ms)/g,"-$1").toLowerCase();t+=d+" : "+("number"===typeof g[i]&&!a.cssNumber[i]?g[i]+"px":g[i])+"; ";k+=d+" : "+("number"===typeof b[i]&&!a.cssNumber[i]?b[i]+"px":b[i])+"; "});var n=t+="} to {"+k+" }}",q,v;a.each(j,function(a,b){n===b.style?(q=b.name,b.age=0):b.age+=1});if(!q&&(s||(f=document.createElement("style"),f.setAttribute("type","text/css"),f.setAttribute("media","screen"),document.getElementsByTagName("head")[0].appendChild(f),window.createPopup||f.appendChild(document.createTextNode("")), s=f.sheet),f=s,q="jquerypp_animation_"+r++,f.insertRule("@"+l().prefix+"keyframes "+q+" "+n,f.cssRules&&f.cssRules.length||0),j.push({name:q,style:n,age:0}),j.sort(function(a,b){return a.age-b.age}),20<j.length)){v=j.pop();v=v.name;for(var w=f.cssRules.length-1;0<=w;w--){var z=f.cssRules[w];if(7===z.type&&z.name==v){f.deleteRule(w);break}}}f=q;m=f+".run";a._data(this,m,{stop:function(a){h.css(o({"animation-play-state":"paused"}));h.off(l().transitionEnd,u);a?u(b,!0):u(h.styles.apply(h,c),!1)}});h.css(o({"animation-duration":p+ "ms","animation-name":f,"animation-fill-mode":"forwards"}));h.one(l().transitionEnd,function(){u(b,!0);e()})});return this}})(jQuery);
function haversine(a,b){
	console.log(a);
	console.log(b);
	return 6367500*2*Math.asin(
		Math.sqrt(
			Math.pow(
				Math.sin(
					(b[0]-a[0])/2
				),
				2
			)+
			Math.cos(a[0])*Math.cos(b[0])*
			Math.pow(Math.sin((b[1]-a[1])/2))
		)
	);
}
function beginMapping(lat, lng, zoom){
		this.map = new google.maps.Map(
				map,
			{
					center: new google.maps.LatLng(lat,lng),
					zoom:zoom,
					mapTypeId: google.maps.MapTypeId.HYBRID
			}
		);
		var broadcastRadius=100;
		this.selfMarker = new google.maps.Marker({
			position: new google.maps.LatLng(lat, lng),
			map:this.map,
			draggable:true,
			animation: google.maps.Animation.DROP,
			title:"Here I am!"
		});
		var mp = this;
		this.setRadius=function(rad){
			broadcastRadius=rad;
			tweenCircle(mp.broadcastCircle, rad, 10);

		}
		this.tweenCircle = function(circle, destination, time, m){
			if (circle.radius !== destination && time>0){
				circle.setRadius(circle.radius+((destination-circle.radius)/time));
				setTimeout(tweenCircle, 1, circle, destination, time-1);
			}
			else{
				circle.setRadius(destination);
			}
		}
		var x;
		var tweenCircle=this.tweenCircle;
		google.maps.event.addListener(
			this.selfMarker,
			"dragend",
			(x=function(e){
				if(typeof mp.broadcastCircle === "undefined"){
					mp.broadcastCircle = new google.maps.Circle({
						strokeColor:"#6D0000",
						strokeOpacity: 0.8,
						strokeWeight:2,
						fillColour:"#ffffff",
						fillOpacity:0.35,
						map:mp.map,
						center: this.position
					});
					tweenCircle(mp.broadcastCircle, broadcastRadius, 10);
				}
				else{
					mp.broadcastCircle.setCenter(this.position);
					tweenCircle(mp.broadcastCircle, broadcastRadius, 10);
				}
			}
		));
		x();
		google.maps.event.addListener(
			this.selfMarker,
			"dragstart",
			function(e){
				if (typeof mp.broadcastCircle !== "undefined"){
					tweenCircle(mp.broadcastCircle, 0, 10);
				}
			}
		);
		$(".okay", mapholder).click(function(){
			var pos=window.mapH.selfMarker.getPosition();
			var lat=pos.Xa;
			var lng=pos.Ya;
			$.ajax({
				url:"api/niceplaces.php?action=get&lat="+lat+"&lng="+lng+"&radius="+($(".radius", mapholder).val()*0.0000089982)+"&count=10",
			}).done(function(d){
				$("footer").slideUp("fast", function(){
					var data = d["data"];
					$.each(data, function(){if(this["type"].indexOf("CoastalBathingWater")!==-1){
						$("footer").append("<div><header data-distance='"+haversine([lat,lng],[this.samplePoint.latitude,this.samplePoint.longitude])+"m'>"+
this["name"]+"</header><div>Water quality: "+
this["lastTest"]["verdict"]+"<br/>Types: "+
this["type"].map(camelToTitle).join(", ")+
"<br/><span class='.location'>Location:<span><span>Lat/Lng</span>"+
this.samplePoint["latitude"]+", "+this.samplePoint["longitude"]+
"</span><span><span>OSGB</span>Northing:"+
this.samplePoint["OSGB"]["northing"]+"</br>Easting"+
this.samplePoint["OSGB"]["easting"]+"</span><img src='"+this.image+"'/></div><div class='x'></div>");	
					}});
					$("footer").slideDown("slow");
				});
			});
		});
	
}
function camelToTitle(x){
	var o="", uppercase="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	for(var i=0, ed=x.length;i<ed;i++){
		if(uppercase.indexOf(x[i])===-1){
			o+=x[i];
		}
		else{
			o+=" "+x[i];
		}
	}
	return o;
	
}
function areDefined(){
	for(var i=0, x, end=arguments.length;i<end;i++){
		var x=arguments[i];
		if(typeof x==="undefined"){
			return false;
		}
	}
	return true;
}
function addMap(lat, lng, zoom){
	if(typeof window.mapH === "undefined"){
	if(!areDefined(lat, lng, zoom)){
		addMap(52,-1, 6);
	}
		$(mapholder).slideDown("slow", function(){	
				window.mapH=new beginMapping(lat, lng, zoom);
		});
		var ths;
		(ths=$(".radius", mapholder)).change(function(){
			window.mapH.setRadius(ths.val());
		});
	}
	else{
		if(areDefined(lat, lng, zoom)){
			var here;
			window.mapH.map.setCenter((here= new google.maps.LatLng(lat, lng)));
			window.mapH.zoom=8;
			window.mapH.selfMarker.setPosition(here);
			if (typeof window.map.broadcastCircle !== "undefined"){
				tweenCircle(mp.broadcastCircle, 0, 10);
				window.mapH.broadcastCircle.setCenter(here);
				tweenCircle(mp.broadcastCircle, broadcastRadius, 10);
			}
		}
	}
}

function ndMenuItems(){
	$("#buttons>figure").each(function(){
			$(this).stop().animate({
					"background-color":"#ffffff",
					"color":"#000000"
				}, 200);
			$(this).hover(function(){
			if(!$(this).hasClass("s")){
				$(this).stop().animate({
					"color":"#6D0000"
				}, 200);
			}},function(){
			if(!$(this).hasClass("s")){
				$(this).stop().animate({
					"background-color":"#ffffff",
					"color":"#000000"
				}, 200);
			}});
		$(this).click(function(){
			if(!$(this).hasClass("s")){
				$.each(this.parentNode.childNodes,function(){
					if($(this).hasClass("s")){
						$(this).removeClass("s");
						$(this).animate({
							"color":"#000000"
						}, 200);
					}
				});
				$(this).addClass("s");
				function handleNewInput(a){
						var thisAction = $(a).attr("data-open");
						if(thisAction == "locate"){
							if (navigator.geolocation) {
								navigator.geolocation.getCurrentPosition(function(position){
									addMap(
										position.coords.latitude, 
										position.coords.longitude,
										9
									);
								}, function(){
									alert("Could not locate you. :(")
								});
							}
							else {
								alert("Your browser isn't modern enough to support this feature. :'(");
							}
								
						}
						else if(thisAction == "map"){
							addMap();
						}
						else if(thisAction == "ltlng"){
							//Add the two inputs to inputcanvas
							$(inputcanvas).append("<span style=display:none  class=posinput><input placeholder=Latitude id=lat /><input placeholder=Longitude id=lng /><a class=okay>Okay</a></span>");
							$("span>a.okay", inputcanvas).click(function(){
								addMap($(lat).val(),$(lng).val(),8);	
							});
						$(".posinput").slideDown("slow");
						}
				}
				var posInput;
				var ths=this;
				((posInput=$(".posinput")).length&&(posInput.slideUp("medium", function(){
					posInput.remove();
					handleNewInput(ths);
				})))||
				handleNewInput(ths);
			}
		});
	});
}
o=String.fromCharCode(36,40,77,73,109,41,59);
