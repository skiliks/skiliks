//videos = {
//    divTop: 50,
//    divLeft: 50,
//    divWidth: 50,
//    divHeight: 50,
//    divZindex:5,
//
//    curId : '',
//    isset : 0,
//    curFile:'',
//    setDivTop: function(val)
//    {
//        this.divTop = val;
//    },
//    setDivLeft: function(val)
//    {
//        this.divLeft = val;
//    },
//    setDivWidth: function(val)
//    {
//        this.divWidth = val;
//    },
//    setDivHeight: function(val)
//    {
//        this.divHeight = val;
//    },
//    test:function()
//    {
//        var elem = document.createElement('video'), bool = false;
//       try {
//          if ( bool = !!elem.canPlayType ) {
//             bool = Boolean(bool);
//             bool.ogg = elem.canPlayType('video/ogg; codecs="theora"');
//             // Workaround required for IE9, which doesn't
//             //  report video support without audio codec specified.
//              // bug 599718 @ msft connect
//              var h264 = 'video/mp4; codecs="avc1.42E01E';
//             bool.h264 = elem.canPlayType(h264 + '"') || elem.canPlayType(h264 + ', mp4a.40.2"');
//             bool.webm = elem.canPlayType('video/webm; codecs="vp8, vorbis"');
//           }
//       } catch(e) { }
//        return bool;
//    },
//    start: function(file)
//    {
//        var result = this.test();
//
//        if(this.isset==1){
//            videos.stop();
//        }
//
//        this.isset = 1;
//        this.curFile = file;
//
//        var topZindex = php.getTopZindexOf();
//        var video = document.createElement('video');
//        video.setAttribute('id', 'videoMainDiv');
//
//        video.setAttribute('src', SKConfig.assetsUrl + '/videos/'+file);
//        video.load();
//        video.play();
//
//        video.style.position = "absolute";
//        video.style.zIndex = this.divZindex;
//
//        $('.canvas').append($(video));
//
//        video.addEventListener('ended', function(){videos.onEndFunc(file);});
//
//    },
//
//    onEndFunc: function(file)
//    {
//        if(file != this.curFile){
//            return;
//        }
//        videos.stop();
//    },
//
//    stop: function()
//    {
//        if(this.isset == 0){return;}
//        var playme = document.getElementById('videoMainDiv');
//        playme.pause();
//        $('#videoMainDiv').remove();
//        this.isset = 0;
//    }
//}