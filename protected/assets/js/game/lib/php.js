/*php = {
    str_replace:function (needle, replacement, haystack) { 
	var temp = haystack.split(needle);
	return temp.join(replacement); 
    },
    arraySearch:function(arr,val) {
        for (var i=0; i<arr.length; i++)
        if (arr[i] == val)
        return i;
        return false;
    },
    is_array:function(input){
        return typeof(input)=='object'&&(input instanceof Array);
    },
    is_string:function(input){
        return typeof(input)=='string';
    },
    is_float:function (input) {
        return +input === input && !!(input % 1);
    },
    is_numeric: function(input){
        return typeof(input)=='number';
    },
    is_int: function(input){
        return parseInt(input)==input;
    },
    LdgZero: function (num,count) 
    {
        var numZeropad = num + '';
        while(numZeropad.length < count) {
            numZeropad = "0" + numZeropad;
        }
        return numZeropad;
    },
    count: function (obj) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) size++;
        }
        return size;
    },
    asc:function(String)
    {
        return String.charCodeAt(0);
    },
    chr: function (AsciiNum)
    {
        return String.fromCharCode(AsciiNum)
    },
    num2alpha: function (n) {
        var r = '';
        for (var i = 1; n >= 0 && i < 10; i++) {
            r = php.chr(0x41 + (n % Math.pow(26, i) / Math.pow(26, i - 1))) + r;
            n -= Math.pow(26, i);
        }
        return r;
    },
    last: function(array)
    {
        return array[array.length - 1];
    },
    parseGetParams: function () { 
       var $_GET = {}; 
       var __GET = window.location.search.substring(1).split("&"); 
       for(var i=0; i<__GET.length; i++) { 
          var getVar = __GET[i].split("="); 
          $_GET[getVar[0]] = typeof(getVar[1])=="undefined" ? "" : getVar[1]; 
       } 
       return $_GET; 
    },
    toUnicode: function (theString) {
      var unicodeString = '';
      for (var i=0; i < theString.length; i++) {
        var theUnicode = theString.charCodeAt(i).toString(16).toUpperCase();
        while (theUnicode.length < 4) {
          theUnicode = '0' + theUnicode;
        }
        theUnicode = '\\u' + theUnicode;
        unicodeString += theUnicode;
      }
      return unicodeString;
    },
    toUnicode2 : function () {
        var uni = [],
            i = this.length;
        while (i--) {
            uni[i] = this.charCodeAt(i);
        }
        return "&#" + uni.join(';&#') + ";";
    },
    getTopZindexOf: function (element)
    {
        var topZindex = 0;
        if(typeof(element) == 'undefined' || ! element){
            element = 'body';
        }
        
        $(element).children().each(function (i) {
            var display = $(this).css('display');
            if(display != 'none'){
                var zTemp = $(this).css('z-index');
                zTemp = parseInt(zTemp);
                if(zTemp > topZindex){
                    topZindex = zTemp;
                }
            }
        });
        return topZindex;
    },
    objReverseSort:function(myObj){
        var keys = [],
            k, i, len,
            newObj={};

        for (k in myObj)
        {
            if (myObj.hasOwnProperty(k))
            {
                keys.push(k);
            }
        }

        keys.reverse();
        len = keys.length;

        for (i = 0; i < len; i++)
        {
            k = keys[i];
            newObj[k] = myObj[k];
        }
        
        return newObj;
    },
    include: function (url, rec_flag, requirements) {
        if(rec_flag && rec_flag==1)
            {
                for (var key in requirements)
                {
                    var requirement = requirements[key];
                    if (eval(requirement) == "undefined")
                    {
                        setTimeout(function() { //У-е-бан-ство
                            php.include(url, rec_flag, requirements); 
                            }, 500);
                        return;
                    }
                }
            }
      var script = document.createElement('script');
      script.setAttribute('type', 'text/javascript')
      script.setAttribute('src', url);
      document.getElementsByTagName('head').item(0).appendChild(script);
    }
}*/