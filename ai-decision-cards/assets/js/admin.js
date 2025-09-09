(function(){
	function qs(sel){return document.querySelector(sel);} 
	function qsa(sel){return document.querySelectorAll(sel);} 

	function toggleApiFields(){
		var apiKeyLabel = qs('#api_key_label');
		var apiBaseLabel = qs('#api_base_label');
		var apiBaseDesc = qs('#api_base_desc');
		var modelLabel = qs('#model_label');
		var modelDesc = qs('#model_desc');
		if(!apiKeyLabel) return;
		// Keep server-rendered descriptions intact (avoid overwriting additional security notes)
		apiKeyLabel.textContent = 'API Key';
		if(apiBaseLabel) apiBaseLabel.textContent = 'API Base URL';
		if(apiBaseDesc) apiBaseDesc.textContent = 'Example: https://api.openai.com/ or https://openrouter.ai/api/v1/';
		if(modelLabel) modelLabel.textContent = 'Model';
		if(modelDesc) modelDesc.textContent = 'Example: gpt-3.5-turbo, gpt-4, claude-3-haiku, etc.';
	}

	function initSettingsPage(){
		var testBtn = qs('#aidc_test_api');
		var resultDiv = qs('#aidc_test_result');
		var apiTypeSelect = qs('#aidc_api_type');
		if(!testBtn) return;
		
		// Initialize API fields on page load
		toggleApiFields();
		
		// Bind change event to API type select
		if(apiTypeSelect){
			apiTypeSelect.addEventListener('change', toggleApiFields);
		}
			testBtn.addEventListener('click', function(){
			var apiType = qs('#aidc_api_type') ? qs('#aidc_api_type').value : 'openai';
			var apiKey = qs('#aidc_api_key') ? qs('#aidc_api_key').value : '';
			var apiBase = qs('#aidc_api_base') ? qs('#aidc_api_base').value : '';
			var model = qs('#aidc_model') ? qs('#aidc_model').value : '';
			// Allow empty apiKey here; server will fall back to stored key if available.
			testBtn.disabled = true;
			testBtn.textContent = (aidcAdmin && aidcAdmin.i18n ? aidcAdmin.i18n.testing : 'Testing...');
			if(resultDiv){ resultDiv.innerHTML = '<div class="notice notice-info"><p>'+ (aidcAdmin && aidcAdmin.i18n ? aidcAdmin.i18n.testingConnection : 'Testing API connection...') +'</p></div>'; }
			var formData = new FormData();
			formData.append('action', 'aidc_test_api');
			formData.append('aidc_test_nonce', aidcAdmin && aidcAdmin.nonce ? aidcAdmin.nonce : '');
			formData.append('api_type', apiType);
			formData.append('api_key', apiKey);
			formData.append('api_base', apiBase);
			formData.append('model', model);
			fetch(aidcAdmin && aidcAdmin.ajaxUrl ? aidcAdmin.ajaxUrl : ajaxurl, { method: 'POST', body: formData })
				.then(function(r){ return r.json(); })
				.then(function(data){
					if(resultDiv){
						if(data.success){
							resultDiv.innerHTML = '<div class="notice notice-success"><p>'+ data.data +'</p></div>';
						}else{
							resultDiv.innerHTML = '<div class="notice notice-error"><p>'+ data.data +'</p></div>';
						}
					}
				})
				.catch(function(){
					if(resultDiv){ resultDiv.innerHTML = '<div class="notice notice-error"><p>Network error occurred while testing API.</p></div>'; }
				})
				.finally(function(){
					testBtn.disabled = false;
					testBtn.textContent = (aidcAdmin && aidcAdmin.i18n ? aidcAdmin.i18n.testApiKey : 'Test API Key');
				});
		});
	}

	function initGeneratePage(){
		var form = document.querySelector('form[action*="admin-post.php"]');
		var button = qs('#aidc_generate_btn');
		var statusDiv = qs('#aidc_generate_status');
		if(!form || !button || !statusDiv) return;
		form.addEventListener('submit', function(e){
			var conversationEl = qs('#aidc_conversation');
			var conversation = conversationEl ? conversationEl.value.trim() : '';
			if(!conversation){
				statusDiv.style.display = 'block';
				statusDiv.innerHTML = '<div class="notice notice-error"><p>'+ (aidcAdmin && aidcAdmin.i18n ? aidcAdmin.i18n.genPleaseEnterConversation : 'Please enter a conversation before generating.') +'</p></div>';
				e.preventDefault();
				return false;
			}
			button.disabled = true;
			button.value = (aidcAdmin && aidcAdmin.i18n ? aidcAdmin.i18n.genGenerating : 'Generating... Please wait');
			statusDiv.style.display = 'block';
			statusDiv.innerHTML = '<div class="notice notice-info"><p>'+ (aidcAdmin && aidcAdmin.i18n ? aidcAdmin.i18n.genAnalyzing : 'AI is analyzing your conversation and creating a decision card... This may take 10-30 seconds.') +'</p></div>';
			setTimeout(function(){
				if(button.disabled){
					button.disabled = false;
					button.value = (aidcAdmin && aidcAdmin.i18n ? aidcAdmin.i18n.genGenerateButton : 'Generate Summary & Create Draft');
					statusDiv.innerHTML = '<div class="notice notice-warning"><p>'+ (aidcAdmin && aidcAdmin.i18n ? aidcAdmin.i18n.genLongerThanExpected : 'Taking longer than expected. Please check your API settings or try again.') +'</p></div>';
				}
			}, 45000);
			return true;
		});
	}

	function initShortcodeMetaBox(){
		var shortcodeInputs = qsa('.aidc-shortcode-input');
		if(!shortcodeInputs || shortcodeInputs.length === 0) return;
		
		// Add click event to all shortcode inputs for auto-selection
		for(var i = 0; i < shortcodeInputs.length; i++){
			shortcodeInputs[i].addEventListener('click', function(){
				this.select();
			});
		}
	}

	document.addEventListener('DOMContentLoaded', function(){
		initSettingsPage();
		initGeneratePage();
		initShortcodeMetaBox();
	});
})();


