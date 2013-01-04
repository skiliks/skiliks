/* 
 * 
 */
(function() {
    "use strict";
    window.SKEmail = Backbone.Model.extend({
        // @var integer, MySQL id

        mySqlId: undefined,
        
        // @var string, 'M1', 'MS2'
        code: undefined,

        // @var string, 
        constructorCode: undefined,

        // @var string, 
        text: undefined,

        // @var string, 
        previouseEmailText: undefined,

        // @var instance of SkMAilSubject, 
        subject: undefined,

        // @var array of SkCharacter
        recipients: new Array(),
        
        // @var array of SkCharacter
        copyTo: new Array(),
        
        // @var array of SkAttachment
        attachment: undefined,
        
        // @var array of SkMailPhrases
        phrases: new Array(),
        
        // @var bool, use markReaded(), markUnreaded(), isReaded()
        is_readed: false,
        
        // @var bool
        is_has_attachment: false,        
        
        // @var string, 
        sendedAt: undefined,
        
        // @var string,
        // @todo: replace with link to SKCharacter
        senderNameString: undefined,
        
        // @var string,
        // @todo: replace with link to SKCharacter
        senderEmailString: undefined,
        
        // @var string, 
        // @todo: replace with link to SKCharacter
        recipientNameString: undefined,
        
        // @var string, 
        // @todo: replace with link to SKCharacter
        recipientEmailString: undefined,
        
        // @var string, 
        // @todo: replace with links to SKCharacters
        copyToString: undefined,        

        /**
         */
        markReaded: function() {
            this.is_readed = 1;
        },

        /**
         */

        markUnreaded: function() {
            this.is_readed = 0;
        },

        /**
         */

        isReaded: function() {
            return this.is_readed === 1;
        },
        
        /**
         * return string
         */
        getSubjectText: function() {
            return this.subject.getText();
        },
        
        /**
         * @return string, CSS style
         */
        getIsReadedCssClass: function() {
            if (true == this.is_readed) {
                return '';
            } else {
                return ' notreaded ';
            }
        },
        
        /**
         * @return string, CSS style
         */
        getIsHasAttachmentCss: function() {
            if (this.is_has_attachment) {
                return ' display: block; ';
            } else {
                return ' display: none; ';
            }
        }
    });
})();
