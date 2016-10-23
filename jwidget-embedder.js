(function() {
		function async_load(){
			var s = document.createElement('script');
			s.type = 'text/javascript';
			s.async = true;
			var theUrl = 'http://breakoutkings.netlify.com/server/getscript';
			s.src = theUrl + ( theUrl.indexOf("?") >= 0 ? "&" : "?") + 'ref=' + encodeURIComponent(window.location.href);
			var embedder = document.getElementById('jWidget-embedder');
			embedder.parentNode.insertBefore(s, embedder);
		}
		if (window.attachEvent)
			window.attachEvent('onload', async_load);
		else
			window.addEventListener('load', async_load, false);
	})();
