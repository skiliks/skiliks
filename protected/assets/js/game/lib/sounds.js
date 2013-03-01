//sounds = {
//    curId : '',
//    isset : false,
//    curFile:'',
//    test:function()
//    {
//        var elem = document.createElement('audio'), bool = false;
//           try {
//              if ( bool = !!elem.canPlayType ) {
//                 bool = new Boolean(bool);
//                 bool.ogg = elem.canPlayType('audio/ogg; codecs="vorbis"');
//                 bool.mp3 = elem.canPlayType('audio/mpeg;');
//                 bool.wav = elem.canPlayType('audio/wav; codecs="1"');
//                 bool.m4a = elem.canPlayType('audio/x-m4a;') || elem.canPlayType('audio/aac;');
//              }
//           }
//         catch(e) { }   return bool;
//    },
//    start: function(file, cb)
//    {
//
//        var result = this.test();
//        if(this.isset){
//            this.stop();
//        }
//
//        this.isset = true;
//        this.curFile = file;
//
//        var audio = new Audio();
//        audio.setAttribute('id', 'audioMainDiv');
//        audio.setAttribute('src', SKConfig.assetsUrl + '/sounds/'+file);
//        audio.setAttribute('type', 'audio/wav');
//        audio.load();
//        audio.play();
//
//
//        document.body.appendChild(audio);
//        if (cb !== undefined) {
//            audio.addEventListener("loadedmetadata", function(_event) {
//                cb(audio);
//            });
//        }
//        audio.addEventListener('ended', function(){sounds.onEndFunc(file);});
//
//    },
//
//    onEndFunc: function(file)
//    {
//
//        if(file != this.curFile){
//            return;
//        }
//
//        sounds.stop();
//    },
//
//    stop: function()
//    {
//        if(!this.isset){return;}
//        var playme = document.getElementById('audioMainDiv');
//        playme.pause();
//        $('#audioMainDiv').remove();
//
//        this.isset = false;
//    }
//}