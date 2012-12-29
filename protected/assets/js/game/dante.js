function Dante() {
	
	var _canvas;
	var _canvasContext;
	var _canvasBuffer;
	var _canvasBufferContext; 
	
	
	this.fps = 30;
	
	/**
	 * Кастомная отрисовка
	 */
	this.draw;
	
	/**
	 * Обработка ввода
	 */
	this.input;
        
        /**
         * идентификатор автообновления
         */
        this.refreshIntervalId;
        
        this.gameInit;
	
	this.init = function() {
		_canvas = document.getElementById('canvas');
		if (_canvas && _canvas.getContext) {
			_canvasContext = _canvas.getContext('2d');
			
			_canvasBuffer = document.createElement('canvas');
			_canvasBuffer.width = _canvas.width;
			_canvasBuffer.height = _canvas.height;
			_canvasBufferContext = _canvasBuffer.getContext('2d');
			//return true;
		}
		
		keyboard = Input.Keyboard;
		
                this.gameInit();
                
		this.refreshIntervalId = setInterval(this.gameLoop, this.fps);
	};
	
	this.drawScene = function()
	{
		//clear canvas
		_canvasBufferContext.clearRect(0, 0, _canvas.width, _canvas.height);
		_canvasContext.clearRect(0, 0, _canvas.width, _canvas.height); 
		
		this.draw(_canvasBufferContext);
		
		//draw buffer on screen
		_canvasContext.drawImage(_canvasBuffer, 0, 0); 
	};
	
	this.gameLoop = function()
	{
      //пока отключим логирование клавиш
      //Input.Keyboard.capture();
      
      dante.input();
      dante.drawScene();
	};
	
	this.run = function()
	{
		this.init();
	};
        
        this.stop = function()
        {
            clearInterval(this.refreshIntervalId);
        };
}