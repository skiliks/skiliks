Input = 
{
        
	Keyboard:
	{
		/**
		 * Starts Keyboard Capture.
		 */
		capture: function()
		{
			
                    window.addEventListener('keydown', Input.Keyboard._onkeydown_pd, true);
                    window.addEventListener('keyup', Input.Keyboard._onkeyup_pd, true);
                    window.addEventListener('keypress', Input.Keyboard._onkeypress, true);
			
		},

		/**
		 * Stops Keyboard Capture.
		 */
		release: function()
		{
			window.removeEventListener('keydown', Input.Keyboard._onkeydown, true);
			window.removeEventListener('keyup', Input.Keyboard._onkeyup, true);
			window.removeEventListener('keypress', Input.Keyboard._onkeypress, true);
			window.removeEventListener('keydown', Input.Keyboard._onkeydown_pd, true);
			window.removeEventListener('keyup', Input.Keyboard._onkeyup_pd, true);
		},

		_onkeydown: function(evt)
		{
			Input.Keyboard[evt.keyCode] = true;
		},

		_onkeydown_pd: function(evt)
		{
			Input.Keyboard[evt.keyCode] = true;
			evt.preventDefault();
		},

		_onkeyup: function(evt)
		{
			Input.Keyboard[evt.keyCode] = undefined;
		},

		_onkeyup_pd: function(evt)
		{
			Input.Keyboard[evt.keyCode] = undefined;
			evt.preventDefault();
		},

		_onkeypress: function(evt)
		{
			evt.preventDefault();
		}
	}
}