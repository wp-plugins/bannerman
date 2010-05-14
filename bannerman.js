var BannerMan = 
{
	el: null,
	banner: '',
	banners: [],
	refresh: 0,
	days: 30,
	location: 'top',
	cookie: true,
	animate: true,
	background: '#333',
	foreground: '#FFF',
	// from http://ejohn.org/blog/flexible-javascript-events/
	addEvent: function(obj, type, fn) {
		if ( obj.attachEvent ) {
			obj['e'+type+fn] = fn;
			obj[type+fn] = function(){obj['e'+type+fn]( window.event );}
			obj.attachEvent( 'on'+type, obj[type+fn] );
		} else {
			obj.addEventListener( type, fn, false );
		}
	},
	// from http://www.quirksmode.org/js/cookies.html
	createCookie: function(name,value,days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = name+"="+value+expires+"; path=/";
	},
	readCookie: function(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	},
	eraseCookie: function(name) {
		BannerMan.createCookie(name,"",-1);
	},
	Load: function() {
		var cookie = BannerMan.readCookie("bannerman");
		if (document.getElementsByTagName && document.getElementsByTagName('body') && (BannerMan.banner != '' || BannerMan.banners.length) && (cookie == null || cookie != "hide")){
			if (BannerMan.banner == '' && BannerMan.banners.length) {
				BannerMan.banner = BannerMan.banners[0];
			}
			var b = document.getElementsByTagName('body')[0];
			var w = document.createElement('div');
			w.id = 'bannerman';
			w.className = BannerMan.location;
			w.innerHTML = BannerMan.banner;
			w.style.background = BannerMan.background;
			w.style.color = BannerMan.foreground;
			if (BannerMan.cookie) {
				var a = document.createElement('a');
				a.id = 'bannermanclose';
				a.innerHTML = 'X';
				w.appendChild(a);
				w.style.paddingRight = '30px';
			}
			BannerMan.el = w;
			if (BannerMan.background != '') {
				BannerMan.Dim();
			}
			b.appendChild(w);
			var height = w.offsetHeight;
			if (BannerMan.location == 'top'){
				if (BannerMan.animate) {
					w.style.top = (height*-1) + 'px';
					BannerMan.OpenTop();
				} else {
					document.getElementsByTagName("body")[0].style.marginTop = (height - 1) + 'px';
				}
				if (BannerMan.cookie) {
					BannerMan.addEvent(a, 'click', function() {
						BannerMan.CloseTop();
						BannerMan.SetCookie();
					});
				}
			} else {
				if (BannerMan.animate) {
					w.style.bottom = (height*-1) + 'px';
					BannerMan.OpenBottom();
				} else {
					document.getElementsByTagName("body")[0].style.marginBottom = (height - 1) + 'px';
				}
				if (BannerMan.cookie) {
					BannerMan.addEvent(a, 'click', function() {
						BannerMan.CloseBottom();
						BannerMan.SetCookie();
					});
				}
			}
			if (BannerMan.background != '') {
				BannerMan.addEvent(w, 'mouseover', BannerMan.Highlight);
				BannerMan.addEvent(w, 'mouseout', BannerMan.Dim);
			}
			if (BannerMan.refresh != 0 && BannerMan.banners.length) {
				window.bannermanrefresh = window.setInterval(BannerMan.RefreshBanner, (BannerMan.refresh*1000));
				BannerMan.addEvent(window, 'unload', function() {
					window.clearInterval(window.bannermanrefresh);
				});
			}
		}
	},
	RefreshBanner: function() {
		var newbanner = BannerMan.banners[Math.floor(Math.random() * BannerMan.banners.length)];
		document.getElementById("bannerman").innerHTML = newbanner;
	},
	SetCookie: function() {
		BannerMan.createCookie("bannerman", "hide", BannerMan.days);
	},
	Highlight: function() {
		var o = 10;
		BannerMan.el.style.opacity = o/10;
		BannerMan.el.style.filter = 'alpha(opacity=' + o*10 + ')';
	},
	Dim: function() {
		var o = 5;
		BannerMan.el.style.opacity = o/10;
		BannerMan.el.style.filter = 'alpha(opacity=' + o*10 + ')';
	},
	OpenTop: function() {
		var top = parseInt(BannerMan.el.style.top.replace('px', ''));
		if (top < 0) {
			BannerMan.el.style.top = (top + 1) + 'px';
			setTimeout(BannerMan.OpenTop, 10);
		}
	},
	CloseTop: function() {
		var height = BannerMan.el.offsetHeight;
		var top = parseInt(BannerMan.el.style.top.replace('px', ''));
		if (top > (height*-1)) {
			BannerMan.el.style.top = (top - 1) + 'px';
			setTimeout(BannerMan.CloseTop, 10);
		}
	},
	OpenBottom: function() {
		var bottom = parseInt(BannerMan.el.style.bottom.replace('px', ''));
		if (bottom < 0) {
			BannerMan.el.style.bottom = (bottom + 1) + 'px';
			setTimeout(BannerMan.OpenBottom, 10);
		}
	},
	CloseBottom: function() {
		var height = BannerMan.el.offsetHeight;
		var bottom = parseInt(BannerMan.el.style.bottom.replace('px', ''));
		if (bottom > (height*-1)) {
			BannerMan.el.style.bottom = (bottom - 1) + 'px';
			setTimeout(BannerMan.CloseBottom, 10);
		}
	}
}