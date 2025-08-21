(function(){
	window.aidcToggleEmbedInfo = function(){
		var content = document.getElementById('aidc-embed-content');
		var button = document.querySelector('.aidc-embed-button');
		if(!content || !button) return;
		if (content.style.display === 'none') {
			content.style.display = 'block';
			button.setAttribute('aria-expanded','true');
		} else {
			content.style.display = 'none';
			button.setAttribute('aria-expanded','false');
		}
	};
	// Bind click if present
	document.addEventListener('DOMContentLoaded', function(){
		var btn = document.querySelector('.aidc-embed-button');
		if(btn){ btn.addEventListener('click', window.aidcToggleEmbedInfo); }
	});
})();


